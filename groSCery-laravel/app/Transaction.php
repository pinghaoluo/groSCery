<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id', 'price'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];

    /**
     * Define relationships
     */
    public function group() {
        return $this->belongsTo('App\Group');
    }
    public function user() {
        return $this->belongsTo('App\User');
    }
    public function item() {
        return $this->belongsTo('App\Item');
    }
}
