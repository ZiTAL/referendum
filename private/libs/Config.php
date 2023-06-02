<?php

class Config
{
    private static $instance;

    public static function get()
    {
        if(!self::$instance)
        {
            $file         = __DIR__."/../db/config.json";
            $file_content = file_get_contents($file);
            $config       = json_decode($file_content, true);
            self::$instance = $config;
        }
        return self::$instance;
    }
}