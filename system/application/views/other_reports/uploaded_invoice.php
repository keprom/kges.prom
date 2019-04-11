<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Выгруженные счет-фактуры в файл за <?php echo $period->name; ?></title>
    <link rel="stylesheet" href="/css/fullpage.css" type="text/css">
    <link rel="shortcut icon" type="image/png" href="/img/favicon.png"/>
</head>
<body>
<table class="border-table">
    <caption> Выгруженные счет-фактуры в файл за <?php echo $period->name; ?> <button onclick="window.print()">Печать</button></caption>
    <thead>
    <tr>
        <th>№</th>
        <th>Дог.</th>
        <th>Наименование</th>
        <th>кВт</th>
        <th>Тариф</th>
        <th>Без НДС</th>
        <th>НДС</th>
        <th>С НДС</th>
        <th>Номер</th>
    </tr>
    </thead>
    <tbody>
    <?php $i = 1; ?>
    <?php foreach ($invoice as $inv): ?>
        <tr class="tr-hover">
            <td><?php echo $i++; ?></td>
            <td><?php echo $inv->dog; ?></td>
            <td><?php echo anchor("billing/firm/$inv->firm_id", $inv->name); ?></td>
            <td class="td-number"><?php echo prettify_number($inv->kvt); ?></td>
            <td class="td-number"><?php echo prettify_number($inv->tarif); ?></td>
            <td class="td-number"><?php echo prettify_number($inv->beznds); ?></td>
            <td class="td-number"><?php echo prettify_number($inv->nds); ?></td>
            <td class="td-number"><?php echo prettify_number($inv->snds); ?></td>
            <td class="td-number"><?php echo $inv->nomer; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>