<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
}
