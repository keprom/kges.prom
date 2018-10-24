<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo base_url() . 'css/fullpage.css'; ?>">
    <title>Document</title>
</head>
<body>
<h2>Сведения о потреблении элетроэнергии по договору ФЛ(ИП, ЮЛ)</h2>
<table class="border-table">
    <tr>
        <td><?php echo $report[0]->period_year; ?> год</td>
        <?php foreach ($report as $r): ?>
            <td><?php echo $r->period_name; ?></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td> в кВт</td>
        <?php foreach ($report as $r): ?>
            <td class="td-number"><?php echo prettify_number($r->sum_itog_kvt); ?></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td> в тенге</td>
        <?php foreach ($report as $r): ?>
            <td class="td-number"><?php echo prettify_number($r->sum_itog_tenge); ?></td>
        <?php endforeach; ?>
    </tr>
</table>
</body>
</html>