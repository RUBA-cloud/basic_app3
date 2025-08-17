<div id="dialogContent"
     class="dialog bg-white rounded-xl shadow-2xl p-6 max-w-2xl w-full relative animate-fade-in">

    <div class="dialog-body">
        <!-- Image -->
        <div class="dialog-image">
            <div class="image-container">
                <img src="{{ $category->image ? asset($category->image) : 'https://placehold.co/300x200?text=Category+Image' }}"
                     alt="Category Image"
                     class="category-image">
            </div>
        </div>

        <!-- Details -->
        <div class="dialog-details">
            <h2 class="category-title">{{ $category->name_en }}</h2>
            <div class="category-subtitle">
                <strong>{{ $category->name_ar }}</strong>
            </div>

            <div class="status-container">
                @if($category->is_active)
                    <span class="status-badge active">Active</span>
                @else
                    <span class="status-badge inactive">Inactive</span>
                @endif
            </div>

            <div class="branches-container">
                <h4 class="branches-title">Branches:</h4>
                @if($category->branches->count())
                    <ul class="branches-list">
                        @foreach($category->branches as $branch)
                            <li>
                                <a href="{{ route('companyBranch.show', $branch->id) }}" class="branch-link">
                                    {{ $branch->name_en }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="no-branches">No branches available.</p>
                @endif
            </div>

            <div class="edit-button">
                <a href="{{ route('categories.edit', $category->id) }}"
                   class="edit-link">
                    Edit Category
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Animation */
@keyframes fade-in {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

/* Layout */
.dialog-body {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
}
.dialog-image {
    flex: 1.2;
    min-width: 240px;
}
.dialog-details {
    flex: 2;
    min-width: 240px;
}

/* Image */
.image-container {
    background: #f7f7fa;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    justify-content: center;
    align-items: center;
}
.category-image {
    max-width: 100%;
    max-height: 200px;
    border-radius: 10px;
    object-fit: cover;
}

/* Titles & Text */
.category-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #22223B;
    margin-bottom: 6px;
}
.category-subtitle {
    color: #888;
    font-size: 1rem;
    margin-bottom: 10px;
}
.status-container {
    margin-bottom: 14px;
}
.branches-container {
    margin-bottom: 18px;
}
.branches-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2D3748;
    margin-bottom: 6px;
}
.branches-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.branch-link {
    color: #6C63FF;
    font-weight: 600;
    text-decoration: none;
}
.branch-link:hover {
    text-decoration: underline;
}
.no-branches {
    color: #666;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 8px;
    font-size: 0.9rem;
}
.status-badge.active {
    background: #d1fae5;
    color: #059669;
}
.status-badge.inactive {
    background: #fee2e2;
    color: #b91c1c;
}

/* Button */
.edit-button {
    margin-top: 10px;
}
.edit-link {
    display: inline-block;
    background: #4F46E5;
    color: #fff;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
}
.edit-link:hover {
    background: #4338CA;
}
</style>
