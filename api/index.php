<?php
require(__DIR__ . '/../vendor/autoload.php');

use GuzzleHttp\Client;

header("Content-Type: application/json");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'] ?? null;
$topic = $_SERVER['HTTP_X_SHOPIFY_TOPIC'] ?? null;
$data = file_get_contents('php://input');
$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $_ENV['SHOPIFY_SIGNATURE'], true));

$endpoint = "https://api.indexnow.org/indexnow?url={url}&key={key}";

if ($hmac_header == $calculated_hmac) {
    // HMAC is valid, process the request
    // ...
    error_log($topic);
    $client = new Client();

    if(str_contains($topic, "products")) {
        $url = $_ENV['DOMAIN'] . "products/" . $data['handle'];

        try {
            error_log('Sending request for product');
            $body = $client->request(
                'GET',
                str_replace(
                    ['{url}', '{key}'],
                    [urlencode($url), $_ENV['BING_KEY']],
                    $endpoint
                )
            )->getBody();
            error_log($body->getContents());
        }  catch (\GuzzleHttp\Exception\GuzzleException $e) {
            error_log("FAILED TO REGISTER {$url}");
            error_log($e->getMessage());
            http_response_code(500);
            die();
        }

        http_response_code(200);
    }
    if(str_contains($topic, "collections")) {
        $url = $_ENV['DOMAIN'] . "collections/" . $data['handle'];

        try {
            error_log('Sending request for collection');
            $client->request(
                'GET',
                str_replace(
                    ['{url}', '{key}'],
                    [urlencode($url), $_ENV['BING_KEY']],
                    $endpoint
                )
            )->getBody();
        }  catch (\GuzzleHttp\Exception\GuzzleException $e) {
            error_log("FAILED TO REGISTER {$url}");
            error_log($e->getMessage());
            http_response_code(500);
            die();
        }

        http_response_code(200);
    }

    http_response_code(404);
} else {
    // HMAC is invalid, return a 401 response
    error_log("Invalid HMAC - sent {$hmac_header} compared to {$calculated_hmac}");
    error_log(json_encode($_SERVER));
    http_response_code(401);
}