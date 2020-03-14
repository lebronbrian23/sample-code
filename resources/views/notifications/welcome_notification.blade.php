<li style="background-color: {{ $notification->read_at == '' ? '#f8f8f8': '' }};">
    <a href="{{ Auth::user()->isCLient() ? url('client/notification-read/'.$notification->id.'/'.$notification->data['user_id'].'/'.$notification->data['name'] ) : url('admin/notification-read/'.$notification->id.'/'.$notification->data['user_id'].'/'.$notification->data['name'] ) }}">
        <i class="fa fa-gift text-aqua"></i> Welcome to WantedSasa
        <small style="float:right"><i class="fa fa-clock-o"></i> {{  $notification->created_at->diffForHumans() }}</small>
    </a>
</li>
