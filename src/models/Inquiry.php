<?php
// src/models/Inquiry.php
namespace App\Models;

class Inquiry extends Model {
    protected $table = 'inquiries';
    protected $primaryKey = 'id';
    protected $fillable = ['property_id', 'user_id', 'name', 'email', 'phone', 'message', 'status'];
    
    public function property() {
        return (new Property())->find($this->property_id);
    }
    
    public function user() {
        return (new User())->find($this->user_id);
    }
}