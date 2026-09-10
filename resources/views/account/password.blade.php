@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="icp-form-card">

            {{-- Header --}}
            <div class="icp-form-header d-flex align-items-center gap-3">
                <div class="form-icon">
                    @svg('heroicon-o-key', 'icp-icon-lg')
                </div>
                <div>
                    <h1 class="icp-page-title">Change Password</h1>
                    <p class="mb-0 small icp-page-subtitle">Update the password for your own account.</p>
                </div>
            </div>

            <div class="icp-form-body">
                <form method="POST" action="{{ route('account.password.update') }}">
                    @csrf
                    @method('PUT')

                    @include('admin.templates.partials.errors')

                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <label for="current_password" class="form-label fw-semibold small icp-label">Current password</label>
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                placeholder="••••••••••••"
                                required
                                autofocus
                                class="form-control icp-input @error('current_password') is-invalid @enderror"
                            >
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="password" class="form-label fw-semibold small icp-label">New password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="••••••••••••"
                                required
                                class="form-control icp-input @error('password') is-invalid @enderror"
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="password_confirmation" class="form-label fw-semibold small icp-label">Confirm new password</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                placeholder="••••••••••••"
                                required
                                class="form-control icp-input"
                            >
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-5 py-2">
                            <span>Update Password</span>
                            @svg('heroicon-m-check', 'icp-icon-sm')
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection
