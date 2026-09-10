@extends('admin.templates.create')

@section('title', 'Create '.$title)

@section('form_content')
    @include('admin.admissionYears._form')
@endsection
