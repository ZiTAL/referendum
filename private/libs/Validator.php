<?php
require_once('Referendum.php');
require_once('Db.php');
require_once('Session.php');
require_once('CustomError.php');


class Validator
{    
    private static function dni($dni)
    { 
        $dni = self::dniPrepare($dni);
        if (preg_match('/^[XYZ]?\d{7,8}[A-Z]$/', $dni))
        {
            $map             = 'TRWAGMYFPDXBNJZSQVHLCKE';
            $letter          = substr($dni, -1);
            $number          = substr($dni, 0, -1);
            $expected_letter = $map[$number % 23];
            
            if ($letter === $expected_letter)
                return $dni;
        }
        //CustomError::response("DNI error", $code = '401');
        return $dni;
    }
    
    private static function dniDb($dni)
    {
        if(Db::exists($dni))
            CustomError::response("DNI already exists in database", $code = '401');
    }
    
    private static function dniPrepare($dni)
    {
        $dni = strtoupper($dni);
        $dni = preg_replace("/[^A-Z0-9]+/", '', $dni);
        return $dni;
    }
    
    private static function sanitizePostParams()
    {
        $params = [];
        
        foreach ($_POST as $key => $value)
            $params[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
        
        return $params;
    }
    
    private static function required($params)
    {
        $required =
        [
            'answer',
            'dni',
            'csrf'
        ];
        
        foreach($required as $r)
        {
            if(!in_array($r, array_keys($params)))
                CustomError::response("The fields required are: ".implode(', ', $required), 400);
        }
    }
    
    private static function CSRF($csrf)
    {
        Session::start();
        if (!empty($_POST['csrf']))
        {
            if (hash_equals($_SESSION['csrf'], $csrf))
                return true;
        }
        Session::destroy();
        CustomError::response("CSRF error", 401);
    }

    public static function getCSRF()
    {
        Session::start();
        return Session::getCSRF();
    }    
    
    private static function insert($params)
    {
        try
        {
            Db::insert($params);
            Session::destroy();
        }
        catch(Exception $e)
        {
            CustomError::response('Error inserting data into database', 500);
        }    
    }

    private static function valueExists($value)
    {
        if(Referendum::getNameByValue($value)!==NULL)
            return true;
        CustomError::response('Error value not exists', 500);
    }

    private static function sessionStart()
    {
        $life_time = 1 * 60; // 1 min
        ini_set('session.gc_maxlifetime', $life_time);
        session_set_cookie_params($life_time);
        session_start();
    }    

    private static function sessionDestroy()
    {
        session_destroy();
    }
    
    public function request()
    {
        // post parametroak garbitu
        $params = self::sanitizePostParams();

        // csrf kodea
        self::CSRF($params['csrf']);

        // session timeout
        Session::verifyLifeTime();
    
        // derrigorrezko parametroak
        self::required($params);

        // parametro egokiak
        self::valueExists($params['answer']);
    
        // dni
        $dni           = self::dni($params['dni']);
        $params['dni'] = $dni;
        self::dniDb($dni);
    
        // datu basea
        self::insert($params);
    }    
}