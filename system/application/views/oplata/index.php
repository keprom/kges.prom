<html>
<head>
    <title>Работа с оплатой</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="shortcut icon" type="image/png" href="<?php echo base_url(); ?>img/favicon.png"/>
    <link rel="stylesheet" href="<?php echo base_url() . 'css/fullpage.css'; ?>">
</head>
<body>
<?php
function f_d($var)
{
    if ($var == 0) return "&nbsp;"; else
        return sprintf("%22.2f", $var);
} ?>
<h2>Работа с оплатой</h2>
<?php if ($this->session->flashdata('added') == 'true') echo "<h3>Оплата добавлена по орг-ции:" . $this->session->flashdata('firm_name') . "</h3>"; ?>
<br>

<?php echo form_open('billing/adding_oplata'); ?>
<fieldset style="display: inline">
    <legend>Добавить оплату</legend>
    <table>
        <tbody>
        <tr>
            <td>Дата</td>
            <td>
                <input class="block" type="date" name=data value='<?php echo $this->session->userdata('data'); ?>'>
            </td>
        </tr>
        <tr>
            <td>Номер счета</td>
            <td>
                <input class="block" name=payment_number value='<?php echo $this->session->userdata('number'); ?>'>
            </td>
        </tr>
        <tr>
            <td>Договор</td>
            <td>
                <input class="block" name=dogovor><br>
            </td>
        </tr>
        <tr>
            <td>Сумма</td>
            <td>
                <input class="block" name=value>
            </td>
        </tr>
        <tr>
            <td>Номер документа</td>
            <td><input class="block" name=document_number>
            </td>
        </tr>
        <tr>
            <td>Оплата</td>
            <td>
                <select class="block" name="pay_type" id="">
                    <option value="1">электроэнергии</option>
                    <option value="2">пени</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input name=nds type=hidden value='12'>
                <input class="block" type=submit value="Добавить">
            </td>
        </tr>
        </tbody>
    </table>

</fieldset>
<?php echo form_close(); ?>


<br>

<fieldset style="display: inline">
    <legend>Установить период</legend>
    <?php echo form_open('billing/change_oplata_period'); ?>
    <input type="date" name=begin_data value='<?php echo $this->session->userdata('begin_data'); ?>'>
    <input type="date" name=end_data value='<?php echo $this->session->userdata('end_data'); ?>'>
    <input type=submit value=Подтвердить>
    <?php echo form_close(); ?>
</fieldset>


<br>
<br>
<br>


<?php $last_opl_data = -1; ?>
<table class="border-table stripped-table">
    <thead>
    <tr>
        <th>Номер<br>договора</th>
        <th>Дата<br>оплаты</th>
        <th>Номер<br>счета</th>
        <th>Сумма<br>оплаты</th>
        <th>НДС</th>
        <th>Сумма<br>без НДС</th>
        <th>Номер<br>документа</th>
        <th>X</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($oplata->result() as $o): ?>
        <?php if ($o->data != $last_opl_data): ?>
            <tr>
                <td colspan="8" align="center" style="background-color: #4F5155; color: white;">
                    <b><?php echo $o->data; ?></b></td>
            </tr>
            <?php $last_opl_data = $o->data; ?>
        <?php endif; ?>
        <tr>
            <td><?php echo $o->dogovor; ?></td>
            <td><?php echo $o->data; ?></td>
            <td><?php echo $o->number; ?></td>
            <td class="td-number"><?php echo prettify_number($o->value * 1.12); ?></td>
            <td class="td-number"><?php echo prettify_number($o->value * 0.12); ?></td>
            <td class="td-number"><?php echo prettify_number($o->value); ?></td>
            <td align="right"><?php echo $o->document_number; ?></td>
            <td><a href="<?php echo site_url("billing/oplata_delete/{$o->id}"); ?>">X</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!--FINE-->
<br>
<br>
<?php $last_opl_data = -1; ?>
<?php if (isset($fine_oplata)): ?>
    <table id="fine" class="border-table stripped-table">
        <caption>Пеня</caption>
        <thead>
        <tr>
            <th>Номер<br>договора</th>
            <th>Дата<br>оплаты</th>
            <th>Номер<br>счета</th>
            <th>Сумма<br>оплаты</th>
            <th>НДС</th>
            <th>Сумма<br>без НДС</th>
            <th align=right>Номер<br>документа</th>
            <th>X</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($fine_oplata as $fo): ?>
            <?php if ($fo->data != $last_opl_data): ?>
                <tr>
                    <td colspan="8" align="center" style="background-color: #4F5155; color: white;">
                        <b><?php echo $fo->data; ?></b></td>
                </tr>
                <?php $last_opl_data = $fo->data; ?>
            <?php endif; ?>
            <tr>
                <td><?php echo $fo->dogovor; ?></td>
                <td><?php echo $fo->data; ?></td>
                <td><?php echo $fo->number; ?></td>
                <td class="td-number"><?php echo prettify_number($fo->value * 1.12); ?></td>
                <td class="td-number"><?php echo prettify_number($fo->value * 0.12); ?></td>
                <td class="td-number"><?php echo prettify_number($fo->value); ?></td>
                <td align="right"><?php echo $fo->document_number; ?></td>
                <td><a href="<?php echo site_url("billing/fine_oplata_delete/{$fo->id}"); ?>">X</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<!--END FINE-->

</body>
</html>