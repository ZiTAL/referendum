<?php
require_once('../libs/Db.php');
$all    = Db::get();
$length = count($all);

for($i=0; $i<$length; $i++)
{
    $current = $all[$i];
    if(isset($all[$i+1]))
    {
        $next         = $all[$i+1];
        $next_hash    = $next['PREVIOUS_RECORD_HASH'];
        $current_hash = Db::hash($current);
        if($current_hash===$next_hash)
        {
            echo "OK: ID: {$current['ID']} Hash:{$current_hash} - ID: {$next['ID']} Hash:{$next_hash}\n";
        }
        else
        {
            echo "ERROR: ID: {$current['ID']} Hash:{$current_hash} - ID: {$next['ID']} Hash:{$next_hash}\n";
            echo "REFERENDUM NOT VALID: DATABASE HAS BEEN MANIPULATED\n";
            exit();
        }
    }
}
echo "REFERENDUM IS VALID\n";