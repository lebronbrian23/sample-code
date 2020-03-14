<?php

namespace SampleProject;

use Illuminate\Database\Eloquent\Model;

class sms extends Model
{
    protected $fillable = ['no_of_sms' , 'user_id'];
    //
    /**
     * Get the user that owns the sms .
     */
    public function user()
    {
        return $this->belongsTo('SampleProject\User');
    }

}
