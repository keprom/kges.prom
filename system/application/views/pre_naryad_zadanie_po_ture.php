<?php echo form_open("billing/naryad_zadanie_po_ture"); ?>
ТУРЭ
<select name=ture_id>
<?php foreach ($ture->result() as $t):?>
	<option value=<?php echo $t->id;?>><?php echo $t->name;?></option>
<?php endforeach;?>
</select>
<br>
ТИП:
<select name=type >
<option value=1> Выдать помеченных как подключенных к тп</option>
<option value=2> Выдать всех на данном турэ</option>
</select>
<br>
<br>
<input type=submit value='Выдать отчет' />
</form>
