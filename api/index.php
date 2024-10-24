<?php
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../classes/IndexNow.php');

header("Content-Type: application/json");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'] ?? null;
$topic = $_SERVER['HTTP_X_SHOPIFY_TOPIC'] ?? null;
$data = file_get_contents('php://input');
$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $_ENV['SHOPIFY_SIGNATURE'], true));

$data = json_decode($data, true);

if($hmac_header != $calculated_hmac) {
    // HMAC is invalid, return a 401 response
    error_log("Invalid HMAC - sent {$hmac_header} compared to {$calculated_hmac}");
    error_log(json_encode($_SERVER));
    http_response_code(401);
    die();
}

// Check table is scaffolded
try {
    $pdo = new Database();

    // Check if table exists, if not, create it
    $tableCheck = count($pdo->select("SHOW TABLES LIKE 'urls'"));
    if ($tableCheck === 0) {
        // Table does not exist, so we create it
        $sql = "CREATE TABLE urls (
            id INT AUTO_INCREMENT PRIMARY KEY,
            url VARCHAR(500) NOT NULL,
            sent TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        );";
        $pdo->execute($sql);
        echo "Table 'urls' created successfully.";
    } else {
        echo "Table 'urls' already exists.";
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// HMAC is valid, process the request
// ...
error_log($topic);
$indexNow = new IndexNow($_ENV['BING_KEY']);

if(str_contains($topic, "products")) {
    $url = $_ENV['DOMAIN'] . "products/" . $data['handle'];

    try {
        $indexNow
            ->sendChangedUrl($url);
        http_response_code(200);
        die();
    }  catch (\GuzzleHttp\Exception\GuzzleException $e) {
        http_response_code(500);
        die();
    }
}
if(str_contains($topic, "collections")) {
    $url = $_ENV['DOMAIN'] . "collections/" . $data['handle'];

    try {
        $indexNow
            ->sendChangedUrl($url);
        http_response_code(200);
        die();
    }  catch (\GuzzleHttp\Exception\GuzzleException $e) {
        http_response_code(500);
        die();
    }
}

http_response_code(404);