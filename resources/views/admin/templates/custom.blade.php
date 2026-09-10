@extends('layouts.app')
@section('ext_css')
    @yield('css')
@stop
@section('sub_header')
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <h3 class="text-dark font-weight-bold my-2 mr-5">
                    {{$title}}
                </h3>
                <span class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-4 bg-gray-200"></span>
                <span class="text-muted font-weight-bold mr-4" id="kt_subheader_total">
                    {{$subtitle??"Index"}}</span>
            </div>
            <div class="d-flex align-items-center">
                @yield('sub_header_action')
            </div>
        </div>
    </div>
@endsection
@section('content')
    @yield('sub_content')
    <!-- begin:: Content -->
    <div class="container">
        @yield('search_section')
        @yield('index_content')
    </div>
@endsection
@section('ext_js')
   @yield('js')
@stop
@push('scripts')
    <script>
        $('#kt_subheader_search_form').submit(function () {
            return false;
        });
    </script>
@endpush
