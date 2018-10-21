<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogSubTypes extends Model
{
    /**
     * The name of table
     *
     * @var string
     */
    protected $table = 'log_sub_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'log_type_id'
    ];

}
