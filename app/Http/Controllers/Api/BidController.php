<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use App\Models\Bid;
use App\Services\BidService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BidController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $bids = Bid::with('user')
            ->where('item_id', $id)
            ->orderBy('amount', 'desc')
            ->paginate(10);
        return JsonResponse::success(null, $bids);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, BidService $bidService, $id)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $bid = $bidService->submit($id, auth()->user()->id, $request->amount);

        return JsonResponse::success('Bid added successfully', $bid, Response::HTTP_CREATED);
    }
}
