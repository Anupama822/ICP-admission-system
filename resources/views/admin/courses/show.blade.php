@extends('admin.templates.show')

@section('title', 'View '.$title)

@section('form_content')
    <dl class="row g-0 mb-0 icp-detail-list">
        <dt class="col-12 col-sm-4">Title</dt>
        <dd class="col-12 col-sm-8">{{ $item->title }}</dd>

        <dt class="col-12 col-sm-4">Display title</dt>
        <dd class="col-12 col-sm-8">{{ $item->displayTitle() }}</dd>

        <dt class="col-12 col-sm-4">Levels</dt>
        <dd class="col-12 col-sm-8">{{ $item->levels ? implode(', ', $item->levels) : '—' }}</dd>

        <dt class="col-12 col-sm-4">Credits</dt>
        <dd class="col-12 col-sm-8">{{ $item->credits ?? '—' }}</dd>

        <dt class="col-12 col-sm-4">Description</dt>
        <dd class="col-12 col-sm-8">{{ $item->description ?: '—' }}</dd>

        <dt class="col-12 col-sm-4">Created at</dt>
        <dd class="col-12 col-sm-8">{{ $item->created_at?->format('d M, Y \a\t H:i') }}</dd>
    </dl>
@endsection
