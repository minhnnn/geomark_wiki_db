@extends('builder::layouts.master')
@section('after-styles')
    <style>
        ul.build-actions {
            padding-left: 0px;
        }

        ul.build-actions li {
            list-style: none;
            margin-bottom: 10px;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <form action="{{route('admin.node.curdCreate',$currentNode->id)}}" method="POST">
            {!! csrf_field() !!}
            <div class="form-group">
                <div class="col-md-2">
                    <label for="module">Select Module</label>
                </div>
                <div class="col-md-6">
                    <select name="module" class="form-control" id="">
                        @foreach($modules as $module)
                            <option value="{{$module->getName()}}"
                                    @if(old('module') === $module->getName())
                                        selected
                                @endif
                            >{{$module->getName()}}</option>
                        @endforeach
                    </select></div>
            </div>
            <div class="col-md-12">
                <ul class="build-actions">
                    <li>
                        <button type="submit" value="baseRepository" name="action" class="btn btn-primary">Build Base Repository
                        </button>
                        <div style="font-size: 10px; color: red">( build base reposiory file in Admin module )</div>
                    </li>
                    <li>
                        <button type="submit" value="baseIntertia" name="action" class="btn btn-primary">Build Base Intertia
                        </button>
                        <div style="font-size: 10px; color: red">( build intertia shared file )</div>
                    </li>
                </ul>
            </div>
        </form>
    </div>
@stop
