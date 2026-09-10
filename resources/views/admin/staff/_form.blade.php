@php($item = $item ?? null)

<div class="row g-4 mb-4">

    {{-- Full Name --}}
    <div class="col-12 col-sm-6">
        <label for="name" class="form-label fw-semibold small icp-label">Full name</label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $item?->name) }}"
            placeholder="e.g. Maya Gurung"
            required
            autofocus
            class="form-control icp-input @error('name') is-invalid @enderror"
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="col-12 col-sm-6">
        <label for="email" class="form-label fw-semibold small icp-label">Email address</label>
        <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email', $item?->email) }}"
            placeholder="e.g. maya@icp.edu.np"
            required
            class="form-control icp-input @error('email') is-invalid @enderror"
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Position --}}
    <div class="col-12 col-sm-6">
        <label for="position" class="form-label fw-semibold small icp-label">Position / role</label>
        <input
            id="position"
            name="position"
            type="text"
            value="{{ old('position', $item?->position) }}"
            placeholder="e.g. Admissions Counselor"
            required
            class="form-control icp-input @error('position') is-invalid @enderror"
        >
        @error('position')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Status --}}
    <div class="col-12 col-sm-6">
        <label for="status" class="form-label fw-semibold small icp-label">Status</label>
        <select id="status" name="status" required class="form-select icp-input @error('status') is-invalid @enderror">
            <option value="active" @selected(old('status', $item?->status ?? 'active') === 'active')>Active (Login Allowed)</option>
            <option value="inactive" @selected(old('status', $item?->status) === 'inactive')>Inactive (Login Denied)</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @unless($item)
        {{-- Password (create only; staff change their own password afterwards) --}}
        <div class="col-12 col-sm-6">
            <label for="password" class="form-label fw-semibold small icp-label d-flex align-items-center justify-content-between">
                <span>Password</span>
                <button type="button" id="js-generate-password" class="btn btn-link btn-sm p-0 small d-inline-flex align-items-center gap-1 text-decoration-none">
                    @svg('heroicon-m-sparkles', 'icp-icon-sm')
                    <span>Generate</span>
                </button>
            </label>
            <div class="input-group">
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="form-control icp-input @error('password') is-invalid @enderror"
                >
                <button type="button" id="js-toggle-password" class="btn icp-btn-muted" title="Show password">
                    @svg('heroicon-m-eye', 'icp-icon-sm', ['id' => 'js-toggle-password-icon-show'])
                    @svg('heroicon-m-eye-slash', 'icp-icon-sm d-none', ['id' => 'js-toggle-password-icon-hide'])
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text small">Share this with the staff member &mdash; they can change it afterwards from their own account.</div>
        </div>
    @endunless

</div>

@unless($item)
    {{-- Permissions (create only; edit these again later from the staff member's profile) --}}
    <div class="icp-card mb-4">
        <div class="icp-card-header">
            <p class="icp-card-title">Permissions</p>
            <p class="icp-card-subtitle">
                A sensible baseline is pre-ticked &mdash; add or remove anything before creating the account.
            </p>
        </div>
        <div class="p-3">
            @include('admin.staff.partials.permission-fields')
        </div>
    </div>
@endunless

@if(!$item)
    @push('scripts')
        <script>
            (function ($) {
                'use strict';

                function generatePassword(length) {
                    var charset = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%^&*';
                    var values = new Uint32Array(length);
                    window.crypto.getRandomValues(values);

                    var password = '';
                    for (var i = 0; i < length; i++) {
                        password += charset[values[i] % charset.length];
                    }

                    return password;
                }

                function showPassword(show) {
                    $('#password').attr('type', show ? 'text' : 'password');
                    $('#js-toggle-password-icon-show').toggleClass('d-none', show);
                    $('#js-toggle-password-icon-hide').toggleClass('d-none', !show);
                    $('#js-toggle-password').attr('title', show ? 'Hide password' : 'Show password');
                }

                $(document).on('click', '#js-toggle-password', function () {
                    showPassword($('#password').attr('type') === 'password');
                });

                $(document).on('click', '#js-generate-password', function () {
                    $('#password').val(generatePassword(14));
                    showPassword(true);
                });
            })(jQuery);
        </script>
    @endpush
@endif
