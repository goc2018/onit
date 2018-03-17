<?php

namespace App\Http\Controllers\Phone;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function list()
    {
        return [
            'success' => true,
            'result' => Resource::all()
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
