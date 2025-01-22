<?php declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    public function poneyChoosen(): HasMany
    {
        return $this->hasMany(PoneyChoice::class);
    }
    public function clientName(): HasOne
    {
        return $this->hasOne(Client::class,'id','client_id')->select('name');
    }

}
