<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use OnIt\BikePoint\Logic\BikePointLogic;

class ResourceController extends Controller
{
    /**
     * @var BikePointLogic
     */
    private $bikePointLogic;

    /**
     * @param BikePointLogic $bikePointLogic
     */
    public function __construct(BikePointLogic $bikePointLogic)
    {
        $this->bikePointLogic = $bikePointLogic;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function list(Request $request)
    {
        return [
            'success' => true,
            'result' => $this->bikePointLogic->around(
                $request->longitude,
                $request->latitude,
                1000
            )
        ];
    }

    public function reserve(Request $request)
    {
        $reservation = new Reservation();
        $reservation->resource_id = $request->resource_id;
        $reservation->user_id = auth()->id();
        $reservation->created_at = $reservation->updated_at = date('Y-m-d H:i:s');
        $reservation->expired_at = date('Y-m-d H:i:s', strtotime('now + 20 minute'));

        $reservation->save();

        return [
            'success' => true,
            'result' => $reservation ?: []
        ];
    }
}
