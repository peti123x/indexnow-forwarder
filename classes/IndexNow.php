<?php
use GuzzleHttp\Client;

final class IndexNow
{
    private const ENDPOINT = "https://api.indexnow.org/indexnow?url={url}&key={key}";
    private Client $client;

    public function __construct(private readonly string $key)
    {
        $this->client = new Client();
    }

    public function sendChangedUrl(string $url): void
    {
        try {
            $this->client->request(
                'GET',
                str_replace(
                    ['{url}', '{key}'],
                    [urlencode($url), $this->key],
                    self::ENDPOINT
                )
            );
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            error_log("FAILED TO REGISTER {$url}");
            error_log($e->getMessage());
        }

    }


}