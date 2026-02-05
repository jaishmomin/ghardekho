<?php
// src/controllers/VisitController.php
namespace App\Controllers;

use App\Models\Visit;
use App\Models\Property;
use App\Services\MailerService;

class VisitController extends BaseController {
    protected $mailer;
    
    public function __construct() {
        parent::__construct();
        $this->mailer = new MailerService();
    }
    
    public function schedule() {
        try {
            $user = $this->auth->getCurrentUser(); 

            $data = $this->getJsonInput();
            $visit = new Visit();
            $visit->fill($data);
            $visit->user_id = $user->id;
            $visit->status = 'pending';
            $visit->save();
            // $visit->user_id = $user ? $user->id : null;

            $this->mailer->sendVisitConfirmation(
            $visit->toArray(),
            $property->toArray()
        );
            
            // Notify property owner
            $property = (new Property())->find($data['property_id']);
            if ($property) {
                $this->mailer->sendVisitScheduledNotification($property->user_id, $visit);
            }
            
            return $this->jsonResponse(true, 'Visit scheduled successfully', $visit->toArray(), 201);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), null, 400);
        }
        
        error_log('VISIT: schedule() started');

        try {
            $data = $this->getJsonInput();
            error_log('VISIT DATA: ' . print_r($data, true));

            if (empty($data['property_id'])) {
                return $this->jsonResponse(false, 'Property ID missing', null, 400);
            }

            $visit = new Visit();
            error_log('VISIT MODEL CREATED');

            $visit->fill($data);
            error_log('VISIT FILLED');

            $visit->user_id = $this->auth->getCurrentUser()['id'] ?? null;
            $visit->status = 'pending';

            $visit->save();
            error_log('VISIT SAVED');

            return $this->jsonResponse(true, 'Visit scheduled successfully');
        } catch (\Throwable $e) {
            error_log('VISIT ERROR: ' . $e->getMessage());
            return $this->jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
    
    public function updateStatus($id) {
        try {
            $data = $this->getJsonInput();
            $visit = (new Visit())->find($id);
            
            if (!$visit) {
                return $this->jsonResponse(false, 'Visit not found', null, 404);
            }
            
            $visit->status = $data['status'];
            $visit->save();
            
            // Notify user about status change
            $this->mailer->sendVisitStatusUpdate($visit->user_id, $visit);
            
            return $this->jsonResponse(true, 'Visit status updated', $visit->toArray());
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), null, 400);
        }
    }
}