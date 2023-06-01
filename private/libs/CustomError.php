<?php

class CustomError
{
    function response($error, $code = '401')
    {
        http_response_code($code);
        $VIEWS = '../private/views';
        include("{$VIEWS}/custom_error.blade.php");
        exit();
    }    
}