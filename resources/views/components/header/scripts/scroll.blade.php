@props(['isHome'])

<script>
    // 2. Fungsi Scroll Effect (Hanya berjalan di Beranda)
    const isHome = @json($isHome);
    
    if (isHome) {
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('main-nav');
            const logo = document.getElementById('nav-logo');
            const loginBtn = document.getElementById('nav-login');
            const registerBtn = document.getElementById('nav-register-btn');
            const userName = document.getElementById('nav-user-name');
            const navLinks = document.querySelectorAll('.nav-link');
            const userBtn = document.getElementById('nav-user-btn');
            const userChevron = document.getElementById('nav-user-chevron');
            const notifBtn = document.getElementById('nav-notif-btn');
            const langContainer = document.getElementById('nav-lang-container'); // NEW

            if (window.scrollY > 50) {
                // SAAT SCROLL KE BAWAH (Header Putih)
                nav.classList.replace('bg-transparent', 'bg-white');
                nav.classList.add('shadow-md', 'border-b', 'border-gray-200');

                logo.classList.replace('text-white', 'text-[#1a3a5c]');
                if (loginBtn) loginBtn.classList.replace('text-white', 'text-[#1a3a5c]');

                if (langContainer) {
                    langContainer.classList.replace('text-white', 'text-[#1a3a5c]');
                }

                // Ubah tombol daftar jadi solid biru
                if (registerBtn) {
                    registerBtn.classList.replace('bg-white', 'bg-[#1a3a5c]');
                    registerBtn.classList.replace('text-[#1a3a5c]', 'text-white');
                }

                // Ubah capsule user profile
                if (userBtn) {
                    userBtn.classList.replace('bg-white/10', 'bg-slate-100');
                    userBtn.classList.replace('border-white/20', 'border-slate-200');
                    userBtn.classList.replace('hover:bg-white/20', 'hover:bg-slate-200/80');
                }
                if (userName) {
                    userName.classList.replace('text-white', 'text-[#1a3a5c]');
                }
                if (userChevron) {
                    userChevron.classList.replace('text-white', 'text-[#1a3a5c]');
                }

                // Ubah notifikasi button
                if (notifBtn) {
                    notifBtn.classList.replace('bg-white/10', 'bg-slate-100');
                    notifBtn.classList.replace('text-white', 'text-[#1a3a5c]');
                    notifBtn.classList.replace('hover:bg-white/20', 'hover:bg-slate-200/80');
                }

                navLinks.forEach(link => {
                    if (!link.classList.contains('text-[#e84b7a]')) {
                        link.classList.replace('text-white', 'text-[#1a3a5c]');
                    }
                });

            } else {
                // SAAT DI ATAS (Header Transparan)
                nav.classList.replace('bg-white', 'bg-transparent');
                nav.classList.remove('shadow-md', 'border-b', 'border-gray-200');

                logo.classList.replace('text-[#1a3a5c]', 'text-white');
                if (loginBtn) loginBtn.classList.replace('text-[#1a3a5c]', 'text-white');

                if (langContainer) {
                    langContainer.classList.replace('text-[#1a3a5c]', 'text-white');
                }

                // Kembalikan tombol daftar jadi putih
                if (registerBtn) {
                    registerBtn.classList.replace('bg-[#1a3a5c]', 'bg-white');
                    registerBtn.classList.replace('text-white', 'text-[#1a3a5c]');
                }

                // Kembalikan capsule user profile
                if (userBtn) {
                    userBtn.classList.replace('bg-slate-100', 'bg-white/10');
                    userBtn.classList.replace('border-slate-200', 'border-white/20');
                    userBtn.classList.replace('hover:bg-slate-200/80', 'hover:bg-white/20');
                }
                if (userName) {
                    userName.classList.replace('text-[#1a3a5c]', 'text-white');
                }
                if (userChevron) {
                    userChevron.classList.replace('text-[#1a3a5c]', 'text-white');
                }

                // Kembalikan notifikasi button
                if (notifBtn) {
                    notifBtn.classList.replace('bg-slate-100', 'bg-white/10');
                    notifBtn.classList.replace('text-[#1a3a5c]', 'text-white');
                    notifBtn.classList.replace('hover:bg-slate-200/80', 'hover:bg-white/20');
                }

                navLinks.forEach(link => {
                    if (!link.classList.contains('text-[#e84b7a]')) {
                        link.classList.replace('text-[#1a3a5c]', 'text-white');
                    }
                });
            }
        });
    }
</script>
