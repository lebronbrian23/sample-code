<li style="background-color: {{ $notification->read_at == '' ? '#f8f8f8': '' }};">
    <a href="{{ Auth::user()->isCLient() ? url('client/notification-read/'.$notification->id.'/'.$notification->data['user_id'].'/'.$notification->data['item_name'] ) : url('admin/notification-read/'.$notification->id.'/'.$notification->data['user_id'].'/'.$notification->data['item_name'] ) }}">
        <i class="fa fa-gift text-aqua"></i> {{$notification->data['buyer_name']}} {{$notification->data['buyer_phone']}}  wants {{$notification->data['item_name']}}  {{$notification->data['location']}}
        <small style="float:right"><i class="fa fa-clock-o"></i> {{  $notification->created_at->diffForHumans() }}</small>
    </a>
</li>
