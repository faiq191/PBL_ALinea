<script type="text/javascript">
    // Initialize Google Translate Widget
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'id', 
            includedLanguages: 'en,id',
            autoDisplay: false
        }, 'google_translate_element');
    }

    // Function to handle custom EN / ID button clicks
    function changeLanguage(lang) {
        const domain = window.location.hostname;
        if (lang === 'en') {
            document.cookie = `googtrans=/id/en; path=/; domain=${domain}`;
            document.cookie = `googtrans=/id/en; path=/;`;
        } else {
            // Delete cookies to revert to default Indonesian
            document.cookie = `googtrans=/id/id; path=/; domain=${domain}`;
            document.cookie = `googtrans=/id/id; path=/;`;
            document.cookie = `googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=${domain}`;
            document.cookie = `googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
        }
        window.location.reload(); // Reload to apply translation automatically
    }
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
