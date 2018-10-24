<?php echo form_open("billing/diff_tariff_spisok_3"); ?>
Период
<select name=period_id>
<?php foreach ($period->result() as $p):?>
	<option value=<?php echo $p->id;?>><?php echo $p->name;?></option>
<?php endforeach;?>
</select>
<br>
<select name=type >
	<option value = 1 >Выдать всех</option>
	<option value = 2 >Только ИП</option>
	<option value = 3 >Только ТОО</option>

</select>

<br>
<br>
<input type=submit value='Выдать отчет' />
</form>
