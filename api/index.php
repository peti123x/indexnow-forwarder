<?php
require(__DIR__ . '/../vendor/autoload.php');

header("Content-Type: application/json");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'] ?? null;
$topic = $_SERVER['HTTP_X_SHOPIFY_TOPIC'] ?? null;
$data = file_get_contents('php://input');
$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $_ENV['SHOPIFY_SIGNATURE'], true));

if ($hmac_header == $calculated_hmac) {
    // HMAC is valid, process the request
    // ...
    error_log($topic);
    switch($topic) {
        case "products/create":
            $url = $_ENV['DOMAIN'] . "products/" . $data['handle'];

            $indexNow = new IndexNow($_ENV['BING_KEY']);
            $indexNow->sendChangedUrl($url);
            break;
        case "products/update":
            $url = $_ENV['DOMAIN'] . "products/" . $data['handle'];

            $indexNow = new IndexNow($_ENV['BING_KEY']);
            $indexNow->sendChangedUrl($url);

            break;
        case "collections/create":
            $url = $_ENV['DOMAIN'] . "collections/" . $data['handle'];

            $indexNow = new IndexNow($_ENV['BING_KEY']);
            $indexNow->sendChangedUrl($url);
            break;
        case "collections/update":
            $url = $_ENV['DOMAIN'] . "collections/" . $data['handle'];

            $indexNow = new IndexNow($_ENV['BING_KEY']);
            $indexNow->sendChangedUrl($url);
            break;
        default:
            http_response_code(404);
    }

    http_response_code(200);
} else {
    // HMAC is invalid, return a 401 response
    error_log("Invalid HMAC - sent {$hmac_header} compared to {$calculated_hmac}");
    error_log(json_encode($_SERVER));
    http_response_code(401);
}