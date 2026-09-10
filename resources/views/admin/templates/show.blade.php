@extends('adminlte::page')
@section('css')
    @stack('styles')
@stop
@section('title', 'Show '.$title)
@section('content_header')
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col">
                    <!-- general form elements -->
                    <div class="card mt-4">
                                        <div class="d-flex justify-content-between align-items-center m-3">
                        <div class="d-flex align-items-center">
                            @if(!isset($hideDefaultBack))
{{--                            @if(!isset($backToIndex))--}}
                            <a href="javascript:history.back();" class="btn btn-outline-gear rounded-lg btn-sm mr-2">
                                <i class="fas fa-arrow-left fa-sm"></i>
                            </a>
                            @else
                            <a href="{{route($route.'index')}}" class="btn btn-outline-gear rounded-lg btn-sm mr-2">
                                <i class="fas fa-arrow-left fa-sm"></i>
                            </a>
                            @endif
                            <h3 class="card-title mb-0">{{$title}}</h3>
                        </div>
                        @if(!isset($hideEdit))
                            <a href="{{route($route.'edit', $item->id)}}" class="btn btn-gear float-right">
                                <i class="fa fa-edit"></i>
                                <span class="kt-hidden-mobile">Edit</span>
                            </a>
                        @endif
                    </div>

                        <div class="card-body">
                            @yield('form_content')

                        </div>
                        <!-- <div class="m-3">
                            <a href="javascript:history.back();" class="btn btn-default float-right">Cancel</a>
                        </div> -->
                    </div>
                    <!-- /.card -->
                </div>
                <!--/.col (left) -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
@endsection

@section('js')
    @stack('scripts')
    <script>
        jQuery(document).ready(function () {
            $('#form input').attr('readonly', true);
            $('#form select').attr('disabled', true);
        });
    </script>
@stop
