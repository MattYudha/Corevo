<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterTag extends Model
{
    protected $fillable = [
        'tag_name',
        'input_type',
        'default_value',
        'dropdown_type',
        'dropdown_options',
        'dropdown_model',
    ];
}
