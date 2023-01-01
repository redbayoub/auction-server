<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = Item::where('auction_closes_at', '<', now())
            ->whereHas('highestBid', fn ($q) => $q->where('user_id', auth()->user()->id))
            ->get()
            ->map
            ->append('bill_url');

        return JsonResponse::success(null, $items);
    }
}
