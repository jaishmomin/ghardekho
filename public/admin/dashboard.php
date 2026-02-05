<!-- <?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

<h1 class="text-3xl font-bold mb-4">
    Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?>
</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="properties.php" class="bg-white p-6 rounded shadow hover:bg-gray-50">
        🏠 Manage Properties
    </a>
    <a href="inquiries.php" class="bg-white p-6 rounded shadow hover:bg-gray-50">
        📩 View Inquiries
    </a>
    <a href="visits.php" class="bg-white p-6 rounded shadow hover:bg-gray-50">
        📅 Manage Visits
    </a>
</div>

<a href="/logout.php" class="inline-block mt-6 text-red-600 underline">
    Logout
</a>

</body>
</html> -->
<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /');
    exit;
}
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GharDekho</title>
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
        
        .gold-text {
            color: var(--gold);
        }
        
        .maroon-text {
            color: var(--maroon);
        }
        
        .saffron-text {
            color: var(--saffron);
        }
        
        .green-text {
            color: var(--green);
        }
        
        .indian-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FF9933' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .rangoli-border {
            border-bottom: 4px solid transparent;
            border-image: linear-gradient(to right, var(--saffron), var(--green), var(--blue)) 1;
        }
        
        .admin-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }
        
        .admin-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .admin-card-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 32px;
        }
        
        .icon-inquiry {
            background: linear-gradient(135deg, var(--saffron), #ffb366);
            color: white;
        }
        
        .icon-visit {
            background: linear-gradient(135deg, var(--green), #2db34a);
            color: white;
        }
        
        .admin-stats {
            background: linear-gradient(135deg, var(--blue), #3333cc);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
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
                        <p class="text-sm text-gray-600">Super Admin</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-saffron to-green-600 flex items-center justify-center">
                        <i class="fas fa-user-shield text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container mx-auto px-4 py-12">
        <!-- Dashboard Header -->
        <div class="text-center mb-12">
            <h2 class="heading-font text-3xl md:text-4xl font-bold mb-4">Admin <span class="maroon-text">Dashboard</span></h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Manage your GharDekho platform with complete control and insights</p>
        </div>
        
        <!-- Admin Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto mb-12">
            <!-- Inquiries Card -->
            <a href="inquiries.php" class="admin-card bg-white rounded-lg shadow-lg">
                <div class="p-8">
                    <div class="admin-card-icon icon-inquiry">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Manage Inquiries</h3>
                    <p class="text-gray-600 mb-6">View and update all customer inquiries. Track responses and manage communication efficiently.</p>
                    
                    <div class="admin-stats">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm opacity-90">New Today</p>
                                <p class="text-2xl font-bold">12</p>
                            </div>
                            <div>
                                <p class="text-sm opacity-90">Total</p>
                                <p class="text-2xl font-bold">156</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 bg-white text-blue-700 rounded-full text-sm font-bold">
                                    <i class="fas fa-arrow-up mr-1"></i> 24%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Visits Card -->
            <a href="visits.php" class="admin-card bg-white rounded-lg shadow-lg">
                <div class="p-8">
                    <div class="admin-card-icon icon-visit">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Manage Visits</h3>
                    <p class="text-gray-600 mb-6">Approve or reject property visit requests. Schedule and manage property viewing appointments.</p>
                    
                    <div class="admin-stats">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm opacity-90">Scheduled</p>
                                <p class="text-2xl font-bold">8</p>
                            </div>
                            <div>
                                <p class="text-sm opacity-90">Pending</p>
                                <p class="text-2xl font-bold">5</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 bg-white text-green-700 rounded-full text-sm font-bold">
                                    <i class="fas fa-check-circle mr-1"></i> 63%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Quick Stats
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-4xl mx-auto mb-12">
            <h3 class="text-xl font-bold mb-6 text-gray-800">Platform Overview</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center p-4 border-r">
                    <p class="text-3xl font-bold saffron-text">245</p>
                    <p class="text-gray-600">Total Properties</p>
                </div>
                <div class="text-center p-4 border-r">
                    <p class="text-3xl font-bold green-text">1,248</p>
                    <p class="text-gray-600">Registered Users</p>
                </div>
                <div class="text-center p-4 border-r">
                    <p class="text-3xl font-bold blue-text">₹8.5Cr</p>
                    <p class="text-gray-600">Total Value</p>
                </div>
                <div class="text-center p-4">
                    <p class="text-3xl font-bold maroon-text">98%</p>
                    <p class="text-gray-600">Satisfaction</p>
                </div>
            </div>
        </div> -->
        
        <!-- Logout Button -->
        <div class="text-center">
            <a href="/logout.php" class="inline-flex items-center px-8 py-3 saffron-bg text-white font-bold rounded-lg hover:bg-orange-500 transition duration-300 shadow-lg">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <div class="flex items-center justify-center mb-4">
                <div class="w-10 h-10 rounded-full saffron-bg flex items-center justify-center mr-2">
                    <div class="w-6 h-6 rounded-full green-bg flex items-center justify-center">
                        <div class="w-3 h-3 rounded-full blue-bg"></div>
                    </div>
                </div>
                <h2 class="heading-font text-xl">
                    <span class="text-saffron">Ghar</span><span class="text-green-400">Dekho</span>
                    <span class="text-white text-lg"> Admin Panel</span>
                </h2>
            </div>
            <p class="text-gray-400">© 2024 GharDekho Real Estate. All rights reserved.</p>
            <p class="text-gray-400 text-sm mt-2">Secure Admin Access Only</p>
        </div>
    </footer>

    <script>
        // Add hover effect to cards
        document.querySelectorAll('.admin-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>