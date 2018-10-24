<?php echo form_open("billing/nachislenie_v_analiz"); ?>
Период 
<select name=period_id>
<?php foreach ($period->result() as $p):?>
	<option value=<?php echo $p->id;?>><?php echo $p->name;?></option>
<?php endforeach;?>
</select>
<br>
<br>
Способ
<select name="met" id="met">
    <option value="old" selected>Старый способ</option>
    <option value="new">Новый способ (быстрее)</option>
</select>
<br>
<br>
<input type=submit value='Экспортировать начисление' />
</form>
