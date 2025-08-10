<?php include 'includes/header.php';?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Artisan Coffee House</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap');
        
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        
        /* Custom animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .nav-item {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-item::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: linear-gradient(90deg, #8B4513, #D2691E);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-item:hover::after {
            width: 100%;
        }
        
        .coffee-steam {
            position: relative;
        }
        
        .coffee-steam::before {
            content: '☁️';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            animation: float 2s ease-in-out infinite;
            opacity: 0.7;
        }

        .profile-dropdown {
            transition: all 0.3s ease;
        }

        .profile-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .image-overlay {
            background: linear-gradient(45deg, rgba(139, 69, 19, 0.8), rgba(210, 105, 30, 0.6));
        }

        .story-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
    </style>
</head>
<body class="font-inter bg-amber-50">

    <!-- Hero Section with Large Image -->
    <section class="relative h-screen">
        <div class="absolute inset-0">
            <svg class="w-full h-full object-cover" viewBox="0 0 1200 800" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="coffeeGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#8B4513;stop-opacity:1" />
                        <stop offset="50%" style="stop-color:#D2691E;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#CD853F;stop-opacity:1" />
                    </linearGradient>
                    <pattern id="coffeePattern" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                        <circle cx="50" cy="50" r="3" fill="#8B4513" opacity="0.1"/>
                    </pattern>
                </defs>
                <rect width="1200" height="800" fill="url(#coffeeGradient)"/>
                <rect width="1200" height="800" fill="url(#coffeePattern)"/>
                <!-- Coffee shop interior illustration -->
                <rect x="100" y="200" width="1000" height="400" rx="20" fill="#F5DEB3" opacity="0.9"/>
                <rect x="200" y="300" width="150" height="200" rx="10" fill="#8B4513"/>
                <rect x="400" y="320" width="120" height="160" rx="8" fill="#D2691E"/>
                <rect x="600" y="310" width="140" height="170" rx="8" fill="#CD853F"/>
                <circle cx="275" cy="280" r="15" fill="#FFFFFF"/>
                <circle cx="460" cy="300" r="12" fill="#FFFFFF"/>
                <circle cx="670" cy="290" r="14" fill="#FFFFFF"/>
                <!-- Coffee beans scattered -->
                <ellipse cx="300" cy="150" rx="8" ry="12" fill="#8B4513" transform="rotate(45 300 150)"/>
                <ellipse cx="500" cy="120" rx="6" ry="10" fill="#654321" transform="rotate(30 500 120)"/>
                <ellipse cx="700" cy="140" rx="7" ry="11" fill="#8B4513" transform="rotate(60 700 140)"/>
                <ellipse cx="900" cy="130" rx="8" ry="12" fill="#654321" transform="rotate(15 900 130)"/>
            </svg>
        </div>
        <div class="image-overlay absolute inset-0"></div>
        <div class="relative z-10 flex items-center justify-center h-full text-center text-white">
            <div class="animate-fadeInUp">
                <h1 class="font-playfair text-6xl md:text-8xl font-bold mb-6">Our Story</h1>
                <p class="text-xl md:text-2xl font-light max-w-2xl mx-auto">Where passion meets perfection in every cup</p>
            </div>
        </div>
    </section>

    <!-- Story Timeline with Images -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-16 items-center mb-20">
                <div class="order-2 md:order-1">
                    <svg class="w-full h-80 rounded-lg shadow-xl hover-lift" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                        <rect width="400" height="300" fill="#F5DEB3"/>
                        <rect x="50" y="50" width="300" height="200" rx="15" fill="#8B4513"/>
                        <rect x="70" y="70" width="260" height="160" rx="10" fill="#D2691E"/>
                        <text x="200" y="120" text-anchor="middle" fill="white" font-size="24" font-family="serif">1985</text>
                        <text x="200" y="150" text-anchor="middle" fill="white" font-size="16">First Café</text>
                        <circle cx="100" cy="200" r="20" fill="#654321"/>
                        <circle cx="150" cy="180" r="15" fill="#8B4513"/>
                        <circle cx="200" cy="190" r="18" fill="#654321"/>
                        <circle cx="250" cy="200" r="16" fill="#8B4513"/>
                        <circle cx="300" cy="185" r="14" fill="#654321"/>
                    </svg>
                </div>
                <div class="order-1 md:order-2 story-card p-8 rounded-lg">
                    <h2 class="font-playfair text-4xl font-bold text-amber-900 mb-4">The Beginning</h2>
                    <p class="text-gray-700 text-lg leading-relaxed">Started in a small corner shop with a dream to serve the perfect cup of coffee to our community.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-16 items-center mb-20">
                <div class="story-card p-8 rounded-lg">
                    <h2 class="font-playfair text-4xl font-bold text-amber-900 mb-4">Growing Roots</h2>
                    <p class="text-gray-700 text-lg leading-relaxed">Expanding our family of coffee lovers while maintaining our commitment to quality and community.</p>
                </div>
                <div>
                    <svg class="w-full h-80 rounded-lg shadow-xl hover-lift" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                        <rect width="400" height="300" fill="#DEB887"/>
                        <rect x="20" y="100" width="120" height="150" rx="10" fill="#8B4513"/>
                        <rect x="160" y="80" width="120" height="170" rx="10" fill="#D2691E"/>
                        <rect x="300" y="90" width="80" height="160" rx="8" fill="#CD853F"/>
                        <text x="200" y="50" text-anchor="middle" fill="#654321" font-size="20" font-family="serif">Multiple Locations</text>
                        <circle cx="80" cy="70" r="8" fill="#FFFFFF"/>
                        <circle cx="220" cy="50" r="10" fill="#FFFFFF"/>
                        <circle cx="340" cy="60" r="6" fill="#FFFFFF"/>
                        <!-- Coffee steam -->
                        <path d="M80 60 Q85 50 80 40 Q75 30 80 20" stroke="#FFFFFF" stroke-width="2" fill="none" opacity="0.7"/>
                        <path d="M220 40 Q225 30 220 20 Q215 10 220 0" stroke="#FFFFFF" stroke-width="2" fill="none" opacity="0.7"/>
                    </svg>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div class="order-2 md:order-1">
                    <svg class="w-full h-80 rounded-lg shadow-xl hover-lift" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                        <rect width="400" height="300" fill="#F4E4BC"/>
                        <circle cx="200" cy="150" r="100" fill="#8B4513"/>
                        <circle cx="200" cy="150" r="80" fill="#D2691E"/>
                        <circle cx="200" cy="150" r="60" fill="#CD853F"/>
                        <text x="200" y="155" text-anchor="middle" fill="white" font-size="18" font-family="serif">Sustainability</text>
                        <!-- Leaves around the circle -->
                        <ellipse cx="120" cy="100" rx="15" ry="8" fill="#228B22" transform="rotate(45 120 100)"/>
                        <ellipse cx="280" cy="100" rx="15" ry="8" fill="#32CD32" transform="rotate(-45 280 100)"/>
                        <ellipse cx="120" cy="200" rx="15" ry="8" fill="#228B22" transform="rotate(-45 120 200)"/>
                        <ellipse cx="280" cy="200" rx="15" ry="8" fill="#32CD32" transform="rotate(45 280 200)"/>
                        <text x="200" y="50" text-anchor="middle" fill="#654321" font-size="16" font-family="serif">Eco-Friendly Practices</text>
                    </svg>
                </div>
                <div class="order-1 md:order-2 story-card p-8 rounded-lg">
                    <h2 class="font-playfair text-4xl font-bold text-amber-900 mb-4">Our Mission</h2>
                    <p class="text-gray-700 text-lg leading-relaxed">Committed to sustainable practices and ethical sourcing while creating memorable experiences for every guest.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section with Large Visual Elements -->
    <section class="py-20 bg-gradient-to-br from-amber-100 to-orange-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="font-playfair text-5xl font-bold text-amber-900 mb-4">Our Values</h2>
                <p class="text-xl text-gray-700">What drives us every day</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Quality -->
                <div class="text-center hover-lift bg-white p-8 rounded-xl shadow-lg">
                    <div class="mb-6">
                        <svg class="w-32 h-32 mx-auto" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="90" fill="#8B4513"/>
                            <circle cx="100" cy="100" r="70" fill="#D2691E"/>
                            <circle cx="100" cy="100" r="50" fill="#CD853F"/>
                            <polygon points="100,60 110,90 140,90 118,108 128,138 100,120 72,138 82,108 60,90 90,90" fill="#FFD700"/>
                            <text x="100" y="170" text-anchor="middle" fill="#654321" font-size="14" font-family="serif">Premium</text>
                        </svg>
                    </div>
                    <h3 class="font-playfair text-2xl font-bold text-amber-900 mb-3">Quality</h3>
                    <p class="text-gray-600">Premium beans, expert roasting</p>
                </div>

                <!-- Community -->
                <div class="text-center hover-lift bg-white p-8 rounded-xl shadow-lg">
                    <div class="mb-6">
                        <svg class="w-32 h-32 mx-auto" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="90" fill="#228B22"/>
                            <circle cx="70" cy="80" r="25" fill="#32CD32"/>
                            <circle cx="130" cy="80" r="25" fill="#32CD32"/>
                            <circle cx="100" cy="130" r="25" fill="#32CD32"/>
                            <circle cx="100" cy="100" r="15" fill="#FFFFFF"/>
                            <text x="100" y="170" text-anchor="middle" fill="#654321" font-size="14" font-family="serif">Together</text>
                        </svg>
                    </div>
                    <h3 class="font-playfair text-2xl font-bold text-amber-900 mb-3">Community</h3>
                    <p class="text-gray-600">Building connections, one cup at a time</p>
                </div>

                <!-- Sustainability -->
                <div class="text-center hover-lift bg-white p-8 rounded-xl shadow-lg">
                    <div class="mb-6">
                        <svg class="w-32 h-32 mx-auto" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="90" fill="#4169E1"/>
                            <ellipse cx="100" cy="100" rx="60" ry="80" fill="#87CEEB"/>
                            <ellipse cx="80" cy="70" rx="20" ry="10" fill="#228B22" transform="rotate(45 80 70)"/>
                            <ellipse cx="120" cy="70" rx="20" ry="10" fill="#32CD32" transform="rotate(-45 120 70)"/>
                            <ellipse cx="100" cy="130" rx="25" ry="12" fill="#228B22"/>
                            <text x="100" y="170" text-anchor="middle" fill="#654321" font-size="14" font-family="serif">Earth First</text>
                        </svg>
                    </div>
                    <h3 class="font-playfair text-2xl font-bold text-amber-900 mb-3">Sustainability</h3>
                    <p class="text-gray-600">Caring for our planet's future</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section with Visual Focus -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="font-playfair text-5xl font-bold text-amber-900 mb-4">Meet Our Team</h2>
                <p class="text-xl text-gray-700">The passionate people behind your perfect cup</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Team Member 1 -->
                <div class="text-center hover-lift">
                    <div class="mb-6">
                        <svg class="w-48 h-48 mx-auto rounded-full shadow-lg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="100" fill="#F5DEB3"/>
                            <circle cx="100" cy="80" r="30" fill="#DEB887"/>
                            <ellipse cx="100" cy="140" rx="50" ry="60" fill="#8B4513"/>
                            <circle cx="85" cy="75" r="3" fill="#000"/>
                            <circle cx="115" cy="75" r="3" fill="#000"/>
                            <path d="M90 90 Q100 100 110 90" stroke="#000" stroke-width="2" fill="none"/>
                            <rect x="80" y="120" width="40" height="20" rx="5" fill="#FFFFFF"/>
                            <text x="100" y="185" text-anchor="middle" fill="#654321" font-size="12" font-family="serif">Head Barista</text>
                        </svg>
                    </div>
                    <h3 class="font-playfair text-2xl font-bold text-amber-900 mb-2">Nur Rahah</h3>
                    <p class="text-gray-600">Master of the perfect espresso</p>
                </div>

                <!-- Team Member 2 -->
                <div class="text-center hover-lift">
                    <div class="mb-6">
                        <svg class="w-48 h-48 mx-auto rounded-full shadow-lg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="100" fill="#F5DEB3"/>
                            <circle cx="100" cy="80" r="30" fill="#DEB887"/>
                            <ellipse cx="100" cy="140" rx="50" ry="60" fill="#2F4F4F"/>
                            <circle cx="85" cy="75" r="3" fill="#000"/>
                            <circle cx="115" cy="75" r="3" fill="#000"/>
                            <path d="M90 90 Q100 100 110 90" stroke="#000" stroke-width="2" fill="none"/>
                            <rect x="85" y="125" width="30" height="15" rx="3" fill="#8B4513"/>
                            <text x="100" y="185" text-anchor="middle" fill="#654321" font-size="12" font-family="serif">Founder</text>
                        </svg>
                    </div>
                    <h3 class="font-playfair text-2xl font-bold text-amber-900 mb-2">Nurul Asyikin</h3>
                    <p class="text-gray-600">Visionary behind our coffee journey</p>
                </div>

                <!-- Team Member 3 -->
                <div class="text-center hover-lift">
                    <div class="mb-6">
                        <svg class="w-48 h-48 mx-auto rounded-full shadow-lg" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="100" fill="#F5DEB3"/>
                            <circle cx="100" cy="80" r="30" fill="#DEB887"/>
                            <ellipse cx="100" cy="140" rx="50" ry="60" fill="#4B0082"/>
                            <circle cx="85" cy="75" r="3" fill="#000"/>
                            <circle cx="115" cy="75" r="3" fill="#000"/>
                            <path d="M90 90 Q100 100 110 90" stroke="#000" stroke-width="2" fill="none"/>
                            <rect x="75" y="120" width="50" height="25" rx="5" fill="#FFFFFF"/>
                            <text x="100" y="185" text-anchor="middle" fill="#654321" font-size="12" font-family="serif">Roast Master</text>
                        </svg>
                    </div>
                    <h3 class="font-playfair text-2xl font-bold text-amber-900 mb-2">Aina Damia</h3>
                    <p class="text-gray-600">Crafting the perfect roast profile</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action with Large Visual -->
    <section class="py-20 bg-gradient-to-r from-amber-800 to-orange-700 text-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <div class="mb-8">
                <svg class="w-32 h-32 mx-auto animate-float" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="90" fill="#FFFFFF" opacity="0.2"/>
                    <circle cx="100" cy="100" r="70" fill="#FFFFFF" opacity="0.3"/>
                    <circle cx="100" cy="100" r="50" fill="#FFFFFF" opacity="0.4"/>
                    <text x="100" y="110" text-anchor="middle" fill="#FFFFFF" font-size="60">☕</text>
                </svg>
            </div>
            <h2 class="font-playfair text-5xl font-bold mb-6">Visit Us Today</h2>
            <p class="text-xl mb-8 opacity-90">Experience the passion in every cup</p>
            <button class="bg-white text-amber-800 px-8 py-4 rounded-full font-semibold text-lg hover:bg-amber-50 transition-all duration-300 hover-lift">
                Find Our Locations
            </button>
        </div>
    </section>


    <script>
        // Add scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadeInUp');
                }
            });
        }, observerOptions);

        // Observe all story cards and value cards
        document.querySelectorAll('.story-card, .hover-lift').forEach(el => {
            observer.observe(el);
        });

        // Add smooth scrolling for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'96cc771984724485',t:'MTc1NDc5NzE1Ny4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>


<?php include 'includes/footer.php'; ?>