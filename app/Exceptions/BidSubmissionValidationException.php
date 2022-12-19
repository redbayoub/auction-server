<?php

namespace App\Exceptions;

use App\Http\JsonResponse;
use Exception;
use Illuminate\Http\Response;

class BidSubmissionValidationException extends Exception
{
    public $message;

    public function __construct($message)
    {
        parent::__construct($message);
    }


    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return JsonResponse::fail(
            $this->getMessage(),
            ['amount' => [$this->getMessage()]],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
