<?php echo form_open('billing/diff_tariff_year'); ?>
    <fieldset>
        <legend>Годовой отчет по дифф.тарифу</legend>
        <select name="period_year" id="">
            <?php foreach ($period_years as $year): ?>
                <option value="<?php echo $year->period_year; ?>"><?php echo $year->period_year; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Выдать">
    </fieldset>
<?php echo form_close(); ?>