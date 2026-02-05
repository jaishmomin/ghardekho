<?php
// src/models/Favorite.php
namespace App\Models;

class Favorite extends Model {
    protected $table = 'favorites';
    protected $primaryKey = 'id';
    protected $fillable = ['property_id', 'user_id'];
    
    public function property() {
        return (new Property())->find($this->property_id);
    }
    
    public function user() {
        return (new User())->find($this->user_id);
    }
}