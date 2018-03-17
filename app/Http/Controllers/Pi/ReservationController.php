<?php

namespace App\Http\Controllers\Pi;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;


class ReservationController extends Controller
{
    public function list(Request $request)
    {
        $reservation = Reservation::where('resource_id', $request->resource_id)->first();

        return [
            'success' => true,
            'result' => $reservation ?: []
        ];
    }
}
