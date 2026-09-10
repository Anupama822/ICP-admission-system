@extends('admin.templates.edit')

@section('title', 'Edit '.$title)

@section('form_content')
    @include('admin.admissionYears._form', ['item' => $item])
@endsection
