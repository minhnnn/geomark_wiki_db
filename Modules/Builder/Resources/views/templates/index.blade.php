@extends('layouts.app')
@push('after-styles')
@endpush

@section('title')
    All $NAME$
@endsection

@section('content')
    <div class="content-body">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <a href="{{route('$FOLDER$.$SLUG$.create')}}" class="btn btn-flat btn-success">{{__('Create')}}</a>
                <form action="" style="display: inline-block;"  id="index-search-form">
                    <input type="text" name="search" class="form-control index-search"><i class="fas fa-search"></i>

                </form>
            </div>
            <div class="card-body pad table-responsive">
                <table class="table table-striped">
                    <thead>
                    <th>#</th>
                    $THEADCONTENT$
                    </thead>
                    @foreach($all$NAME$ as $index => $$KEY$)
                        <tr>
                            <td>{{($all$NAME$->currentPage() - 1) * $all$NAME$->perPage() +$index + 1}}</td>
                            $TBODYCONTENT$
                        </tr>
                    @endforeach
                </table>
                {!! $all$NAME$->links() !!}
            </div>
        </div>
    </div>
@stop
@push('after-scripts')
@endpush
