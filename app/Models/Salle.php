<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Passation;

class Salle extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'nombre_lits'];

    public function passations()
    {
        return $this->hasMany(Passation::class);
    }
}
