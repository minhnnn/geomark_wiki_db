@extends('builder::layouts.master')
@section('after-styles')
    <style>
        .type_collection {
            display: none;
        }
        #select_collection_object {
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="col-md-12">
        <h1>Build Form Fields</h1>
        <form action="{!! route('admin.node.store') !!}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="node_id" value="{{$currentNode->id}}">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="select_root">Root Node</label>
                    </div>
                </div>
                <div class="row">
                    <select name="parent_id" class="form-control col-md-6" id="select_root">
                        @foreach($allRootChild as $rootNode)
                            <option value="{{$rootNode['id']}}"
                                    @if($rootNode['id'] == $nodeChartData['id'])
                                    selected
                                @endif
                            >{{$rootNode['name']}}</option>
                        @endforeach
                    </select>
                    <div class="col-md-6">
                        <a href="{{route('admin.node.baseFileBuilder',$nodeChartData['id'])}}" class="btn btn-default">Build Base File</a>
                        <a href="{{route('admin.node.getFetchTable',$nodeChartData['id'])}}" class="btn btn-primary">Fetch Table</a>
                        <a href="{{route('admin.node.curdBuilder',$nodeChartData['id'])}}" class="btn btn-success">Build CURD</a>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="name">Node Name</label>
                <input type="text" class="form-control col-md-6" value="{{$currentNode->name}}" name="name">

            </div>

            <div class="form-group">
                <label for="key">Node Key</label>
                <input type="text" class="form-control col-md-6" id="key" value="{{$currentNode->key}}" name="key">
            </div>
            <div class="form-group">
                <label for="type">Node Type</label>
                <select name="type" id="type" class="form-control col-md-6" id="">
                    @foreach($allTypes as $key => $type)
                        <option value="{{$key}}"
                            @if($key == $currentNode->type)
                                selected
                            @endif
                        >{{$type}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group type_collection"  id="select_collection_type">
                <label for="collection_type ">Collection Type</label>
                <select name="collection_type" class="form-control col-md-6" id="">
                    <option value="string" >String</option>
                    <option value="string">Number</option>
                    <option value="string">Object</option>
                </select>
            </div>

            <div class="form-group">
                <label for="table">Binding table</label>
                    <select name="binding_table" id="table" class="form-control col-md-6">
                        <option value="">No table</option>
                    @foreach($tableArr as $table)
                            <option value="{{$table}}"
                                    @if($currentNode->binding_table == $table)
                                    selected
                                @endif
                            >{{$table}}</option>
                        @endforeach
                </select>
            </div>

            <div class="form-group type_collection"  id="select_collection_object">
                <label for="collection_object">Collection Object</label>

                <select name="collection_object" class="form-control col-md-6">
                    @foreach($allRootChild as $rootNode)
                        <option value="{{$rootNode['id']}}">{{$rootNode['name']}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <button type="submit" name="form_action" value="create" class="btn btn-success">Create</button>
                @if($currentNode->id)
                    <button type="submit" name="form_action" value="update" class="btn btn-primary">Update</button>
                @endif
            </div>
        </form>
        <div id="node-chart" class="form-group">
            <node-app :node-chart-data="{{json_encode($nodeChartData)}}" ></node-app>
        </div>
    </div>
@stop

@section('after-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#select_root', 0).change(function() {
                console.log('hit')
                window.location.replace('/admin/node/' + $(this).val() + '/?root_id=' + $(this).val());
            });

            $('select[name=type]').change(function() {
                if ($(this).val() === 'collection') {
                    $('#select_collection_type').show();
                } else {
                    $('.type_collection').hide();
                }
            });
        });
    </script>

@endsection
