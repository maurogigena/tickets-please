<?php

namespace App\Traits;

use Illuminate\Support\Facades\Gate;

trait ApiResponses {

    protected string $namespace = 'App\\Policies\\V1';

    protected function ok($message, $data = []) {
        return $this->success($message, $data, 200);
    }
    
    protected function success($message, $data = [], $statusCode = 200) {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }

    // app.php / ->withExceptions() <->
    // that function will provide a customizer error in json format
    
    // GENERAL FUNCTION TO RETURN CUSTOMIZED ERRORS
    protected function error($errors = [], $statusCode = null) {
        // if the error is passed as a string (ex: "unauthorized")... 
        if (is_string($errors)) {
            return response()->json([
                'message' => $errors, // simple error message
                'status' => $statusCode // http code (optional)
            ], $statusCode);
        }

        // if a structured array of errors is passed...
        return response()->json([
            'errors' => $errors // return the errors at the expected format by the API 
        ]);
    }

    // SPECIFIC FUNCTION TO HANDLING AUTHORIZATION ERRORS (401 - UNAUTHORIZED
    protected function notAuthorized($message) {
        // calls to the error() function with a specific structure for authorization errors
        return $this->error([
            'status' => 401,
            'message' => $message,
            'source' => ''
        ]);
    }
}