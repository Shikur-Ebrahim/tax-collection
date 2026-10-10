<?php
// ============================================================
// Vercel API Router
// Routes all incoming requests to the correct PHP file
// ============================================================

// Set the project root as the working directory
$rootDir = dirname(__DIR__);
chdir($rootDir);

// Parse the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Strip any leading slash, default to index.php
$uri = ltrim($uri, '/');
if ($uri === '' || $uri === '/') {
    $uri = 'index.php';
}

// Ensure .php extension
if (pathinfo($uri, PATHINFO_EXTENSION) === '') {
    $uri = rtrim($uri, '/') . '/index.php';
}

// Build absolute path to the requested file
$file = $rootDir . '/' . $uri;

// Security: prevent directory traversal
$realFile = realpath($file);
$realRoot = realpath($rootDir);

if ($realFile === false || strpos($realFile, $realRoot) !== 0) {
    http_response_code(403);
    echo "<h1>403 Forbidden</h1>";
    exit;
}

// Serve PHP files
if (file_exists($realFile) && pathinfo($realFile, PATHINFO_EXTENSION) === 'php') {
    // Change directory to the file's own directory so relative paths inside it work
    chdir(dirname($realFile));
    require $realFile;
} else {
    http_response_code(404);
    echo "<h1>404 Not Found</h1><p>The page <code>" . htmlspecialchars($uri) . "</code> was not found.</p>";
}
