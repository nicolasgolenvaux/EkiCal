<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poney extends Model
{
    protected $fillable = [
        'id',
        'name',
        'tps_w',
        'weight',
        'birth',
        'image_path',
        'medicalVisit',
        'pedigree'
    ];
    public function poneyName(): HasOne
    {
        return $this->hasOne(PoneyChoice::class);
    }
    public function agendaCount(): HasMany
    {
        return $this->hasMany('App\Models\Agenda', 'id', 'poney_id')->selectRaw('count(*) as count, poney_id');
    }

}
