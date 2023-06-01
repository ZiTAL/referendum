<?php
require_once('CustomError.php');

class Session
{
    private static $life_time = 1 * 60; // 1 min

    public static function start()
    {
        session_start();
        self::setTimestampStart();
    }

    public static function destroy()
    {
        session_destroy();
    }    

    public static function setTimestampStart()
    {
        if (empty($_SESSION['timestamp_start']))
            $_SESSION['timestamp_start'] = time();
    }

    public static function verifyLifeTime()
    {
        if(!empty($_SESSION['timestamp_start']))
        {
            $now = time();
            $c = $now - (int)$_SESSION['timestamp_start'];
            if($c<=self::$life_time)
                return true;
        }
        self::destroy();
        CustomError::response('Error timeout', 400);
    }

    public static function getCSRF()
    {
        self::start();
        if (empty($_SESSION['csrf']))
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf'];        
    }
}