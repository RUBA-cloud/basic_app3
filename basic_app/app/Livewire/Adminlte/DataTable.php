<?php

namespace App\Livewire\Adminlte;

use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /** Passed-in props (camelCase public properties used by the Blade view) */
    public array $fields = [];                 // [['key'=>'name','label'=>'Name','type'=>null], ...]
    public string $model;                      // FQCN, e.g. App\Models\User

    public ?string $detailsRoute = null;       // route name for Details link (optional)
    public ?string $editRoute = null;          // route name
    public ?string $deleteRoute = null;        // route name for delete (optional)
    public ?string $reactiveRoute = null;      // route name for re-activate (optional)
    public ?string $detailsView = null;        // Blade view for modal (optional)
    public ?string $initialRoute = null;       // route NAME or absolute URL (optional)

    /** Behavior */
    public array $searchIn = ['name_en','name_ar']; // columns to search in
    public int $perPage = 12;
    public string $orderBy = 'id';
    public string $orderDir = 'desc';

    /** State */
    public string $search = '';
    public ?string $initialRouteUrl = null;
    public ?string $detailsHtml = null;

    /**
     * Accept both camelCase and snake_case args so the component works with:
     *  - details-route="sizes.show"
     *  - detailsRoute="sizes.show"
     *  - details_route="sizes.show"
     */
    public function mount(
        array $fields,
        string $model,

        // Route props (both forms accepted)
        ?string $detailsRoute = null,
        ?string $details_route = null,

        ?string $editRoute = null,
        ?string $edit_route = null,

        ?string $deleteRoute = null,
        ?string $delete_route = null,

        ?string $reactiveRoute = null,
        ?string $reactive_route = null,

        ?string $detailsView = null,
        ?string $details_view = null,

        ?string $initialRoute = null,
        ?string $initial_route = null,

        // Search / paging (both forms accepted)
        array $searchIn = ['name_en','name_ar'],
        array $search_in = ['name_en','name_ar'],
        int $perPage = 12,
        int $per_page = 12,
    ): void {
        $this->fields = $fields;
        $this->model  = $model;

        // Normalize to camelCase public properties
        $this->detailsRoute  = $detailsRoute  ?? $details_route  ?? $this->detailsRoute;
        $this->editRoute     = $editRoute     ?? $edit_route     ?? $this->editRoute;
        $this->deleteRoute   = $deleteRoute   ?? $delete_route   ?? $this->deleteRoute;
        $this->reactiveRoute = $reactiveRoute ?? $reactive_route ?? $this->reactiveRoute;
        $this->detailsView   = $detailsView   ?? $details_view   ?? $this->detailsView;
        $this->initialRoute  = $initialRoute  ?? $initial_route  ?? $this->initialRoute;

        $this->searchIn = !empty($searchIn) ? $searchIn : $this->searchIn;
        if (!empty($search_in)) {
            $this->searchIn = $search_in;
        }

        $this->perPage = $perPage ?: $this->perPage;
        if (!empty($per_page)) {
            $this->perPage = $per_page;
        }

        // Resolve initial route to absolute URL (accept route name OR absolute/relative URL)
        if ($this->initialRoute) {
            $this->initialRouteUrl = Str::startsWith($this->initialRoute, ['http://','https://','/'])
                ? $this->initialRoute
                : route($this->initialRoute);
        }
    }

    /** Make it easy for the Blade to check if a details route exists */
    public function getHasDetailsRouteProperty(): bool
    {
        return filled($this->detailsRoute);
    }

    /** If search changes, reset to first page */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        /** @var \Illuminate\Database\Eloquent\Builder $q */
        $q = ($this->model)::query();

        if (filled($this->search) && !empty($this->searchIn)) {
            $term = $this->search;
            $q->where(function ($w) use ($term) {
                foreach ($this->searchIn as $col) {
                    $w->orWhere($col, 'like', "%{$term}%");
                }
            });
        }

        $collection = $q->orderBy($this->orderBy, $this->orderDir)
            ->with($this->guessRelationsFromFields())
            ->paginate($this->perPage)
            ->withQueryString();

        return view('livewire.adminlte.data-table', [
            'rows' => $collection,
        ]);
    }

    /** Eager-load relations inferred from dotted field keys (e.g., "company.name") */
    protected function guessRelationsFromFields(): array
    {
        $rels = [];
        foreach ($this->fields as $f) {
            $key = $f['key'] ?? '';
            if (!is_string($key) || $key === '' || !str_contains($key, '.')) {
                continue;
            }
            $rel = \Illuminate\Support\Str::before($key, '.');

            // only eager-load if the relation method exists on the model class
            if (method_exists($this->model, $rel)) {
                $rels[] = $rel;
            }
        }
        return array_values(array_unique($rels));
    }

    /** Delete via Livewire (if you don't want to post to route) */
    public function delete(int $id): void
    {
        $model = ($this->model)::findOrFail($id);
        $model->delete();

        $this->dispatch('toast', type: 'success', message: __('adminlte::adminlte.delete'));
        $this->resetPage();
    }

    /** Reactivate (toggle is_active to true) via Livewire */
    public function reactivate(int $id): void
    {
        $model = ($this->model)::findOrFail($id);
        if (!is_null($model->is_active)) {
            $model->is_active = true;
            $model->save();
            $this->dispatch('toast', type: 'success', message: __('Reactivated.'));
            $this->resetPage();
        }
    }

    /** Load details (render a view or fallback to pretty JSON) and open modal */
    public function details(int $id): void
    {
        $item = ($this->model)::with($this->guessRelationsFromFields())->findOrFail($id);

        if ($this->detailsView) {
            $this->detailsHtml = view($this->detailsView, compact('item'))->render();
        } else {
            $this->detailsHtml = '<pre class="mb-0">'.e(json_encode($item->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)).'</pre>';
        }

        $this->dispatch('show-details-modal');
    }

    /** Optional hard reload to the initial route URL */
    public function reloadToInitialRoute()
    {
        if ($this->initialRouteUrl) {
            return redirect()->to($this->initialRouteUrl);
        }
    }

    /** Helper for blade to resolve dotted keys */
    public function resolveValue($item, string $key)
    {
        $segments = explode('.', $key);
        $data = $item;

        foreach ($segments as $seg) {
            if (is_array($data)) {
                $data = $data[$seg] ?? null;
            } elseif (is_object($data)) {
                $data = $data->{$seg} ?? null;
            } else {
                return null;
            }
        }
        return $data;
    }
}
