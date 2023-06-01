<?php
$PRIVATE = '../private';
$LIBS    = "{$PRIVATE}/libs";
$VIEWS   = "{$PRIVATE}/views";

require_once("{$LIBS}/Validator.php");
require_once("{$LIBS}/Db.php");
require_once("{$LIBS}/Referendum.php");

if($_POST)
{
    $params = Validator::request();
    include("{$VIEWS}/voted.blade.php");    
}
else
{
    $csrf  = Session::getCSRF();
    $array = Referendum::get();
    $rows  = Db::get();
    include("{$VIEWS}/index.blade.php");
}