<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RootController extends Controller
{
    public function sendError($statusMessage, $statusCode = 500, $exceptionMessage = '',)
    {
        // if exception message does not exist, dont send key value
        if (!$exceptionMessage) {
            return response()->json([
                'status' =>  false,
                'message' => $statusMessage,
            ], $statusCode);
        }

        return response()->json([
            'status' =>  false,
            'message' => $statusMessage,
            'exceptionMessage' => $exceptionMessage
        ], $statusCode);
    }

    public function sendSuccess($statusMessage, $attribute = '', $items = [])
    {

        // if attribute is empty, dont send the empty array

        return $attribute ?
            response()->json([
                'status' =>  true,
                'message' => $statusMessage,
                $attribute => $items
            ], 200)
            :
            response()->json([
                'status' =>  true,
                'message' => $statusMessage,
            ], 200);
    }
}
