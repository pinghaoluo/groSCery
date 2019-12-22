<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Group extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    /**
     * Get the users of a group
     */
    public function users() {
        return $this->hasMany('App\User');
    }

    /**
     * Get the items of a group
     */
    public function items() {
        return $this->hasMany('App\Item');
    }

    /**
     * Check if a group exists
     */
    public function exists($group_name) {
        return Group::where('name', $group_name)->firstOrFail();
    }

    /**
     * Subscribe a user to this group
     */
    public function subscribeUser() {
        Auth::user()->group_id = $this->id;
        Auth::user()->save();
    }

}
