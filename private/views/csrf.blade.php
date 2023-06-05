<!DOCTYPE html>
<html lang="eu">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
<title>csrf: <?=$params['CSRF']?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible"         content="IE=Edge">
<meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline' 'unsafe-eval' data: blob:;">
<meta name="viewport"                      content="width=device-width, initial-scale=1">
</head>
<body>
    <h1>QR Code information</h1>
    <table>
        <?php foreach($params as $key => $value):?>
            <tr>
                <td><?=$key?>: </td>
                <td><?=$value?></td>
            </tr>
        <?php endforeach ?>
    </table>    
</body>
</html>