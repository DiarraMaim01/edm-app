<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Test simple: http://localhost/edmApp/public/
if ($path === '/edmApp/public/' || $path === '/edmApp/public') {
  echo "EDM App OK";
  exit;
}

// Test DB: http://localhost/edm-app/public/health/db
if ($path === '/edmApp/public/health/db') {
  try {
    $pdo = db();
    $row = $pdo->query('SELECT 1 AS ok')->fetch();
    json(['db' => 'connected', 'result' => $row['ok']]); // => {"db":"connected","result":1}
  } catch (Throwable $e) {
    json(['db' => 'error', 'message' => $e->getMessage()], 500);
  }
}

http_response_code(404);
echo "Not Found";
