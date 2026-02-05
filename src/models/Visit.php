<?php
// src/models/Visit.php
declare(strict_types=1);
namespace App\Models;

class Visit extends Model {
    protected $table = 'visits';


    protected $fillable = [
        'inquiry_id',
        'property_id',
        'visit_type',
        'visit_date',
        'status',
        'name',
        'email',
        'phone'
    ];
    
    public function property() {
        return (new Property())->find($this->property_id);
    }
    
    public function user() {
        return (new User())->find($this->user_id);
    }
}