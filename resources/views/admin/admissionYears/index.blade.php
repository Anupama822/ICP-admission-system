@extends('admin.templates.index')

@section('title', 'Admission Year')

@section('index_content')

{{-- Toolbar: Search + Export --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

    <div class="d-flex align-items-center gap-2">
        <div class="position-relative">
            <input type="text" id="searchInput" value="{{ request('search') }}"
                class="form-control ps-4" placeholder="Search admission year..."
                style="border-radius:10px;min-width:240px">
            <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:.8rem"></i>
        </div>

        <select id="perPage" class="form-select" style="border-radius:10px;width:auto">
            @foreach([10, 25, 50, 100] as $pp)
                <option value="{{ $pp }}" {{ request('per_page', 10) == $pp ? 'selected' : '' }}>{{ $pp }} / page</option>
            @endforeach
        </select>
    </div>

    {{-- Export options - only shown if export routes are provided --}}
    @if(!isset($hideExport))
    <div class="dropdown">
        <button class="btn dropdown-toggle d-inline-flex align-items-center gap-2 px-3 py-2"
            type="button" data-bs-toggle="dropdown"
            style="background:#f3f4f6;color:var(--general-color);border:none;border-radius:10px">
            <i class="fas fa-file-export"></i> Export
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:10px">
            @isset($exportRoutes['pdf'])
            <li><a class="dropdown-item export-link" href="{{ route($exportRoutes['pdf']) }}" data-type="pdf">
                <i class="fas fa-file-pdf text-danger me-2"></i> Export as PDF
            </a></li>
            @endisset
            @isset($exportRoutes['excel'])
            <li><a class="dropdown-item export-link" href="{{ route($exportRoutes['excel']) }}" data-type="excel">
                <i class="fas fa-file-excel text-success me-2"></i> Export as Excel
            </a></li>
            @endisset
            @isset($exportRoutes['csv'])
            <li><a class="dropdown-item export-link" href="{{ route($exportRoutes['csv']) }}" data-type="csv">
                <i class="fas fa-file-csv text-primary me-2"></i> Export as CSV
            </a></li>
            @endisset
        </ul>
    </div>
    @endif

</div>

{{-- Table wrapper: this whole block gets swapped on AJAX search/sort/paginate --}}
<div id="tableWrapper" style="position:relative;min-height:200px">
    @include('admin.admissionYears._table', ['admissionYears' => $admissionYears])
</div>

@endsection

@push('scripts')
<script>
(function () {
    const wrapper   = document.getElementById('tableWrapper');
    const searchBox = document.getElementById('searchInput');
    const perPage   = document.getElementById('perPage');
    const baseUrl   = "{{ route($route.'index') }}";

    let debounceTimer = null;
    let currentParams = new URLSearchParams(window.location.search);

    function showLoading() {
        // Dim the existing table and show a spinner overlay instead of blanking it,
        // so the layout doesn't jump and the user keeps context.
        let overlay = document.getElementById('tableLoadingOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'tableLoadingOverlay';
            overlay.style.cssText = `
                position:absolute; inset:0; background:rgba(255,255,255,.6);
                display:flex; align-items:center; justify-content:center;
                border-radius:10px; z-index:5;
            `;
            overlay.innerHTML = `
                <div class="d-flex flex-column align-items-center gap-2">
                    <div class="spinner-border" role="status"
                        style="width:2rem;height:2rem;color:var(--highlight-heading-color, #4f46e5)">
                    </div>
                    <small style="color:var(--general-color)">Loading...</small>
                </div>`;
            wrapper.appendChild(overlay);
        }
        overlay.style.opacity = '1';
        wrapper.style.opacity = '0.6';
    }

    function hideLoading() {
        const overlay = document.getElementById('tableLoadingOverlay');
        if (overlay) overlay.remove();
        wrapper.style.opacity = '1';
    }

    function fetchTable(params, pushState = true) {
        showLoading();

        const url = `${baseUrl}?${params.toString()}`;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.text();
        })
        .then(html => {
            wrapper.innerHTML = html;
            if (pushState) window.history.pushState({}, '', url);
        })
        .catch(() => {
            wrapper.innerHTML = `
                <div class="text-center py-5" style="color:#e05252">
                    <i class="fas fa-triangle-exclamation mb-2 d-block" style="font-size:1.5rem"></i>
                    Something went wrong. Please try again.
                </div>`;
        })
        .finally(hideLoading);
    }

    // Debounced search
    searchBox.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            currentParams.set('search', searchBox.value);
            currentParams.set('page', 1);
            fetchTable(currentParams);
        }, 400);
    });

    // Per page
    perPage.addEventListener('change', function () {
        currentParams.set('per_page', perPage.value);
        currentParams.set('page', 1);
        fetchTable(currentParams);
    });

    // Delegate: pagination links + sortable headers, since table content is replaced
    document.addEventListener('click', function (e) {
        const pageLink = e.target.closest('#tableWrapper .pagination a');
        const sortLink = e.target.closest('#tableWrapper th[data-sort]');

        if (pageLink) {
            e.preventDefault();
            const linkParams = new URL(pageLink.href).searchParams;
            currentParams.set('page', linkParams.get('page') || 1);
            fetchTable(currentParams);
        }

        if (sortLink) {
            e.preventDefault();
            const field = sortLink.dataset.sort;
            const currentSort = currentParams.get('sort');
            const currentDir  = currentParams.get('dir', 'asc');
            const newDir = (currentSort === field && currentDir === 'asc') ? 'desc' : 'asc';

            currentParams.set('sort', field);
            currentParams.set('dir', newDir);
            fetchTable(currentParams);
        }
    });

    // Back/forward browser buttons
    window.addEventListener('popstate', function () {
        currentParams = new URLSearchParams(window.location.search);
        fetchTable(currentParams, false);
    });
})();
</script>
@endpush