<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Salle;

class Passation extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_patient',
        'prenom',
        'cin',
        'ip',
        'description',
        'date_passation',
        'user_id',
        'salle_id',
    ];

    protected $dates = ['date_passation'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

    public function editLogs()
    {
        return $this->hasMany(PassationEditLog::class);
    }

}
