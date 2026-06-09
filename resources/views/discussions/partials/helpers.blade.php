@php
    if (!function_exists('parseGifsInContent')) {
        function parseGifsInContent($escapedContent) {
            // 1. Identify GIF URLs and replace with placeholders
            $gifRegex = '/(https?:\/\/[^\s<>\"]+?\.(?:gif)(?:[?#][^\s<>\"]*)?|https?:\/\/(?:www\.)?media\.tenor\.com\/[^\s<>\"]+|https?:\/\/(?:www\.)?tenor\.com\/view\/[^\s<>\"]+)/i';
            $gifPlaceholders = [];
            $escapedContent = preg_replace_callback($gifRegex, function($matches) use (&$gifPlaceholders) {
                $placeholder = '___GIF_PLACEHOLDER_' . count($gifPlaceholders) . '___';
                $gifPlaceholders[$placeholder] = html_entity_decode($matches[1]);
                return $placeholder;
            }, $escapedContent);
            
            // 2. Match any other URLs and replace with clickable warning links (blue highlight)
            $urlRegex = '/(https?:\/\/[^\s<>\"]+)/i';
            $escapedContent = preg_replace_callback($urlRegex, function($matches) {
                $url = html_entity_decode($matches[1]);
                $safeUrl = e($url);
                return '<a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer" ' .
                       'class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 underline font-medium bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 text-xs transition duration-200" ' .
                       'title="Peringatan: Tautan eksternal dari pengguna lain. Harap berhati-hati saat membuka tautan dari luar." ' .
                       'onclick="return confirm(\'Peringatan Keamanan: Tautan ini berasal dari luar ALinea. Membuka tautan eksternal dari orang asing berpotensi bahaya (phishing, malware, dll).\\n\\nApakah Anda yakin ingin membuka: ' . addslashes($safeUrl) . '?\')">' .
                       '<span>' . $safeUrl . '</span>' .
                       '<i data-lucide="external-link" class="w-3 h-3 text-blue-500"></i>' .
                       '</a>';
            }, $escapedContent);
            
            // 3. Restore GIF placeholders with image tags
            foreach ($gifPlaceholders as $placeholder => $url) {
                $imgHtml = '<div class="relative max-w-xs overflow-hidden rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition mt-2">' .
                           '<img src="' . e($url) . '" class="w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition" onclick="openLightbox(\'' . e($url) . '\')">' .
                           '</div>';
                $escapedContent = str_replace($placeholder, $imgHtml, $escapedContent);
            }
            
            return $escapedContent;
        }
    }
@endphp
