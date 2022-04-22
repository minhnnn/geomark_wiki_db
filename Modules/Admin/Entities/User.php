<?php

namespace Modules\Admin\Entities;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    //use SoftDeletes;
    protected $fillable = [
        'email','name','password',
    ];
    public function getFillable()
    {
        return $this->fillable;
    }
}
