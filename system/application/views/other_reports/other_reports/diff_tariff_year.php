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
<h2>
    <b>Ведомость учета полученной товарной продукции ( электроэнергии ) по филиалуТОО "Кокше Энерго Центр"
        Горэлектросети
        <br> за <?php echo $report[0]->period_year; ?> год</b>

</h2>
<table class="border-table">

    <tr>
        <td rowspan=3>№</td>
        <td rowspan=3>Наименование потребителя</td>
        <td colspan=8>Получено товарной продукции</td>
        <td rowspan=3>Начислено НДС тенге</td>
        <td rowspan=3>Всего с НДС тенге</td>
    </tr>
    <tr>
        <td colspan=2>По тарифу дневной</td>
        <td colspan=2>По тарифу ночной</td>
        <td colspan=2>По тарифу вечерний</td>
        <td colspan=2>Итого</td>
    </tr>
    <tr>
        <td>кВт/ч</td>
        <td>тенге</td>
        <td>кВт/ч</td>
        <td>тенге</td>
        <td>кВт/ч</td>
        <td>тенге</td>
        <td>кВт/ч</td>
        <td>тенге</td>

    </tr>

    <?php $j = 1; ?>
    <?php
    $skd = 0;
    $std = 0;
    $ske = 0;
    $ste = 0;
    $skn = 0;
    $stn = 0;
    $sum_k = 0;
    $sum_t = 0;
    $nds = 0;
    $s_nds = 0;

    ?>
    <?php foreach ($report as $r): ?>
        <tr>
            <td><?php echo $j++; ?></td>
            <td><?php echo $r->firm_subgroup_name; ?></td>
            <td class="td-number"><?php echo prettify_number($r->sum_kvt_day); ?></td>
            <td class="td-number"><?php echo prettify_number($r->sum_tenge_day); ?></td>

            <td class="td-number"><?php echo prettify_number($r->sum_kvt_night); ?></td>
            <td class="td-number"><?php echo prettify_number($r->sum_tenge_night); ?></td>

            <td class="td-number"><?php echo prettify_number($r->sum_kvt_evening); ?></td>
            <td class="td-number"><?php echo prettify_number($r->sum_tenge_evening); ?></td>

            <td class="td-number"><?php echo prettify_number($r->sum_kvt_day + $r->sum_kvt_evening + $r->sum_kvt_night); ?></td>
            <td class="td-number"><?php echo prettify_number($r->sum_tenge_night + $r->sum_tenge_evening + $r->sum_tenge_day); ?></td>

            <td class="td-number"><?php echo prettify_number(($r->sum_tenge_night + $r->sum_tenge_evening + $r->sum_tenge_day) * $r->nds / 100); ?></td>
            <td class="td-number"><?php echo prettify_number(($r->sum_tenge_night + $r->sum_tenge_evening + $r->sum_tenge_day) * ($r->nds + 100) / 100); ?></td>
        </tr>
        <?php
        $skd += $r->sum_kvt_day;
        $std += $r->sum_tenge_day;
        $ske += $r->sum_kvt_night;
        $ste += $r->sum_tenge_night;
        $skn += $r->sum_kvt_evening;
        $stn += $r->sum_tenge_evening;
        $sum_k += $r->sum_kvt_day + $r->sum_kvt_evening + $r->sum_kvt_night;
        $sum_t += $r->sum_tenge_night + $r->sum_tenge_evening + $r->sum_tenge_day;
        $nds += ($r->sum_tenge_night + $r->sum_tenge_evening + $r->sum_tenge_day) * $r->nds / 100;
        $s_nds += ($r->sum_tenge_night + $r->sum_tenge_evening + $r->sum_tenge_day) * ($r->nds + 100) / 100;
        ?>
    <?php endforeach; ?>
    <tr>
        <td></td>
        <td>ИТОГО</td>
        <td class="td-number"><?php echo prettify_number($skd); ?></td>
        <td class="td-number"><?php echo prettify_number($std); ?></td>
        <td class="td-number"><?php echo prettify_number($ske); ?></td>
        <td class="td-number"><?php echo prettify_number($ste); ?></td>
        <td class="td-number"><?php echo prettify_number($skn); ?></td>
        <td class="td-number"><?php echo prettify_number($stn); ?></td>
        <td class="td-number"><?php echo prettify_number($sum_k); ?></td>
        <td class="td-number"><?php echo prettify_number($sum_t); ?></td>
        <td class="td-number"><?php echo prettify_number($nds); ?></td>
        <td class="td-number"><?php echo prettify_number($s_nds); ?></td>

    </tr>
</table>
</body>
</html>