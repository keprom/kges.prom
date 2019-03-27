<?php echo anchor("billing/pre_schetfactura/" . $firm->id, "назад"); ?><br><br>
    <b><?php echo $firm->dogovor." ".$firm->name; ?></b><br><br>
<?php
$r=$r->row();
echo form_open("billing/schetfactura");
echo "<input type=hidden name=firm_id value=".$firm_id." >";
//echo "<input type=checkbox name=html  ><br>";
//echo "Выдать актом выполненых работ <input type=checkbox name=akt_vypolnenyh_rabot  ><br>";
//echo "Новая счет-фактура <input type=checkbox name=new_schetfactura ><br>";
echo "Выдача накладной <input type=checkbox name=nakladnaya><br>";
echo "Подписывает счет фактуру Ташкенова <input type=checkbox name=podpisyvaet><br>";
echo "Подписывает счет фактуру Ламнова <input type=checkbox name=podpisyvaet2><br>";
echo "<input type=hidden name=period_id value=".$period_id." >";
?>
Другая дата выдачи: <input name=data_schet value='<?php echo $r->date?>' > <br><br>
Другой номер счетфактуры: <input name=schet2 value='' > <br><br>
Последний номер счета-фактуры <input type="text" disabled value="<?php echo $max_schet_number; ?>"><br><br>
Другой номер накладной(пустой если как у счетфактуры): <input name=schet_nakl value='<?php echo $r->schet_nakl;?>' > <br><br>
Другая дата выдачи накладной(пустой если как у счетфактуры): <input name=data_nakl value='<?php echo $r->data_nakl;?>' > <br><br>
Условия оплаты по договору: <input name=edit1 value='<?php echo $firm->edit1; ?>' > <br><br>
Пункт назначения поставляемых товаров (работ, услуг): <input name=edit2 value='<?php echo $firm->edit2; ?>'> <br> <br>
Поставка товаров осуществлена по доверености: <input name=edit3 value='<?php echo $firm->edit3; ?>'> <br> <br>
Способ отправления: <input name=edit4  value='<?php echo $firm->edit4; ?>'> <br> <br>
Грузоотправитель:<input name=edit5  value=''> <br> <br>
Грузополучатель:<input name=edit6  value='<?php echo $firm->edit6; ?>'> <br> <br>
Отправитель:<input name=edit7  value='<?php echo $firm->edit7; ?>'> <br> <br>
Адрес отправителя:<input name=edit8  value='<?php echo $firm->edit8; ?>'> <br> <br>
Номер договора со всякой фигней:<input name=dog2  value='<?php echo $firm->dog2; ?>'> <br> <br>


<?php

echo "<input type=submit value='Открыть счетфактуру' />";
echo "</form>";


?>