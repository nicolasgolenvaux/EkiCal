<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Agenda extends Model
{
    protected $fillable = [
        'id',
        'jour',
        'start',
        'end',
        'client_id',
        'type',
        'nbr',
        'facturation_type',
        'prix'
    ];
    public function poneyChoosen(): HasMany
    {
        return $this->hasMany(PoneyChoice::class);
    }
    public function client(): belongsTo
    {
        return $this->belongsTo(Client::class);
    }

}
