<?php

namespace SampleProject;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    //relation between  a transaction and a user
    public function user()
    {
        return $this->belongsTo('SampleProject\User', 'paid_by');
    }
}
