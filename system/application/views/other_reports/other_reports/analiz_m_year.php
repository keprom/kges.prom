<?php
function f_d($var)
{
    if ($var == 0) return "&nbsp;"; else {
        if ($var == null) return "&nbsp;"; else
            return sprintf("%22.2f", $var);
    }
}

function datetostring($date)
{
    $d = explode("-", $date);
    return $d['0'] . '.' . $d['1'] . '.' . $d['2'];
}

?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
</head>
<body>

<center>
    <b>Анализ учета полученной товарной продукции ( электроэнергии )<br> по
        <?php echo $org->org_name; ?> <br> с применением многоуровневого тарифа за
        <?php echo $diff[0]->period_year; ?> год</b>
</center>
<br>
<br>

<table width=100% border=1px cellspacing=0px style="border: black;font-family: Verdana; font-size: x-small;">
    <tr align=center>
        <td rowspan=3>№
        </td>
        <td rowspan=3>Наименование потребителя
        </td>
        <td colspan=6>Получено товарной продукции
        </td>
        <td rowspan=3 width=80px>Всего кВт/ч
        </td>
        <td rowspan=3 width=100px>Всего тенге (без НДС)
        </td>
        <td rowspan=3 width=100px>Всего тенге (с НДС)
        </td>
        <td rowspan=3 width=110px>По ср. отпускному тарифу (с НДС 15.39)
        </td>
        <td rowspan=3 width=110px>Разница (+,-)
        </td>
    </tr>
    <tr>
        <td colspan=2>
            По 1 уровню (13.652)
        </td>
        <td colspan=2>
            По 2 уровню (16.964)
        </td>
        <td colspan=2>
            По 3 уровню (21.214)
        </td>
    </tr>
    <tr>
        <td>
            кВт/ч
        </td>
        <td>
            тенге
        </td>
        <td>
            кВт/ч
        </td>
        <td>
            тенге
        </td>
        <td>
            кВт/ч
        </td>
        <td>
            тенге
        </td>


    </tr>
    <?php
    $sum_ur1_kvt = 0;
    $sum_ur1_tenge = 0;
    $sum_ur2_kvt = 0;
    $sum_ur2_tenge = 0;
    $sum_ur3_kvt = 0;
    $sum_ur3_tenge = 0;
    $sum_bez_nds = 0;
    $sum_with_nds = 0;
    $vsego_kvt = 0;
    $po_otp_tarif = 0;
    $buf_sum_with_nds = 0;
    $buf_sum_otp_nds = 0;
    $buf_vsego_kvt = 0;

    $j = 1;
    foreach ($diff as $d):

        $sum_ur1_kvt += $d->itogo_kvt_ur1;
        $sum_ur1_tenge += $d->tenge_ur1;
        $sum_ur2_kvt += $d->itogo_kvt_ur2;
        $sum_ur2_tenge += $d->tenge_ur2;
        $sum_ur3_kvt += $d->itogo_kvt_ur3;
        $sum_ur3_tenge += $d->tenge_ur3;
        $buf_sum_with_nds = 0;
        $buf_sum_otp_nds = 0;
        $buf_vsego_kvt = 0;

        $sum_bez_nds += ($d->tenge_ur1 + $d->tenge_ur2 + $d->tenge_ur3);
        $sum_with_nds += (($d->tenge_ur1 + $d->tenge_ur2 + $d->tenge_ur3) * (100 + $d->nds) / 100);
        $vsego_kvt += ($d->itogo_kvt_ur1 + $d->itogo_kvt_ur2 + $d->itogo_kvt_ur3);
        $buf_vsego_kvt += ($d->itogo_kvt_ur1 + $d->itogo_kvt_ur2 + $d->itogo_kvt_ur3);
        $buf_sum_with_nds += (($d->tenge_ur1 + $d->tenge_ur2 + $d->tenge_ur3) * (($d->nds + 100) / 100));
        $buf_sum_otp_nds = ($buf_vsego_kvt) * 15.21;

        ?>
        <tr>
            <td>
                <?php echo $j++; ?>
            </td>
            <td>
                <?php echo $d->firm_subgroup_name; ?>
            </td>
            <td>
                <?php echo f_d($d->itogo_kvt_ur1); ?>
            </td>
            <td>
                <?php echo f_d($d->tenge_ur1); ?>
            </td>
            <td>
                <?php echo f_d($d->itogo_kvt_ur2); ?>
            </td>
            <td>
                <?php echo f_d($d->tenge_ur2); ?>
            </td>
            <td>
                <?php echo f_d($d->itogo_kvt_ur3); ?>
            </td>
            <td>
                <?php echo f_d($d->tenge_ur3); ?>
            </td>

            <td>
                <?php echo f_d($buf_vsego_kvt); ?>
            </td>
            <td>
                <?php echo f_d($d->tenge_ur1 + $d->tenge_ur2 + $d->tenge_ur3); ?>
            </td>
            <td>
                <?php echo f_d($buf_sum_with_nds); ?>
            </td>
            <td>
                <?php echo f_d($buf_sum_otp_nds); ?>
            </td>
            <td>
                <?php echo f_d($buf_sum_with_nds - $buf_sum_otp_nds); ?>
            </td>

        </tr>
    <?php endforeach; ?>
    <tr>
        <td>&nbsp;
        </td>
        <th align=right>
            <b>Итого</b>
        </th>
        <th align=right>
            <b><?php echo f_d($sum_ur1_kvt); ?></b>
        </th>
        <th align=right>
            <b><?php echo f_d($sum_ur1_tenge); ?></b>
        </th>
        <th align=right>
            <b><?php echo f_d($sum_ur2_kvt); ?></b>
        </th>
        <th align=right>
            <b><?php echo f_d($sum_ur2_tenge); ?></b>
        </th>
        <th align=right>
            <b><?php echo f_d($sum_ur3_kvt); ?></b>
        </th>

        <th align=right>
            <b><?php echo f_d($sum_ur3_tenge); ?></b>
        </th>
        <th align=right>
            <b><?php echo f_d($sum_ur1_kvt + $sum_ur2_kvt + $sum_ur3_kvt); ?></b>
        </th>
        <th align=right>
            <b><?php echo f_d($sum_ur1_tenge + $sum_ur2_tenge + $sum_ur3_tenge); ?></b>
        </th>
        <th align=right>
            <b><?php echo f_d($sum_with_nds); ?></b>
        </th>
        <th align=right>
            <b><?php echo f_d($vsego_kvt * 15.21); ?></b>
        </th>
        <th align=right>
            <b><?php echo f_d($sum_with_nds - ($vsego_kvt * 15.21)); ?></b>
        </th>

    </tr>
</table>
<br>
<table width=100% style="border: black;font-family: Verdana; font-size:  small;">
    <tr align=center>
        <td> Директор филиала ТОО КЭЦ Горэлектросети</td>
        <td> <?php echo $org->director; ?></td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr align=center>
        <td> Зам. Директора по сбыту</td>
        <td> <?php echo $org->zam_directora_po_sbytu; ?></td>
    </tr>
</body>
</html>