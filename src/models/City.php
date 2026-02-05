<?php
// src/models/City.php
namespace App\Models;

class City extends Model {
    protected $table = 'cities';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'state'];
    
    public function properties() {
        return (new Property())->where('city_id', $this->id);
    }
}