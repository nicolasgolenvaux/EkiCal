<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

}
