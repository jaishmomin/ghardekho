<?php
session_start();
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GharDekho - Find Your Dream Home in India</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --saffron: #FF9933;
            --green: #138808;
            --blue: #000080;
            --gold: #FFD700;
            --maroon: #8B0000;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
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
        
        .indian-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FF9933' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .rangoli-border {
            border-bottom: 4px solid transparent;
            border-image: linear-gradient(to right, var(--saffron), var(--green), var(--blue)) 1;
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
                        </h1>
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8">
                    <a href="#home" class="font-medium hover:text-saffron-600 transition">Home</a>
                    <a href="#properties" class="font-medium hover:text-saffron-600 transition">Properties</a>
                    <a href="#about" class="font-medium hover:text-saffron-600 transition">About Us</a>
                    <a href="#services" class="font-medium hover:text-saffron-600 transition">Services</a>
                    <a href="#contact" class="font-medium hover:text-saffron-600 transition">Contact</a>
                </div>
                
                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <?php if ($user): ?>
                        <span class="text-sm text-gray-700">
                            Welcome, <span class="hidden font-semibold" id="user-name"><?= htmlspecialchars($user['name']) ?></span>
                        </span>
                        <a href="/logout.php"
                            id="logout-btn"
                            class="hidden px-4 py-2 rounded-md bg-red-500 text-white font-medium hover:bg-red-600 transition">
                            Log Out
                        </a>
                    <?php else: ?>
                        <button id="login-btn" type="button" class="px-4 py-2 rounded-md bg-white border border-saffron-500 text-saffron-600 font-medium hover:bg-saffron-50 transition">
                            Log In
                    </button>
                        <button id="signup-btn" type="button" class="px-4 py-2 rounded-md saffron-bg text-white font-medium hover:bg-saffron-600 transition">
                            Sign Up
                    </button>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-700 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu (hidden by default) -->
            <div id="mobile-menu" class="hidden md:hidden py-4 border-t">
                <a href="#home" class="block py-2 font-medium">Home</a>
                <a href="#properties" class="block py-2 font-medium">Properties</a>
                <a href="#about" class="block py-2 font-medium">About Us</a>
                <a href="#services" class="block py-2 font-medium">Services</a>
                <a href="#contact" class="block py-2 font-medium">Contact</a>
                    <div class="mt-4 pt-4 border-t">
                    <?php if ($user): ?>
                        <p class="block py-2 text-center text-gray-700">
                            Logged in as <span class="font-semibold"><?= htmlspecialchars($user['name']) ?></span>
                        </p>
                        <button id="logout-button-mobile" class="w-full block py-2 text-center bg-red-500 text-white rounded-md">
                            Log Out
                        </button>
                    <?php else: ?>
                        <a href="#" id="open-login-mobile" class="block py-2 text-center border border-saffron-500 text-saffron-600 rounded-md mb-2">
                            Log In
                        </a>
                        <a href="#" id="open-signup-mobile" class="block py-2 text-center saffron-bg text-white rounded-md">
                            Sign Up
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="py-12 md:py-20">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="heading-font text-4xl md:text-5xl font-bold mb-6">
                        Find Your <span class="gold-text">Dream Home</span> in <span class="green-text">Incredible India</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">
                        Discover premium properties across India with GharDekho. From modern apartments in Mumbai to traditional havelis in Rajasthan, we connect you with the perfect home.
                    </p>
                    
                    <!-- Search Box -->
                    <div class="bg-white rounded-lg shadow-lg p-4 md:p-6 mb-8">
                        <h3 class="font-bold text-xl mb-4">Search Properties</h3>
                        <form id="search-form">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 text-sm mb-2">Location</label>
                                    <div class="relative">
                                        <select id="search-city" name="city" class="w-full border border-gray-300 rounded-md py-3 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-saffron-500">
                                            <option value="">Select City</option>
                                            <option value="Mumbai">Mumbai</option>
                                            <option value="Delhi">Delhi</option>
                                            <option value="Bangalore">Bangalore</option>
                                            <option value="Hyderabad">Hyderabad</option>
                                            <option value="Chennai">Chennai</option>
                                            <option value="Kolkata">Kolkata</option>
                                            <option value="Ahmedabad">Ahmedabad</option>
                                            <option value="Pune">Pune</option>
                                        </select>
                                        <i class="fas fa-city absolute left-3 top-3.5 text-gray-400"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm mb-2">Property Type</label>
                                    <div class="relative">
                                        <select id="search-type" name="type" class="w-full border border-gray-300 rounded-md py-3 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-saffron-500">
                                            <option value="">Any Type</option>
                                            <option value="flat">Flat</option>
                                            <option value="villa">Villa</option>
                                            <option value="bungalow">Independent House</option>
                                            <option value="shop">Commercial</option>
                                        </select>
                                        <i class="fas fa-home absolute left-3 top-3.5 text-gray-400"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm mb-2">Budget (₹)</label>
                                    <div class="relative">
                                        <select id="search-budget" name="budget" class="w-full border border-gray-300 rounded-md py-3 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-saffron-500">
                                            <option value="">Any Budget</option>
                                            <option value="5000000">Under 50 Lakhs</option>
                                            <option value="10000000">Under 1 Crore</option>
                                            <option value="50000000">Under 5 Crores</option>
                                        </select>
                                        <i class="fas fa-rupee-sign absolute left-3 top-3.5 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-saffron to-green-600 text-black font-bold rounded-md hover:opacity-90 transition">
                                <i class="fas fa-search mr-2"></i> Search Properties
                            </button>
                        </form>
                        <div id="search-results"
                            class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center">
                        <div class="mr-6 mb-4">
                            <p class="text-2xl font-bold text-blue-700">10,000+</p>
                            <p class="text-gray-600">Properties Listed</p>
                        </div>
                        <div class="mr-6 mb-4">
                            <p class="text-2xl font-bold text-green-600">50+</p>
                            <p class="text-gray-600">Cities Across India</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-2xl font-bold text-saffron">5,000+</p>
                            <p class="text-gray-600">Happy Customers</p>
                        </div>
                    </div>
                </div>
                
                <div class="md:w-1/2 md:pl-12">
                    <div class="relative">
                        <div class="rounded-lg overflow-hidden shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Modern Indian Apartment" class="w-full h-64 md:h-96 object-cover">
                        </div>
                        <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-lg shadow-lg w-3/4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 saffron-bg rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-trophy text-white text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">Property of the Month</h4>
                                    <p class="text-gray-600">Luxury Villa in Goa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Properties -->
    <section id="properties" class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="heading-font text-3xl md:text-4xl font-bold mb-4">Featured <span class="maroon-text">Properties</span></h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Explore our handpicked selection of premium properties from across India</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Property 1 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1613977257592-4871e5fcd7c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Luxury Apartment Mumbai" class="w-full h-56 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-saffron text-white text-sm rounded-full">Featured</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl mb-2">Sea View Apartment, Mumbai</h3>
                        <p class="text-gray-600 mb-4"><i class="fas fa-map-marker-alt text-saffron mr-2"></i> Bandra West, Mumbai</p>
                        <div class="flex justify-between mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-bed text-blue-600 mr-2"></i>
                                <span>3 Beds</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-bath text-blue-600 mr-2"></i>
                                <span>3 Baths</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                                <span>1800 sqft</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-2xl font-bold text-green-600">₹ 4.2 Cr</p>
                            <a href="/property.php?id=1"
                            class="px-4 py-2 bg-blue-700 text-white rounded-md inline-block">
                            View Details
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Property 2 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1580587771525-78b9dba3b914?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Modern Villa Delhi" class="w-full h-56 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-green-600 text-white text-sm rounded-full">New Listing</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl mb-2">Modern Villa, Delhi</h3>
                        <p class="text-gray-600 mb-4"><i class="fas fa-map-marker-alt text-saffron mr-2"></i> Vasant Kunj, Delhi</p>
                        <div class="flex justify-between mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-bed text-blue-600 mr-2"></i>
                                <span>4 Beds</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-bath text-blue-600 mr-2"></i>
                                <span>4 Baths</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-layer-group text-blue-600 mr-2"></i>
                                <span>3200 sqft</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-2xl font-bold text-green-600">₹ 6.8 Cr</p>
                            <a href="/property.php?id=2"
                            class="px-4 py-2 bg-blue-700 text-white rounded-md inline-block">
                            View Details
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Property 3 -->
                <div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1616594039964-ae9021a400a0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Traditional Haveli Jaipur" class="w-full h-56 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-maroon text-white text-sm rounded-full">Heritage</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl mb-2">Traditional Haveli, Jaipur</h3>
                        <p class="text-gray-600 mb-4"><i class="fas fa-map-marker-alt text-saffron mr-2"></i> Civil Lines, Jaipur</p>
                        <div class="flex justify-between mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-bed text-blue-600 mr-2"></i>
                                <span>6 Beds</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-bath text-blue-600 mr-2"></i>
                                <span>5 Baths</span>
                            </div>
                            <div class="fas fa-layer-group text-blue-600 mr-2"></i>
                                <span>5200 sqft</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-2xl font-bold text-green-600">₹ 8.5 Cr</p>
                            <a href="/property.php?id=3"
                            class="px-4 py-2 bg-blue-700 text-white rounded-md inline-block">
                            View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <a href="#" class="inline-flex items-center px-6 py-3 saffron-bg text-white font-bold rounded-md hover:bg-saffron-600 transition">
                    View All Properties <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
        <div id="search-results" class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
        </div>
    </section>

    <!-- How It Works -->
    <section id="services" class="py-12">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="heading-font text-3xl md:text-4xl font-bold mb-4">How <span class="green-text">GharDekho</span> Works</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Three simple steps to find your dream property in India</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-r from-red-500 to-red-400 flex items-center justify-center">
                        <i class="fas fa-search text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">1. Search Property</h3>
                    <p class="text-gray-600">Browse through thousands of verified properties across India with detailed filters.</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-r from-green-500 to-green-700 flex items-center justify-center">
                        <i class="fas fa-eye text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">2. Virtual/Physical Visit</h3>
                    <p class="text-gray-600">Schedule a virtual tour or visit the property with our expert guides.</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-r from-blue-600 to-blue-800 flex items-center justify-center">
                        <i class="fas fa-file-contract text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">3. Make It Yours</h3>
                    <p class="text-gray-600">Complete paperwork and legal formalities with our assistance for a smooth purchase.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-blue-900 to-blue-700 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="heading-font text-3xl md:text-4xl font-bold mb-6">Ready to Find Your Dream Home?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Join thousands of happy homeowners who found their perfect property with GharDekho</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="#" class="px-8 py-3 bg-white text-blue-800 font-bold rounded-md hover:bg-gray-100 transition">
                    <i class="fas fa-home mr-2"></i> Browse Properties
                </a>
                <a href="#" class="px-8 py-3 bg-transparent border-2 border-white text-white font-bold rounded-md hover:bg-white hover:text-blue-800 transition">
                    <i class="fas fa-user-plus mr-2"></i> Create Free Account
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white pt-12 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 rounded-full saffron-bg flex items-center justify-center mr-2">
                            <div class="w-6 h-6 rounded-full green-bg flex items-center justify-center">
                                <div class="w-3 h-3 rounded-full blue-bg"></div>
                            </div>
                        </div>
                        <h2 class="heading-font text-2xl font-bold">
                            <span class="text-saffron">Ghar</span><span class="text-green-400">Dekho</span>
                        </h2>
                    </div>
                    <p class="text-gray-400 mb-4">India's premier real estate platform connecting buyers with their dream properties since 2010.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-saffron transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-saffron transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-saffron transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-saffron transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-6">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-saffron transition">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-saffron transition">Properties</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-saffron transition">Agents</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-saffron transition">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-saffron transition">Careers</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-6">Cities</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-saffron transition">Mumbai</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-saffron transition">Delhi</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-saffron transition">Bangalore</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-saffron transition">Hyderabad</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-saffron transition">Chennai</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-xl font-bold mb-6">Contact Us</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt text-saffron mr-3"></i>
                            <span class="text-gray-400">Near Gokhale College, Kolhapur, India</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone text-saffron mr-3"></i>
                            <span class="text-gray-400">+91 98765 43210</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-saffron mr-3"></i>
                            <span class="text-gray-400">info@ghardekho.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-gray-800 text-center text-gray-400">
                <p>&copy; 2023 GharDekho Real Estate. All rights reserved. | Made with <i class="fas fa-heart text-red-500"></i> in India</p>
            </div>
        </div>
        <form id="contact-form" class="mt-4 space-y-3">
        <input type="text" name="name" placeholder="Your Name" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700" required>
        <input type="email" name="email" placeholder="Your Email" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700" required>
        <textarea name="message" placeholder="Your Message" class="w-full px-3 py-2 rounded bg-gray-800 text-gray-200 border border-gray-700" rows="3" required></textarea>
        <button type="submit" class="w-full py-2 saffron-bg text-white rounded hover:bg-saffron-600 transition">
            Send Message
        </button>
        <p id="contact-message" class="text-sm mt-2"></p>
    </form>
    </footer>
    
    <!-- LOGIN MODAL -->
    <center>
    <div id="login-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Log In to GharDekho</h2>
        <form id="login-form" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1"></label>
            <input type="email" name="email" id="login-email" placeholder="Email" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1"></label>
            <input type="password" name="password" id="login-password" placeholder="Password" class="w-full border rounded px-3 py-2" required>
        </div>
        <div id="login-error" class="text-sm text-red-600 hidden"></div>
        <div class="flex justify-end space-x-2 pt-2">
            <button onclick="this.closest('.fixed').classList.add('hidden')">Cancel</button>
            <button type="submit" class="px-4 py-2 saffron-bg text-white rounded">Log In</button>
        </div>
        </form>
        <p id="login-message" class="mt-3 text-sm"></p>
    </div>
    </div>
    </center>

    <!-- Sign Up Modal (simple div) -->
    <center>
    <div id="signup-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Create Free Account</h2>
        <form id="signup-form">
        <input type="text"
            id="signup-name"
            placeholder="Name"
            class="w-full border p-2 mb-3"
            required>

        <input type="email"
            id="signup-email"
            placeholder="Email"
            class="w-full border p-2 mb-3"
            required>

        <input type="password"
            id="signup-password"
            placeholder="Password"
            class="w-full border p-2 mb-3"
            required>
            
            <div class="flex justify-end space-x-2 pt-2">
                <button onclick="this.closest('.fixed').classList.add('hidden')">Cancel</button>
                <button type="submit" class="px-4 py-2 saffron-bg text-white rounded">Sign Up</button>
            </div>
    </form>
        <p id="signup-message" class="mt-3 text-sm"></p>
    </div>
    </div>
    </center>

    <!-- Mobile menu script -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobile-menu');
            const button = document.getElementById('mobile-menu-button');
            
            if (!menu.contains(event.target) && !button.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
        
         // ================= SIGN UP =================
    const signupModal = document.getElementById('signup-modal');
    const openSignupBtn = document.getElementById('open-signup');
    const signupCancel = document.getElementById('signup-cancel');
    const signupForm = document.getElementById('signup-form');
    const signupMessage = document.getElementById('signup-message');

    // open modal
    openSignupBtn.addEventListener('click', function(e) {
        e.preventDefault();
        signupModal.classList.remove('hidden');
        signupModal.classList.add('flex');
    });

    // close modal
    signupCancel.addEventListener('click', function() {
        signupModal.classList.add('hidden');
        signupModal.classList.remove('flex');
    });

    // handle signup form submit
    signupForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(signupForm);
        const payload = {
            name: formData.get('name'),
            email: formData.get('email'),
            password: formData.get('password')
        };

        signupMessage.textContent = 'Creating account...';

        try {
            const res = await fetch('/api/auth.php?action=register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            signupMessage.textContent = data.message || 'Done';

            if (data.success) {
                signupMessage.className = 'mt-3 text-sm text-green-600';
                signupForm.reset();
            } else {
                signupMessage.className = 'mt-3 text-sm text-red-600';
            }
        } catch (err) {
            signupMessage.textContent = 'Error connecting to server.';
            signupMessage.className = 'mt-3 text-sm text-red-600';
        }
    });

        // ================= SEARCH PROPERTIES =================
    const searchForm = document.getElementById('search-form');

    searchForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const city = document.getElementById('search-city').value;
        const type = document.getElementById('search-type').value;
        const budget = document.getElementById('search-budget').value;

        // convert budget range to min/max in rupees
        let min = '', max = '';
        if (budget === '10-50') { min = 1000000; max = 5000000; }
        if (budget === '50-100') { min = 5000000; max = 10000000; }
        if (budget === '100-500') { min = 10000000; max = 50000000; }
        if (budget === '500+') { min = 50000000; }

        const params = new URLSearchParams();
        if (city) params.append('city', city);
        if (type) params.append('type', type);
        if (min) params.append('budget_min', min);
        if (max) params.append('budget_max', max);

        try {
            const res = await fetch('/api/properties.php?' + params.toString());
            const data = await res.json();

            if (!data.success) {
                alert(data.message || 'Search failed');
                return;
            }

            // For now, just show results in console
            console.log('Search results:', data.data);

            // OPTIONAL: replace Featured Properties cards dynamically
            // You can create HTML from data.data and inject into the grid
        } catch (err) {
            alert('Error contacting server');
        }
    });

        // ================= VIEW DETAILS =================
    const detailButtons = document.querySelectorAll('.view-details-btn');

    detailButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.getAttribute('data-id');
            try {
                const res = await fetch('/api/properties.php?id=' + encodeURIComponent(id));
                const data = await res.json();
                if (!data.success) {
                    alert(data.message || 'Property not found');
                    return;
                }
                const p = data.data;
                alert(`Title: ${p.title}\nCity: ${p.city_name}\nBeds: ${p.beds}\nPrice: ${p.price}`);
                // Later you can show this in a nice modal instead of alert
            } catch (err) {
                alert('Error fetching details');
            }
        });
    });

        // ================= CONTACT FORM / INQUIRY =================
    const contactForm = document.getElementById('contact-form');
    const contactMsg = document.getElementById('contact-message');

    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(contactForm);
        const payload = {
            type: 'contact',
            name: formData.get('name'),
            email: formData.get('email'),
            message: formData.get('message')
        };

        contactMsg.textContent = 'Sending...';

        try {
            const res = await fetch('/api/inquiries.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            contactMsg.textContent = data.message || 'Done';

            if (data.success) {
                contactMsg.className = 'text-sm text-green-400 mt-2';
                contactForm.reset();
            } else {
                contactMsg.className = 'text-sm text-red-400 mt-2';
            }
        } catch (err) {
            contactMsg.textContent = 'Error contacting server.';
            contactMsg.className = 'text-sm text-red-400 mt-2';
        }
    });

    // ================= LOGIN =================
    const loginModal   = document.getElementById('login-modal');
    const openLoginBtn = document.getElementById('open-login');
    const loginCancel  = document.getElementById('login-cancel');
    const loginForm    = document.getElementById('login-form');
    const loginMessage = document.getElementById('login-message');
    const loginError   = document.getElementById('login-error');

    // Open login modal
    if (openLoginBtn) {
        openLoginBtn.addEventListener('click', function (e) {
            e.preventDefault();
            loginMessage.textContent = '';
            loginError.textContent = '';
            loginError.classList.add('hidden');
            loginModal.classList.remove('hidden');
            loginModal.classList.add('flex');
        });
    }

    // Close login modal
    if (loginCancel) {
        loginCancel.addEventListener('click', function () {
            loginModal.classList.add('hidden');
            loginModal.classList.remove('flex');
        });
    }

    // Basic validation
    function validateLoginForm(email, password) {
        if (!email || !password) return 'Please enter both email and password.';
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) return 'Please enter a valid email address.';
        return '';
    }

    // Handle login submit
    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email    = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;

            const validationError = validateLoginForm(email, password);
            if (validationError) {
                loginError.textContent = validationError;
                loginError.classList.remove('hidden');
                loginMessage.textContent = '';
                return;
            } else {
                loginError.classList.add('hidden');
            }

            loginMessage.textContent = 'Logging in...';
            loginMessage.className = 'mt-3 text-sm text-gray-600';

            try {
                const res = await fetch('/api/auth.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',   // ✅ make sure session cookie is sent
                    body: JSON.stringify({ email, password })
                });

                const text = await res.text();
                console.log('Login raw response:', text);

                let data;
                try {
                    data = JSON.parse(text);
                } catch (err) {
                    loginMessage.textContent = 'Server returned invalid response: ' + text;
                    loginMessage.className = 'mt-3 text-sm text-red-600';
                    return;
                }

                loginMessage.textContent = data.message || 'Done';

                if (data.success) {
                    loginMessage.className = 'mt-3 text-sm text-green-600';
                    // ✅ THIS is the important part:
                    setTimeout(() => {
                        // Force full reload so PHP re-renders navbar with $_SESSION['user']
                        window.location.href = window.location.pathname;
                    }, 700);
                } else {
                    loginMessage.className = 'mt-3 text-sm text-red-600';
                }
            } catch (err) {
                console.error(err);
                loginMessage.textContent = 'Could not reach server.';
                loginMessage.className = 'mt-3 text-sm text-red-600';
            }
        });
    }
    document.getElementById('logout-btn').addEventListener('click', async () => {
    await fetch('/api/auth.php?action=logout', { method: 'POST' });
    location.reload();
    });
    </script>
    <script>
    async function checkLogin() {
        const res = await fetch('/api/me.php');
        const data = await res.json();

        if (data.loggedIn) {
            document.getElementById('user-name').textContent = data.user.name;
            document.getElementById('user-name').classList.remove('hidden');

            document.getElementById('logout-btn').classList.remove('hidden');

            document.getElementById('login-btn').style.display = 'none';
            document.getElementById('signup-btn').style.display = 'none';
        }
    }

    checkLogin();
    </script>
    <script>
    document.getElementById('logout-btn')?.addEventListener('click', async () => {
        await fetch('/api/auth.php?action=logout', { method: 'POST' });
        location.reload();
    });
    </script>
    <script>
    const loginBtn  = document.getElementById('login-btn');
    const signupBtn = document.getElementById('signup-btn');

    const loginModal  = document.getElementById('login-modal');
    const signupModal = document.getElementById('signup-modal');

    loginBtn?.addEventListener('click', () => {
        loginModal.classList.remove('hidden');
    });

    signupBtn?.addEventListener('click', () => {
        signupModal.classList.remove('hidden');
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {

        const loginBtn  = document.getElementById('login-btn');
        const signupBtn = document.getElementById('signup-btn');

        const loginModal  = document.getElementById('login-modal');
        const signupModal = document.getElementById('signup-modal');

        const closeLogin  = document.getElementById('close-login');
        const closeSignup = document.getElementById('close-signup');

        if (!loginBtn || !signupBtn || !loginModal || !signupModal) {
            console.error('Login/Signup elements missing');
            return;
        }

        loginBtn.addEventListener('click', () => {
            loginModal.classList.remove('hidden');
        });

        signupBtn.addEventListener('click', () => {
            signupModal.classList.remove('hidden');
        });

        closeLogin.addEventListener('click', () => {
            loginModal.classList.add('hidden');
        });

        closeSignup.addEventListener('click', () => {
            signupModal.classList.add('hidden');
        });
    });
    </script>
    <script>
    document.getElementById('signup-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const payload = {
            name: document.getElementById('signup-name').value,
            email: document.getElementById('signup-email').value,
            password: document.getElementById('signup-password').value
        };

        const res = await fetch('/api/auth.php?action=register', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        alert(data.message);

        if (data.success) {
            document.getElementById('signup-form').reset();
            document.getElementById('signup-modal').classList.add('hidden');
        }
    });
    </script>
    <script>
    document.getElementById('login-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        const payload = {
            email: document.getElementById('login-email').value,
            password: document.getElementById('login-password').value
        };

        const res = await fetch('/api/auth.php?action=login', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        alert(data.message);

        if (data.success) {
            location.reload();
        }
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('signup-form');

        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const nameInput = document.getElementById('signup-name');
            const emailInput = document.getElementById('signup-email');
            const passwordInput = document.getElementById('signup-password');

            if (!nameInput || !emailInput || !passwordInput) {
                alert('Signup inputs not found');
                return;
            }

            const payload = {
                name: nameInput.value.trim(),
                email: emailInput.value.trim(),
                password: passwordInput.value
            };

            const res = await fetch('/api/auth.php?action=register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            let data;
            try {
                data = await res.json();
            } catch (e) {
                alert('Server returned invalid response');
                return;
            }

            // 🔑 SAFE MESSAGE HANDLING
            if (data.message) {
                alert(data.message);
            } else {
                alert('Unexpected server response');
                console.log('Full response:', data);
            }

            if (data.success) {
                form.reset();
                document.getElementById('signup-modal').classList.add('hidden');
            }
        });
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {

        const loginForm = document.getElementById('login-form');
        if (!loginForm) return;

        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const payload = {
                email: document.getElementById('login-email').value.trim(),
                password: document.getElementById('login-password').value
            };

            const res = await fetch('/api/auth.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            alert(data.message);

            if (data.success) {
                // 🔑 ROLE-BASED REDIRECT
                if (data.role === 'admin') {
                    window.location.href = '/admin/dashboard.php';
                } else {
                    window.location.reload();
                }
            }
        });
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {

        const searchForm = document.getElementById('search-form');

        if (!searchForm) {
            console.error('Search form not found');
            return;
        }

        searchForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            console.log('Search submitted'); // 🔴 DEBUG

            const city   = document.getElementById('search-city')?.value || '';
            const type   = document.getElementById('search-type')?.value || '';
            const budget = document.getElementById('search-budget')?.value || '';

            console.log({ city, type, budget }); // 🔴 DEBUG

            let url = `/api/properties.php?`;
            if (city)   url += `city=${encodeURIComponent(city)}&`;
            if (type)   url += `type=${encodeURIComponent(type)}&`;
            if (budget) url += `budget_max=${budget}&`;

            const res = await fetch(url);
            const data = await res.json();

            console.log('API response:', data); // 🔴 DEBUG

            const container = document.getElementById('search-results');
            container.innerHTML = '';

            if (!data.success || data.data.length === 0) {
                container.innerHTML = `<p class="text-gray-600">No properties found.</p>`;
                return;
            }

            data.data.forEach(p => {
                container.innerHTML += `
                    <div class="bg-white rounded shadow p-4">
                        <h3 class="font-bold">${p.title}</h3>
                        <p class="text-gray-500">${p.city}</p>
                        <p class="text-green-600 font-bold mt-2">
                            ₹ ${Number(p.price).toLocaleString()}
                        </p>
                        <a href="/property.php?id=${p.id}"
                        class="inline-block mt-3 bg-blue-600 text-white px-4 py-2 rounded">
                            View Details
                        </a>
                    </div>
                `;
            });
        });
    });
    </script>
</body>
</html>