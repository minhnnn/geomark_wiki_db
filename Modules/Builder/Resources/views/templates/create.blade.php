@extends('layouts.app')
@push('after-styles')
@endpush

@section('title')
    Create new $NAME$
@endsection

@section('content')
    <div class="content-body">
        <div class="card card-primary card-outline">
            <div class="card-body pad table-responsive">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{route('$FOLDER$.$SLUG$.store')}}" method="post">
                    {!! csrf_field() !!}
                    @include('$FOLDER$::$SLUG$.form')
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Create $NAME$</button>
                        <a href="{{route('$FOLDER$.$SLUG$.index')}}" class="btn btn-secondary ">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@push('after-scripts')
    {!! JsValidator::formRequest(\Modules\Admin\Http\Requests\$NAME$\CreateRequest::class) !!}
@endpush

