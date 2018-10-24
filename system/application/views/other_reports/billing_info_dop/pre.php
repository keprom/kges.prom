<?php echo form_open("billing/billing_info_dop"); ?>
    <fieldset>
        <legend>Информация по точкам учета, ТУРЭ:</legend>
        <select name="ture_id" id="">
            <option value="-1">Все</option>
            <?php foreach ($ture as $t): ?>
                <option value="<?php echo $t->id; ?>"><?php echo $t->name; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Открыть">
        <label for="to_excel"><input type="checkbox" name="to_excel" value="1">в Excel</label>
    </fieldset>
<?php echo form_close(); ?>