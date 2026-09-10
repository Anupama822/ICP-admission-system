@extends('admin.templates.create')

@section('title', 'Create '.$title)

@section('form_content')
    @include('admin.courses._form')
@endsection
