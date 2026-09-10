@extends('layouts.app')

@section('title', 'Create '.$title)


@section('content')
<div class="row justify-content-center my-4">
    <div class="col-12 col-md-10 col-lg-7">

        <div class="icp-form-card">

            {{-- Header --}}
            <div class="icp-form-header d-flex align-items-center gap-3">
                <div class="form-icon">
                    <svg class="icon-primary" width="20" height="20"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>                </div>
                <div>
                    <h1 style="font-size:1.4rem;font-weight:800;color:var(--highlight-heading-color);margin:0;letter-spacing:-.3px">
                        Create {{$title}}
                    </h1>
                    @if($description)
                    <p class="mb-0 small" style="color:var(--general-color);margin-top:.2rem">
                        {{ $description }}
                    </p>
                    @endif
                </div>
            </div>


            <div class="icp-form-body">
                <form method="POST" action="{{route($route.'store')}}" enctype="multipart/form-data">
                    @csrf


                    @yield('form_content')

                    {{-- Submit --}}
                    <div class="d-flex justify-content-end gap-3">
                        <a href="javascript:history.back();"
                            class="btn px-4 py-2 fw-bold"
                            style="background:#f3f4f6;color:var(--general-color);border:none;border-radius:10px">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-5 py-2">
                            Create {{$title}}
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

@endsection
