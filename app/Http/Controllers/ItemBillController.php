<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;


class ItemBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        if (Carbon::parse($item->auction_closes_at)->greaterThan(now()))
            return JsonResponse::fail('The bill will be available after the item auction closes');

        $customer = new Buyer([
            'name'          => $item->highestBid->user->username,
            'custom_fields' => [
                'email' =>  $item->highestBid->user->email,
            ],
        ]);

        $item = (new InvoiceItem())->title($item->name)->pricePerUnit($item->price);

        $invoice = Invoice::make()
            ->buyer($customer)
            ->addItem($item);

        return $invoice->stream();
    }
}
