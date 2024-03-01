<?php

namespace App\Exceptions;

use Exception;

class UserExistPreviouslyException extends Exception
{
    public function render($request)
    {
        return response()->json([
                                'message' => $this->getMessage(),
                                'success' => false
                                ], 303);
    }
}
