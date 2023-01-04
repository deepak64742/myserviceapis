<?php

namespace Modules\CityFinder\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;

    protected $table = 'states';
    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\CityFinder\Database\factories\StateFactory::new();
    }
}
