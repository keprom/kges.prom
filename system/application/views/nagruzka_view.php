<hr>
<?php
function f_d($var)
{
	if ($var==0) return "&nbsp;"; else
	return sprintf("%22.2f",$var);
}
function f_d3($var)
{
	if ($var==0) return "&nbsp;"; else
	return sprintf("%22.3f",$var);
}
function f_d2($var)
{
	if ($var==0) return "&nbsp;"; else
	return sprintf("%22.0f",$var);
}
?>
<h3>Нагрузки:</h3><br> 
<?php
foreach($nagruz->result() as $row):
?>
<b>Значение Сумарные потери (ХХ+нагр):</b> 
<?php 
	echo f_d2(($row->wnag+$row->holostoi)*1000). "Квт";
?>
<?php endforeach;?>
<?php
foreach($nagruz1->result() as $row):
?>
<?php echo anchor("billing/delete_nagruz/".$row->id,"<img src=".base_url()."img/delete.png />"); ?>
<br>
<?php endforeach;?>