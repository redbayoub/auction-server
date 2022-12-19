<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use App\Models\Bot;
use Illuminate\Http\Request;

class BotController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bot  $bot
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $bot = Bot::where('user_id', auth()->user()->id)->firstOrFail();
        return JsonResponse::success(null, $bot);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bot  $bot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'maxAmount' => 'required|integer|min:1',
            'percentageAlert' => 'required|integer|min:1|max:100',
        ]);

        $bot = Bot::updateOrCreate(
            ['user_id' => auth()->user()->id],
            [
                'maxAmount' => $request->maxAmount,
                'percentageAlert' => $request->percentageAlert,
            ]
        );

        return JsonResponse::success('Bot configs updated successfully', $bot);
    }
}
