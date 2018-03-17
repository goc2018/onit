<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018. 03. 17.
 * Time: 13:30
 */

namespace OnIt\OpenData\BikePoint\Repository;


use GuzzleHttp\Client;

class BikePointRepository
{
    const BASE_URI = 'https://api.tfl.lu/v1/BikePoint';

    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param float $longitude
     * @param float $latitude
     * @param float $radiusMeter
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function around(float $longitude, float $latitude, float $radiusMeter)
    {
        $uriParts = [
            'around',
            $longitude,
            $latitude,
            $radiusMeter
        ];

        return $this->client->request(
            'GET',
            implode('/', $uriParts)
        );
    }
}