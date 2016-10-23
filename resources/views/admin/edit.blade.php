@section('content')

    <div class="container">

        {!! Form::open() !!}
        {!! Form::hidden('menu_id', Request::old("menu_id", $link->menu_id?:0)) !!}
        {!! Form::hidden('parent_id', Request::old("parent_id", $link->parent_id)) !!}
        <div class="panel panel-default">
            <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">Title</label>
                    {!! Form::text('title', Request::old("title", $link->title), ['class' => 'form-control']) !!}
                </div>
                <div class="form-group" id="menu-link">
                    <label for="url" class="left">Link</label>
                    {!! Form::text('url', Request::old("url", $link->url), ['id' => 'url', 'class' => 'form-control']) !!}

                    <div class="row">
                        <div class="col-md-3">
                            <p class="help-block"><a href="#" id="page_toggle">Choose page</a></small></p>
                        </div>
                        <div class="col-md-9">
                            <div id="page-link" class="form-group" style="display:none;">
                                {!! Form::label('page', 'Page') !!}
                                <select name="pages" id="pages" class="form-control">
                                    {!! $pages !!}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('groups', 'Groups Permissions', ['data-toggle'=>'tooltip', 'title'=>'Only these groups will have permission to view this link.']) !!}
                    {!! Form::select('groups[]', $user_groups, ! empty($link->groups)?$link->groups:[], ['class'=>'select','placeholder'=>'Allow only..', 'multiple' => 'multiple']) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div id="menu-content" class="form-group">
                    {!! Form::label('content', 'Content') !!}
                    {!! Form::textarea('content', Request::old("content"), ['id' => 'htmleditor', 'class' => 'form-control html_editor']) !!}
                </div>
            </div>
        </div>
        </div>
        <div class="panel-footer">
            {!! Form::submit('Save', array('class' => 'btn btn-primary')) !!}
            <a href="{{ route('get.admin.menuitem.index', [$link->menu_id]) }}" class="btn btn-default">Cancel</a>
        </div>
        </div>
        {!! Form::close() !!}

    </div>

    <script type="text/javascript">
        $(function(){
            $('#page_toggle').click(function(e) {
                e.preventDefault();
                $('#page-link').fadeToggle('fast');
            });
            $('#pages').on('change blur',function() {
                $('#url').val($(':selected', this).val());
                $('#page-link').fadeOut('fast');
            });
            $('#slug').on('change', function(e){
                $('#slug_uri').text(this.value);
            });
        });
    </script>

@show