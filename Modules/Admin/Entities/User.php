<?php

namespace Modules\Admin\Entities;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    //use SoftDeletes;
    protected $fillable = [
        'User','email','email_verified_at','name','password','remember_token',
    ];
    public function getFillable()
    {
        return $this->fillable;
    }
}
