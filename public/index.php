<?php
$PRIVATE = __DIR__."/../private";
$LIBS    = "{$PRIVATE}/libs";

require_once("{$LIBS}/Config.php");
require_once("{$LIBS}/Validator.php");
require_once("{$LIBS}/Db.php");
require_once("{$LIBS}/View.php");

$_GET = Validator::sanitizeParams($_GET);

if($_POST)
{
    $params = Validator::request();
    View::load('voted.blade.php', $params);
    exit();
}
else if(isset($_GET['csrf']))
{
    $params        = Db::getRecord(['CSRF' => $_GET['csrf']]);
    $params['DNI'] = Validator::stringHide($params['DNI'], 2);

    View::load('csrf.blade.php', $params);
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

    $rows = Db::get();
    $rows = array_map(function($row)
    {
        $row['DNI'] = Validator::stringHide($row['DNI'], 2);
        return $row;
    }, $rows);

    $params =
    [
        'csrf'  => $csrf,
        'array' => $array,
        'rows'  => $rows
    ];

    View::load('index.blade.php', $params);
    exit();
}