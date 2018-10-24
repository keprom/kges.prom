<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

</head>

<?php
function f_d($var)
{
	if ($var==0) return "&nbsp;"; else
	return sprintf("%22.2f",$var);
}
function datetostring($date)
{
	$d=explode("-",$date); 
	return $d['0'].'.'.$d['1'].'.'.$d['2'];
}
echo "<table>";
echo "<tr>
<td></td>
        <td>название фирмы</td>
		<td>название ТУ</bd> 
		<td>ТП</td>
		<td>Счетчик</td>
		<td>Показание</td>
		<td>Тариф</td>
		<td>Дата показания</td>
		 </tr>";
		
		$j=1;
foreach ($nalog->result() as $n)
{
	echo "<tr>
	<td>".($j++)."</td>
		<td>{$n->firm_name}</bd>
		<td>{$n->bill_name}</bd>
		<td>{$n->tp_name}</td>
		<td>{$n->counter_type_name}</td>
		<td>{$n->counter_value_value}</td>
		<td>{$n->tariff_name}</td>
		<td>{$n->counter_value_data}</td>
		
		
		</tr>";
	
}

?>
</table>
</html>