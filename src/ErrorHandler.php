<?php

class ErrorHandler
{
    public static function handleException(Throwable $th): void
    {   
        http_response_code(500);
        echo json_encode(array(
            'code'=> $th->getCode(),
            "message"=>$th->getMessage(),
            "file"=>$th->getFile(),
            "line"=>$th->getLine()
        ));
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline):bool
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}