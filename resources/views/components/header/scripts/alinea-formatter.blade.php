<script>
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
