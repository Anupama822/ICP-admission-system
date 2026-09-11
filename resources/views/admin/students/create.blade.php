@extends('admin.templates.create')

@section('title', 'Create '.$title)

@section('form_content')
    @include('admin.students._form')
@endsection
