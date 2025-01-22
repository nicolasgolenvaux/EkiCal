<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoneyChoice extends Model
{
    protected $table = 'poneyChoice';
    protected $fillable = [
        'agenda_id',
        'poney_id'
    ];



}
