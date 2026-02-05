<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\Database;

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /');
    exit;
}

$db = Database::getConnection();
$rows = $db->query("
    SELECT i.*, p.title 
    FROM inquiries i
    LEFT JOIN properties p ON i.property_id = p.id
    ORDER BY i.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Inquiries | GharDekho</title>
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
        
        .badge-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ff9800;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .badge-contacted {
            background-color: rgba(76, 175, 80, 0.2);
            color: #4caf50;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }
        
        .badge-closed {
            background-color: rgba(158, 158, 158, 0.2);
            color: #757575;
            border: 1px solid rgba(158, 158, 158, 0.3);
        }
        
        .table-row-hover:hover {
            background-color: rgba(255, 153, 51, 0.05);
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
                        <p class="text-sm text-gray-600">Inquiries Management</p>
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
                        Manage <span class="saffron-text">Inquiries</span>
                    </h1>
                    <p class="text-gray-600">View and manage all customer inquiries and requests</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Total Inquiries</p>
                    <p class="text-2xl font-bold blue-text"><?= count($rows) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Statistics Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold saffron-text mb-2">
                    <?= count(array_filter($rows, fn($r) => $r['status'] === 'pending')) ?>
                </div>
                <p class="text-gray-600">Pending</p>
                <div class="mt-2">
                    <span class="badge badge-pending">Action Required</span>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold green-text mb-2">
                    <?= count(array_filter($rows, fn($r) => $r['status'] === 'contacted')) ?>
                </div>
                <p class="text-gray-600">Contacted</p>
                <div class="mt-2">
                    <span class="badge badge-contacted">In Progress</span>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold blue-text mb-2">
                    <?= count(array_filter($rows, fn($r) => $r['status'] === 'closed')) ?>
                </div>
                <p class="text-gray-600">Closed</p>
                <div class="mt-2">
                    <span class="badge badge-closed">Completed</span>
                </div>
            </div>
        </div>

        <!-- Inquiries Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-saffron to-green-600 text-white">
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
                                    Name
                                </div>
                            </th>
                            <th class="p-4 text-left font-semibold">
                                <div class="flex items-center">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Email
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
                                    <i class="fas fa-calendar mr-2"></i>
                                    Date
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr class="border-t border-gray-100 table-row-hover">
                            <td class="p-4">
                                <div class="font-medium text-gray-800">
                                    <?= htmlspecialchars($r['title']) ?>
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-map-marker-alt text-saffron mr-1"></i>
                                    <?= htmlspecialchars($r['address'] ?? 'N/A') ?>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-gray-800">
                                    <?= htmlspecialchars($r['name']) ?>
                                </div>
                                <?php if (!empty($r['phone'])): ?>
                                <div class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-phone text-green-500 mr-1"></i>
                                    <?= htmlspecialchars($r['phone']) ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <a href="mailto:<?= htmlspecialchars($r['email']) ?>" 
                                   class="text-blue-600 hover:text-blue-800 hover:underline">
                                    <?= htmlspecialchars($r['email']) ?>
                                </a>
                                <?php if (!empty($r['type'])): ?>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle text-saffron mr-1"></i>
                                    <?= ucfirst($r['type']) ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <?php 
                                $statusClass = match($r['status']) {
                                    'pending' => 'badge-pending',
                                    'contacted' => 'badge-contacted',
                                    'closed' => 'badge-closed',
                                    default => 'badge-pending'
                                };
                                ?>
                                <span class="badge <?= $statusClass ?>">
                                    <?= ucfirst(htmlspecialchars($r['status'])) ?>
                                </span>
                                <?php if ($r['status'] === 'pending'): ?>
                                <div class="mt-2">
                                    <button class="text-xs bg-orange text-black px-3 py-1 rounded hover:bg-orange-500 transition"
                                            onclick="markAsContacted(<?= $r['id'] ?>)">
                                        <i class="fas fa-check mr-1"></i> Mark Contacted
                                    </button>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <div class="text-gray-800">
                                    <?= date('d M Y', strtotime($r['created_at'])) ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?= date('h:i A', strtotime($r['created_at'])) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                <i class="fas fa-inbox fa-3x text-gray-300 mb-4"></i>
                                <p class="text-lg">No inquiries found</p>
                                <p class="text-sm mt-2">All customer inquiries will appear here</p>
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
                        Showing <?= count($rows) ?> of <?= count($rows) ?> inquiries
                    </div>
                    <div class="flex space-x-2">
                        <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-download mr-2"></i> Export
                        </button>
                        <button class="px-4 py-2 bg-saffron text-white rounded-lg hover:bg-orange-500">
                            <i class="fas fa-filter mr-2"></i> Filter
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
        function markAsContacted(inquiryId) {
            if (confirm('Mark this inquiry as contacted?')) {
                fetch('/api/inquiries/' + inquiryId + '/status', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ status: 'contacted' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Inquiry marked as contacted!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
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
    </script>
</body>
</html>
