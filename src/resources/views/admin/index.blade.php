@section('title')
    Menu Items -
    @parent
@stop

{{-- Content --}}
@section('content')

    <div class="container">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="pull-right">
                    <a class="btn btn-sm btn-primary" href="{{ route('get.admin.menuitem.create', [$menu_id]) }}" title="Add Link" data-toggle="tooltip"><i class="fa fa-plus"></i></a>

                </div>
                <h4>Menus</h4>
            </div>
            <div class="panel-body">

                <div class="cf nestable-lists">
                    <div class="dd" id="nestable">
                        <ol class="dd-list">
                            {!! $content !!}
                        </ol>
                    </div>
                </div>

            </div>
        </div>

        <script src="{{ asset('js/jquery.nestable.js') }}"></script>

        <script>
            $(function(){
                var updateOutput = function(e){

                    var list = e.length ? e : $(e.target), output = list.data('output');
                    if (window.JSON) {
                        var data = {
                            _token: '{{ Session::token() }}',
                            menu_id: '{{ $menu_id }}',
                            pages:window.JSON.stringify(list.nestable('serialize'))
                        };
                        $.post('/admin/menus/sort', data, function(){});
                    } else {
                        output.val('JSON browser support required for this demo.');
                    }
                };

                $('#nestable').nestable({
                    maxDepth: 10
                }).on('change', updateOutput);

            });
        </script>
        @section('style')
            <link rel="stylesheet" href="{{ asset('assets/css/dd.css') }}">
        @show
    </div>
@show