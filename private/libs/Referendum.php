<?php

class Referendum
{
    public static function get()
    {
        $file  = __DIR__."/../db/referendum.json";
        $file  = realpath($file);
        $file  = file_get_contents($file);
        $array = json_decode($file, true);
        return $array;
    }

    public static function getNameByValue($value)
    {
        $referendum = self::get();
        foreach($referendum['answers'] as $answer)
        {
            if($answer['value']==$value)
                return $answer['name'];
        }
        return NULL;
    }
}