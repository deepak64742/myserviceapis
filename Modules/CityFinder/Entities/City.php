<?php

namespace Modules\CityFinder\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CityFinder\Entities\State;
use Modules\CityFinder\Entities\Country;

class City extends Model
{
    use HasFactory;

    protected $table = 'cities';
    protected $fillable = [];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    protected static function newFactory()
    {
        return \Modules\CityFinder\Database\factories\CityFactory::new();
    }
}
