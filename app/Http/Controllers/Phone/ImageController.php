<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OnIt\Image\Logic\ImageLogic;

class ImageController extends Controller
{
    /**
     * @var ImageLogic
     */
    private $imageLogic;

    public function __construct(ImageLogic $imageLogic)
    {
        $this->imageLogic = $imageLogic;
    }

    /**
     * @param Request $request
     */
    public function upload(Request $request)
    {
        $this->imageLogic->train($request);
    }
}
