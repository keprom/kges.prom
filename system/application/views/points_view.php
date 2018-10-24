<div id="pagecontent">
<h4>Точки учета</h4>
<ul class="tabset_tabs">
<?php 
$fir = -1;
$last_tp = -1;
$str = 0;
foreach ($result->result() as $r):
if (($fir == 2)&&($last_tp != $r->tp_id)){echo "<li ><a class=\"\" href=\"#tp".$r->tp_id."\" >ТП-".$r->tp_name."</a></li>";$str+=1;$last_tp = $r->tp_id;}
if (($fir == 1)&&($last_tp != $r->tp_id)){echo "<li><a class=\"preActive\" href=\"#tp".$r->tp_id."\">ТП-".$r->tp_name."</a></li>";$fir = 2;$last_tp = $r->tp_id;$str+=1;}
if (($fir == 0)&&($last_tp != $r->tp_id)){ echo "<li><a class=\"preActive postActive\" href=\"#tp".$r->tp_id."\">ТП-".$r->tp_name."</a></li>"; $fir = 1;$last_tp = $r->tp_id;$str+=1;}
if ($fir == -1){ echo "<li class=\"firstchild\"><a href=\"#tp".$r->tp_id."\" class=\"preActive active\">ТП-".$r->tp_name."</a></li>"; $fir = 0;$last_tp = $r->tp_id; $str+=1;}
if ($str == 9) {echo "<br />"; $str = 0;}
endforeach;

$fir = -1;
$last_tp = -1;
foreach ($result->result() as $r):
		 
if (($fir == 1)&&($last_tp == $r->tp_id))	
{ ?>

<tr>
<td> <?php  echo anchor("billing/point/".$r->id,$r->name."<br><font color=green>".$r->gos_nomer."</font> <font color=blue>".$r->counter_type_name."</font><br/>
<font color=red >".$r->data_gos_proverki."</font><br/>
<font color=purpure>".$r->crafted_year."</font>"); ?></td>
<td> <?php  echo $r->tp_name; ?></td>
<td> <?php  echo $r->address."<br>".anchor("billing/last_edit/".$r->id,"гос проверка:".$r->last_gos_proverka."<br>Плановая проверка:".$r->last_plan_proverka); ?></td>
<td> <?php  echo anchor("billing/delete_billing_point/".$r->id,"Удалить")."<br/>".
				anchor("billing/edit_billing_point/".$r->id,"Редактировать")."<br/><br/>".
				anchor("billing/tp_billing_point/".$r->id."/".$r->firm_id,"<font color='".($r->in_tp=='t'?"green":"red")."'>В ТП</font>")."<br/><br/>".
				anchor("billing/close_billing_point/".$r->id."/".$r->firm_id,"снять точку учета"); 
				
	?></td>
</tr>
<?php 
}	

if (($fir == 1)&&($last_tp != $r->tp_id))	
{ echo "</table></p></div> <div id=\"tp".$r->tp_id."\" class=\"tabset_content\"><h2  class=\"tabset_label\">ТП-".$r->tp_name."</h2><p>";?>
<table border = 1 width = 100% cellspacing = 0>
<tr>
<td><b>Название точки учета</b></td><td><b>ТП</b></td><td><b>Адрес</b></td><td><b>Операции</b></td>
</tr>
<tr>
<td> <?php  echo anchor("billing/point/".$r->id,$r->name."<br><font color=green>".$r->gos_nomer."</font> <font color=blue>".$r->counter_type_name."</font><br/>
<font color=red >".$r->data_gos_proverki."</font><br/>
<font color=purpure>".$r->crafted_year."</font>"); ?></td>
<td> <?php  echo $r->tp_name; ?></td>
<td> <?php  echo $r->address."<br>".anchor("billing/last_edit/".$r->id,"гос проверка:".$r->last_gos_proverka."<br>Плановая проверка:".$r->last_plan_proverka); ?></td>
<td> <?php  echo anchor("billing/delete_billing_point/".$r->id,"Удалить")."<br/>".
				anchor("billing/edit_billing_point/".$r->id,"Редактировать")."<br/><br/>".
				anchor("billing/tp_billing_point/".$r->id."/".$r->firm_id,"<font color='".($r->in_tp=='t'?"green":"red")."'>В ТП</font>")."<br/><br/>".
				anchor("billing/close_billing_point/".$r->id."/".$r->firm_id,"снять точку учета"); 
				
	?></td>
</tr>
<?php 


}

if ($fir == -1){ echo "<div id=\"tp".$r->tp_id."\" class=\"tabset_content tabset_content_active\"><h2 class=\"tabset_label\">ТП-".$r->tp_name."</h2><p>";
?>
<table  border = 1 width = 100% cellspacing = 0>
<tr>
<td><b>Название точки учета</b></td><td><b>ТП</b></td><td><b>Адрес</b></td><td><b>Операции</b></td>
</tr>
<tr>
<td> <?php  echo anchor("billing/point/".$r->id,$r->name."<br><font color=green>".$r->gos_nomer."</font> <font color=blue>".$r->counter_type_name."</font><br/>
<font color=red >".$r->data_gos_proverki."</font><br/>
<font color=purpure>".$r->crafted_year."</font>"); ?></td>
<td> <?php  echo $r->tp_name; ?></td>
<td> <?php  echo $r->address."<br>".anchor("billing/last_edit/".$r->id,"гос проверка:".$r->last_gos_proverka."<br>Плановая проверка:".$r->last_plan_proverka); ?></td>
<td> <?php  echo anchor("billing/delete_billing_point/".$r->id,"Удалить")."<br/>".
				anchor("billing/edit_billing_point/".$r->id,"Редактировать")."<br/><br/>".
				anchor("billing/tp_billing_point/".$r->id."/".$r->firm_id,"<font color='".($r->in_tp=='t'?"green":"red")."'>В ТП</font>")."<br/><br/>".
				anchor("billing/close_billing_point/".$r->id."/".$r->firm_id,"снять точку учета"); 
				
	?></td>
</tr>
<?php
	$fir = 1; }

 $last_tp = $r->tp_id;	



 
endforeach;
?>
 </table>	</p></div> 
</div>
<?php 
echo "<br><br><br>";
?>
<hr>
<h4>Добавление точки учета</h4>

