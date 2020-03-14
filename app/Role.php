<?php

namespace SampleProject;

use Illuminate\Database\Eloquent\Model;

class role extends Model
{
    protected $fillable = ['name'];

    protected $table = 'roles';

    /**
     * The user that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('SampleProject\User', 'role_users');
    }
}
