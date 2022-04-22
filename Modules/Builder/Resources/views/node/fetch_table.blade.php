@extends('default')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.node.postFetchTable', $currentNode->id)}}" method="POST">
                    @csrf
                    <table class="table">
                        <tr>
                            <th>Column</th>
                            <th>Name</th>
                            <th>Select</th>
                        </tr>
                        @foreach($allColumns as $column)
                            <tr>
                                <th>{{$column}}</th>
                                <td><input type="checkbox" name="columns[{{$column}}][select]" value="1" id="{{$column}}[select]"></td>
                                <td><input type="text" name="columns[{{$column}}][name]"></td>
                                <td>
                                    <select name="columns[{{$column}}][type]" id="{{$column}}-type" class="form-control col-md-6" id="">
                                        @foreach($allTypes as $key => $type)
                                            <option value="{{$key}}">{{$type}}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="form-group">
                        <input type="submit" value="Fetch" class="btn btn-success">
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@push('after-scripts')
    <script src="{{asset('js/pages/node_app.js')}}"></script>

@endpush
