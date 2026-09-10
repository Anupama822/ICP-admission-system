@if ($errors->any())
    <div class="alert alert-danger d-flex gap-2 align-items-start" role="alert">
        @svg('heroicon-m-exclamation-triangle', 'icp-icon-sm flex-shrink-0 mt-1')
        <div>
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
@endif
