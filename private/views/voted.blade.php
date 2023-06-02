<!DOCTYPE html>
<html lang="eu">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
<title>Success</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible"         content="IE=Edge">
<meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline' 'unsafe-eval' data: blob:;">
<meta name="viewport"                      content="width=device-width, initial-scale=1">
</head>
<body>
    <h1>Success</h1>
    <h2>Save this page / Print screen</h2>
    <ul>
        <?php foreach($params as $key => $value):?>
        <li><?="{$key}: {$value}" ?></li>
        <?php endforeach ?>
    </ul>
    <h2>QR Code</h2>
    <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&choe=UTF-8&chl=<?=urlencode("https://{$_SERVER['SERVER_NAME']}/?csrf={$params['csrf']}")?>}">
</body>
</html>