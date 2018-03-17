<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018. 03. 17.
 * Time: 0:07
 */

namespace OnIt\PythonBackend\Logic;


use CURLFile;
use OnIt\PythonBackend\Repository\PythonBackendRepository;
use Psr\Http\Message\ResponseInterface;

class PythonBackendLogic
{
    /**
     * @var PythonBackendRepository
     */
    private $repository;

    /**
     * PythonBackendLogic constructor.
     * @param PythonBackendRepository $repository
     */
    public function __construct(PythonBackendRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CURLFile $file
     *
     * @return ResponseInterface
     */
    public function detect(CURLFile $file)
    {
        return $this->repository->detect($file);
    }

    /**
     * @param array $data
     *
     * @return mixed|ResponseInterface
     */
    public function train(array $data)
    {
        return $this->repository->train($data);
    }
}