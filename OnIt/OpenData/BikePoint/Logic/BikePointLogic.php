<?php

namespace OnIt\BikePoint\Logic;

use OnIt\OpenData\BikePoint\Repository\BikePointRepository;
use OnIt\PythonBackend\Repository\PythonBackendRepository;

class BikePointLogic
{
    /**
     * @var PythonBackendRepository
     */
    private $repository;

    /**
     * PythonBackendLogic constructor.
     *
     * @param BikePointRepository $repository
     */
    public function __construct(BikePointRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param float $longitude
     * @param float $latitude
     * @param float $radiusMeter
     *
     * @return array
     */
    public function around(float $longitude, float $latitude, float $radiusMeter = 1000)
    {
        $response = $this->repository->around($longitude, $latitude, $radiusMeter);

        $featureCollection = json_decode($response->getBody()->getContents(), true);

        $features = $featureCollection['features'];

        $result = [];
        foreach ($features as $feature) {
            $featureProperties = $feature['properties'];
            $featureCoordinates = $feature['geometry']['coordinates'];
            $id = substr($featureProperties['id'], -2);
            $result[$id] = [
                'id' => 4, //$id,
                'name' => $featureProperties['name'],
                'address' => $featureProperties['address'],
                'longitude' => $featureCoordinates[0],
                'latitude' => $featureCoordinates[1],
            ];
        }

        return array_values($result);
    }
}