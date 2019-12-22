<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Item extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','in_stock','group_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'pivot',
    ];

    /**
     * Get the group for the item
     */
    public function group() {
        return $this->belongsTo('App\Group');
    }

    /**
     * Get the users for the item
     */
    public function users() {
        return $this->belongsToMany('App\User');
    }

    /**
     * Subscribe a user to this group
     */
    public function subscribeUser() {
        $this->users()->attach(Auth::user());
    }

    /**
     * Unsubscribe a user to this group
     */
    public function unsubscribeUser() {
        $this->users()->detach(Auth::user());
    }
}
