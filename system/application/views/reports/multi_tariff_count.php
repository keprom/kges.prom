<?php
function f_d($var)
{
	if ($var==0) return "&nbsp;"; else
	return sprintf("%22.2f",$var);
}
function datetostring($date)
{
	$d=explode("-",$date); 
	return $d['1'].'.'.$d['0'].'.'.$d['2'];
}
?>
<html>
<head>
<title></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

</head>


<center>Количество многоставочных и одноставочных счетчиков
<?php
if ($_POST['ture_id']!=-1)
  echo  "по ТУРЭ ".$ture->name; 
?>
<br><?php echo ' за '.date("m.d.y");?>
</center>

<table width=100% border=1px cellspacing=0px style="border: black;font-family: Verdana; font-size: x-small;">
<tr>

<td><b>№</b></td>
<td><b>Договор</b></td>
<td><b>Наименование</b></td>
<td><b>Кол-во учетов</b></td>
<td><b>Кол-во выполнено</b></td>
<td><b>Остаток</b></td>
<?php if($nado=='true'):?>
<td><b>Дата уведомления</b></td>
<td><b>Срок </b></td>
<td><b>Должность ФИО</b></td>
<td><b>Подпись</b></td>
<?php endif;?>
</tr>
<tr><td colspan=<?php echo ($nado=='true'?"10":"6");?>><b>ТОО</b> <td></tr>
<?php $i=1;$sum_multi=0;$sum_single=0;
$itogo=0;$sum1=0;$sum2=0;
foreach($too->result() as $firm):
$sum_multi+=$firm->count_multi_tariff;
$sum_single+=$firm->count_single_tariff;
?>
<tr>
<td><?php echo $i++;?></td>
<td><?php echo $firm->dogovor;?></td>
<td><?php echo $firm->firm_name;?></td>
<td><?php echo $firm->count_single_tariff+$firm->count_multi_tariff;?></td>
<td><?php echo $firm->count_multi_tariff;?></td>
<td><?php echo $firm->count_single_tariff?></td>

<?php if($nado=='true'):?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif;?>
</tr>
<?php endforeach; 
$itogo+=$i-1;$sum1+=$sum_multi;$sum2+=$sum_single;
?>

<tr>
<td colspan=3>итого </td>
<td><?php echo $sum_multi+$sum_single;?></td>
<td><?php echo $sum_multi;?></td>
<td><?php echo $sum_single?></td>
<?php if($nado=='true'):?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif;?>
</tr>

<tr><td colspan=<?php echo ($nado=='true'?"10":"6");?>><b>Субабоненты </b> <td></tr>
<?php $i=1;$sum_multi=0;$sum_single=0;

foreach($sub->result() as $firm):
$sum_multi+=$firm->count_multi_tariff;
$sum_single+=$firm->count_single_tariff;
?>
<tr>
<td><?php echo $i++;?></td>
<td><?php echo $firm->dogovor;?></td>
<td><?php echo $firm->firm_name;?></td>
<td><?php echo $firm->count_single_tariff+$firm->count_multi_tariff;?></td>
<td><?php echo $firm->count_multi_tariff;?></td>
<td><?php echo $firm->count_single_tariff?></td>
<?php if($nado=='true'):?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif;?>
</tr>
<?php endforeach; 
$itogo+=$i-1;$sum1+=$sum_multi;$sum2+=$sum_single;
?>

<tr>
<td colspan=3>итого </td>
<td><?php echo $sum_multi+$sum_single;?></td>
<td><?php echo $sum_multi;?></td>
<td><?php echo $sum_single?></td>
<?php if($nado=='true'):?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif;?>
</tr>

<tr><td colspan=<?php echo ($nado=='true'?"10":"6");?>><b>Бюджет</b> <td></tr>
<?php $i=1;$sum_multi=0;$sum_single=0;

foreach($gos->result() as $firm):
$sum_multi+=$firm->count_multi_tariff;
$sum_single+=$firm->count_single_tariff;
?>
<tr>
<td><?php echo $i++;?></td>
<td><?php echo $firm->dogovor;?></td>
<td><?php echo $firm->firm_name;?></td>
<td><?php echo $firm->count_single_tariff+$firm->count_multi_tariff;?></td>
<td><?php echo $firm->count_multi_tariff;?></td>
<td><?php echo $firm->count_single_tariff?></td>
<?php if($nado=='true'):?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif;?>
</tr>
<?php endforeach; 
$itogo+=$i-1;$sum1+=$sum_multi;$sum2+=$sum_single;
?>

<tr>
<td colspan=3>итого </td>
<td><?php echo $sum_multi+$sum_single;?></td>
<td><?php echo $sum_multi;?></td>
<td><?php echo $sum_single?></td>
<?php if($nado=='true'):?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif;?>
</tr>

<?php if ($_POST['type']==2): ?>
	<tr><td colspan=<?php echo ($nado=='true'?"10":"6");?>><b>ИП</b> <td></tr>
	<?php $i=1;$sum_multi=0;$sum_single=0;

	foreach($ip->result() as $firm):
	$sum_multi+=$firm->count_multi_tariff;
	$sum_single+=$firm->count_single_tariff;
	?>
	<tr>
	<td><?php echo $i++;?></td>
	<td><?php echo $firm->dogovor;?></td>
	<td><?php echo $firm->firm_name;?></td>
	<td><?php echo $firm->count_single_tariff+$firm->count_multi_tariff;?></td>
	<td><?php echo $firm->count_multi_tariff;?></td>
	<td><?php echo $firm->count_single_tariff?></td>
	<?php if($nado=='true'):?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif;?>
	</tr>
	<?php endforeach; 
	$itogo+=$i-1;$sum1+=$sum_multi;$sum2+=$sum_single;?>
<tr>
<td colspan=3>итого </td>
<td><?php echo $sum_multi+$sum_single;?></td>
<td><?php echo $sum_multi;?></td>
<td><?php echo $sum_single?></td>
<?php if($nado=='true'):?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif;?>
</tr>


<?php endif;
?>

<tr><td colspan=<?php echo ($nado=='true'?"10":"6");?>><b>ПРОЧИЕ</b> <td></tr>
	<?php $i=1;$sum_multi=0;$sum_single=0;

	foreach($last_firm->result() as $firm):
	$sum_multi+=$firm->count_multi_tariff;
	$sum_single+=$firm->count_single_tariff;
	?>
	<tr>
	<td><?php echo $i++;?></td>
	<td><?php echo $firm->dogovor;?></td>
	<td><?php echo $firm->firm_name;?></td>
	<td><?php echo $firm->count_single_tariff+$firm->count_multi_tariff;?></td>
	<td><?php echo $firm->count_multi_tariff;?></td>
	<td><?php echo $firm->count_single_tariff?></td>
	<?php if($nado=='true'):?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif;?>
	</tr>
	<?php endforeach; 
	$itogo+=$i-1;$sum1+=$sum_multi;$sum2+=$sum_single;?>
<tr>
<td colspan=3>итого </td>
<td><?php echo $sum_multi+$sum_single;?></td>
<td><?php echo $sum_multi;?></td>
<td><?php echo $sum_single?></td>
<?php if($nado=='true'):?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif;?>
</tr>

<tr>
<td colspan=3>ИТОГО <?php echo $itogo;?></td>
<td><?php echo $sum1+$sum2;?></td>
<td><?php echo $sum1;?></td>
<td><?php echo $sum2?></td>
<?php if($nado=='true'):?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif;?>
</tr>
</table>

</html>