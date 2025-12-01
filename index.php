<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div>
        <style>
        .hero-background {
            background-image: url('https://img.daisyui.com/images/stock/photo-1507358522600-9f71e620c44e.webp'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .overlay {
            background-color: rgba(0, 0, 0, 0.4); 
        }
    </style>
</head>
<body>

    <section class="relative w-full h-screen hero-background flex items-center justify-center">
        <div class="absolute inset-0 overlay"></div>

        <div class="relative z-10 text-center text-white p-6 max-w-xl mx-auto">
            <h1 class="text-5xl md:text-6xl font-bold mb-4">LIBRIX</h1>
            <p class="text-lg md:text-xl mb-8 leading-relaxed">
                Welcome to LIBRIX - Your Ultimate Library Management System! Discover, borrow, and manage your favorite books with ease.
        </p>
            <a href="app/views/auth/login.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition duration-300 ease-in-out">
                Get Started
            </a>
            
        </div>
    </section>
  </div>
</div>
    </div>
</body>
</html>