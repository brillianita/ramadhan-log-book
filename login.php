<?php
require_once 'config.php';

// Jika sudah login, redirect ke index
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ramadhan Glow Up</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;600&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #8A9A5B 0%, #E2725B 100%);
        }
        .font-serif-modern { font-family: 'Playfair Display', serif; }
        .font-handwriting { font-family: 'Dancing Script', cursive; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8">
        <!-- Logo Area -->
        <div class="text-center mb-8">
            <h1 class="font-serif-modern text-4xl text-[#8A9A5B] mb-2">Ramadhan Glow Up</h1>
            <p class="text-sm text-gray-600">30 Hari Menata Hati, Merawat Diri</p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-4 mb-6 border-b">
            <button onclick="switchTab('login')" id="loginTab" class="flex-1 pb-3 font-semibold text-[#8A9A5B] border-b-2 border-[#8A9A5B]">
                Login
            </button>
            <button onclick="switchTab('register')" id="registerTab" class="flex-1 pb-3 font-semibold text-gray-400">
                Register
            </button>
        </div>

        <!-- Alert -->
        <div id="alertBox" class="hidden mb-4 p-3 rounded-lg"></div>

        <!-- Login Form -->
        <form id="loginForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#8A9A5B] focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#8A9A5B] focus:border-transparent">
            </div>
            <button type="submit" class="w-full bg-[#8A9A5B] hover:bg-[#7a8a4b] text-white font-semibold py-3 rounded-lg transition duration-200">
                Login
            </button>
        </form>

        <!-- Register Form -->
        <form id="registerForm" class="space-y-4 hidden">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#8A9A5B] focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#8A9A5B] focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required minlength="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#8A9A5B] focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
            </div>
            <button type="submit" class="w-full bg-[#E2725B] hover:bg-[#d2625b] text-white font-semibold py-3 rounded-lg transition duration-200">
                Register
            </button>
        </form>
    </div>

    <script>
        function switchTab(tab) {
            const loginTab = document.getElementById('loginTab');
            const registerTab = document.getElementById('registerTab');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');

            if (tab === 'login') {
                loginTab.classList.add('text-[#8A9A5B]', 'border-b-2', 'border-[#8A9A5B]');
                loginTab.classList.remove('text-gray-400');
                registerTab.classList.remove('text-[#8A9A5B]', 'border-b-2', 'border-[#8A9A5B]');
                registerTab.classList.add('text-gray-400');
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
            } else {
                registerTab.classList.add('text-[#8A9A5B]', 'border-b-2', 'border-[#8A9A5B]');
                registerTab.classList.remove('text-gray-400');
                loginTab.classList.remove('text-[#8A9A5B]', 'border-b-2', 'border-[#8A9A5B]');
                loginTab.classList.add('text-gray-400');
                registerForm.classList.remove('hidden');
                loginForm.classList.add('hidden');
            }
            hideAlert();
        }

        function showAlert(message, type) {
            const alertBox = document.getElementById('alertBox');
            alertBox.className = `mb-4 p-3 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            alertBox.textContent = message;
            alertBox.classList.remove('hidden');
        }

        function hideAlert() {
            document.getElementById('alertBox').classList.add('hidden');
        }

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'login');

            try {
                const response = await fetch('auth.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => window.location.href = 'index.php', 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan. Silakan coba lagi.', 'error');
            }
        });

        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'register');

            try {
                const response = await fetch('auth.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success');
                    setTimeout(() => window.location.href = 'index.php', 1000);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan. Silakan coba lagi.', 'error');
            }
        });
    </script>
</body>
</html>