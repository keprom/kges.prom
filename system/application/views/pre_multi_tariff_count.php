<?php echo form_open("billing/multi_tariff_count"); ?>
ТУРЭ
<select name=ture_id>
<option value=-1>Без деления по турэ</option>
<?php foreach ($ture->result() as $t):?>
	<option value=<?php echo $t->id;?>><?php echo $t->name;?></option>
<?php endforeach;?>
</select>
<br>
<select name=type>
<option value=1>Без ИП</option>
<option value=2>Включая ИП</option>
</select>
<br>
<select name=dop>
<option value=1>С доп. полями</option>
<option value=2>Без доп. полей</option>
</select>
<br>
<input type=submit value='Выдать отчет' />
</form>
