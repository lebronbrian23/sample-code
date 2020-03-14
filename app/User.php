<?php

namespace SampleProject;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens , Notifiable;

    public function routeNotificationForAfricasTalking($notification)
    {
        return $this->mobile_no;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password' ,'mobile_no',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    /**
     * Get the items for the user.
     */
    public function items()
    {
        return $this->hasMany('SampleProject\Item');
    }
    /**
     * Get the items for the user.
     */
    public function sms()
    {
        return $this->hasOne('SampleProject\Sms');
    }
    /**
     * @return role definitions assigned to user
     */
    public function roles()
    {
        return $this->belongsToMany('SampleProject\Role', 'role_users');
    }
    /**
     * Assigns Type definitions to user
     */
    public function makeUser($title)
    {
        $assigned_types = [];

        switch ($title) {

            case 'Admin':
                $assigned_types[] = self::getRoleId($title);
                break;
            case 'Client':
                $assigned_types[] = self::getRoleId($title);
                break;

            default:
                throw new Exception("The account type entered does not exist");
        }

        $this->roles()->sync($assigned_types);
    }
    public static function getRoleId($title)
    {
        foreach(Role::all() as $role)
        {
            if($role->name == $title) return $role->id;
        }
        throw new UnexpectedValueException;
    }

    public function createSms($user)
    {
        $this->sms()->save($user);
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany('SampleProject\Transaction');
    }
    /**
     * @return bool
     */
    public function isAdmin(){
        return in_array('Admin', array_pluck($this->roles->toArray(), 'name'));
    }

    /**
     * @return bool
     */
    public function isClient(){
        return in_array('Client', array_pluck($this->roles->toArray(), 'name'));
    }
}
