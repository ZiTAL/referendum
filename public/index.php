<?php
$PRIVATE = '../private';
$LIBS    = "{$PRIVATE}/libs";
$VIEWS   = "{$PRIVATE}/views";

require_once("{$LIBS}/Config.php");
require_once("{$LIBS}/Validator.php");
require_once("{$LIBS}/Db.php");

if($_POST)
{
    $params = Validator::request();
    include("{$VIEWS}/voted.blade.php");    
    exit();
}
else if(isset($_GET['csrf']))
{
    echo "{$_GET['csrf']}";
    exit();
}
else
{
    Session::start();
    $csrf   = Session::getCSRF();
    $config = Config::get();
    $array  =
    [
        'question' => $config['question'],
        'answers'  => $config['db']['values']['ANSWER']
    ];
    $rows   = Db::get();
    include("{$VIEWS}/index.blade.php");
}