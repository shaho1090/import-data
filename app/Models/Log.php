<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'date',
        'http_verb',
        'http_protocol',
        'path',
        'status_code',
    ];
}
