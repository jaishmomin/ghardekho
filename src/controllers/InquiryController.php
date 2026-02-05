<?php
// src/controllers/InquiryController.php
namespace App\Controllers;

use App\Models\Inquiry;
use App\Models\Property;
use App\Services\MailerService;

class InquiryController extends BaseController {
    protected $mailer;
    
    public function __construct() {
        parent::__construct();
        $this->mailer = new MailerService();
    }
    
    public function store() {
        try {
            $user = $this->auth->getCurrentUser();
            $data = $this->getJsonInput();
            
            $inquiry = new Inquiry();
            $inquiry->fill($data);
            $inquiry->user_id = $user ? $user->id : null;
            $inquiry->status = 'pending';
            $inquiry->save();
            
            $this->mailer->sendInquiryConfirmation([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'message' => $data['message']
            ], $property ? $property->toArray() : null);

            // Notify property owner
            $property = (new Property())->find($data['property_id']);
            if ($property) {
                $this->mailer->sendInquiryNotification($property->user_id, $inquiry);
            }
            
            return $this->jsonResponse(true, 'Inquiry submitted successfully', $inquiry->toArray(), 201);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), null, 400);
        }
    }
    
    public function updateStatus($id) {
        try {
            $data = $this->getJsonInput();
            $inquiry = (new Inquiry())->find($id);
            
            if (!$inquiry) {
                return $this->jsonResponse(false, 'Inquiry not found', null, 404);
            }
            
            $inquiry->status = $data['status'];
            $inquiry->save();
            
            return $this->jsonResponse(true, 'Inquiry status updated', $inquiry->toArray());
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), null, 400);
        }
    }
}