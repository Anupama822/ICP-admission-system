@extends('admin.templates.edit')

@section('title', 'Edit '.$title)

@section('form_content')
    @include('admin.staff._form')
@endsection
