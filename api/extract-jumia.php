<?php
header('Content-Type: application/json');

/**
 * Jumia Product Extractor
 * Attempts to scrape product details from a Jumia Kenya URL.
 * Falls back gracefully if blocked or unavailable.
 */
$url = $_GET['url'] ?? '';

if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid URL']);
    exit;
}

// Check if the URL is a Jumia Kenya link
if (strpos($url, 'jumia.co.ke') === false) {
    echo json_encode(['success' => false, 'message' => 'Please provide a Jumia Kenya URL']);
    exit;
}

try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
            'timeout' => 10,
            'follow_location' => true,
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ]);

    $html = @file_get_contents($url, false, $context);

    if ($html === false) {
        echo json_encode(['success' => false, 'message' => 'Could not fetch the page. Please enter product details manually.']);
        exit;
    }

    $result = ['success' => true];

    // Extract product name
    if (preg_match('/<h1[^>]*>([^<]+)<\/h1>/i', $html, $m)) {
        $result['name'] = html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
    }

    // Extract price
    if (preg_match('/KSh\s*([\d,]+(?:\.\d{2})?)/i', $html, $m)) {
        $result['price'] = str_replace(',', '', $m[1]);
    }

    // Extract description
    if (preg_match('/<div[^>]*class="[^"]*markup[^"]*"[^>]*>(.*?)<\/div>/is', $html, $m)) {
        $desc = strip_tags($m[1]);
        $desc = preg_replace('/\s+/', ' ', $desc);
        $result['description'] = trim(substr($desc, 0, 500));
    }

    // Extract images
    $images = [];
    if (preg_match_all('/data-zoom-image="([^"]+)"/i', $html, $m)) {
        $images = array_unique($m[1]);
    } elseif (preg_match_all('/<img[^>]+src="(https:\/\/[^"]*jumia[^"]*\.jpg[^"]*)"/i', $html, $m)) {
        $images = array_unique($m[1]);
    }
    if (!empty($images)) {
        $result['images'] = array_values(array_slice($images, 0, 5));
    }

    if (empty($result['name']) && empty($result['price'])) {
        echo json_encode(['success' => false, 'message' => 'Could not extract product details. The page may be protected. Please enter details manually.']);
        exit;
    }

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Extraction failed: ' . $e->getMessage()]);
}
