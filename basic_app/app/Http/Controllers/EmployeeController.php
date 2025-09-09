<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use App\Models\EmployeeHistory;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request) {
        $q = trim((string)$request->get('q', '')); $employees = User::employees() ->when($q !== '', fn($qq) => $qq->where(function($w) use ($q) { $w->where('name','like',"%$q%") ->orWhere('email','like',"%$q%"); }) ) ->with('permissions') ->latest() ->paginate(12) ->withQueryString(); return view('employee.index', compact('employees','q'));
    }
     public function history(Request $request) {
        $q = trim((string)$request->get('q', '')); $employees = EmployeeHistory::employees() ->when($q !== '', fn($qq) => $qq->where(function($w) use ($q) { $w->where('name','like',"%$q%") ->orWhere('email','like',"%$q%"); }) ) ->with('permissions') ->latest() ->paginate(12) ->withQueryString();
        return view('employee.history', compact('employees','q'));
    }


    public function create()
    {
        $permissions = Permission::orderBy('created_at')->get();
        return view('employee.create', compact('permissions'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => 'employee',
            'avatar_path' => $avatarPath,
        ]);

        $user->permissions()->sync($data['permissions'] ?? []);
        return redirect()->route('employees.show', $user)
            ->with('success', __('adminlte::menu.employees'));
    }
public function reactivate($historyId)
{
    // 1) Load the history snapshot (with permissions if relation exists)
    $history = EmployeeHistory::with(['permissions' => function ($q) {
        $q->select('permissions.id');
    }])->findOrFail($historyId);

    // Optional: sanity check it’s an employee record
    if (method_exists($history, 'isEmployee') && !$history->isEmployee()) {
        abort(404);
    }

    // 2) Try to find the original user (even if soft-deleted)
    $user = null;
    if (!empty($history->user_id)) {
        $user = User::withTrashed()->find($history->user_id);
    }
    if (!$user && !empty($history->email)) {
        $user = User::withTrashed()->where('email', $history->email)->first();
    }

    // 3) Decide target email (must be unique)
    $targetEmail = (string) $history->email;
    $conflict = fn ($email, $excludeId = null) =>
        User::withTrashed()
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where('email', $email)
            ->exists();

    if ($user) {
        // if another user already uses the same email, retag the email
        if ($conflict($targetEmail, $user->id)) {
            $local = Str::before($targetEmail, '@');
            $domain = Str::after($targetEmail, '@');
            $targetEmail = $local . '+' . now()->format('YmdHis') . '@' . $domain;
        }
    } else {
        if ($conflict($targetEmail)) {
            $local = Str::before($targetEmail, '@');
            $domain = Str::after($targetEmail, '@');
            $targetEmail = $local . '+' . now()->format('YmdHis') . '@' . $domain;
        }
    }

    // 4) Restore or (re)create user from history
    if ($user) {
        if (method_exists($user, 'trashed') && $user->trashed()) {
            $user->restore();
        }

        $user->name        = (string) $history->name;
        $user->email       = $targetEmail;
        $user->role        = 'employee';
        if (!empty($history->avatar_path)) {
            $user->avatar_path = $history->avatar_path;
        }
        // If your history stored the hashed password, you can restore it safely
        if (!empty($history->password)) {
            $user->password = $history->password; // assumed already hashed in history
        }
        $user->save();
    } else {
        $user = User::create([
            'name'        => (string) $history->name,
            'email'       => $targetEmail,
            'password'    => $history->password ?: Str::password(16), // already-hashed preferred
            'role'        => 'employee',
            'avatar_path' => $history->avatar_path ?: null,
        ]);
    }

    // 5) Re-attach permissions from history (supports relation OR array attr)
    $permIds = [];
    if (method_exists($history, 'permissions')) {
        $permIds = $history->permissions->pluck('id')->all();
    } elseif (is_array($history->permissions ?? null)) {
        $permIds = array_filter(array_map('intval', $history->permissions));
    }
    if (!empty($permIds)) {
        $user->permissions()->sync($permIds);
    }

    // 6) Log the reactivation in history (optional but recommended)
    if (method_exists(EmployeeHistory::class, 'log')) {
        EmployeeHistory::log($user, 'reactivated', [
            'source'           => 'employees.reactivate',
            'from_history_id'  => $history->id,
        ], true);
    }

    return redirect()
        ->route('employees.show', $user)
        ->with('success', __('Employee reactivated successfully.'));
}
   public function show(User $employee) {
    abort_unless($employee->role === 'employee', 404);
     $employee->load('permissions'); return view('employee.show', compact('employee'));
     }
   public function edit($id) {
    $employee = User::find($id);
    abort_unless($employee->role === 'employee', 404);
    $permissions = Permission::orderBy('name_en')->get();
    $employee->load('permissions');
    return view('employee.edit', compact('employee','permissions')); }

    public function update(UpdateEmployeeRequest $request, User $employee)
    {
        abort_unless($employee->role === 'employee', 404);

        $data = $request->validated();

        // 1) Log BEFORE snapshot
       EmployeeHistory::log($employee, 'updated', ['source' => 'employees.update', 'when' => 'before'],true);

        // 2) Apply changes
        if ($request->hasFile('avatar')) {
            if ($employee->avatar_path && Storage::disk('public')->exists($employee->avatar_path)) {
                Storage::disk('public')->delete($employee->avatar_path);
            }
            $employee->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        $employee->name  = $data['name'];
        $employee->email = $data['email'];
        if (!empty($data['password'])) {
            $employee->password = Hash::make($data['password']);
        }
        $employee->save();

        $employee->permissions()->sync($data['permissions'] ?? []);

        // (Optional) Also log an AFTER snapshot (comment out if you only want "old" values)
        // EmployeeHistory::log($employee, 'updated', ['source' => 'employees.update', 'when' => 'after']);

        return redirect()->route('employees.show', $employee)
            ->with('success', __('adminlte::menu.employees'));
    }

    public function destroy(User $employee)
    {
        abort_unless($employee->role === 'employee', 404);

        // Log BEFORE delete (so we keep the old data)
        EmployeeHistory::log($employee, 'deleted', ['source' => 'employees.destroy', 'when' => 'before'],false);

        if ($employee->avatar_path && Storage::disk('public')->exists($employee->avatar_path)) {
            Storage::disk('public')->delete($employee->avatar_path);
        }
        $employee->delete();

        return redirect()->route('employees.index')->with('success', __('adminlte::menu.account_settings'));
    }
}
