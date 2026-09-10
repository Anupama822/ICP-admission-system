@extends('layouts.app')

@section('title', 'View '.$title)

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-12 col-md-10 col-lg-7">

        <div class="icp-form-card">

            {{-- Header --}}
            <div class="icp-form-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route($route.'index') }}" class="btn icp-btn-icon" title="Back to {{ $title }}">
                        @svg('heroicon-m-arrow-left', 'icp-icon-sm')
                    </a>
                    <div class="form-icon">
                        @svg($icon ?? 'heroicon-o-eye', 'icp-icon-lg')
                    </div>
                    <div>
                        <h1 class="icp-page-title">{{ $title }}</h1>
                        @isset($description)
                        <p class="mb-0 small icp-page-subtitle">{{ $description }}</p>
                        @endisset
                    </div>
                </div>

                @unless($hideEdit ?? false)
                <a href="{{ route($route.'edit', $item->id) }}"
                    class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                    @svg('heroicon-m-pencil-square', 'icp-icon-sm')
                    <span>Edit</span>
                </a>
                @endunless
            </div>

            <div class="icp-form-body">
                @yield('form_content')
            </div>

        </div>
    </div>
</div>
@endsection
