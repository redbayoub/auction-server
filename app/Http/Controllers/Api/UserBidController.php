<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserBidController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $bids = Bid::with('item')->where('user_id',  auth()->user()->id)
            ->get()
            ->groupBy('item_id')
            ->map(
                fn ($group) => $group->sortbyDesc('amount')
                    ->first()
                    ->append('status')
            )
            ->flatten();


        return JsonResponse::success(null, $bids);
    }
}
