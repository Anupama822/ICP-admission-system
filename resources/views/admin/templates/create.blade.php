@extends('layouts.app')

@section('title', 'Create '.$title)

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-12 col-md-10 col-lg-7">

        <div class="icp-form-card">

            {{-- Header --}}
            <div class="icp-form-header d-flex align-items-center gap-3">
                <div class="form-icon">
                    @svg($icon ?? 'heroicon-o-plus-circle', 'icp-icon-lg')
                </div>
                <div>
                    <h1 class="icp-page-title">Create {{ $title }}</h1>
                    @isset($description)
                    <p class="mb-0 small icp-page-subtitle">{{ $description }}</p>
                    @endisset
                </div>
            </div>

            <div class="icp-form-body">
                <form method="POST" action="{{ route($route.'store') }}" enctype="multipart/form-data">
                    @csrf

                    @include('admin.templates.partials.errors')

                    @yield('form_content')

                    {{-- Submit --}}
                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route($route.'index') }}" class="btn icp-btn-muted px-4 py-2 fw-bold">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-5 py-2">
                            <span>Create {{ $title }}</span>
                            @svg('heroicon-m-arrow-right', 'icp-icon-sm')
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection
