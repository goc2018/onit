<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018. 03. 17.
 * Time: 3:01
 */

namespace OnIt\Recognition\Logic;


use App\Models\Reservation;
use OnIt\PythonBackend\Logic\PythonBackendLogic;


class RecognitionLogic
{
    /**
     * @var PythonBackendLogic
     */
    private $pythonBackendLogic;

    /**
     * @param PythonBackendLogic $pythonBackendLogic
     */
    public function __construct(PythonBackendLogic $pythonBackendLogic)
    {
        $this->pythonBackendLogic = $pythonBackendLogic;
    }

    /**
     * @param array $face_encodings
     * @param int   $resource_id
     *
     * @return bool
     */
    public function recognize(array $face_encodings, int $resource_id)
    {
        $reservation = Reservation::where('resource_id', $resource_id)->first();

        foreach ($face_encodings as $face_encoding)
        {
            $response = $this->pythonBackendLogic->recognize($face_encoding);
            $userId   = $response->getBody()->getContents();

            if ($reservation->user_id === $userId)
            {
                return true;
            }
        }

        return false;
    }
}