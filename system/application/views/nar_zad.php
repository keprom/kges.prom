<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .border-table, .border-table td, .border-table tr {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head>
<body>
<h2>Снятие показания по ТУРЭ №7 "Красноярский" за май 2018 года</h4>
<table class="border-table">
    <thead>
    <tr>
        <td><b>№</b></td>
        <td><b>ТП</b></td>
        <td><b>Договор</b></td>
        <td><b>Наименование</b></td>
        <td><b>№ счетчика</b></td>
        <td><b>Тр-ры тока</b></td>
        <td><b>Показания</b></td>
    </tr>
    </thead>
    <tbody>
    <?php $i = 0; ?>
    <?php $last_tp_id = -1; ?>
    <?php foreach ($narzad as $n): ?>
        <tr>
            <?php
            if ($last_tp_id !== $n->tp_id) {
                $i++;
            }
            $last_tp_id = $n->tp_id;
            ?>
            <td><b><?php echo $i; ?></b></td>
            <td><b><?php echo $n->tp_name; ?></b></td>
            <td><b><?php echo $n->firm_dog; ?></b></td>
            <td><b><?php echo $n->firm_name; ?></b></td>
            <td><b><?php echo $n->gos_nomer; ?></b></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>тотал-дн-Т1</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>тотал-вч-Т2</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>тотал-нч-Т3</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>