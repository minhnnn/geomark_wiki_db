@extends('default')
@push('after-styles')
    <style>
        ul.build-actions {
            padding-left: 0px;
        }

        ul.build-actions li {
            list-style: none;
            margin-bottom: 10px;
        }
    </style>
@endpush
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
            <table class="table">
                @foreach($allFields as $field)

                    <tr>
                        <th>{{$field->name}}</th>
                        <td>{{$field->key}}</td>
                        <td>{{$field->type}}</td>
                        <td><input type="checkbox" name="fields[]" value="{{$field->key}}" id="" checked></td>
                    </tr>
                @endforeach
            </table>
            <div class="col-md-12">
                <ul class="build-actions">
                    <li>
                        <button type="submit" value="baseRepository" name="action" class="btn btn-primary">Build Base Repository
                        </button>
                        <div style="font-size: 10px; color: red">( build in Admin module only use if have Admin module and don't have base file repository )</div>
                    </li>
                    <li>
                        <button type="submit" value="model" name="action" class="btn btn-primary">Build Model
                        </button>
                    </li>
                    <li>
                        <button type="submit" value="controller" name="action" class="btn btn-primary">Build Controller
                        </button>
                    </li>
                    <li>
                        <button type="submit" value="form" name="action" class="btn btn-primary">Build Form
                        </button>
                    </li>
                    <li>
                        <button type="submit" value="request" name="action" class="btn btn-primary">Build Request
                        </button>
                    </li>
                    <li>
                        <button type="submit" value="view" name="action" class="btn btn-primary">Build view
                        </button>
                    </li>
                </ul>
            </div>
        </form>
    </div>
@stop
