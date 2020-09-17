<?php

namespace Tests\Setup\Models;

use Gopankombiyil\Larango\Eloquent\Model;

class Characteristic extends Model
{
    protected $table = 'characteristics';

    protected $fillable = [
        '_key',
        'en',
        'de',
    ];

    public function characters()
    {
        return $this->hasMany(Characteristic::class);
    }
}
