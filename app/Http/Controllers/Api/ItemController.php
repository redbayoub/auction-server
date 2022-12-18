<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'searchTerm' => "nullable|string|min:3",
            'sort' => ["nullable", "string", Rule::in("price:asc", "price:desc")],
        ]);

        $itemsQuery = Item::query();

        if ($request->has('searchTerm'))
            $itemsQuery = $itemsQuery->whereLike(['name', 'description'], $request->searchTerm);

        if ($request->has('sort')) {
            [$column, $direction] = explode(':', $request->sort);
            $itemsQuery = $itemsQuery->orderBy('startingPrice', $direction);
        }

        $items = $itemsQuery->paginate(10);

        return JsonResponse::success(null, $items);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'startingPrice' => 'required|integer|min:1',
            'auction_closes_at' => 'required|date|after:now',
            'image' => 'required|image',
        ]);

        $imagePath = $request->file('image')->storePublicly("/public/images");
        $item = Item::create([
            'name' => $request->name,
            'description' => $request->description,
            'startingPrice' => $request->startingPrice,
            'auction_closes_at' => $request->auction_closes_at,
            'image' => $imagePath,
        ]);

        return JsonResponse::success('Item created successfully', $item, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::findOrFail($id);
        if (auth()->user()->isAdmin)
            $item = $item->makeVisible(['startingPrice']);
        return JsonResponse::success(null, $item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'startingPrice' => 'required|integer|min:1',
            'auction_closes_at' => 'required|date|after:now',
            'image' => 'required|image',
        ]);

        $item = Item::findOrFail($id);

        $imagePath = $request->file('image')->storePublicly("/public/images");

        $item->update([
            'name' => $request->name,
            'description' => $request->description,
            'startingPrice' => $request->startingPrice,
            'auction_closes_at' => $request->auction_closes_at,
            'image' => $imagePath,
        ]);

        return JsonResponse::success('Item updated successfully', $item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        return JsonResponse::success('Item deleted successfully');
    }
}
