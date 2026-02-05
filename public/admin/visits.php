<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Database;

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /');
    exit;
}

$db = Database::getConnection();
$visits = $db->query("
    SELECT v.*, p.title 
    FROM visits v
    LEFT JOIN properties p ON v.property_id = p.id
    ORDER BY v.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Visit Requests | GharDekho</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --saffron: #FF9933;
            --green: #138808;
            --blue: #000080;
            --gold: #FFD700;
            --maroon: #8B0000;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        
        .heading-font {
            font-family: 'Playfair Display', serif;
        }
        
        .saffron-bg {
            background-color: var(--saffron);
        }
        
        .green-bg {
            background-color: var(--green);
        }
        
        .blue-bg {
            background-color: var(--blue);
        }
        
        .saffron-text {
            color: var(--saffron);
        }
        
        .green-text {
            color: var(--green);
        }
        
        .blue-text {
            color: var(--blue);
        }
        
        .indian-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FF9933' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .rangoli-border {
            border-bottom: 4px solid transparent;
            border-image: linear-gradient(to right, var(--saffron), var(--green), var(--blue)) 1;
        }
        
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-scheduled {
            background-color: rgba(33, 150, 243, 0.2);
            color: #2196f3;
            border: 1px solid rgba(33, 150, 243, 0.3);
        }
        
        .badge-completed {
            background-color: rgba(76, 175, 80, 0.2);
            color: #4caf50;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }
        
        .badge-cancelled {
            background-color: rgba(244, 67, 54, 0.2);
            color: #f44336;
            border: 1px solid rgba(244, 67, 54, 0.3);
        }
        
        .badge-no-show {
            background-color: rgba(158, 158, 158, 0.2);
            color: #757575;
            border: 1px solid rgba(158, 158, 158, 0.3);
        }
        
        .badge-sold {
            background-color: rgba(255, 153, 51, 0.2);
            color: var(--saffron);
            border: 1px solid rgba(255, 153, 51, 0.3);
        }
        
        .visit-type-badge {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .type-virtual {
            background-color: rgba(103, 58, 183, 0.1);
            color: #673ab7;
            border: 1px solid rgba(103, 58, 183, 0.2);
        }
        
        .type-physical {
            background-color: rgba(255, 152, 0, 0.1);
            color: #ff9800;
            border: 1px solid rgba(255, 152, 0, 0.2);
        }
        
        .table-row-hover:hover {
            background-color: rgba(255, 153, 51, 0.05);
        }
        
        .action-btn {
            padding: 6px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        
        .btn-approve {
            background-color: #4caf50;
            color: white;
            border: 1px solid #45a049;
        }
        
        .btn-approve:hover {
            background-color: #45a049;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
        }
        
        .btn-reject {
            background-color: #f44336;
            color: white;
            border: 1px solid #e53935;
        }
        
        .btn-reject:hover {
            background-color: #e53935;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(244, 67, 54, 0.2);
        }
        
        .btn-sold {
            background-color: var(--saffron);
            color: white;
            border: 1px solid #e68900;
        }
        
        .btn-sold:hover {
            background-color: #e68900;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 153, 51, 0.2);
        }
        
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        
        .status-scheduled {
            background-color: #2196f3;
        }
        
        .status-completed {
            background-color: #4caf50;
        }
        
        .status-cancelled {
            background-color: #f44336;
        }
    </style>
</head>
<body class="text-gray-800 indian-pattern">
    <!-- Navigation -->
    <nav class="bg-white shadow-md rangoli-border">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full saffron-bg flex items-center justify-center mr-2">
                            <div class="w-6 h-6 rounded-full green-bg flex items-center justify-center">
                                <div class="w-3 h-3 rounded-full blue-bg"></div>
                            </div>
                        </div>
                        <h1 class="heading-font text-2xl font-bold">
                            <span class="saffron-text">Ghar</span><span class="green-text">Dekho</span>
                            <span class="text-gray-600 text-lg ml-2">Admin</span>
                        </h1>
                    </div>
                </div>
                
                <!-- Admin Info -->
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="font-bold">Administrator</p>
                        <p class="text-sm text-gray-600">Visit Management</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-saffron to-green-600 flex items-center justify-center">
                        <i class="fas fa-user-shield text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="heading-font text-3xl font-bold text-gray-800 mb-2">
                        Visit <span class="saffron-text">Requests</span>
                    </h1>
                    <p class="text-gray-600">Manage property visit requests and scheduling</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Total Requests</p>
                    <p class="text-2xl font-bold blue-text"><?= count($visits) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Statistics Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">
                    <?= count(array_filter($visits, fn($v) => $v['status'] === 'scheduled')) ?>
                </div>
                <p class="text-gray-600">Scheduled</p>
                <div class="mt-2">
                    <span class="badge badge-scheduled">Upcoming</span>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">
                    <?= count(array_filter($visits, fn($v) => $v['status'] === 'completed')) ?>
                </div>
                <p class="text-gray-600">Completed</p>
                <div class="mt-2">
                    <span class="badge badge-completed">Done</span>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-red-600 mb-2">
                    <?= count(array_filter($visits, fn($v) => $v['status'] === 'cancelled')) ?>
                </div>
                <p class="text-gray-600">Cancelled</p>
                <div class="mt-2">
                    <span class="badge badge-cancelled">Declined</span>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-orange-600 mb-2">
                    <?= count(array_filter($visits, fn($v) => $v['visit_type'] === 'virtual')) ?>
                </div>
                <p class="text-gray-600">Virtual</p>
                <div class="mt-2">
                    <span class="visit-type-badge type-virtual">Online</span>
                </div>
            </div>
        </div>

        <!-- Visits Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-green-600 to-blue-600 text-white">
                        <tr>
                            <th class="p-4 text-left font-semibold">
                                <div class="flex items-center">
                                    <i class="fas fa-home mr-2"></i>
                                    Property
                                </div>
                            </th>
                            <th class="p-4 text-left font-semibold">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    Visitor Details
                                </div>
                            </th>
                            <th class="p-4 text-left font-semibold">
                                <div class="flex items-center">
                                    <i class="fas fa-tv mr-2"></i>
                                    Type
                                </div>
                            </th>
                            <th class="p-4 text-left font-semibold">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Date & Time
                                </div>
                            </th>
                            <th class="p-4 text-left font-semibold">
                                <div class="flex items-center">
                                    <i class="fas fa-tag mr-2"></i>
                                    Status
                                </div>
                            </th>
                            <th class="p-4 text-left font-semibold">
                                <div class="flex items-center">
                                    <i class="fas fa-cogs mr-2"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($visits as $v): ?>
                        <tr class="border-t border-gray-100 table-row-hover">
                            <td class="p-4">
                                <div class="font-medium text-gray-800">
                                    <?= htmlspecialchars($v['title']) ?>
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-map-marker-alt text-saffron mr-1"></i>
                                    <?= htmlspecialchars($v['address'] ?? 'N/A') ?>
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    ID: <?= $v['property_id'] ?>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-gray-800">
                                    <?= htmlspecialchars($v['name']) ?>
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-envelope text-green-500 mr-1"></i>
                                    <?= htmlspecialchars($v['email'] ?? '') ?>
                                </div>
                                <?php if (!empty($v['phone'])): ?>
                                <div class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-phone text-blue-500 mr-1"></i>
                                    <?= htmlspecialchars($v['phone']) ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <span class="visit-type-badge <?= $v['visit_type'] === 'virtual' ? 'type-virtual' : 'type-physical' ?>">
                                    <?= $v['visit_type'] === 'virtual' ? 'Virtual Tour' : 'Physical Visit' ?>
                                </span>
                                <?php if ($v['visit_type'] === 'virtual'): ?>
                                <div class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-video text-purple-500 mr-1"></i>
                                    Online Meeting
                                </div>
                                <?php else: ?>
                                <div class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-building text-orange-500 mr-1"></i>
                                    On-site Visit
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-gray-800">
                                    <?= date('d M Y', strtotime($v['visit_date'])) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-clock text-saffron mr-1"></i>
                                    <?= date('h:i A', strtotime($v['visit_date'])) ?>
                                </div>
                                <?php 
                                $visitDate = strtotime($v['visit_date']);
                                $now = time();
                                $diff = $visitDate - $now;
                                $days = floor($diff / (60 * 60 * 24));
                                ?>
                                <?php if ($v['status'] === 'scheduled'): ?>
                                <div class="text-xs mt-2 <?= $days < 0 ? 'text-red-500' : ($days <= 1 ? 'text-orange-500' : 'text-green-500') ?>">
                                    <?php if ($days < 0): ?>
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                                    <?php elseif ($days <= 1): ?>
                                        <i class="fas fa-clock mr-1"></i>Tomorrow
                                    <?php else: ?>
                                        <i class="fas fa-calendar mr-1"></i><?= $days ?> days
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <?php 
                                $statusClass = match($v['status']) {
                                    'scheduled' => 'badge-scheduled',
                                    'completed' => 'badge-completed',
                                    'cancelled' => 'badge-cancelled',
                                    'no_show' => 'badge-no-show',
                                    default => 'badge-scheduled'
                                };
                                ?>
                                <span class="badge <?= $statusClass ?>">
                                    <span class="status-indicator status-<?= $v['status'] ?>"></span>
                                    <?= ucfirst($v['status']) ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col space-y-2">
                                    <?php if ($v['status'] === 'scheduled'): ?>
                                    <button onclick="updateStatus(<?= $v['id'] ?>,'approved')"
                                        class="action-btn btn-approve">
                                        <i class="fas fa-check mr-1"></i> Approve
                                    </button>
                                    <button onclick="updateStatus(<?= $v['id'] ?>,'rejected')"
                                        class="action-btn btn-reject">
                                        <i class="fas fa-times mr-1"></i> Reject
                                    </button>
                                    <?php endif; ?>
                                    
                                    <!-- Mark Sold Section -->
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <?php if ($v['status'] !== 'sold'): ?>
                                        <form method="post" action="mark-sold.php" class="inline">
                                            <input type="hidden" name="property_id" value="<?= $v['property_id'] ?>">
                                            <button type="submit" class="action-btn btn-sold w-full">
                                                <i class="fas fa-tag mr-1"></i> Mark Sold
                                            </button>
                                        </form>
                                        <?php else: ?>
                                        <span class="badge badge-sold inline-flex items-center">
                                            <i class="fas fa-check-circle mr-1"></i> Sold
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($visits)): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                <i class="fas fa-calendar-alt fa-3x text-gray-300 mb-4"></i>
                                <p class="text-lg">No visit requests found</p>
                                <p class="text-sm mt-2">All property visit requests will appear here</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Table Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        Showing <?= count($visits) ?> of <?= count($visits) ?> visit requests
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-calendar-alt mr-2"></i> Calendar View
                        </button>
                        <button class="px-4 py-2 bg-gradient-to-r from-green-600 to-blue-600 text-white rounded-lg hover:opacity-90">
                            <i class="fas fa-plus mr-2"></i> Add Visit
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="dashboard.php" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold rounded-lg hover:opacity-90 transition duration-300 shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i> Back To Dashboard
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-6 mt-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <div class="w-8 h-8 rounded-full saffron-bg flex items-center justify-center mr-2">
                        <div class="w-4 h-4 rounded-full green-bg flex items-center justify-center">
                            <div class="w-2 h-2 rounded-full blue-bg"></div>
                        </div>
                    </div>
                    <h2 class="heading-font text-lg">
                        <span class="text-saffron">Ghar</span><span class="text-green-400">Dekho</span>
                        <span class="text-white text-sm"> Admin Panel</span>
                    </h2>
                </div>
                <div class="text-gray-400 text-sm">
                    <p>© 2024 GharDekho Real Estate. Secure Admin Access</p>
                    <p class="mt-1">Last updated: <?= date('d M Y, h:i A') ?></p>
                </div>
            </div>
        </div>
    </footer>

    <script>
    async function updateStatus(id, status) {
        if (confirm('Are you sure you want to ' + status + ' this visit request?')) {
            try {
                const response = await fetch(`/api/visits.php/${id}`, {
                    method: 'PUT',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({status})
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Visit request ' + status + ' successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        }
    }
    
    // Add hover effects to table rows
    document.querySelectorAll('.table-row-hover').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(255, 153, 51, 0.05)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
    // Add button hover effects
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    </script>
</body>
</html>