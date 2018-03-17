<?php

namespace App\Http\Controllers\Pi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OnIt\Recognition\Logic\RecognitionLogic;

class RecognitionController extends Controller
{
    /**
     * @var RecognitionLogic
     */
    private $recognitionLogic;

    /**
     * @param RecognitionLogic $recognitionLogic
     */
    public function __construct(RecognitionLogic $recognitionLogic)
    {
        $this->recognitionLogic = $recognitionLogic;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function recognize(Request $request)
    {
        return [
            'success' => true,
            'result' => $this->recognitionLogic->recognize(
                $request->face_encodings,
                $request->resource_id
            )
        ];
    }
}
