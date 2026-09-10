@php
    $sort = request('sort');
    $dir  = request('dir', 'asc');

    // Helper to render sort icon
    $sortIcon = function ($field) use ($sort, $dir) {
        if ($sort !== $field) return '<i class="fas fa-sort text-muted ms-1" style="font-size:.7rem;opacity:.5"></i>';
        return $dir === 'asc'
            ? '<i class="fas fa-sort-up ms-1" style="font-size:.75rem;color:var(--highlight-heading-color)"></i>'
            : '<i class="fas fa-sort-down ms-1" style="font-size:.75rem;color:var(--highlight-heading-color)"></i>';
    };
@endphp

<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead>
            <tr style="border-bottom:2px solid #f0f1f3">
                <th style="color:var(--general-color);font-weight:700;font-size:.85rem">#</th>
                <th data-sort="year" role="button" style="color:var(--general-color);font-weight:700;font-size:.85rem;user-select:none">
                    Admission Year {!! $sortIcon('year') !!}
                </th>
                <th data-sort="status" role="button" style="color:var(--general-color);font-weight:700;font-size:.85rem;user-select:none">
                    Status {!! $sortIcon('status') !!}
                </th>
                <th data-sort="created_at" role="button" style="color:var(--general-color);font-weight:700;font-size:.85rem;user-select:none">
                    Created At {!! $sortIcon('created_at') !!}
                </th>
                <th style="color:var(--general-color);font-weight:700;font-size:.85rem" class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admissionYears as $key => $admissionYear)
            <tr style="border-bottom:1px solid #f5f5f5">
                <td>{{ $admissionYears->firstItem() + $key }}</td>
                <td style="font-weight:600;color:var(--highlight-heading-color)">
                    {{ $admissionYear->year }}
                </td>
                <td>
                    @if($admissionYear->status == 1)
                        <span class="badge" style="background:#e6f7ec;color:#1a9d4e;font-weight:600;padding:.4rem .8rem;border-radius:20px">
                            Active
                        </span>
                    @else
                        <span class="badge" style="background:#fdeaea;color:#e05252;font-weight:600;padding:.4rem .8rem;border-radius:20px">
                            Inactive
                        </span>
                    @endif
                </td>
                <td style="color:var(--general-color)">
                    {{ $admissionYear->created_at?->format('d M, Y') }}
                </td>
                <td class="text-end">
                    <div class="d-inline-flex gap-2">
                        {{-- href="{{ route($route.'edit', $admissionYear->id) }}" --}}
                        <a 
                            class="btn btn-sm"
                            style="background:#eef2ff;color:#4f46e5;border:none;border-radius:8px;padding:.4rem .7rem">
                            <i class="fas fa-pen"></i>
                        </a>
                        {{-- action="{{ route($route.'destroy', $admissionYear->id) }}" --}}
                        <form  method="POST"
                            class="d-inline js-delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm"
                                style="background:#fdeaea;color:#e05252;border:none;border-radius:8px;padding:.4rem .7rem">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5" style="color:var(--general-color)">
                    <i class="fas fa-inbox mb-2 d-block" style="font-size:1.8rem;opacity:.4"></i>
                    No admission years found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($admissionYears->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
    <small style="color:var(--general-color)">
        Showing {{ $admissionYears->firstItem() }}–{{ $admissionYears->lastItem() }} of {{ $admissionYears->total() }}
    </small>
    {{ $admissionYears->appends(request()->query())->links() }}
</div>
@endif