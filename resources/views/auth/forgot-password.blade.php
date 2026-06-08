<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Kata Sandi - ALinea</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Base deep blue radial background */
        .bg-p3r-base {
            background: radial-gradient(circle at center, #153966 0%, #07182e 100%);
        }
        
        /* Wavy Animation CSS */
        .parallax > use {
            animation: move-forever 25s cubic-bezier(.55,.5,.45,.5) infinite;
        }
        .parallax > use:nth-child(1) { animation-delay: -2s; animation-duration: 7s; }
        .parallax > use:nth-child(2) { animation-delay: -3s; animation-duration: 10s; }
        .parallax > use:nth-child(3) { animation-delay: -4s; animation-duration: 13s; }
        .parallax > use:nth-child(4) { animation-delay: -5s; animation-duration: 20s; }
        
        @keyframes move-forever {
            0% { transform: translate3d(-90px,0,0); }
            100% { transform: translate3d(85px,0,0); }
        }

        /* Force transparent background even when browser autofills */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            transition: background-color 5000s ease-in-out 0s;
            -webkit-text-fill-color: white !important;
        }
    </style>
</head>

<body class="relative h-screen flex items-center justify-center bg-p3r-base font-sans overflow-hidden">

    <svg class="absolute bottom-0 left-0 w-full h-[65vh] z-0 pointer-events-none opacity-80" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
        <defs>
            <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
        </defs>
        <g class="parallax">
            <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(106, 165, 227, 0.15)" /> 
            <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(62, 120, 179, 0.3)" /> 
            <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(21, 57, 102, 0.5)" /> 
            <use xlink:href="#gentle-wave" x="48" y="7" fill="rgba(10, 53, 110, 0.8)" /> 
        </g>
    </svg>

    <div class="relative z-10 w-full max-w-sm p-8 text-white flex flex-col items-center">
        
        <div class="w-20 h-20 border-2 border-white rounded-full flex items-center justify-center mb-4 bg-[#153966]/30 backdrop-blur-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>

        <h2 class="text-2xl font-light tracking-widest mb-10 uppercase text-shadow-sm">masukkan surel!</h2>

        <form method="POST" action="/forgot-password" class="w-full" novalidate>
            @csrf

            @if (session('error'))
                <div class="w-full bg-red-500/20 border border-red-500/50 text-red-100 px-4 py-3 rounded-xl mb-6 text-sm text-center backdrop-blur-sm shadow-md">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('status'))
                <div class="w-full bg-green-500/20 border border-green-500/50 text-green-100 px-4 py-3 rounded-xl mb-6 text-sm text-center backdrop-blur-sm shadow-md">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="w-full bg-red-500/20 border border-red-500/50 text-red-100 px-4 py-3 rounded-xl mb-6 text-sm backdrop-blur-sm shadow-md">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-6 w-full">
                <div class="relative flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-1 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <input type="email" name="email" placeholder="Surel" value="{{ old('email') }}" required
                        style="background-color: transparent !important;"
                        class="w-full border-0 border-b border-white text-white pl-10 py-2 focus:outline-none focus:ring-0 placeholder-white/80 appearance-none drop-shadow-md">
                </div>
                @error('email')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                class="w-full bg-[#0a356e] text-white py-3 text-sm font-semibold tracking-widest uppercase hover:bg-[#15468f] transition shadow-lg mt-4 backdrop-blur-md">
                Kirim
            </button>
        </form>

    </div>

</body>
</html>