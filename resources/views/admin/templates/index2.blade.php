@extends('layouts.app')
@section('ext_css')
@stop
@section('sub_header')
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <h3 class="text-dark font-weight-bold my-2 mr-5">
                    {{$title}}
                </h3>
                <span class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-4 bg-gray-200"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                <span class="text-muted font-weight-bold mr-4" id="kt_subheader_total">
                    {{$items->total()}} Total </span>
                    <form class="kt-margin-l-20" id="kt_subheader_search_form"
                          action="{{route($route.'index')}}">
                        <div class="kt-input-icon kt-input-icon--right kt-subheader__search">
                            <input type="text" name="query" class="form-control" placeholder="Search..."
                                   id="generalSearch">
                            <span class="kt-input-icon__icon kt-input-icon__icon--right">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                     width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <path
                                            d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>

                                <!--<i class="flaticon2-search-1"></i>-->
                            </span>
                        </span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <a href="#" class="">
                </a>
                <a href="{{route($route.'create')}}" class="btn btn-label-brand btn-bold">
                    Add new</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap kt-grid__item kt-grid__item--fluid">
        <div class="card card--mobile">
            <div class="card-body">
                @yield('form_content')
            </div>
        </div>
    </div>
@endsection
