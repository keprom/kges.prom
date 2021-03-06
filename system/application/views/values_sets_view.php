<?php echo anchor(base_url()."/billing/counter/{$counter_id}","Назад к списку тарифов" )."";?><br><br>
<b><?php echo $firm->dogovor." ".$firm->name; ?></b><br>
<H4>Показания счетчика по тарифу <?php echo $sets_type; ?></H4>
Коэффициент трансформации:
<?php
echo $counter_data->transform;
?>&nbsp;&nbsp;<br>
Разрядность счетчика:
<?php
echo $counter_data->digit_count;
?><br>
Гос номер счетчика:
<?php
echo $counter_data->gos_nomer;

function datetostring($date)
{
    $d = explode("-", $date);
    return $d['2'] . '/' . $d['1'] . '/' . $d['0'];
}

?>
<br><br>
<table style="border-collapse: collapse">
    <tr align=center>
        <td><b>
                <small>Показание</small>
            </b></td>
        <td>
            <small><b>Разность</small>
            </b> </td>
        <td><b>
                <small>Дата показания</small>
            </b></td>
        <td><b>
                <small>Тариф</small>
            </b></td>
        <td><b>
                <small>НДС</small>
            </b></td>
        <td><b>
                <small>Итого квт</small>
            </b></td>
        <td><b>
                <small>Итого тенге</small>
            </b></td>
        <td><b>
                <small>Итого НДС</small>
            </b></td>
        <td><b>
                <small>Сумма</small>
            </b></td>
    </tr>
    <?php foreach ($query->result() as $r): ?>
        <?php
        $itogo_kvt = $counter_data->transform * $r->diff;
        $itogo_tenge = $counter_data->transform * $r->diff * $r->tariff_value;
        $itogo_nds = $counter_data->transform * $r->diff * $r->tariff_value * ($r->nds / 100);
        $summa = $counter_data->transform * $r->diff * $r->tariff_value * ($r->nds / 100 + 1);
        ?>
        <tr class="tr-hover">
            <td align="right" style="border: 1px solid #c5cae9;">
                <small> <?php echo $r->value; ?> </small>
            </td>
            <td align="right" style="border: 1px solid #c5cae9;">
                <small> <?php echo $r->diff; ?> </small>
            </td>
            <td align="center" style="border: 1px solid #c5cae9;">
                <small> <?php echo datetostring($r->data); ?></small>
            </td>
            <td align="right" style="border: 1px solid #c5cae9;">
                <small> <?php echo prettify_number($r->tariff_value); ?> </small>
            </td>
            <td  style="border: 1px solid #c5cae9;">
                <small> <?php echo prettify_number($r->nds); ?>%</small>
            </td>
            <td align="right" style="border: 1px solid #c5cae9;">
                <small> <?php echo prettify_number($itogo_kvt); ?> </small>
            </td>
            <td align="right" style="border: 1px solid #c5cae9;">
                <small> <?php echo prettify_number($itogo_tenge); ?> </small>
            </td>
            <td align="right" style="border: 1px solid #c5cae9;">
                <small> <?php echo prettify_number($itogo_nds); ?> </small>
            </td>
            <td align="right" style="border: 1px solid #c5cae9;">
                <small> <?php echo prettify_number($summa); ?>  </small>
            </td>
            <td align="right" style="border: 1px solid #c5cae9;"><?php echo anchor("billing/delete_pokazanie/" . $r->id, "<img src=" . base_url() . "img/delete.png />"); ?></td>

        </tr>
    <?php endforeach; ?>
</table>
<br><br>

