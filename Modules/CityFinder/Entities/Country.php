<?php

namespace Modules\CityFinder\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';
    protected $fillable = [];

    public function getTimezonesAttribute($value){
        return json_decode($value);
    }

    public function getTranslationsAttribute($value){
        return json_decode($value);
    }
    
    protected static function newFactory()
    {
        return \Modules\CityFinder\Database\factories\CountryFactory::new();
    }
}
