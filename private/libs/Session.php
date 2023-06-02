<?php
require_once('Config.php');
require_once('CustomError.php');

class Session
{
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
            $config  = Config::get();
            $timeout = (int)$config['timeout'];

            $now = time();
            $c = $now - (int)$_SESSION['timestamp_start'];
            if($c<=$timeout)
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