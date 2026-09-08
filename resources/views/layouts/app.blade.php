@extends('adminlte::page')

@section('title', trim($__env->yieldContent('title', 'Admission Management System')))

@section('adminlte_css_pre')
    <style>
        :root { --bs-body-font-family: 'Manrope', sans-serif; }
        body, * { font-family: 'Manrope', sans-serif !important; }
    </style>
@stop

@section('css')
    @vite('resources/sass/app.scss')
@stop

@section('content_top_nav_right')
    @auth
        @php($activeAcademicYear = \App\Models\AdmissionYear::where('is_active', true)->first())
        @if(Auth::user()->isAdmin())
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle fw-semibold" href="#" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--heading-color)">
                    <i class="fas fa-calendar-alt me-1" style="color: var(--btn-color)"></i>
                    {{ $activeAcademicYear?->title ?? 'Set year' }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:12px">
                    <li><h6 class="dropdown-header fw-bold" style="color:var(--general-color)">Academic Year</h6></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admission-year.create') }}">
                            <i class="fas fa-plus me-2" style="color:var(--btn-color)"></i>Create new academic year
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admission-year.setup') }}">
                            <i class="fas fa-list me-2" style="color:var(--btn-color)"></i>Manage academic years
                        </a>
                    </li>
                </ul>
            </li>
        @else
            <li class="nav-item">
                <span class="nav-link fw-semibold" style="color: var(--heading-color)">
                    <i class="fas fa-calendar-alt me-1" style="color:var(--btn-color)"></i>{{ $activeAcademicYear?->title ?? 'Not set' }}
                </span>
            </li>
        @endif
    @endauth
@stop

@section('content_top_area')
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@stop
