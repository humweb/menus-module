@section('content')

    {!! Form::open(['route' => 'post.admin.menu.create']) !!}
    <div class="panel panel-default">
        <div class="panel-heading"><h4>Create menu</h4></div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">Title</label>
                {!! Form::text('title', Request::old("title"), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                <label for="slug">Slug</label>
                {!! Form::text('slug', Request::old("slug"), ['class' => 'form-control', 'id' => 'slug']) !!}
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}


    <script>
        $(function () { $('[name=title]').slugify() })
    </script>

@show