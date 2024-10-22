<?php
require(__DIR__ . '/../vendor/autoload.php');

header("Content-Type: application/json");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$hmac_header = isset($_SERVER['X-Shopify-Hmac-Sha256']) ? $_SERVER['X-Shopify-Hmac-Sha256'] : null;
$topic = isset($_SERVER['X-Shopify-Topic']) ? $_SERVER['X-Shopify-Topic'] : null;
$data = file_get_contents('php://input');
$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $_ENV['SHOPIFY_SIGNATURE'], true));

if ($hmac_header == $calculated_hmac) {
    // HMAC is valid, process the request
    // ...
    switch($topic) {
        case "products/create":
            $url = $_ENV['DOMAIN'] . "products/" . $data['handle'];
            break;
        case "products/update":
            $url = $_ENV['DOMAIN'] . "products/" . $data['handle'];

            $indexNow = new \Baraja\IndexNow\IndexNow(
                apiKey: $_ENV['BING_KEY'],
                searchEngine: 'bing'
            );
            $indexNow->sendChangedUrl($url);

            $indexNow = new \Baraja\IndexNow\IndexNow(
                apiKey: $_ENV['BING_KEY'],
                searchEngine: 'yahoo'
            );
            $indexNow->sendChangedUrl($url);

            break;
        case "collections/create":
            $url = $_ENV['DOMAIN'] . "collections/" . $data['handle'];
            break;
        case "collections/update":
            $url = $_ENV['DOMAIN'] . "collections/" . $data['handle'];
            break;
        default:
            http_response_code(404);

    }

    http_response_code(200);
} else {
    // HMAC is invalid, return a 401 response
    http_response_code(401);
}