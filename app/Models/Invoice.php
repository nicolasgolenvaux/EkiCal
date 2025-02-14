<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'id',
        'name' ,
        'email',
        'jour',
        'heure',
        'nbr',
        'facturation_type',
        'qt',
        'prix',
        'total' ,
        'htva',
        'tva',
        'updated_at',
        'created_at'
    ];

}