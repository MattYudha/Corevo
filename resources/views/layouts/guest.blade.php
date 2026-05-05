<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HRIS Aratech') }}</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('img/HRIS ARATECH logo tr.png') }}" type="image/png">
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#0d9488', // Teal 600
                            hover: '#0f766e',   // Teal 700
                            focus: '#14b8a6',   // Teal 500
                        },
                        surface: {
                            // Dark mode colors (Zinc)
                            base: '#09090b',    // Zinc 950
                            card: '#18181b',    // Zinc 900
                            input: '#27272a',   // Zinc 800
                            border: '#27272a',  // Zinc 800
                            
                            // Light mode colors (Zinc/Gray)
                            lightBase: '#f4f4f5', // Zinc 100
                            lightCard: '#ffffff', // White
                            lightInput: '#ffffff', // White
                            lightBorder: '#e4e4e7', // Zinc 200
                        }
                    }
                }
            }
        }
    </script>
    <script>
        // Check and apply theme immediately to avoid flash
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style>
        /* Custom Checkbox Style for Webkit */
        input[type="checkbox"]:checked {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
            border-color: transparent;
            background-color: transparent;
        }
        /* Remove default eye icon in Edge/IE */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }
    </style>
</head>
<body class="bg-surface-lightBase dark:bg-surface-base text-zinc-800 dark:text-zinc-300 font-sans antialiased min-h-screen selection:bg-primary/30 selection:text-primary-focus transition-colors duration-300">
    <!-- Theme Toggle -->
    <div class="fixed top-6 right-6 z-50">
        <button onclick="toggleTheme()" class="p-2.5 rounded-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 shadow-sm hover:shadow-md transition-all duration-200 group focus:outline-none focus:ring-2 focus:ring-primary">
            <!-- Sun Icon (shows in dark mode) -->
            <svg id="theme-toggle-sun" class="w-5 h-5 text-zinc-400 group-hover:text-yellow-400 hidden dark:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <!-- Moon Icon (shows in light mode) -->
            <svg id="theme-toggle-moon" class="w-5 h-5 text-zinc-500 group-hover:text-indigo-500 dark:hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>

    <main class="grid grid-cols-1 lg:grid-cols-2 min-h-screen w-full">
        <!-- Left Pane: Visual & Branding -->
        <section class="hidden lg:flex flex-col justify-center items-center p-12 relative overflow-hidden bg-gradient-to-br from-teal-50/50 via-zinc-50 to-zinc-100 dark:bg-none dark:bg-surface-base border-r border-zinc-200 dark:border-surface-border shadow-[4px_0_24px_rgba(0,0,0,0.03)] dark:shadow-none z-10 transition-colors duration-300">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0 bg-cover bg-center opacity-[0.15] dark:opacity-20 mix-blend-multiply dark:mix-blend-luminosity grayscale transition-all duration-300"
                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCn53oAyTRLoXjKYUztExGrh8Z-VTNhqlLezyYXRWNlo72KV2kL94og9ACD0Uo-X19fF6NDSlwIZ5etZPkSS849rej6js2pKIUOnU9zrGajyKp9rwBJh8lFWNHJtSUuhpag_S_28RINY1f-JPa4KvhwkOEOtPII47xahNbTghccQCkpWqm13p1W2KK6tP4zIy5FDVso43IWtkudr4l1KhBrzlDFM6siXX5zrWlS1GNJTkIronLAH2EtomypnGOEoL9N5C1FJiL49N0');">
            </div>
            <!-- Subtle Radial Gradient Overlay to ensure text readability -->
            <div class="absolute inset-0 z-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white/20 via-white/60 to-white/90 dark:from-surface-base/40 dark:via-surface-base/80 dark:to-surface-base pointer-events-none transition-colors duration-300"></div>
            
            <!-- Central Content Container -->
            <div class="z-10 relative flex flex-col items-center text-center max-w-xl w-full -mt-12 md:-mt-24">
                
                <!-- Logo with Highlight Glow for Dark Mode -->
                <div class="relative mb-10 group">
                    <!-- Glow effect (subtle in light mode, pronounced in dark mode to highlight black text) -->
                    <div class="absolute inset-0 bg-white/40 dark:bg-white/10 blur-2xl rounded-full scale-[1.8] z-0 transition-opacity duration-300"></div>
                    <div class="absolute inset-0 bg-primary/10 dark:bg-primary/20 blur-3xl rounded-full scale-[2] z-0 transition-opacity duration-300"></div>
                    <!-- Logo Image -->
                    <img src="{{ asset('corevo-logo.png') }}" alt="Aratech Logo" class="dark:hidden relative z-10 h-24 md:h-26 w-auto object-contain transition-transform duration-500 group-hover:scale-105">
                    <img src="{{ asset('corevo-logo-white.png') }}" alt="Aratech Logo" class="hidden dark:block relative z-10 h-24 md:h-26 w-auto object-contain transition-transform duration-500 group-hover:scale-105">
                </div>

                <!-- Headline -->
                <h1 class="flex flex-col gap-5 text-4xl md:text-5xl lg:text-[3.5rem] font-bold text-zinc-900 dark:text-white mb-6 leading-[1.15] tracking-tight transition-colors duration-300">
    
                    <span>
                        THE CORE OF
                    </span>
                    
                    <span class="text-primary dark:text-primary-focus transition-colors duration-300">
                        YOUR OPERATIONS
                    </span>

                </h1>
                
                <!-- Description -->
                <p class="text-lg md:text-xl text-zinc-600 dark:text-zinc-400 max-w-lg leading-relaxed transition-colors duration-300 mx-auto">
                    Everything you need to run your operations in one place
                </p>
            </div>
            
            <!-- Bottom Content: Footer -->
            <div class="absolute bottom-12 left-0 right-0 z-10 flex justify-center">
                <div class="flex items-center gap-3 opacity-60 hover:opacity-100 transition-opacity duration-300">
                    <div class="h-px w-8 bg-zinc-400 dark:bg-zinc-600"></div>
                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 tracking-[0.2em] uppercase transition-colors duration-300">
                        PT Aratech Nusantara Indonesia
                    </p>
                    <div class="h-px w-8 bg-zinc-400 dark:bg-zinc-600"></div>
                </div>
            </div>
        </section>

        <!-- Right Pane: Form Area -->
        <section class="flex flex-col items-center justify-center min-h-screen px-4 sm:px-6 lg:px-12 py-8 sm:py-12 relative bg-white dark:bg-surface-base transition-colors duration-300 z-0">
    
            <div class="w-full max-w-md sm:max-w-lg lg:max-w-md relative z-10">
                
                <!-- Branding Header -->
                <div class="mb-8 sm:mb-10 text-center flex flex-col items-center">
                    <img 
                        src="{{ asset('corevo-logo.png') }}" 
                        class="dark:hidden h-14 sm:h-16 md:h-20 w-auto object-contain mb-6 sm:mb-8 transition-transform hover:scale-105" 
                        alt="HRIS Aratech Logo"
                    >
                    <img 
                        src="{{ asset('corevo-logo-white.png') }}" 
                        class="hidden dark:block h-14 sm:h-16 md:h-20 w-auto object-contain mb-6 sm:mb-8 transition-transform hover:scale-105" 
                        alt="HRIS Aratech Logo"
                    >
                </div>

                <!-- Card -->
                <div class="bg-surface-lightCard dark:bg-surface-card border border-surface-lightBorder dark:border-surface-border rounded-xl p-5 sm:p-6 md:p-8 shadow-lg sm:shadow-xl shadow-zinc-200/50 dark:shadow-black/50 relative overflow-hidden transition-colors duration-300">
                    
                    <!-- Subtle top inner border -->
                    <div class="absolute top-0 left-0 right-0 h-[1px] bg-white/5 hidden dark:block"></div>
                    
                    {{ $slot }}
                </div>
                
                <!-- Footer -->
                <div class="mt-8 sm:mt-10 lg:hidden text-center">
                    <p class="text-[10px] sm:text-xs font-semibold text-zinc-500 tracking-[0.2em] uppercase">
                        PT Aratech Nusantara Indonesia
                    </p>
                </div>
            </div>
        </section>
    </main>

    <script>
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>
</body>
</html>