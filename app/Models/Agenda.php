<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $fillable = [
        'id',
        'jour',
        'start',
        'end',
        'client_id',
        'type',
        'nbr'
    ];
}
