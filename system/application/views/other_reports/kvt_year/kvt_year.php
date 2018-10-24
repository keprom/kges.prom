<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="<?php echo base_url() . 'css/fullpage.css'; ?>">
</head>
<body>
<table class="border-table">
    <caption>Свод кВт за <?php echo $report[0]->period_year; ?> год</caption>
    <thead>
    <tr>
        <th>Дог</th>
        <th>Фирма</th>
        <th>Январь</th>
        <th>Февраль</th>
        <th>Март</th>
        <th>Апрель</th>
        <th>Май</th>
        <th>Июнь</th>
        <th>Июль</th>
        <th>Август</th>
        <th>Сентябрь</th>
        <th>Октябрь</th>
        <th>Ноябрь</th>
        <th>Декабрь</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($report as $r): ?>
    <tr>
        <td><?php echo $r->dogovor; ?></td>
        <td><?php echo $r->name; ?></td>
        <td class="td-number"><?php echo prettify_number($r->m1); ?></td>
        <td class="td-number"><?php echo prettify_number($r->m2); ?></td>
        <td class="td-number"><?php echo prettify_number($r->m3); ?></td>
        <td class="td-number"><?php echo prettify_number($r->m4); ?></td>
        <td class="td-number"><?php echo prettify_number($r->m5); ?></td>
        <td class="td-number"><?php echo prettify_number($r->m6); ?></td>
        <td class="td-number"><?php echo prettify_number($r->m7); ?></td>
        <td class="td-number"><?php echo prettify_number($r->m8); ?></td>
        <td class="td-number"><?php echo prettify_number($r->m9); ?></td>
        <td class="td-number"><?php echo prettify_number($r->m10); ?></td>
        <td class="td-number"><?php echo prettify_number($r->m11); ?></td>
        <td class="td-number"><?php echo prettify_number($r->m12); ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>