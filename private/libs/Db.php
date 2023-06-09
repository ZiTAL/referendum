<?php
require_once('Config.php');
require_once('CustomError.php');

class Db
{
    private static $instance;

    private static function config()
    {
        return Config::get();
    }

    public static function create()
    {
        $config   = self::config();
        $instance = self::getInstance();

        $query    = "CREATE TABLE IF NOT EXISTS {$config['db']['table']} (\n";

        $keys = array_keys($config['db']['fields']);
        foreach ($keys as $index => $key)
        {
            if($index>0)
                $query.=", \n";
            $query.= "{$key} {$config['db']['fields'][$key]}";
        }
        $query    .= "\n)";

        // Execute the query to create the table
        $instance->exec($query);        
    }

    public static function drop()
    {
        $config   = self::config();
        $instance = self::getInstance();
        $query    = "DROP TABLE {$config['db']['table']}";
        $instance->exec($query);        
    }    

    public static function exists($array)
    {
        $config   = self::config();
        $instance = self::getInstance();
        $query    = "SELECT count(*) as count FROM {$config['db']['table']} ";
        $query   .= self::buildSelectWhere($array);

        $stmt     = $instance->prepare($query);
        foreach($array as $key => $value)
            $stmt->bindValue(":{$key}", $value);

        $result   = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $stmt->close();

        if($row['count']>0)
            return (int)$row['count'];
        return false;
    }

    public static function insert($params)
    {
        try
        {
            $config                         = self::config();
            $instance                       = self::getInstance();
            $params['PREVIOUS_RECORD_HASH'] = self::getPreviousRecordHash();
            $query                      = "
                INSERT INTO {$config['db']['table']}
                (".implode(', ', array_keys($params)).")
                VALUES
                (:".implode(', :', array_keys($params)).")";

            $stmt                       = $instance->prepare($query);
    
            foreach($params as $key => $value)
                $stmt->bindValue(":{$key}", $value);
    
            $stmt->execute();
        }
        catch(Exception $e)
        {
            CustomError::response('Error connecting to the database', 500);
        }        
    }

    public static function get()
    {
        try
        {
            $config   = self::config();
            $instance = self::getInstance();

            $query    = "SELECT * FROM {$config['db']['table']}";
            $stmt     = $instance->prepare($query);
            if(!$stmt)
                CustomError::response('Error connecting to the database', 500);

            $result   = $stmt->execute();    
            $rows     = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC))
            {
                $row['ANSWER_NAME'] = Db::getNameByValue($row['ANSWER']);
                $rows[] = $row;
            }
    
            $stmt->close();
            return $rows;
        }
        catch(Exception $e)
        {
            CustomError::response('Error connecting to the database', 500);
        }        
    }

    public static function getPreviousRecord()
    {
        $config   = self::config();
        $instance = self::getInstance();
        $key      = array_keys($config['db']['fields'])[0];
        $query    = "SELECT * FROM {$config['db']['table']} ORDER BY {$key} DESC LIMIT 1";
        $result   = $instance->query($query);
        $previous = $result->fetchArray(SQLITE3_ASSOC);

        if($previous!==false)
        {
            $previous['ANSWER_NAME'] = Db::getNameByValue($previous['ANSWER']);
            return $previous;
        }
        else
            return '';
    }

    public static function getRecord($params)
    {
        $config   = self::config();
        $instance = self::getInstance();        
        $where    = self::buildSelectWhere($params);
        $query    = "SELECT * FROM {$config['db']['table']} {$where} LIMIT 1";

        $stmt     = $instance->prepare($query);
        foreach($params as $key => $value)
            $stmt->bindValue(":{$key}", $value);

        $result      = $stmt->execute();
        $row         = $result->fetchArray(SQLITE3_ASSOC);
        if($row!==false)
        {
            $row['ANSWER_NAME'] = Db::getNameByValue($row['ANSWER']);
            return $row;
        }
        $stmt->close();
        CustomError::response('Record not found', 404);
        return NULL;
    }

    public static function getPreviousRecordHash()
    {
        $previous = self::getPreviousRecord();
        $hash     = ($previous!=='')?self::hash($previous):'';
        return $hash;
    }

    public static function hash($row)
    {
        return hash('sha256', serialize($row));
    }

    public static function getInstance()
    {
        try
        {
            $config = self::config();

            if(!self::$instance)
                self::$instance = new SQLite3(__DIR__."/../db/{$config['db']['file']}");

            return self::$instance;
        }
        catch(Exception $e)
        {
            CustomError::response('Error connecting to the database', 500);
        }
    }

    private static function buildSelectWhere($array)
    {
        $query = 'WHERE ';
        $keys = array_keys($array);
        foreach ($keys as $index => $key)
        {
            if($index>0)
                $query.="AND \n";
            $query.= "{$key} = :{$key}";
        }
        $query    .= "\n";
        return $query;
    }

    public static function getNameByValue($value)
    {
        $config  = self::config();
        $answers = $config['db']['values']['ANSWER'];
        foreach($answers as $answer)
        {
            if($answer['value']==$value)
                return $answer['name'];
        }
        return NULL;
    }
}