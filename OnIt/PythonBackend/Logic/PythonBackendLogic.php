<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018. 03. 17.
 * Time: 0:07
 */

namespace OnIt\PythonBackend\Logic;


use Illuminate\Http\UploadedFile;
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
     * @param UploadedFile $file
     *
     * @return ResponseInterface
     */
    public function detect($file)
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

    /**
     * @param array $face_encoding
     *
     * @return mixed|ResponseInterface
     */
    public function recognize(array $face_encoding)
    {
        return $this->repository->recognize($face_encoding);
    }
}