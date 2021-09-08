<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'start_date',
        'end_date',
        'incharge_user',
        'percentage',
    ];
}
