<?php
require_once('Session.php');

class CustomError
{
    function response($error, $code = '401')
    {
        Session::destroy();
        http_response_code($code);
        include(__DIR__."/../views/custom_error.blade.php");
        exit();
    }    
}