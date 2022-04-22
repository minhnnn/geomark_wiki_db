<!--email Input -->
<div class="form-group">
    <label for="email">email</label>
    <input type="text" name="email" id="email" value="{{$user->email}}" class="form-control col-md-6" id="">

</div>
<!--name Input -->
<div class="form-group">
    <label for="name">name</label>
    <input type="text" name="name" id="name" value="{{$user->name}}" class="form-control col-md-6" id="">

</div>

@push("after-scripts")
    <script type="text/javascript" src="{{ asset("vendor/jsvalidation/js/jsvalidation.js")}}"></script>
@endpush