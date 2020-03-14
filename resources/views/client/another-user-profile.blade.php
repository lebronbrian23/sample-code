@extends('layouts.app')
@section('title' , $name.'s Profile' )

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <navigation></navigation>
                    <another-user-profile user-no="{{$user_no}}" name="{{$name}}"></another-user-profile>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
