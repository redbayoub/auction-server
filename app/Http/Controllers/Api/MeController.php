<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = User::with('bot')->findOrFail(auth()->id());
        return JsonResponse::success(null, $user);
    }
}
