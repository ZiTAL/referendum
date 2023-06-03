<?php
function dni($dni)
{
        if (preg_match('/^[XYZ]?\d{7,8}[A-Z]$/', $dni))
        {
                $map             = 'TRWAGMYFPDXBNJZSQVHLCKE';
                $letter          = substr($dni, -1);
                $number          = substr($dni, 0, -1);
                $expected_letter = $map[$number % 23];
                if ($letter === $expected_letter)
                        return $dni;
        }
        return false;
}

function generateDnis($dni, $char = '*', $index = 0, $current_dni = '')
{
    $dnis = [];

    if ($index >= strlen($dni))
    {
        $dnis[] = $current_dni;
        return $dnis;
    }

    if ($dni[$index] === '*')
    {
        for ($i = 0; $i <= 9; $i++)
        {
            $current_dni .= $i;
            $dnis         = array_merge($dnis, generateDnis($dni, $char, $index + 1, $current_dni));
            $current_dni  = substr($current_dni, 0, -1);
        }
    }
    else
    {
        $current_dni .= $dni[$index];
        $dnis         = array_merge($dnis, generateDnis($dni, $char, $index + 1, $current_dni));
    }
    return $dnis;
}

$dni     = "**345678A";
$dnis = generateDnis($dni, '*');
foreach($dnis as $d)
{
        if(dni($d))
                echo "Valid: {$d}\n";
}
