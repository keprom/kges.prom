<link rel="shortcut icon" type="image/png" href="<?php echo base_url(); ?>img/favicon.png"/>
<link rel="stylesheet" href="<?php echo base_url() . 'css/fullpage.css'; ?>">
<?php if (empty($do)): ?>
<h2>Нет дублирующихся оплат!</h2>
<?php else: ?>
    <table class="border-table">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Дог</th>
            <th>Наименование</th>
            <th>Сумма с НДС</th>
            <th>Х</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($do as $o): ?>
            <tr>
                <td align="center"><?php echo $o->oplata_data; ?></td>
                <td align="center"><?php echo $o->firm_dogovor; ?></td>
                <td><?php echo $o->firm_name; ?></td>
                <td class="td-number"><?php echo prettify_number($o->oplata_s_nds); ?></td>
                <td><a href="<?php echo site_url("billing/do_delete/{$o->oplata_id}"); ?>">X</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

