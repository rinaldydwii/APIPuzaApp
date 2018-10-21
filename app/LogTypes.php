<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogTypes extends Model
{
    /**
     * The name of table
     *
     * @var string
     */
    protected $table = 'log_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

}
