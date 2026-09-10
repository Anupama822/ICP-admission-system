@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-12">

        <div class="icp-form-card">

            {{-- Header --}}
            <div class="icp-form-header d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-icon">
                        <svg class="icon-primary" width="20" height="20"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </div>
                    <div>
                        <h1 style="font-size:1.4rem;font-weight:800;color:var(--highlight-heading-color);margin:0;letter-spacing:-.3px">
                            {{ $title }}
                        </h1>
                        @if(@$description)
                        <p class="mb-0 small" style="color:var(--general-color);margin-top:.2rem">
                            {{ $description }}
                        </p>
                        @endif
                    </div>
                </div>

                @if(!isset($hideCreate) || (isset($hideCreate) && $hideCreate === false))
                <a href="{{ route($route.'create') }}"
                    class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                    <i class="fas fa-plus"></i>
                    <span>{{ @$add_button_name ?? 'Add new' }}</span>
                </a>
                @endif
            </div>

            <div class="icp-form-body">
                @yield('index_content')
            </div>

        </div>
    </div>
</div>
@endsection