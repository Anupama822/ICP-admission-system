@extends('layouts.app')

@section('title', 'Edit '.$title)

@section('content')
{{--
    AdminLTE's .app-content adds its own fixed 0.5rem side padding on top of
    the Bootstrap grid gutter; a wide form breaks out of both so the card
    sits flush with the sidebar/topbar instead of leaving a visible margin.
--}}
<div class="row justify-content-center my-4" @if($wide ?? false) style="margin-left:calc(var(--bs-gutter-x, 1.5rem) * -.5 - .5rem);margin-right:calc(var(--bs-gutter-x, 1.5rem) * -.5 - .5rem)" @endif>
    <div class="col-12 {{ ($wide ?? false) ? 'px-0' : 'col-md-10 col-lg-7' }}">

        <div class="icp-form-card">

            {{-- Header --}}
            <div class="icp-form-header d-flex align-items-center gap-3">
                <a href="{{ route($route.'index') }}" class="btn icp-btn-icon" title="Back to {{ $title }}">
                    @svg('heroicon-m-arrow-left', 'icp-icon-sm')
                </a>
                <div class="form-icon">
                    @svg($icon ?? 'heroicon-o-pencil-square', 'icp-icon-lg')
                </div>
                <div>
                    <h1 class="icp-page-title">Edit {{ $title }}</h1>
                    @isset($description)
                    <p class="mb-0 small icp-page-subtitle">{{ $description }}</p>
                    @endisset
                </div>
            </div>

            <div class="icp-form-body">
                <form method="POST" action="{{ route($route.'update', $item->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('admin.templates.partials.errors')

                    @yield('form_content')

                    {{-- Submit --}}
                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route($route.'index') }}" class="btn icp-btn-muted px-4 py-2 fw-bold">
                            Cancel
                        </a>
                        <button type="submit" id="icp-form-submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-5 py-2">
                            <span>Update {{ $title }}</span>
                            @svg('heroicon-m-check', 'icp-icon-sm')
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection
