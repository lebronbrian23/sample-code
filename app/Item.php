<?php

namespace SampleProject;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    /**
     * Get the user that owns the item  .
     */
    public function user()
    {
        return $this->belongsTo('SampleProject\User');
    }

}
