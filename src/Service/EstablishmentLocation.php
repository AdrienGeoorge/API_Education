<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EstablishmentLocation
{
    private string $apiUrl;
    private HttpClientInterface $client;

    public function __construct(string $apiUrl, HttpClientInterface $client)
    {
        $this->apiUrl = $apiUrl;
        $this->client = $client;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getList(?string $select, ?string $where): ?array
    {
        if ($select) $this->apiUrl .= '?select=' . $select;
        if ($where) $this->apiUrl .= '&where=' . $where;

        $response = $this->client->request('GET', $this->apiUrl);

        if ($response->getStatusCode() === 200) {
            $response = $response->toArray();
            $return['count'] = count($response);

            foreach ($response as $item) {
                $return['list'][] = $item;
            }

            return $return;
        } else {
            return null;
        }
    }
}