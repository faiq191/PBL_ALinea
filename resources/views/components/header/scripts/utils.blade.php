<script type="text/javascript">
    // --- Google Translate Initialization ---
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

    // --- Dropdown Toggles ---
    function toggleDropdown(event) {
        event.stopPropagation();
        document.getElementById("profileMenu").classList.toggle("hidden");
        const notifMenu = document.getElementById("notifMenu");
        if (notifMenu) notifMenu.classList.add("hidden");
    }

    function toggleNotifDropdown(event) {
        event.stopPropagation();
        const notifMenu = document.getElementById("notifMenu");
        if (notifMenu) notifMenu.classList.toggle("hidden");
        document.getElementById("profileMenu").classList.add("hidden");
    }

    window.addEventListener("click", function() {
        const menu = document.getElementById("profileMenu");
        if (menu) menu.classList.add("hidden");
        const notifMenu = document.getElementById("notifMenu");
        if (notifMenu) notifMenu.classList.add("hidden");
    });

    // --- Alinea Text Formatter ---
    document.addEventListener("DOMContentLoaded", () => {
        // 1. Create a "TreeWalker" to look ONLY at visible text on the page
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
        const nodesToUpdate = [];
        let node;
        
        // 2. Find every piece of text that contains "Alinea"
        while (node = walker.nextNode()) {
            const parent = node.parentElement;
            // Ignore text inside scripts, styles, or things we already protected
            if (parent && !['SCRIPT', 'STYLE', 'NOSCRIPT'].includes(parent.tagName) && !parent.classList.contains('notranslate')) {
                if (node.nodeValue.includes('Alinea')) {
                    nodesToUpdate.push(node);
                }
            }
        }

        // 3. Automatically wrap the word "Alinea" in the notranslate class
        nodesToUpdate.forEach(textNode => {
            const text = textNode.nodeValue;
            const fragment = document.createDocumentFragment();
            const parts = text.split('Alinea'); // Split the sentence around the word
            
            parts.forEach((part, index) => {
                fragment.appendChild(document.createTextNode(part));
                
                // Re-insert "Alinea" but wrapped in the protective span
                if (index < parts.length - 1) {
                    const span = document.createElement('span');
                    span.className = 'notranslate text-inherit'; // text-inherit ensures it doesn't change color
                    span.textContent = 'Alinea';
                    fragment.appendChild(span);
                }
            });
            
            // Replace the old text with the new protected text
            textNode.parentNode.replaceChild(fragment, textNode);
        });
    });
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
