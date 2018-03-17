<?php

namespace OnIt\BikePoint\Logic;

use OnIt\OpenData\BikePoint\Repository\BikePointRepository;
use OnIt\PythonBackend\Repository\PythonBackendRepository;
use Psr\Http\Message\ResponseInterface;

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
     * @return mixed|ResponseInterface
     */
    public function around(float $longitude, float $latitude, float $radiusMeter = 1000)
    {
        return $this->repository->around($longitude, $latitude, $radiusMeter);
    }
}