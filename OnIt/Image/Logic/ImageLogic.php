<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018. 03. 16.
 * Time: 23:37
 */

namespace OnIt\Image\Logic;


use App\Models\FaceEncoding;
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
     * @param Request  $request
     * @param int|null $user_id
     *
     * @return bool
     */
    public function upload(Request $request, $user_id = null)
    {
        // Send the image to the Python backend.
        $response  = $this->pythonBackendLogic->detect($request->file('image'));
        $encodings = json_decode($response->getBody()->getContents(), true);

        if (count($encodings) !== 1)
        {
            // No face detected OR multiple faces detected
            return false;
        }

        // Save to database.
        $encoding = array_shift($encodings);
        $this->saveEncoding($encoding, $user_id);

        // Train.
        $trainResponse = $this->pythonBackendLogic->train(
            [
                'user_id'  => $user_id ?? auth()->id(),
                'encoding' => $encoding['encoding']
            ]
        );

        if ($trainResponse->getStatusCode() !== 200)
        {
            // Train failed.
            return false;
        }

        // Face trained.
        return true;
    }

    /**
     * @param array    $encoding
     * @param int|null $user_id
     */
    private function saveEncoding(array $encoding, $user_id = null)
    {
        $faceEncoding           = new FaceEncoding();
        $faceEncoding->user_id  = $user_id ?? auth()->id();
        $faceEncoding->image    = $encoding['image'];
        $faceEncoding->encoding = json_encode($encoding['encoding']);
        $faceEncoding->save();
    }
}