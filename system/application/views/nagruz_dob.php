<h4>Добавление потерь</H4>
<?php
echo form_open("billing/adding_dob");
echo "<input type=hidden name=billing_point_id value=".$point_id." >";
?>


Мощность Wakt
<input name=wakt value=''   /><br/>
Холостой ход
<input  name=holostoi value='' /><br/>
Дата занесения
<input  name=data value='' /><br/>
<br><br><br>
<input type='submit' value="Ввести данные"/>

</form>
