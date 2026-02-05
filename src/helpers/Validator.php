<?php
// src/helpers/Validator.php
namespace GharDekho\Helpers;

class Validator {
    public static function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $rules = explode('|', $rule);
            
            foreach ($rules as $r) {
                $r = trim($r);
                
                // Required validation
                if ($r === 'required' && empty($value)) {
                    $errors[$field][] = "The $field field is required.";
                }
                
                // Email validation
                if ($r === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "The $field must be a valid email address.";
                }
                
                // Numeric validation
                if ($r === 'numeric' && !is_numeric($value)) {
                    $errors[$field][] = "The $field must be a number.";
                }
                
                // Min length validation
                if (strpos($r, 'min:') === 0) {
                    $min = (int) substr($r, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "The $field must be at least $min characters.";
                    }
                }
                
                // Add more validation rules as needed
            }
        }
        
        return $errors;
    }
}