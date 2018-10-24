<?php echo form_open('billing/report_2000'); ?>
    <fieldset>
        <legend>Отчет по потребленным выше 2000кВт</legend>
        <select name="period_year" id="">
            <?php foreach ($period_years as $year): ?>
                <option value="<?php echo $year->period_year; ?>"><?php echo $year->period_year; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Выдать">
    </fieldset>
<?php echo form_close(); ?>