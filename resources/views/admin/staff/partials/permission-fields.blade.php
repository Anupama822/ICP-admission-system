@foreach($permissionCatalog as $category => $permissions)
    <div class="mb-3">
        <p class="fw-semibold small mb-2">{{ $category }}</p>
        <div class="row g-2">
            @foreach($permissions as $slug => $label)
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="form-check icp-form-check">
                        <input
                            class="form-check-input js-permission-toggle"
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $slug }}"
                            id="permission-{{ $slug }}"
                            @checked(in_array($slug, $checkedPermissions, true))
                        >
                        <label class="form-check-label small" for="permission-{{ $slug }}">{{ $label }}</label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach
