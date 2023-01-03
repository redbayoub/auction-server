<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use App\Models\AutoBidItem;
use App\Models\Item;
use App\Services\BidService;
use Illuminate\Http\Request;

class AutoBidItemController extends Controller
{

    public function store(Request $request, $id)
    {

        $item = Item::findOrFail($id);
        $bot =  auth()->user()->bot;
        $user_id = auth()->user()->id;

        if (!$bot)
            return JsonResponse::fail('You need to configure the bot setting before enabling auto-bid');


        AutoBidItem::firstOrCreate([
            'user_id' => $user_id,
            'item_id' => $item->id,
        ]);

        if ($item->bid_user_id != $user_id && $bot->maxAmount > $item->price) {
            $bidService = new BidService();
            $bidService->submit($item->id, $user_id, $item->price + 1, true);
        }

        return JsonResponse::success('Auto-Bid enabled successfully on the selected item');
    }

    public function show(Request $request, $id)
    {
        $autoBidItem = AutoBidItem::where('user_id', auth()->user()->id)
            ->where('item_id', $id)
            ->firstOrFail();

        return JsonResponse::success(null, $autoBidItem);
    }


    public function destroy($id)
    {
        AutoBidItem::where('user_id', auth()->user()->id)
            ->where('item_id', $id)
            ->firstOrFail()
            ->delete();

        return JsonResponse::success('Auto-Bid disabled successfully on the selected item');
    }
}
