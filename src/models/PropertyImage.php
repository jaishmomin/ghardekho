<?php
// src/Models/PropertyImage.php
namespace App\Models;

class PropertyImage extends Model {
    protected $table = 'property_images';
    protected $primaryKey = 'id';
    protected $fillable = ['property_id', 'file_path', 'is_primary'];
    
    public function property() {
        return (new Property())->find($this->property_id);
    }
    
    public function setAsPrimary() {
        // Reset all primary flags for this property
        $this->db->prepare("UPDATE property_images SET is_primary = 0 WHERE property_id = ?")
                ->execute([$this->property_id]);
        
        // Set this image as primary
        $this->is_primary = 1;
        return $this->save();
    }
}