<div class="d-flex align-items-center gap-2">
    @if($model->photo_path)
        <img src="{{ Storage::url($model->photo_path) }}" alt="{{ $model->fullName() }}"
            class="rounded-circle border" style="width:32px;height:32px;object-fit:cover">
    @else
        <span class="rounded-circle border d-inline-flex align-items-center justify-content-center"
            style="width:32px;height:32px;background:var(--background-color);font-size:.7rem;color:var(--general-color)">
            {{ strtoupper(substr($model->first_name, 0, 1).substr($model->last_name, 0, 1)) }}
        </span>
    @endif
    <div>
        <div class="fw-semibold" style="line-height:1.2">{{ $model->fullName() }}</div>
        <div class="small" style="color:var(--general-color);font-family:monospace">{{ $model->email_1 }}</div>
    </div>
</div>
