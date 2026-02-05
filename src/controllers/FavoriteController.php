<?php
// src/controllers/FavoriteController.php
namespace GharDekho\Controllers;

use GharDekho\Models\Favorite;

class FavoriteController extends BaseController {
    public function toggle() {
        try {
            $user = $this->auth->getCurrentUser();
            if (!$user) {
                return $this->jsonResponse(false, 'Authentication required', null, 401);
            }
            
            $data = $this->getJsonInput();
            $favorite = (new Favorite())->where('user_id', $user->id)
                                      ->where('property_id', $data['property_id'])
                                      ->first();
            
            if ($favorite) {
                // Remove from favorites
                $favorite->delete();
                return $this->jsonResponse(true, 'Removed from favorites');
            } else {
                // Add to favorites
                $favorite = new Favorite();
                $favorite->user_id = $user->id;
                $favorite->property_id = $data['property_id'];
                $favorite->save();
                return $this->jsonResponse(true, 'Added to favorites', $favorite->toArray(), 201);
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), null, 400);
        }
    }
    
    public function userFavorites() {
        try {
            $user = $this->auth->getCurrentUser();
            if (!$user) {
                return $this->jsonResponse(false, 'Authentication required', null, 401);
            }
            
            $favorites = (new Favorite())->where('user_id', $user->id)
                                       ->with('property')
                                       ->get();
            
            return $this->jsonResponse(true, 'User favorites', $favorites);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), null, 400);
        }
    }
}