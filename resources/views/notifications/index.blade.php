@extends('layouts.app')
@section('title' ,'Notifications')

@section('content')
<div class="container">
    <div class="row">
        <div class="cm-md-4">
            <navigation></navigation>
        </div>
        <div class="col-md-8 " >
                @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                            &times;
                        </button>
                        {{ Session::get('success') }}
                    </div>
                @endif
                <div class="panel panel-default custom-panel" style="background-color:#f8f8f8;">
                    <div class="panel-body box-style box-style-no-padding"  style="background-color:#fff;">

                        <div class="col-md-12 custom-design panel-heading">
                            @if( Auth::user()->unreadNotifications->count()  != 0)
                                <div class="col-md-6">You have {{Auth::user()->unreadNotifications->count() }} unread @if( Auth::user()->unreadNotifications->count() == 1)  notification @else notifications @endif
                                </div>
                                <div class="col-md-6 text-right"><a href="{{ Auth::user()->isCLient() ? url('/client/mark-all-notifications-as-read')  : url('/admin/mark-all-notifications-as-read')  }}" > Mark all as read </a></div>

                            @else
                                You have no new notifications

                            @endif
                        </div>
                        <div class="box">
                            <div class="box-body">
                                <table  class="table table-bordered ">
                                    <tbody>
                                    @foreach(Auth::user()->notifications as $notification)
                                        <tr>
                                            <td style="background-color: {{ $notification->read_at == '' ? '#f8f8f8': '' }};">
                                                @include( 'notifications.'. snake_case( class_basename( $notification->type ) ) )
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
