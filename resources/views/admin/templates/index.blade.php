@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-12">

        <div class="icp-form-card">

            {{-- Header --}}
            <div class="icp-form-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-icon">
                        @svg($icon ?? 'heroicon-o-bars-3', 'icp-icon-lg')
                    </div>
                    <div>
                        <h1 class="icp-page-title">{{ $title }}</h1>
                        @isset($description)
                        <p class="mb-0 small icp-page-subtitle">{{ $description }}</p>
                        @endisset
                    </div>
                </div>

                @unless($hideCreate ?? false)
                <a href="{{ route($route.'create') }}"
                    class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                    @svg('heroicon-m-plus', 'icp-icon-sm')
                    <span>{{ $add_button_name ?? 'Add new' }}</span>
                </a>
                @endunless
            </div>

            <div class="icp-form-body">
                @yield('index_content')
            </div>

        </div>
    </div>
</div>
@endsection
