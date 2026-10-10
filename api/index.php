<?php
// Set the working directory to the project root
chdir(__DIR__ . '/../');

// Parse the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Default to index.php if root is requested
if ($uri === '/' || $uri === '') {
    $uri = '/index.php';
}

// Construct the absolute path to the requested file
$file = __DIR__ . '/..' . $uri;

// Ensure the file exists and is a PHP file
if (file_exists($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
    // Execute the requested PHP file
    require $file;
} else {
    // If not found or not PHP, return 404
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
    echo "<p>The requested URL " . htmlspecialchars($uri) . " was not found on this server.</p>";
}
