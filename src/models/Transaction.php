<?php
// src/models/Transaction.php
namespace App\Models;

class Transaction extends Model {
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $fillable = ['property_id', 'buyer_id', 'seller_id', 'amount', 'transaction_date', 'status', 'payment_method'];
    
    public function property() {
        return (new Property())->find($this->property_id);
    }
    
    public function buyer() {
        return (new User())->find($this->buyer_id);
    }
    
    public function seller() {
        return (new User())->find($this->seller_id);
    }
}