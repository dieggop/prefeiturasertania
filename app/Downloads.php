<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Downloads extends Model
{
    use SoftDeletes;

    protected $table = 'downloads';
    protected $fillable = ['title','arquivo','sobre','quantidade'];


    protected $dates = ['deleted_at','created_at','updated_at'];

}
