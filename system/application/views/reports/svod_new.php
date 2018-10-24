<?php
	function f_d($var)
	{
		if ($var == null) return " ";
		if ($var == 0) return " "; else
		return sprintf("%22.2f",$var);
	}
	function datetostring($date)
	{
		$d=explode("-",$date); 
		return $d['0'].'.'.$d['1'].'.'.$d['2'];
	}
?>

<?php 
$periodi_list = array();
$month_count = 0;

foreach($periodi->result() as $period)
{
	$god = explode("-", $period->begin_date);
	$month = explode(" ", $period->name);
	$periodi_list[$god[0]][$month[0]] = $period->id;
	$month_count++;
}

$firm_gruops = $firm_gruops->result();
$firm_count = $firm_count->result();
$firm_count = $firm_count[0]->firm_count;
//$firm_gruops = $firm_gruops;
//print_r(count($periodi->result()));
//print_r($finish_period_id);
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<style type="text/css">

#load{
	width:120px;
	height:30px;
	text-align:center;
	margin: 10px auto;	
}

#load, #start_load, #resume_load, #pause_load div{
	margin: 5px;
	cursor:pointer;
	background: #6483aa;
	color:#fff;
	padding: 4px 10px 4px 10px;
	border:1px solid #2f4561;
	-webkit-border-radius: 2px;
	-moz-border-radius: 2px;
	border-radius: 2px;
}

.progress {
    font-size: 13px;
    margin: 0;
    vertical-align: middle;
    background-color: #F7F7F7;
    background-image: -moz-linear-gradient(center top , #F5F5F5, #F9F9F9);
    background-repeat: repeat-x;
    border-radius: 4px 4px 4px 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) inset;
    height: 28px;
    width: 250px;
    overflow: hidden;
    display: inline-block;
}

.progress .bar {
    background-color: #0E90D2;
    background-image: -moz-linear-gradient(center top , #149BDF, #0480BE);
    background-size: 1px 1px;
    -moz-box-sizing: border-box;
    -moz-transition: width 0.6s ease 0s;
    background-repeat: repeat-x;
    box-shadow: 0 -1px 0 rgba(0, 0, 0, 0.15) inset;
    color: #FFFFFF;
    float: left;
    font-size: 12px;
    height: 100%;
    text-align: left;
    padding: 0px 0px;
    font-size: 13px;
    text-shadow: 1px 1px #333;
    white-space: nowrap;
}

.progress .bar .bar_text {

    padding: 5px 8px;

}

</style>

<script type="text/javascript" src="http://admpc/jquery-1.5.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$(".progress").hide(); // Скрываем прелоадер
	//$("#imgLoad").hide(); // Скрываем прелоадер
	$("#resume_load").hide();
	$("#pause_load").hide();
});

var num = 0; //чтобы знать с какой записи вытаскивать данные
var num_sum = 0;
var loading = false;
var firm_count = <?php echo $firm_count;?>;

var curr_group = 0; //groups are in 0>>19 except 3,10,13,14,15,16

var a = Array();

<?php foreach ($firm_gruops as $key=>$value)
{?>
a[<?php echo $value->id;?>] = <?php echo '"'.$value->name.'"';?>;
<?php
}?>

$(function() {
	
$("#start_load div").click(function(){
	loading = true;
	load_data();
	$("#start_load").hide();
	$("#pause_load").show();
	$(".progress").show();
	});
	
$("#resume_load div").click(function(){
	loading = true;
	load_data();
	$("#resume_load").hide();
	$("#pause_load").show();
	});

$("#pause_load div").click(function(){
	loading = false;
	$("#pause_load").hide();
	$("#resume_load").show();
	});

	
	// // Выполняем если по кнопке кликнули
		function load_data()
		{
			if (!loading) return;
			
			//$("#imgLoad").show(); // Показываем прелоадер
			$(".progress").show();
			
			$.ajax({
				url: "http://admpc/index.php/billing/new_svod_kvt_po_godam_ajax", // Обработчик
				type: "POST",       // Отправляем методом GET
				data: {"_offset": num,"_group": curr_group,"_start_period_id": <?php echo $start_period_id;?>,"_finish_period_id": <?php echo $finish_period_id;?>},
				cache: false,			
				success: function(response){
					if(response == 0)
					{ // Смотрим ответ от сервера и выполняем соответствующее действие
						if (curr_group < 25)
						{
							curr_group = curr_group + 1;
							if (curr_group == 3 || curr_group == 10)
							{
								curr_group = curr_group + 1;
							}
							
							if (curr_group > 12 && curr_group < 17)
							{
								curr_group = 17;
							}
							
							$("#content").append('<tr><td colspan="<?php echo count($periodi->result()) + 3;?>">' + a[curr_group] + '</td></tr>');
							num = 0;
							showProcess (num_sum);
							load_data();
						}
						else
						{
							alert("Построение отчета завершено!");
							//$("#imgLoad").hide();
							$(".progress").hide();
							$("#pause_load").hide();
							$("#loader").hide();
						}
					}else{
						$("#content").append(response);
						num = num + 1;
						num_sum = num_sum +1;
						showProcess (num_sum);
						load_data();
						//$("#imgLoad").hide();
					}
				}
			});
		}
	
	function showProcess (offset) {
        $('.bar_text').text("ЗАВЕРШЕНО "+offset+" из "+firm_count);
        $('.bar').css('width', offset*100/firm_count + '%');
    }
});

</script>
</head>
<body>

<table width=100%>
	<tr><td align=center><b>Электропотребление за периоды по группам</b></td></tr>
</table>

<div id="loader">
<table>
<tr>
<td>
<div class="progress">
	<div class="bar" style="width: 0%;"><div class="bar_text" "></div></div>
</div>

<?php 
/*
<div>
<?php //<div>Загрузить еще</div> ?>
<img src="http://admpc/img/loading.gif" id="imgLoad">
</div>
*/
?>
</td>
<td>
<div id="start_load">
<div>СТАРТ</div> 
</div>

<div id="resume_load">
<div>ПРОДОЛЖИТЬ</div> 
</div>

<div id="pause_load">
<div>ПАУЗА</div> 
</div>
</td>
</tr>
</table>
</div>

<table border = 1 width = 100% id="content">
  <tr>
    <th colspan="3"><center><b>ФИРМА</b></center></th>
    <th colspan="<?php echo $month_count;?>"><center><b>кВт</b></center></th>
  </tr>
  <tr>
    <td rowspan="2"><center><b>№ п.п.</b></center></td>
    <td rowspan="2"><center><b>Договор</b></center></td>
    <td rowspan="2"><center><b>Наименование</b></center></td>
	<?php
	foreach($periodi_list as $god=>$months)
	{
	?>
    <td colspan="<?php echo count($months);?>"><center><b><?php echo $god;?></b></center></td>
    <?php
	}
	?>
  </tr>
  <tr>
    <?php
	foreach($periodi_list as $god=>$months)
	{
		foreach($months as $month=>$id)
		{
		?>
		<td><center><b><?php echo $month;?></b></center></td>
		<?php
		}
	}
	?>
  </tr>
</table>





</body>
</html>