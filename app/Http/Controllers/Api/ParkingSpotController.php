<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateParkingSpotRequest;
use App\Http\Resources\ParkingSpotResource;
use App\Models\ParkingSpot;
use Illuminate\Http\Request;

class ParkingSpotController extends Controller
{
    public function show(Request $request)
    {
        $spot = ParkingSpot::query()
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$spot) {
            return response()->json([
                'spot' => null,
                'updatedAt' => null,
            ]);
        }

        $this->authorize('view', $spot);

        return new ParkingSpotResource($spot);
    }

    public function update(UpdateParkingSpotRequest $request)
    {
        $data = $request->validated();

        // existující nebo nový model (kvůli policy + čisté logice)
        $spot = ParkingSpot::query()
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$spot) {
            $this->authorize('create', ParkingSpot::class);

            $spot = new ParkingSpot([
                'user_id' => $request->user()->id,
            ]);
        } else {
            $this->authorize('update', $spot);
        }

        $spot->spot = $data['spot'];
        $spot->save();

        return new ParkingSpotResource($spot);
    }
}