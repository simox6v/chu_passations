<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PassationEditLog extends Model
{
    protected $fillable = [
        'passation_id', 
        'user_id', 
        'field', 
        'old_value', 
        'new_value'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function passation()
    {
        return $this->belongsTo(Passation::class);
    }
}
