<?php include 'includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe & Netic - Premium Coffee Experience</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .font-playfair { font-family: 'Playfair Display', serif; }
        body { font-family: 'Inter', sans-serif; }
        
        /* Enhanced Background Images */
        .hero-bg {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 146, 60, 0.1) 100%),
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="%23f59e0b" opacity="0.1"/><circle cx="80" cy="40" r="1.5" fill="%23fb923c" opacity="0.1"/><circle cx="40" cy="70" r="2.5" fill="%23f59e0b" opacity="0.1"/><circle cx="70" cy="80" r="1" fill="%23fb923c" opacity="0.1"/><circle cx="10" cy="60" r="1.8" fill="%23f59e0b" opacity="0.1"/><circle cx="90" cy="20" r="2.2" fill="%23fb923c" opacity="0.1"/></svg>');
            background-size: 200px 200px, 100px 100px;
            background-repeat: repeat;
            position: relative;
        }
        
        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(245, 158, 11, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(251, 146, 60, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(245, 158, 11, 0.03) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .features-bg {
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.8) 0%, rgba(245, 158, 11, 0.05) 100%),
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60"><path d="M30 10c-2 0-4 1-4 3s2 3 4 3 4-1 4-3-2-3-4-3zm0 8c-3 0-6 2-6 5s3 5 6 5 6-2 6-5-3-5-6-5z" fill="%23f59e0b" opacity="0.03"/><circle cx="15" cy="45" r="3" fill="%23fb923c" opacity="0.02"/><circle cx="45" cy="15" r="2" fill="%23f59e0b" opacity="0.03"/></svg>');
            background-size: 150px 150px;
        }
        
        /* Enhanced Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(-3deg); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 1; }
        }
        
        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(3deg); }
            75% { transform: rotate(-3deg); }
        }
        
        .animate-fadeInUp { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-float-slow { animation: floatSlow 4s ease-in-out infinite; }
        .animate-pulse-custom { animation: pulse 2s ease-in-out infinite; }
        .animate-wiggle { animation: wiggle 0.5s ease-in-out; }
        
        /* Enhanced Hover Effects */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .hover-glow:hover {
            box-shadow: 0 0 30px rgba(245, 158, 11, 0.3);
            transform: translateY(-5px);
        }
        
        .hover-bounce:hover {
            animation: wiggle 0.5s ease-in-out;
        }
        
        .feature-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(245, 158, 11, 0.1), transparent);
            transition: left 0.5s;
        }
        
        .feature-card:hover::before {
            left: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 25px 50px rgba(245, 158, 11, 0.15);
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.2) rotate(10deg);
        }
        
        .feature-icon {
            transition: all 0.3s ease;
        }
        
        /* Image Gallery Styles */
        .restaurant-image-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .restaurant-image-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .menu-item-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .menu-item-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 25px 50px rgba(245, 158, 11, 0.15);
        }
        
        /* Button Enhancements */
        .btn-primary {
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        /* Floating Elements */
        .floating-element {
            position: absolute;
            pointer-events: none;
            user-select: none;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50 to-orange-50">
    <!-- Hero Section -->
    <section class="hero-bg relative py-20 px-4 text-center min-h-screen flex items-center">
        <div class="max-w-4xl mx-auto relative z-10">
            <h1 class="font-playfair text-5xl md:text-7xl font-bold text-amber-900 mb-6 animate-fadeInUp">
                Welcome to<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-600 to-orange-600">Cafe & Netic</span>
            </h1>
            <p class="text-xl text-gray-700 mb-8 animate-fadeInUp max-w-2xl mx-auto" style="animation-delay: 0.2s;">
                Where every cup tells a story and every moment becomes a memory. Experience the perfect blend of artisanal coffee and cozy atmosphere.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fadeInUp" style="animation-delay: 0.4s;">
                <button class="btn-primary bg-gradient-to-r from-amber-600 to-orange-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:from-amber-700 hover:to-orange-700 transform hover:scale-105 transition-all duration-300 shadow-xl hover-glow">
                    Explore Menu
                </button>
                <button class="border-2 border-amber-600 text-amber-700 px-8 py-4 rounded-full text-lg font-semibold hover:bg-amber-600 hover:text-white transition-all duration-300 hover-lift">
                    Book a Table
                </button>
            </div>
        </div>
        
        <!-- Enhanced Floating Elements -->
        <div class="floating-element top-20 left-10 text-4xl animate-float hover-bounce" style="animation-delay: 0s;">🫘</div>
        <div class="floating-element top-40 right-20 text-3xl animate-float-slow hover-bounce" style="animation-delay: 1s;">☕</div>
        <div class="floating-element bottom-20 left-20 text-2xl animate-float hover-bounce" style="animation-delay: 2s;">🥐</div>
        <div class="floating-element top-60 left-1/4 text-3xl animate-pulse-custom hover-bounce" style="animation-delay: 0.5s;">🍰</div>
        <div class="floating-element top-32 right-1/3 text-2xl animate-float-slow hover-bounce" style="animation-delay: 1.5s;">🧁</div>
        <div class="floating-element bottom-40 right-10 text-4xl animate-float hover-bounce" style="animation-delay: 2.5s;">🍪</div>
        <div class="floating-element top-80 left-1/2 text-2xl animate-pulse-custom hover-bounce" style="animation-delay: 3s;">🥯</div>
        <div class="floating-element bottom-60 left-1/3 text-3xl animate-float-slow hover-bounce" style="animation-delay: 1.8s;">🫖</div>
    </section>

    <!-- Restaurant Gallery Section -->
    <section class="py-16 px-4 bg-white">
        <div class="max-w-6xl mx-auto">
            <h2 class="font-playfair text-4xl font-bold text-center text-amber-900 mb-12 animate-fadeInUp">Our Restaurant</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
                <!-- Restaurant Interior -->
                <div class="restaurant-image-card group relative overflow-hidden rounded-2xl shadow-lg hover-lift">
                    <img src="assets/img/banner/cafe_interior.jpeg" alt="Restaurant Interior" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute bottom-4 left-4 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <h3 class="font-playfair text-xl font-semibold">Cozy Interior</h3>
                    </div>
                </div>
                
                <!-- Outdoor Seating -->
                <div class="restaurant-image-card group relative overflow-hidden rounded-2xl shadow-lg hover-lift">
                    <img src="assets/img/banner/outdoor_seating.jpeg" alt="Outdoor Seating" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute bottom-4 left-4 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <h3 class="font-playfair text-xl font-semibold">Outdoor Terrace</h3>
                    </div>
                </div>
                
                <!-- Coffee Bar -->
                <div class="restaurant-image-card group relative overflow-hidden rounded-2xl shadow-lg hover-lift">
                    <img src="assets/img/banner/cofee_bar.jpeg" alt="Coffee Bar" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute bottom-4 left-4 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <h3 class="font-playfair text-xl font-semibold">Coffee Bar</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Highlights Section -->
    <section class="features-bg py-16 px-4">
        <div class="max-w-6xl mx-auto">
            <h2 class="font-playfair text-4xl font-bold text-center text-amber-900 mb-12 animate-fadeInUp">Our Specialties</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                <!-- Signature Coffee -->
                <div class="menu-item-card group bg-white rounded-2xl shadow-lg overflow-hidden hover-lift">
                    <div class="relative overflow-hidden">
                        <img src="assets/img/product/removebg/latte.png" alt="Signature Coffee" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute top-4 right-4 bg-amber-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Popular
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-playfair text-xl font-semibold text-amber-800 mb-2">Signature Latte</h3>
                        <p class="text-gray-600 text-sm mb-3">Our house blend with steamed milk and artistic foam</p>
                        <div class="text-amber-600 font-bold text-lg">$4.50</div>
                    </div>
                </div>
                
                <!-- Pastries -->
                <div class="menu-item-card group bg-white rounded-2xl shadow-lg overflow-hidden hover-lift">
                    <div class="relative overflow-hidden">
                        <img src="assets/img/product/removebg/croissant.png" alt="Fresh Pastries" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-6">
                        <h3 class="font-playfair text-xl font-semibold text-amber-800 mb-2">Fresh Pastries</h3>
                        <p class="text-gray-600 text-sm mb-3">Daily baked croissants, muffins, and artisan breads</p>
                        <div class="text-amber-600 font-bold text-lg">From $2.50</div>
                    </div>
                </div>
                
                <!-- Breakfast -->
                <div class="menu-item-card group bg-white rounded-2xl shadow-lg overflow-hidden hover-lift">
                    <div class="relative overflow-hidden">
                        <img src="assets/img/product/removebg/breakfast_sandwich.png" alt="Breakfast Plate" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-6">
                        <h3 class="font-playfair text-xl font-semibold text-amber-800 mb-2">Breakfast Special</h3>
                        <p class="text-gray-600 text-sm mb-3">Eggs, toast, and fresh fruit with coffee</p>
                        <div class="text-amber-600 font-bold text-lg">$8.95</div>
                    </div>
                </div>
                
                <!-- Desserts -->
                <div class="menu-item-card group bg-white rounded-2xl shadow-lg overflow-hidden hover-lift">
                    <div class="relative overflow-hidden">
                        <img src="assets/img/product/removebg/blueberry_muffin.png" alt="Desserts" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-6">
                        <h3 class="font-playfair text-xl font-semibold text-amber-800 mb-2">Sweet Treats</h3>
                        <p class="text-gray-600 text-sm mb-3">Cakes, cookies, and seasonal desserts</p>
                        <div class="text-amber-600 font-bold text-lg">From $3.75</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 px-4 bg-white/50">
        <div class="max-w-6xl mx-auto">
            <h2 class="font-playfair text-4xl font-bold text-center text-amber-900 mb-12">Why Choose Cafe & Netic?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card text-center p-8 bg-white rounded-2xl shadow-lg">
                    <div class="feature-icon mb-6 animate-float flex justify-center">
                        <svg width="80" height="80" viewBox="0 0 100 100" class="drop-shadow-lg">
                            <defs>
                                <linearGradient id="coffeeGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#f59e0b"/>
                                    <stop offset="100%" style="stop-color:#fb923c"/>
                                </linearGradient>
                            </defs>
                            <!-- Coffee Cup -->
                            <path d="M20 35 L20 75 Q20 85 30 85 L60 85 Q70 85 70 75 L70 35 Z" fill="url(#coffeeGradient)"/>
                            <!-- Coffee Steam -->
                            <path d="M30 25 Q32 20 30 15 Q28 20 30 25" stroke="#f59e0b" stroke-width="2" fill="none" opacity="0.7"/>
                            <path d="M40 25 Q42 20 40 15 Q38 20 40 25" stroke="#fb923c" stroke-width="2" fill="none" opacity="0.7"/>
                            <path d="M50 25 Q52 20 50 15 Q48 20 50 25" stroke="#f59e0b" stroke-width="2" fill="none" opacity="0.7"/>
                            <!-- Cup Handle -->
                            <path d="M70 45 Q80 45 80 55 Q80 65 70 65" stroke="url(#coffeeGradient)" stroke-width="4" fill="none"/>
                            <!-- Coffee Surface -->
                            <ellipse cx="45" cy="35" rx="25" ry="3" fill="#f8fafc" opacity="0.8"/>
                        </svg>
                    </div>
                    <h3 class="font-playfair text-2xl font-semibold text-amber-800 mb-4">Premium Coffee</h3>
                    <p class="text-gray-600">Sourced from the finest coffee farms around the world, roasted to perfection daily.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card text-center p-8 bg-white rounded-2xl shadow-lg" style="animation-delay: 0.2s;">
                    <div class="feature-icon mb-6 animate-float flex justify-center" style="animation-delay: 1s;">
                        <svg width="80" height="80" viewBox="0 0 100 100" class="drop-shadow-lg">
                            <defs>
                                <linearGradient id="houseGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#f59e0b"/>
                                    <stop offset="100%" style="stop-color:#fb923c"/>
                                </linearGradient>
                            </defs>
                            <!-- House Base -->
                            <rect x="25" y="45" width="50" height="40" fill="url(#houseGradient)" rx="3"/>
                            <!-- Roof -->
                            <path d="M15 45 L50 15 L85 45 Z" fill="#d97706"/>
                            <!-- Door -->
                            <rect x="42" y="60" width="16" height="25" fill="#f8fafc" rx="2"/>
                            <!-- Door Handle -->
                            <circle cx="54" cy="72" r="1.5" fill="#f59e0b"/>
                            <!-- Windows -->
                            <rect x="30" y="52" width="8" height="8" fill="#f8fafc" rx="1"/>
                            <rect x="62" y="52" width="8" height="8" fill="#f8fafc" rx="1"/>
                            <!-- Window Cross -->
                            <line x1="34" y1="52" x2="34" y2="60" stroke="#f59e0b" stroke-width="1"/>
                            <line x1="30" y1="56" x2="38" y2="56" stroke="#f59e0b" stroke-width="1"/>
                            <line x1="66" y1="52" x2="66" y2="60" stroke="#fb923c" stroke-width="1"/>
                            <line x1="62" y1="56" x2="70" y2="56" stroke="#fb923c" stroke-width="1"/>
                            <!-- Chimney -->
                            <rect x="65" y="25" width="8" height="15" fill="#d97706"/>
                        </svg>
                    </div>
                    <h3 class="font-playfair text-2xl font-semibold text-amber-800 mb-4">Cozy Atmosphere</h3>
                    <p class="text-gray-600">A warm, inviting space perfect for work, study, or catching up with friends.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card text-center p-8 bg-white rounded-2xl shadow-lg" style="animation-delay: 0.4s;">
                    <div class="feature-icon mb-6 animate-float flex justify-center" style="animation-delay: 2s;">
                        <svg width="80" height="80" viewBox="0 0 100 100" class="drop-shadow-lg">
                            <defs>
                                <linearGradient id="chefGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#f59e0b"/>
                                    <stop offset="100%" style="stop-color:#fb923c"/>
                                </linearGradient>
                            </defs>
                            <!-- Chef Hat -->
                            <ellipse cx="50" cy="25" rx="20" ry="15" fill="#f8fafc"/>
                            <rect x="30" y="35" width="40" height="8" fill="#f8fafc"/>
                            <!-- Face -->
                            <circle cx="50" cy="50" r="15" fill="#fef3c7"/>
                            <!-- Eyes -->
                            <circle cx="45" cy="47" r="2" fill="#374151"/>
                            <circle cx="55" cy="47" r="2" fill="#374151"/>
                            <!-- Smile -->
                            <path d="M42 55 Q50 62 58 55" stroke="#374151" stroke-width="2" fill="none"/>
                            <!-- Body -->
                            <rect x="35" y="65" width="30" height="25" fill="url(#chefGradient)" rx="3"/>
                            <!-- Apron -->
                            <rect x="40" y="70" width="20" height="20" fill="#f8fafc" rx="2"/>
                            <!-- Apron Strings -->
                            <line x1="40" y1="70" x2="35" y2="65" stroke="#f59e0b" stroke-width="2"/>
                            <line x1="60" y1="70" x2="65" y2="65" stroke="#fb923c" stroke-width="2"/>
                            <!-- Arms -->
                            <circle cx="25" cy="75" r="8" fill="#fef3c7"/>
                            <circle cx="75" cy="75" r="8" fill="#fef3c7"/>
                            <!-- Utensils -->
                            <line x1="20" y1="70" x2="20" y2="85" stroke="#6b7280" stroke-width="2"/>
                            <circle cx="20" cy="68" r="2" fill="#f59e0b"/>
                            <line x1="80" y1="70" x2="80" y2="85" stroke="#6b7280" stroke-width="2"/>
                            <rect x="78" y="68" width="4" height="3" fill="#fb923c"/>
                        </svg>
                    </div>
                    <h3 class="font-playfair text-2xl font-semibold text-amber-800 mb-4">Expert Baristas</h3>
                    <p class="text-gray-600">Our skilled baristas craft each drink with passion and precision.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 px-4 bg-gradient-to-r from-amber-600 to-orange-600 text-white text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="floating-element top-10 left-10 text-6xl animate-float-slow">☕</div>
            <div class="floating-element top-20 right-20 text-4xl animate-float">🫘</div>
            <div class="floating-element bottom-10 left-20 text-5xl animate-pulse-custom">🥐</div>
            <div class="floating-element bottom-20 right-10 text-4xl animate-float-slow">🍰</div>
        </div>
        <div class="max-w-4xl mx-auto relative z-10">
            <h2 class="font-playfair text-4xl font-bold mb-6 animate-fadeInUp">Ready to Experience the Magic?</h2>
            <p class="text-xl mb-8 animate-fadeInUp" style="animation-delay: 0.2s;">Join us today and discover why Cafe & Netic is more than just a coffee shop.</p>
            <button class="btn-primary bg-white text-amber-600 px-8 py-4 rounded-full text-lg font-semibold hover:bg-amber-50 transform hover:scale-105 transition-all duration-300 shadow-xl hover-lift animate-fadeInUp" style="animation-delay: 0.4s;">
                Visit Us Now
            </button>
        </div>
    </section>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'96cd9d9b42e02739',t:'MTc1NDgwOTIyMC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>




<?php include 'includes/footer.php'; ?>