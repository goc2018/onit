<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018. 03. 16.
 * Time: 23:37
 */

namespace OnIt\Image\Logic;

use CURLFile;
use Illuminate\Http\Request;
use OnIt\PythonBackend\Logic\PythonBackendLogic;

class ImageLogic
{
    /**
     * @var PythonBackendLogic
     */
    private $pythonBackendLogic;

    public function __construct(PythonBackendLogic $pythonBackendLogic)
    {
        $this->pythonBackendLogic = $pythonBackendLogic;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function upload(Request $request)
    {
        // Send the image to the Python backend.
        $response = $this->pythonBackendLogic->detect(new CURLFile($request->file('image')->getFilename()));
        $encodings = json_decode($response->getBody()->getContents(), true);

        if (count($encodings) !== 1) {
            // No face detected OR multiple faces detected
            return false;
        }

        $encoding = array_unshift($encodings);

        return true;
        // Save to database.
        // Send every [[user_id][encoding]] pair.
    }
}