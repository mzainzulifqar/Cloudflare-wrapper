<?php

namespace App\Http\?????;
use GuzzleHttp\Client;

class Cloudflare
{
    protected $endpoint = 'https://api.cloudflare.com/client/v4/zones/';
    protected $zone, $ip, $http,$client;

    public function __construct()
    {
        $this->zone = env('CLOUDFLARE_ZONE');
        $this->ip = env('CLOUDFLARE_IP');

		$this->client = new Client();
    }

    public function createDnsRecord($name)
    {
        try {
            $params = [
                'headers' => $this->headers(),

                'json' => [
                    'type'     => 'A',
                    'name'     => $name,
                    'content'  => $this->ip,
                    'ttl'      => 120,
                    'priority' => 10,
                    'proxied'  => true,
                ],
            ];

            $response = $this->client->request('POST',$this->endpoint . $this->zone . '/dns_records', $params);
            return json_decode($response->getBody(), true);
        }
        catch (\Exception $e)
		{
            return ['success' => false, 'message' => $e->getMessage()];
		}
    }

    public function updateDnsRecord($id, $name)
    {
        try {
            $url = $this->endpoint . "zones/{$this->zone}/dns_records/{$id}";
            $params = [
                'headers' => $this->headers(),

                'json' => [
                    'type'     => 'A',
                    'name'     => $name,
                    'content'  => $this->ip,
                    'ttl'      => 120,
                    'priority' => 10,
                    'proxied'  => true,
                ],
            ];

            $response = $this->client->request('POST',$url . $this->zone . '/dns_records', $params);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteDnsRecord($id)
    {
        try {

            $params = [
                'headers' => $this->headers()
            ];
            $url = $this->endpoint . "{$this->zone}/dns_records/{$id}";
            $response = $this->client->request('Delete',$url,$params);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function headers()
	{
		return [
			'Content-Type' => 'application/json',
			'X-Auth-Email' => env('CLOUDFLARE_AUTH_EMAIL'),
			'X-Auth-Key' => env('CLOUDFLARE_AUTH_KEY'),
		];
	}
}
