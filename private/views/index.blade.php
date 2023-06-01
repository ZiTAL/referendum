<!DOCTYPE html>
<html lang="eu">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
<title>Guzan - referendum</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible"          content="IE=Edge">
<meta http-equiv="Content-Security-Policy"  content="default-src * 'unsafe-inline' 'unsafe-eval' data: blob:;">
<meta name="viewport"                       content="width=device-width, initial-scale=1">
</head>
<style>
    table
    {
        width: 90%;
    }
    th, td
    {
        border: 1px solid black;        
        text-align: left;
    }
</style>
<body>
    <h1><?=$array['question']?></h1>
    <form method="post">
        DNI: <input type="text" name="dni" placeholder="12345678A" maxlength="9"><br>
        <?php foreach($array['answers'] as $answer):?>
        <input type="radio" name="answer" value="<?=$answer['value']?>"><?=$answer['name']?><br>
        <?php endforeach ?>
        <input type="hidden" name="csrf" value="<?=$csrf?>">
        <input type="submit">
    </form>
    <?php if(count($rows)>0): ?>
    <table>
        <tr>
            <?php foreach($rows[0] as $key => $value):?>
                <th><?=$key?></th>
            <?php endforeach ?>
        </tr>            
    <?php foreach($rows as $row): ?>
        <tr>
            <?php foreach($row as $key => $value):?>
                <td><?=$value?></td>
            <?php endforeach ?>
        </tr>
    <?php endforeach ?>
    </table>
    <?php endif ?>
</body>
</html>