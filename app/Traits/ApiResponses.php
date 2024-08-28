<?php

namespace App\Traits;

trait ApiResponses 
{
    protected function ok($message, $data = []) 
    {
        return $this->success($message, $data, 200);
    }

    protected function success($message, $data = [], $statusCode = 200) 
    {
        return response()->json([
            'data' => $data,
            'messsage' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }

    protected function error($errors = [], $statusCode = null) 
    {
        if (is_string($errors)) {
            return response()->json([
                'messsage' => $errors,
                'status' => $statusCode,
            ], $statusCode);
        } 

        return response()->json([
            'errors' => $errors
        ]);
    }
}