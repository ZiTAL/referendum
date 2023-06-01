<?php
require_once('Referendum.php');
require_once('CustomError.php');

class Db
{
    private static $instance;

    public static function create()
    {
        $instance = self::getInstance();
        $query    = "CREATE TABLE IF NOT EXISTS REFERENDUM (
            ID INTEGER PRIMARY KEY AUTOINCREMENT,
            DNI TEXT NOT NULL,
            ANSWER INTEGER NOT NULL,
            REGISTER_DATE DATE
        )";

        // Execute the query to create the table
        $instance->exec($query);        
    }

    public static function drop()
    {
        $instance = self::getInstance();
        $query    = "DROP TABLE REFERENDUM";
        $instance->exec($query);        
    }    

    public static function exists($dni)
    {
        $instance = self::getInstance();
        $query    = "SELECT count(*) as count FROM REFERENDUM WHERE DNI = :dni";
        $stmt     = $instance->prepare($query);
        $stmt->bindValue(':dni', $dni);
        $result   = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $stmt->close();

        if($row['count']>0)
            return true;
        return false;
    }

    public static function insert($params)
    {
        try
        {
            $params['register_date'] = date('Y-m-d H:i:s');

            $instance = self::getInstance();
            $query    = "INSERT INTO REFERENDUM (DNI, ANSWER, REGISTER_DATE) VALUES (:dni, :answer, :register_date)";
            $stmt     = $instance->prepare($query);
    
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
            $instance = self::getInstance();
            $query    = "SELECT * FROM REFERENDUM ORDER BY REGISTER_DATE DESC";
            $stmt     = $instance->prepare($query);
            if(!$stmt)
                CustomError::response('Error connecting to the database', 500);

            $result   = $stmt->execute();    
            $rows     = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC))
            {
                $row['NAME'] = Referendum::getNameByValue($row['ANSWER']);
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

    public static function getInstance()
    {
        try
        {
            if(!self::$instance)
                self::$instance = new SQLite3(__DIR__."/../db/referendum.db");

            return self::$instance;
        }
        catch(Exception $e)
        {
            CustomError::response('Error connecting to the database', 500);
        }
    }
}