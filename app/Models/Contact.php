<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [

        'name',

        'email',

        'phone',

        'subject',

        'message',

        'status',

        'processed_at'

    ];

    protected $casts = [

        'processed_at' => 'datetime'

    ];
}
