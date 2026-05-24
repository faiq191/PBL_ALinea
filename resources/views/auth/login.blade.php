<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - ALinea</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-custom-blue {
            background: linear-gradient(135deg, #6aa5e3 0%, #3e78b3 50%, #153966 100%);
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

<body class="relative h-screen flex items-center justify-center bg-custom-blue font-sans">

    <div class="w-full max-w-sm p-8 text-white flex flex-col items-center">
        
        <div class="w-20 h-20 border-2 border-white rounded-full flex items-center justify-center mb-4 bg-[#153966]/30">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>

        <h2 class="text-2xl font-light tracking-widest mb-10 uppercase">Selamat datang!</h2>

        <form method="POST" action="/login" class="w-full">
            @csrf

            <div class="relative flex items-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-1 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <input type="email" name="email" placeholder="Email ID"
                    style="background-color: transparent !important;"
                    class="w-full border-0 border-b border-white text-white pl-10 py-2 focus:outline-none focus:ring-0 placeholder-white/80 appearance-none">
            </div>

            <div class="relative flex items-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-1 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <input type="password" name="password" placeholder="Password"
                    style="background-color: transparent !important;"
                    class="w-full border-0 border-b border-white text-white pl-10 py-2 focus:outline-none focus:ring-0 placeholder-white/80 appearance-none">
            </div>

            <button
                class="w-full bg-[#0a356e] text-white py-3 text-sm font-semibold tracking-widest uppercase hover:bg-[#07244a] transition">
                Login
            </button>
        </form>

        <p class="text-center mt-8 text-sm">
            Belum punya akun?
            <a href="/register" class="underline hover:text-gray-200">Daftar</a>
        </p>

    </div>

</body>
</html>