@extends('layouts.app')
@push('after-styles')
@endpush

@section('title')
    Update $NAME$
@endsection

@section('content')
    <div class="content-body">

        <div class="card card-primary card-outline">
            <div class="card-body pad table-responsive">
                <form action="{{route('$FOLDER$.$SLUG$.update',$$OBJECT_NAME$->id)}}" method="POST">
                    @method('PATCH')
                    {!! csrf_field() !!}
                    @include('$FOLDER$::$SLUG$.form')
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Edit</button>
                        <a href="{{route('$FOLDER$.$SLUG$.index')}}" class="btn btn-secondary">Back</a>
                        <a href="#" style="float:right" class="btn btn-flat btn-danger" id="delete-btn">Delete</a>

                    </div>
                </form>

                <div class="form-group">
                    <form action="{{route('$FOLDER$.$SLUG$.destroy',$$OBJECT_NAME$->id)}}" id="delete-form" method="post">
                        @method('DELETE')
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@push('after-scripts')
    <script>
        $('#delete-btn').click(function(event){
            event.preventDefault();
            const result = confirm('Are you sure you want to delete');
            if(result){
                $('#delete-form').submit();
            }
        });

    </script>
@endpush
@push('after-scripts')
    {!! JsValidator::formRequest(\Modules\Admin\Http\Requests\$NAME$\CreateRequest::class) !!}
@endpush

