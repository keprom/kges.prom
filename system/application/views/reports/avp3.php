<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<?php 
function datetostring($date)
{
	$d=explode("-",$date); 
	return $d['2'].'.'.$d['1'].'.'.$d['0'];
}
function f_d($var)
{
	if (($var==0)or($var==NULL)) return "0.00"; else
	return sprintf("%22.2f",$var);
}
function f_d3($var)
{
	if (($var==0)or($var==NULL)) return "0.000"; else
	return sprintf("%22.3f",$var);
}

?>
<body>
<table border=0 width=100%>
<tr>

	<table width=100% border=0>
	<tr>
	<td width='500'></td>
	<td width='235px' align=center>
		<font size='2'><i>Приложение 50<br>
					к приказу Министра финансов Республики Казахстан<br>
					от 22 декабря 2014 года № 562 </i></font>
					<p align=right>
		Форма Р-1	
		</p>
	</td>
	</tr></table>

</tr><tr><td>
<table width=100% border=1px cellspacing=0 cellpadding=0><font size='2'>
<tr>
	<td width='2100px'>
	
		<table width=100% >
			<tr>
				<td width='588px'>
				</td>
				<td width='147px'>
					<p align=center>ИИН/БИН</p>
				</td>
			</tr>
			<tr>
				<td width='2100px'>
					 Заказчик <b>
					<u><?php 
					echo " "."{$firm->name}"." ".$firm->address; 
					?></u>
					</b>
				</td>
				<td width='147px'>
					<table width='147px' border=1 cellspacing=0 cellpadding=0>
						<tr>
							<td width='147px'>
							
							<?php
							echo  $firm->bin; 
							?>
							
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width='588px' align = center>
					<sup>полное наименование, адрес, данные о средствах связи</sup>
				</td>
				<td width='147px'>
				&nbsp 
				</td>
			</tr>
			<tr>
				<td width='2100px'>
					Исполнитель<b>
					<u><?php 
					echo $org->org_name." ".$org->address;
					?></u>
					</b>
				</td>
				<td width='147px'>
							<table width='147px' border=1 cellspacing=0 cellpadding=0>
								<tr>
									<td width='147px'>
									
									<?php 
									echo $org->bin;
									?> 
									
									</td>
								</tr>
							</table>
				</td>
			</tr>
			<tr>
				<td width='588px'>
					<p align=center><sup>полное наименование, адрес, данные о средствах связи</sup></p>
				</td>
				<td width='147px'>
				</td>
			</tr>
			<tr>
				<td width='588px'>
					<p align=center>Договор (контракт) №
					<u><?php 
					echo "        ".$firm->dogovor."      ".datetostring($firm->dogovor_date); 
					?> </u>
					</p>
				</td>
				<td width='147px'>
				</td>
			</tr>
		</table>
	
	</td>
</tr>
</table>
</td></tr><tr><td>


			<table width='360px' border=1px cellspacing=0 cellpadding=0 align=right>
				<tr>
					<td width='180px' rowspan=2 align=center>
						Номер документа
					</td>
					<td width='180px' rowspan=2 align=center>
						Дата составления
					</td>
					
				</tr>
				<tr>
					
				</tr>
				<tr>
					<td width='118px'>
					<p align=center>
					<?php
					echo $schetfactura_date->id;
					?> 
					</p>	
					</td>
					<td width='82px'>
					<p align=center>
					<?php 
					if (strlen($data_schet)==0){ 
						echo datetostring($schetfactura_date->date);
						} 
						else 
						{
							echo $data_schet; 
							}
						?> 
						</p>	
					</td>
					
					
				</tr><tr></tr>
			</table>

	
</td></tr><tr><td align="center">
		</br>
		
			<p align='right'><h4>АКТ ВЫПОЛНЕННЫХ РАБОТ (ОКАЗАННЫХ УСЛУГ)*<p></h4>
		

</td></tr><tr><td>

<table width=100% border=1px cellspacing=0 cellpadding='2px'>
	<tr>
		<th rowspan=2 width='50px'>
			<font size='2'>Номер по порядку</font>
		</th>
		<th rowspan=2 width='400px'>
			<font size='2'>Наименование работ (услуг) (в разрезе их подвидов в соответствии с техспецификацией, заданием, графиком выполнения работ (услуг) при их наличии)

		</th>
		<th rowspan=2 width='150px'>
			<font size='2'>Дата выполнения работ (оказания услуг) **
		</th>
		<th rowspan=2 width='500px'>
			<font size='2'>Сведения о наличии отчет о маркетинговых исследованиях, консультационных и прочих услуг (дата, номер, количество страниц)
		</th>
		<th rowspan=2 width='70px'>
			<font size='2'>Единица измерения
		</th>
		<th colspan=3 width='180px'>
			<font size='2'>Выполнено работ (оказано услуг)
		</th>
	</tr>
	<tr>
		<th width='60px'>
			<font size='2'>количество
		</th>
		<th width='60px'>
			<font size='2'>цена за единицу
		</th>
		<th width='60px'>
			<font size='2'>стоимость
		</th>
	</tr>
	<tr>
		<td width='50px' align=center>
			<font size='2'>1
		</td>
		<td width='400px' align=center>
			<font size='2'>2
		</td>
		<td  width='150px' align=center>
			<font size='2'>3
		</td>	
		<td width='500px' align=center>
			<font size='2'>4
		</td>
		<td width='70px' align=center>
			<font size='2'>5
		</td>
		<td width='60px' align=center>
			<font size='2'>6
		</td>
		<td width='60px' align=center>
			<font size='2'>7
		</td>
		<td width='60px' align=center>
			<font size='2'>8
		</td>
	</tr>
	
	<?php 
$sum_bez_nds=0;$sum_nds=0;$sum=0;$i=1;

$i_t=$itog->itog_tenge;
$i_nds=$itog->itogo_nds;
$i_itogo=$itog->itogo_with_nds;

foreach($s as $s2 ): 
?>	
	
		<tr>
		<td width='50px' align=center>
			<font size='2'>
			<?php 
			echo $i++;
			?>
			
		</td>
		<td width='400px' align=center>
			<font size='2'>Электроэнергия
		</td>
		<td width='80px' align=center>
					<font size='2'><?php if ($i==3) {
						echo datetostring($period->begin_date)." - ". datetostring($period->end_date) ;} else echo "";
						?>	
					</td>
		<td width='500px' align=center>
			<font size='2'>Нет
		</td>
		<td width='70px' align=center>
			<font size='2'>кВт*ч
		</td>
		<td width='60px' align=center>
			<font size='2'>
			<?php 
			echo f_d($s2->kvt);
			?>
			
		</td>
		<td width='60px' align=center>
			<font size='2'>
			<?php 
			echo f_d($s2->tariff_value);
			?>
			
		</td>
		<td width='60px' align=center>
			<font size='2'>
			<?php
		if ($i_t-$s2->tenge>1)
		{
			echo f_d($s2->tenge);
			$sum_bez_nds+=$s2->tenge;
			$i_t-=f_d($s2->tenge);
		}
		else echo $i_t;
	?>
	
		</td>
	</tr>
	
	<?php 
	endforeach;
	?>
	
			<tr>
		<th width='50px' >
			&nbsp 
		</th>
		<th width='400px' >
			&nbsp 
		</th>
		<th width='150px' >
			&nbsp 
		</th>
		<th width='500px' >
			&nbsp 
		</th>
		<th width='70px' >
			<font size='2'>Итого
		</th>
		<th width='60px' >
			&nbsp
		</th>
		<th width='60px' >
			<font size='2'>x
		</th>
		<th width='60px' >
			<font size='2'>
			<?php
			echo f_d($itog->itog_tenge);
			?>
			
		</th>
	</tr>
				<tr>
				
		<th width='588px' colspan=7 align=center>
			<font size='2'>В том числе НДС
		</th>
		<th width='147px'>
			<font size='2'>
			<?php 
			echo f_d($itog->itogo_with_nds);
			?>
			
		</th>
	</tr>
</table>
</td></tr><tr><td>

<table border=0px cellspacing=0 cellpadding=0>
	<tr>
		<td width='500px'>
			<font size='2'><p>Сведения об использовании запасов, полученных от заказчика</p>
		</td>
		<td width='300px'>
			_______________________________________________________
		</td>
	</tr><tr>
		<td width='500px'>
		</td>
		<td width='300px'>
			<sup>наименование, количество, стоимость</sup>
		</td>
	</tr>
</table>

</td></tr><tr><td>

<table width=100% border=0px cellspacing=0 cellpadding=0>
	<tr>
		<td width='1900px'>
			<font size='2'><p>Приложение. Перечень документации, в том числе отчет(ы) о маркетинговых, научных исследованиях, консультационных и прочих услугах (обязательны при его (их) наличии) на ______________________ страниц</p>
		</td>
	</tr>
</table>

</td></tr><tr><td>

<table width=100% border=0px cellspacing=0 cellpadding=0>
	<tr>
		<td width='600px'>
			<table width='600px'  cellspacing=0 cellpadding=0>
				<tr>
					<td width='600px'>
						<table width='600px' cellspacing=0 cellpadding='5px'>
							<tr>
								<td width='400px'>
									
									Сдал (Исполнитель)
									
								</td>
							
							
								<td width='160px' align=center>
									_____________ /
								</td>
								<td width='210px' align=center>
									_____________ /
								</td>
								<td width='150px' align=center>
									_____________
								</td>
							</tr>
							<tr>
							<td width='100px'></td>
								<td width='150px' align=center>
									<sup>должность</sup>
								</td>
								<td width='130px' align=center>
									<sup>подпись</sup>
								</td>
								<td width='220px' align=center>
									<sup>расшифровка подписи</sup>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<p align=center></br></p>
			<p align=center>М.П.</p>
		</td>
		<td width='100px'>
		</td>
		<td width='600px'>
			<table width='600px'  cellspacing=0 cellpadding=0>
				<tr>
					<td width='600px'>
						<table width='600px'  cellspacing=0 cellpadding='5px'>
							<tr>
								<td width='400px'>
									
									Принял (Заказчик)
									
								</td>
							
							
								<td width='150px' align=center>
									______________ /
								</td>
								<td width='180px' align=center>
									______________ /
								</td>
								<td width='150px' align=center>
									<u><?php echo $firm->director_name; ?></u>
								</td>
							</tr>
							<tr>
							<td width='100px'></td>
								<td width='130px' align=center>
									<p align=center><sup>должность</sup></p>
								</td>
								<td width='130px' align=center>
									<p align=center><sup>подпись</sup></p>
								</td>
								<td width='220px' align=center>
									<p align=center><sup>расшифровка подписи</sup></p>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<p align=center>Дата подписания (принятия) работ (услуг) <u> <?php if (strlen($data_schet)==0){ echo datetostring($schetfactura_date->date);} else { echo $data_schet;}?></u> </p>
			<p align=center>М.П.</p>
		</td>
	</tr>
</table>
	
</td></tr><tr><td></font>



	
</td></tr>
</table>
</body>
</html>