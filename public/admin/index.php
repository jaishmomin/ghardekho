<?php
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
<body class="bg-gray-100">

<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

    <div class="grid grid-cols-2 gap-6">
        <a href="inquiries.php" class="bg-white p-6 rounded shadow hover:bg-gray-50">
            <h2 class="text-xl font-bold">Manage Inquiries</h2>
            <p class="text-gray-600">View and update inquiries</p>
        </a>

        <a href="visits.php" class="bg-white p-6 rounded shadow hover:bg-gray-50">
            <h2 class="text-xl font-bold">Manage Visits</h2>
            <p class="text-gray-600">Approve or reject visits</p>
        </a>
    </div>
</div>
    <a href="/logout.php" class="inline-block mt-6 text-red-600 underline">
            Logout
    </a>
</body>
</html>
