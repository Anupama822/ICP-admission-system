@extends('admin.templates.show')

@section('title', 'View '.$title)

@section('form_content')
    <dl class="row g-0 mb-0 icp-detail-list">
        <dt class="col-12 col-sm-4">Title</dt>
        <dd class="col-12 col-sm-8">{{ $item->title }}</dd>

        <dt class="col-12 col-sm-4">Starting year</dt>
        <dd class="col-12 col-sm-8">{{ $item->year }}</dd>

        <dt class="col-12 col-sm-4">Status</dt>
        <dd class="col-12 col-sm-8">
            @include('admin.admissionYears.partials.status', ['model' => $item])
        </dd>

        <dt class="col-12 col-sm-4">Created at</dt>
        <dd class="col-12 col-sm-8">{{ $item->created_at?->format('d M, Y \a\t H:i') }}</dd>

        <dt class="col-12 col-sm-4">Last updated</dt>
        <dd class="col-12 col-sm-8">{{ $item->updated_at?->format('d M, Y \a\t H:i') }}</dd>
    </dl>

    @unless($item->is_active)
        <form method="POST" action="{{ route($route.'activate', $item->getKey()) }}" class="mt-4">
            @csrf
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2">
                @svg('heroicon-m-bolt', 'icp-icon-sm')
                <span>Make this the active admission year</span>
            </button>
        </form>
    @endunless
@endsection
