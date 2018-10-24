<html>
<head>
<title>Отчет по оплате за электроэнергию</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

</head>
<body>
<center>
<?php 
echo form_open("kassa/view_oplata");
echo "<input name=data value='".$this->session->userdata('kassa_data')."' ><input type=submit value='Просмотреть текущую оплату' /> </form>";
?>
<br />
<br />
<h2>Добавление оплаты по абонентскому номеру</h2>
<br />
<br />
<br />

<?php echo form_open("kassa/add_oplata");?>
<input name="number" /> <br/><br/>
<input type=submit value='Открыть по абонентскому номеру' /></form>
<br/>

<h2>Добавление абонента</h2>
<?php echo form_open("kassa/add_abonent");?>
<input name="number" /></br></br>
<input type=submit value='Добавление нового абонента' /> <br/></form>
<h2>Обновления данных абонента</h2>
<?php echo form_open("kassa/obn_abon");?>
<input name="number" /> <br/><br/>
<input type=submit value='Обновление данных' /> </form>
</form>
</center>
</body>