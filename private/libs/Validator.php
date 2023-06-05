<?php
require_once('Config.php');
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
        if(Db::exists(['DNI' => $dni])>0)
            CustomError::response("DNI already exists in database", $code = '401');
    }
    
    private static function dniPrepare($dni)
    {
        $dni = strtoupper($dni);
        $dni = preg_replace("/[^A-Z0-9]+/", '', $dni);
        return $dni;
    }
    
    public static function sanitizeParams($params)
    {   
        if(isset($params))
        {
            foreach ($params as $key => $value)
                $params[$key] = preg_replace("/[^a-z0-9]+/i", '', $params[$key]);
            return $params;
        }
        return NULL;
    }

    private function allowed($params)
    {
        $allowed =
        [
            'answer',
            'dni',
            'csrf',
            'fingerprint'
        ];

        foreach($params as $key => $value)
        {
            if(!in_array(strtolower($key), $allowed))
                unset($params[$key]);
        }
        return $params;
    }    
    
    private static function required($params)
    {
        $required =
        [
            'answer',
            'dni',
            'csrf',
            'fingerprint'
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
            $params['REGISTER_DATE'] = date('Y-m-d H:i:s');
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
        if(Db::getNameByValue($value)!==NULL)
            return true;
        CustomError::response('Error value not exists', 500);
    }

    private static function addExtraParams($params)
    {
        $ips = [];
        
        if(!empty($_SERVER['REMOTE_ADDR']))
            $ips[] = $_SERVER['REMOTE_ADDR'];
        if(!empty($_SERVER['HTTP_CLIENT_IP']))
            $ips[] = $_SERVER['HTTP_CLIENT_IP'];
        if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ips[] = $_SERVER['HTTP_X_FORWARDED_FOR'];            

        if(count($ips)>0)
            $params['IP'] = (count($ips)>0)?implode('|', $ips):'';

        return $params;
    }

    public static function stringHide($string, $count = 1)
    {
        $replace = str_repeat('*', $count);
        return preg_replace("/^[0-9]{".$count."}/", $replace, $string);
    }

    private function fingerprint($value)
    {
        $config                     = Config::get();
        $fingerprint_repeat_allowed = (int)$config['fingerprint_repeat_allowed'];
        $count                      = Db::exists(['FINGERPRINT' => $value]);

        if($count>=$fingerprint_repeat_allowed)
            CustomError::response('Max Fingerprint value reached', 405);
    }
    
    public function request()
    {
        // post parametroak garbitu
        $params = self::sanitizeParams($_POST);

        // csrf kodea
        self::CSRF($params['csrf']);

        // session timeout
        Session::verifyLifeTime();

        // derrigorrezko parametroak
        self::required($params);

        // behar ditugun parametroak bakarrik utzi
        $params = self::allowed($params);

        // fingerprint
        self::fingerprint($params['fingerprint']);

        // parametro egokiak
        self::valueExists($params['answer']);
    
        // dni
        $dni                   = self::dni($params['dni']);
        $params['dni']         = $dni;
        self::dniDb($dni);
    
        // datu basea
        self::insert($params);

        // Datu baseko baloreak JSON-eko datuekin batu
        $params['answer_name'] = Db::getNameByValue($params['answer']);

        // DNI-a ezkutatu
        $params['dni']         = self::stringHide($params['dni'], 2);

        return $params;
    }    
}