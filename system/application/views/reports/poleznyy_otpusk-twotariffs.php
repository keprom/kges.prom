<?php function f_d($var) { if ($var==0) return "0"; else return sprintf( "%22.2f",$var); }?>
<html>

<head>
    <title>Полезный отпуск</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>

<body>
    <div align=right>Форма 46-ЭС</div>
    <center><b>Полезный отпуск<br/><?php echo $org_info->org_name." за ".$period->row()->name;?></b>
    </center>
    <table border=1 px width=100%>

        <tr>
            <td rowspan=2 align=left>
                <b>Наименование</b>
            </td>
            <td colspan=4 align=center>
                <b>Полезный отпуск</b>
            </td>
            <td colspan=4 rowspan=2 align=right>
                <b>Ср. Тариф</b>
            </td>
        </tr>

        <tr>
            <td colspan=2 align=right>
                <b>кВт/ч</b>
            </td>
            <td colspan=2 align=right>
                <b>тенге</b>
            </td>
        </tr>

        <?php 
		$last_group=-1;
		$last_group_name="" ;
		$sum_kvt_first_tariff=0;
		$sum_kvt_second_tariff=0;
		$sum_tenge_first_tariff=0;
		$sum_tenge_second_tariff=0;
		$_sum_kvt_ft=0;
		$_sum_kvt_st=0;
		$_sum_tenge_ft=0; 
		$_sum_tenge_st=0; 
		foreach($otpusk->result() as $o):?>
        <?php if (($o->firm_subgroup_id!=$last_group)&&($last_group==-1)) { echo "
        <tr>
            <td colspan=4><b>{$o->firm_subgroup_name}</b>
            </td>
        </tr>"; $last_group=$o->firm_subgroup_id; $last_group_name=$o->firm_subgroup_name; } if (($o->firm_subgroup_id!=$last_group)&&($last_group!=-1)) { echo "
        <tr>
            <td align=left><b>Итого {$last_group_name}</b>
            </td>
            <td align=right><b>{$sum_kvt_first_tariff}</b>
            </td>
			<td align=right><b>{$sum_kvt_second_tariff}</b>
            </td>
            <td align=right><b>{$sum_tenge_first_tariff}</b>
            </td>
			<td align=right><b>{$sum_tenge_second_tariff}</b>
            </td>
            <td align=right><b>".
	f_d($sum_tenge_first_tariff/(($sum_kvt_first_tariff==0)?1:$sum_kvt_first_tariff))."</b>
            </td>
			<td align=right><b>".
	f_d($sum_tenge_second_tariff/(($sum_kvt_second_tariff==0)?1:$sum_kvt_second_tariff))."</b>
            </td>
        </tr>"; 
		$sum_kvt_first_tariff=0;
		$sum_kvt_second_tariff=0;
		$sum_tenge_first_tariff=0; 
		$sum_tenge_second_tariff=0; 
		echo "
        <tr>
            <td colspan=7><b>{$o->firm_subgroup_name}</b>
            </td>
        </tr>"; 
		$last_group=$o->firm_subgroup_id; 
		$last_group_name=$o->firm_subgroup_name; 
		} 
		$last_group=$o->firm_subgroup_id; 
		$sum_tenge_first_tariff+= $o->sum_tenge_first_tariff; 
		$sum_tenge_second_tariff+=$o->sum_tenge_second_tariff; 
		$sum_kvt_first_tariff+=   $o->sum_kvt_first_tariff; 
		$sum_kvt_second_tariff+=  $o->sum_kvt_second_tariff; 
		$_sum_tenge_ft+=$o->sum_tenge_first_tariff; 
		$_sum_tenge_st+=$o->sum_tenge_second_tariff; 
		$_sum_kvt_ft+=$o->sum_kvt_first_tariff;
		$_sum_kvt_st+=$o->sum_kvt_second_tariff; 
		?>
        <tr>
            <td align=left>
                <?php echo $o->firm_power_group_name; ?>
            </td>
            <td align=right>
                <?php echo $o->sum_kvt_first_tariff; ?>
            </td>
			<td align=right>
                <?php echo $o->sum_kvt_second_tariff; ?>
            </td>
            <td align=right>
                <?php echo f_d($o->sum_tenge_first_tariff); ?>
            </td>
			<td align=right>
                <?php echo f_d($o->sum_tenge_second_tariff); ?>
            </td>
            <td align=right>
                <?php echo f_d(($o->sum_kvt_first_tariff==0?"0":$o->sum_tenge_first_tariff/$o->sum_kvt_first_tariff)); ?>
            </td>
			<td align=right>
                <?php echo f_d(($o->sum_kvt_second_tariff==0?"0":$o->sum_tenge_second_tariff/$o->sum_kvt_second_tariff)); ?>
            </td>
        </tr>
        <?php endforeach; 
		echo "
		<tr >
		<td align=left><b>Итого {$last_group_name}</b></td>
		<td align=right><b>{$sum_kvt_first_tariff}</b></td>
		<td align=right><b>{$sum_kvt_second_tariff}</b></td>
		<td align=right><b>{$sum_tenge_first_tariff}</b></td>
		<td align=right><b>{$sum_tenge_second_tariff}</b></td>
		<td align=right><b>". f_d($sum_tenge_first_tariff/$sum_kvt_first_tariff). "</b></td> 
		<td align=right><b>". f_d($sum_tenge_second_tariff/$sum_kvt_second_tariff). "</b></td>
		</tr>";
		?>
        <tr>
            <td><b>Всего</b></td>
            <td align=right><b><?php echo f_d($_sum_kvt_ft);?></b></td>
			<td align=right><b><?php echo f_d($_sum_kvt_st);?></b></td>
            <td align=right><b><?php echo f_d($_sum_tenge_ft);?></b></td>
			<td align=right><b><?php echo f_d($_sum_tenge_st);?></b></td>
            <td align=right><b><?php echo f_d($_sum_tenge_ft/$_sum_kvt_ft);?></b></td>
			<td align=right><b><?php echo f_d($_sum_tenge_st/$_sum_kvt_st);?></b></td>
        </tr>
    </table>
    <br>
    <br>
    <table align=center width=70%>
        <tr align=center>
            <td>Директор</td>
            <td>
                <?php echo $org_info->director;?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr align=center>
            <td>Нач. управления сбыта</td>
            <td>
                <?php echo $org_info->nachalnik_otdela_sbyta;?></td>
        </tr>
    </table>
</body>

</html>