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

    public function detect(CURLFile $file)
    {
        return $this->repository->detect($file);
    }
}