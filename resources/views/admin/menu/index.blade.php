@section('title')
    Menus -
    @parent
@stop

{{-- Content --}}
@section('content')
    <div class="card card-default">
        <div class="card-header">
            <div class="pull-right">
                <a class="btn btn-sm btn-secondary" href="{{ route('get.admin.menu.create') }}" title="Add menu" data-toggle="tooltip"><i class="fa fa-plus"></i></a>
            </div>
            <h4>Menus</h4>
        </div>
        <div class="card-body">
            @if ( ! $menus->isEmpty())
                <table class="table">
                    <thead>
                    <th colspan="2">Name</th>
                    </thead>
                    <tbody>
                    @foreach ($menus as $menu)
                        <tr>
                            <td width="75%">{{ $menu->title }}</td>
                            <td class="text-right">
                                <a class="btn btn-primary btn-sm" href="{{ route('get.admin.menuitem.index', [$menu->id]) }}" title="Manage" data-toggle="tooltip">
                                    <i class="fa fa-list"></i></a>
                                <a class="btn btn-primary btn-sm" href="{{ route('get.admin.menu.edit', [$menu->id]) }}" title="Edit" data-toggle="tooltip">
                                    <i class="fa fa-pencil"></i></a>
                                {{--<a class="btn btn-danger btn-sm confirm" href="{{ route('get.admin.menu.delete', ['id'=>$menu->id]) }}" title="Remove" data-toggle="tooltip">--}}
                                {{--<i class="fa fa-remove"></i></a>--}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <h5>No Menus added yet..</h5>
            @endif
        </div>
    </div>
@stop
