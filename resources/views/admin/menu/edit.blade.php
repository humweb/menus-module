@section('content')
    {!! Form::open(['route' => array('post.admin.menu.edit', $menu->id)]) !!}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>Edit Menu <small> : {{ $menu->title }}</small></h4>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">Title</label>
                {!! Form::text('title', Request::old("title", $menu->title), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                <label for="slug">Slug</label>
                 {!! Form::text('slug', Request::old("slug", $menu->slug), ['class' => 'form-control', 'id' => 'slug']) !!}
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
    {!! Form::close() !!}

<script>
$(function(){$('[name=title]').slugify()})
</script>

@show