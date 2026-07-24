<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nagarik+ - Citizen Services Platform</title>
    <link rel="icon" href="/icon.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8faf8 0%, #eef4ee 100%);
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #1f4e3f 0%, #163830 100%);
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2d6a54 0%, #1f4e3f 100%);
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #377a60 0%, #255a48 100%);
            box-shadow: 0 10px 20px rgba(45, 106, 84, 0.25);
        }
        
        .btn-secondary {
            transition: all 0.2s ease;
        }
        
        .btn-secondary:hover {
            background: rgba(45, 106, 84, 0.08);
        }
    </style>
</head>
<body class="min-h-screen antialiased">
    <!-- Navigation -->
    <nav class="bg-white/95 backdrop-blur-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <img src="/icon.png" alt="Nagarik+" class="w-10 h-10 rounded-xl object-cover shadow-sm">
                    <span class="text-xl font-bold text-gray-900 tracking-tight">Nagarik+</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('user.login') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors px-3 py-2 rounded-lg">Login</a>
                    <a href="{{ route('user.register') }}" class="btn-primary text-white px-5 py-2.5 rounded-xl font-semibold shadow-md">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-24 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-full mb-6">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-emerald-700 text-sm font-medium">Trusted by 10,000+ citizens</span>
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-gray-900 mb-6 leading-tight tracking-tight">
                        Simplify Your <span class="text-emerald-700">Citizen Services</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-10 leading-relaxed max-w-lg">
                        Store your documents securely, get reminders for renewals, access government guides, and stay informed with official news - all in one intuitive platform.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('user.register') }}" class="btn-primary text-white px-8 py-4 rounded-xl font-semibold text-lg shadow-lg">
                            Get Started Free
                        </a>
                        <a href="#features" class="btn-secondary border border-emerald-200 text-emerald-800 hover:text-emerald-900 px-8 py-4 rounded-xl font-semibold text-lg bg-white">
                            Explore Features
                        </a>
                    </div>
                    <div class="mt-10 flex items-center gap-4 text-sm text-gray-500">
                        <div class="flex -space-x-2">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 border-2 border-white flex items-center justify-center text-emerald-700 font-semibold text-xs">A</div>
                            <div class="w-10 h-10 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center text-blue-700 font-semibold text-xs">S</div>
                            <div class="w-10 h-10 rounded-full bg-purple-100 border-2 border-white flex items-center justify-center text-purple-700 font-semibold text-xs">K</div>
                            <div class="w-10 h-10 rounded-full bg-amber-100 border-2 border-white flex items-center justify-center text-amber-700 font-semibold text-xs">R</div>
                        </div>
                        <span>Join thousands of satisfied users</span>
                    </div>
                </div>
                <div class="hidden lg:block">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-emerald-100 rounded-3xl blur-2xl opacity-50"></div>
                        <div class="relative bg-white rounded-3xl p-8 shadow-2xl border border-gray-100">
                            <div class="grid grid-cols-2 gap-5">
                                <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl p-6 text-center card-hover cursor-default">
                                    <p class="text-4xl font-bold text-emerald-700 mb-2">12.5K</p>
                                    <p class="text-gray-600 font-medium">Active Users</p>
                                </div>
                                <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-2xl p-6 text-center card-hover cursor-default">
                                    <p class="text-4xl font-bold text-teal-700 mb-2">68K</p>
                                    <p class="text-gray-600 font-medium">Documents Stored</p>
                                </div>
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 text-center card-hover cursor-default">
                                    <p class="text-4xl font-bold text-blue-700 mb-2">250+</p>
                                    <p class="text-gray-600 font-medium">Services</p>
                                </div>
                                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 text-center card-hover cursor-default">
                                    <div class="flex items-center justify-center gap-1 mb-2">
                                        <span class="text-4xl font-bold text-amber-700">4.9</span>
                                        <span class="text-2xl text-amber-500">★</span>
                                    </div>
                                    <p class="text-gray-600 font-medium">User Rating</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 bg-emerald-50 text-emerald-700 rounded-full text-sm font-medium mb-4">Features</span>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 mb-6 tracking-tight">Everything You Need in One Place</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Nagarik+ provides a comprehensive suite of tools to manage your citizen services with ease and efficiency.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-3xl p-8 border border-gray-100 card-hover">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-green-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Secure Document Locker</h3>
                    <p class="text-gray-600 leading-relaxed">Store all your important documents securely with enterprise-grade encryption and access control.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-3xl p-8 border border-gray-100 card-hover">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Smart Reminders</h3>
                    <p class="text-gray-600 leading-relaxed">Never miss a renewal deadline with automatic reminder notifications via email and SMS.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-3xl p-8 border border-gray-100 card-hover">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Service Guides</h3>
                    <p class="text-gray-600 leading-relaxed">Step-by-step guides for government services and applications with form templates.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-3xl p-8 border border-gray-100 card-hover">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Official News</h3>
                    <p class="text-gray-600 leading-relaxed">Stay updated with verified news and announcements from government sources.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 hero-gradient">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-block px-4 py-1.5 bg-white/10 text-emerald-100 rounded-full text-sm font-medium mb-6">
                Get Started Today
            </div>
            <h2 class="text-4xl lg:text-5xl font-extrabold text-white mb-6 tracking-tight">Ready to Simplify Your Life?</h2>
            <p class="text-xl text-emerald-100 mb-10 max-w-2xl mx-auto leading-relaxed">
                Join thousands of citizens who are already using Nagarik+ to streamline their government services and document management.
            </p>
            <a href="{{ route('user.register') }}" class="inline-block bg-white text-emerald-800 hover:bg-gray-100 px-10 py-4 rounded-xl font-bold text-lg shadow-xl transition-all transform hover:scale-105">
                Create Your Free Account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="/icon.png" alt="Nagarik+" class="w-10 h-10 rounded-xl object-cover">
                        <span class="text-2xl font-bold text-white tracking-tight">Nagarik+</span>
                    </div>
                    <p class="text-gray-400 max-w-md leading-relaxed mb-6">
                        A modern, secure platform for managing citizen services, documents, and government-related information.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-6 text-lg">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-6 text-lg">Legal</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500">&copy; {{ date('Y') }} Nagarik+. All rights reserved.</p>
                <div class="flex items-center gap-6 text-sm text-gray-500">
                    <span>Made with ❤️ in Nepal</span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
