<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018. 03. 16.
 * Time: 23:37
 */

namespace OnIt\Image\Logic;

use App\Models\FaceEncoding;
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

        // Save to database.
        $encoding = array_unshift($encodings);
        $this->saveEncoding($encoding);

        // Train.
        $trainResponse = $this->pythonBackendLogic->train([
            'user_id' => auth()->id(),
            'encoding' => $encoding['encoding']
        ]);

        if ($trainResponse->getStatusCode() !== 200) {
            // Train failed.
            return false;
        }

        // Face trained.
        return true;
    }

    /**
     * @param array $encoding
     */
    private function saveEncoding(array $encoding)
    {
        $faceEncoding = new FaceEncoding();
        $faceEncoding->user_id = auth()->id();
        $faceEncoding->image = $encoding['image'];
        $faceEncoding->encoding = $encoding['encoding'];
        $faceEncoding->save();
    }
}