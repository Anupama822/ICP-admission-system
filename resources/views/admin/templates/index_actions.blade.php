@if(!isset($hideShow))
<a href="{{route($route.'show',$id??$item['id'])}}"
   class="btn rounded-lg elevation-1 btn-sm btn-outline-primary mx-1 h-50"><i
        class="fa fa-eye"></i></a>
@endif
@if(!isset($hideEdit))
<a href="{{route($route.'edit',$id??$item['id'])}}"
   class="btn rounded-lg elevation-1 btn-sm btn-outline-success mx-1 h-50"><i
        class="fa fa-pencil-alt"></i></a>
@endif
@if(!isset($hideDelete))
<form class="d-inline" action="{{ route($route.'destroy',$id??$item['id']) }}"
      method="POST" onclick="return confirm('Are you sure?')">
    @csrf
    @method('DELETE')
    <button class="btn rounded-lg elevation-1 btn-sm btn-outline-danger mx-1 h-50"><i
            class="fa fa-trash"></i></button>
</form>
@endif
@foreach($actions??[] as $action)
    {!! $action !!}
@endforeach
