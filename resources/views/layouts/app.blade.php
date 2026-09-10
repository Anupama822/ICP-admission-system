@extends('adminlte::page')

@section('title', trim($__env->yieldContent('title', 'Admission Management System')))

@section('adminlte_css_pre')
    <style>
        :root { --bs-body-font-family: 'Manrope', sans-serif; }
        body, * { font-family: 'Manrope', sans-serif !important; }
    </style>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    @vite('resources/sass/app.scss')
    @stack('styles')
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

    {{-- Overrides the csv/excel/pdf/print buttons so they hit the server through
         yajra/laravel-datatables-buttons and export the whole filtered result set
         instead of only the rows currently on screen. Must load last. --}}
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>

    @stack('scripts')
@stop

@section('content_top_nav_right')
    @auth
        @php($activeAcademicYear = \App\Models\AdmissionYear::active()->first())
        @if(Auth::user()->isAdmin())
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle fw-semibold d-inline-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--heading-color)">
                    @svg('heroicon-m-calendar-days', 'icp-icon-sm', ['style' => 'color: var(--btn-color)'])
                    {{ $activeAcademicYear?->title ?? 'Set year' }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:12px">
                    <li><h6 class="dropdown-header fw-bold" style="color:var(--general-color)">Academic Year</h6></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admission-year.create') }}">
                            @svg('heroicon-m-plus', 'icp-icon-sm', ['style' => 'color: var(--btn-color)'])
                            <span>Create new academic year</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admission-year.index') }}">
                            @svg('heroicon-m-bars-3', 'icp-icon-sm', ['style' => 'color: var(--btn-color)'])
                            <span>Manage academic years</span>
                        </a>
                    </li>
                </ul>
            </li>
        @else
            <li class="nav-item">
                <span class="nav-link fw-semibold d-inline-flex align-items-center gap-1" style="color: var(--heading-color)">
                    @svg('heroicon-m-calendar-days', 'icp-icon-sm', ['style' => 'color: var(--btn-color)'])
                    {{ $activeAcademicYear?->title ?? 'Not set' }}
                </span>
            </li>
        @endif
    @endauth
@stop

@section('content_top_area')
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            @svg('heroicon-m-check-circle', 'icp-icon-sm flex-shrink-0')
            <span>{{ session('status') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@stop
