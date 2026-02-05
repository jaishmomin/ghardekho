<?php

namespace App\Models;

use PDO;
use PDOException;

class Property extends Model {
    protected $table = 'properties';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'title', 'description', 'city_id', 'address', 
        'type', 'beds', 'baths', 'sqft', 'price', 'currency',
        'status', 'latitude', 'longitude', 'year_built', 'garage',
        'amenities', 'featured', 'views'
    ];
    
    protected $hidden = ['user_id'];
    
    // Property types
    const TYPE_FLAT = 'flat';
    const TYPE_VILLA = 'villa';
    const TYPE_SHOP = 'shop';
    const TYPE_OFFICE = 'office';
    const TYPE_BUNGALOW = 'bungalow';
    const TYPE_PLOT = 'plot';
    const TYPE_GODOWN = 'godown';
    
    // Statuses
    const STATUS_AVAILABLE = 'available';
    const STATUS_PENDING = 'pending';
    const STATUS_SOLD = 'sold';
    
    /**
     * Get the validation rules for the property.
     *
     * @return array
     */
    public static function getValidationRules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'city_id' => 'required|integer|exists:cities,id',
            'address' => 'required|string|max:500',
            'type' => 'required|in:flat,villa,shop,office,bungalow,plot,godown',
            'beds' => 'required|integer|min:0',
            'baths' => 'required|integer|min:0',
            'sqft' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'status' => 'in:available,pending,sold',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'year_built' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'garage' => 'integer|min:0',
            'amenities' => 'nullable|string',
            'featured' => 'boolean',
            'images' => 'array',
            'images.*' => 'image|max:5120' // 5MB max per image
        ];
    }
    
    // Relationships
    public function user() {
        return (new User())->find($this->user_id);
    }
    
    public function city() {
        return (new City())->find($this->city_id);
    }
    
    public function images() {
        $sql = "SELECT * FROM property_images WHERE property_id = :property_id ORDER BY is_primary DESC, id ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['property_id' => $this->id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Error fetching property images: " . $e->getMessage());
        }
    }
    
    public function inquiries() {
        return (new Inquiry())->where('property_id', $this->id);
    }
    
    public function visits() {
        return (new Visit())->where('property_id', $this->id);
    }
    
    // Scopes
    public function scopeFeatured($query) {
        return $query->where('featured', 1);
    }
    
    public function scopeAvailable($query) {
        return $query->where('status', self::STATUS_AVAILABLE);
    }
    
    public function scopePriceRange($query, $min, $max) {
        return $query->where('price', '>=', $min)
                    ->where('price', '<=', $max);
    }
    
    public function scopeType($query, $type) {
        return $query->where('type', $type);
    }
    
    // Business logic
    public function incrementViews() {
        $this->views++;
        $this->save();
    }
    
    public function markAsSold() {
        $this->status = self::STATUS_SOLD;
        $this->save();
        
        // Notify interested users
        $this->notifyInterestedUsers();
        
        return true;
    }
    
    protected function notifyInterestedUsers() {
        // Get users who favorited this property
        $sql = "SELECT u.* FROM users u 
                JOIN favorites f ON u.id = f.user_id 
                WHERE f.property_id = :property_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['property_id' => $this->id]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $mailer = new \GharDekho\Services\MailerService();
            
            foreach ($users as $userData) {
                $user = new User();
                $user->fill($userData);
                
                $mailer->sendEmail(
                    $user->email,
                    "Property You're Interested In Has Been Sold",
                    "The property '{$this->title}' has been marked as sold. Check out similar properties on our website!"
                );
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error notifying users: " . $e->getMessage());
            return false;
        }
    }
    
    // Image handling
    public function addImage($file, $isPrimary = false) {
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('property_') . '.' . $extension;
        $uploadPath = $_ENV['UPLOAD_DIR'] . 'properties/' . $this->id . '/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
            // If this is the first image, set as primary
            $images = $this->images();
            if (empty($images) || $isPrimary) {
                // Reset any existing primary image
                $this->resetPrimaryImage();
                $isPrimary = true;
            }
            
            // Save to database
            $sql = "INSERT INTO property_images (property_id, file_path, is_primary, created_at) 
                    VALUES (:property_id, :file_path, :is_primary, NOW())";
            
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'property_id' => $this->id,
                    'file_path' => 'uploads/properties/' . $this->id . '/' . $filename,
                    'is_primary' => $isPrimary ? 1 : 0
                ]);
                
                return $this->db->lastInsertId();
            } catch (PDOException $e) {
                // Clean up the uploaded file if DB insert fails
                unlink($uploadPath . $filename);
                throw new \Exception("Failed to save image to database: " . $e->getMessage());
            }
        }
        
        throw new \Exception("Failed to upload image");
    }
    
    protected function resetPrimaryImage() {
        $sql = "UPDATE property_images SET is_primary = 0 WHERE property_id = :property_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['property_id' => $this->id]);
    }
    
    // Search functionality
    public static function search($filters = []) {
        $query = new self();
        $query = $query->available();
        
        if (!empty($filters['city_id'])) {
            $query = $query->where('city_id', $filters['city_id']);
        }
        
        if (!empty($filters['type'])) {
            $query = $query->where('type', $filters['type']);
        }
        
        if (!empty($filters['min_price'])) {
            $query = $query->where('price', '>=', $filters['min_price']);
        }
        
        if (!empty($filters['max_price'])) {
            $query = $query->where('price', '<=', $filters['max_price']);
        }
        
        if (!empty($filters['beds'])) {
            $query = $query->where('beds', '>=', $filters['beds']);
        }
        
        if (!empty($filters['baths'])) {
            $query = $query->where('baths', '>=', $filters['baths']);
        }
        
        if (!empty($filters['q'])) {
            $query = $query->where(function($q) use ($filters) {
                $q->where('title', 'LIKE', '%' . $filters['q'] . '%')
                  ->orWhere('description', 'LIKE', '%' . $filters['q'] . '%')
                  ->orWhere('address', 'LIKE', '%' . $filters['q'] . '%');
            });
        }
        
        // Add sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = isset($filters['sort_order']) && strtolower($filters['sort_order']) === 'asc' ? 'ASC' : 'DESC';
        
        // Pagination
        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;
        $offset = ($page - 1) * $perPage;
        
        $query = $query->orderBy($sortField, $sortOrder)
                      ->limit($perPage)
                      ->offset($offset);
        
        return $query->get();
    }
    
    // Override save to handle created_at/updated_at
    public function save() {
        $now = date('Y-m-d H:i:s');
        
        if (empty($this->attributes['created_at'])) {
            $this->attributes['created_at'] = $now;
        }
        
        $this->attributes['updated_at'] = $now;
        
        return parent::save();
    }
}