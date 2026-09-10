<div class="d-flex align-items-center gap-2">
    <img src="{{ $model->image_url }}" alt="{{ $model->name }}"
        class="rounded-circle border" style="width:32px;height:32px;object-fit:cover">
    <div>
        <div class="fw-semibold" style="line-height:1.2">{{ $model->name }}</div>
        <div class="small" style="color:var(--general-color);font-family:monospace">{{ $model->email }}</div>
    </div>
</div>
