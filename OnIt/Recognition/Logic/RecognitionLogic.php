<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018. 03. 17.
 * Time: 3:01
 */

namespace OnIt\Recognition\Logic;


use OnIt\PythonBackend\Logic\PythonBackendLogic;

class RecognitionLogic
{
    /**
     * @var PythonBackendLogic
     */
    private $pythonBackendLogic;

    /**
     * RecognitionLogic constructor.
     * @param PythonBackendLogic $pythonBackendLogic
     */
    public function __construct(PythonBackendLogic $pythonBackendLogic)
    {
        $this->pythonBackendLogic = $pythonBackendLogic;
    }

    /**
     * @param array $encoding
     */
    public function recognize(array $face_encodings)
    {
        foreach ($face_encodings as $face_encoding) {
            $response = $this->pythonBackendLogic->recognize($face_encoding);
            $userId = $response->getBody()->getContents();

            if ($userId === 'unknown') {
                continue;
            }

            return true;
        }

        return false;
    }
}