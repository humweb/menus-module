@section('title')
    Menu Items -
    @parent
@stop

{{-- Content --}}
@section('content')

    <div class="container">

        <div class="card card-default">
            <div class="card-header">
                <div class="pull-right">
                    <a class="btn btn-xs btn-primary" href="{{ route('get.admin.menuitem.create', [$menu_id]) }}" title="Add Link" data-toggle="tooltip"><i class="fa fa-plus"></i></a>
                </div>
                <h5>Menus</h5>
            </div>
            <div class="card-body">
                <div class="nestable-lists">
                    <div class="dd" id="nestable">
                        <ol class="dd-list">
                            {!! $content !!}
                        </ol>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    <script src="{{ asset('js/jquery.nestable.js') }}"></script>
    <script>
        $(function () {
            var updateOutput = function (e) {

                var list = e.length ? e : $(e.target), output = list.data('output');
                if (window.JSON) {
                    var data = {
                        _token: '{{ Session::token() }}',
                        menu_id: '{{ $menu_id }}',
                        pages: window.JSON.stringify(list.nestable('serialize'))
                    };
                    $.post('/admin/menus/sort', data, function () {
                    });
                } else {
                    output.val('JSON browser support required for this demo.');
                }
            };

            $('#nestable').nestable({
                maxDepth: 10
            }).on('change', updateOutput);

        });
    </script>
@endsection