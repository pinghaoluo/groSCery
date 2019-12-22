<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

use App\Item;
use App\Group;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_verified_at', 'updated_at', 'pivot'
    ];

    /**
     * Get the group for a user
     * 
     * @return App\Group
     */
    public function group() {
        return $this->belongsTo('App\Group');
    }

    /**
     * Get the items for the user
     * 
     * @return App\Item
     */
    public function items() {
        return $this->belongsToMany('App\Item');
    }
}
