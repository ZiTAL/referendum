<?php
require_once('CustomError.php');

class View
{
    public static function load($view, $params = NULL)
    {
        $view_file = realpath(__DIR__."/../views/{$view}");
        if($view_file)
            include($view_file);
        else
            CustomError::response("view not found: {$view}", 404);
    }
}