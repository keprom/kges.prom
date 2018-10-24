<?php
if (!isset($fine_value)) {
	$fine_value = 0;
}
?>
<?php 
function datetostring($date)
{
	$d=explode("-",$date); 
	return $d['2'].'.'.$d['1'].'.'.$d['0'];
}
function f_d($var)
{
	if (($var==0)or($var==NULL)) return "0.000"; else
	return sprintf("%22.3f",$var);
}
function f_d3($var)
{
	if (($var==0)or($var==NULL)) return "0.000"; else
	return sprintf("%22.3f",$var);
}
function datetostring2($date)
{
	$d=explode("-",$date); 
	 
	if ($d['1']==1) {$d['1']='январь';}
	if ($d['1']==2) {$d['1']='февраль';}
	if ($d['1']==3) {$d['1']='март';}
	if ($d['1']==4) {$d['1']='апрель';}
	if ($d['1']==5) {$d['1']='май';}
	if ($d['1']==6) {$d['1']='июнь';}
	if ($d['1']==7) {$d['1']='июль';}
	if ($d['1']==8) {$d['1']='август';}
	if ($d['1']==9) {$d['1']='сентябрь';}
	if ($d['1']==10) {$d['1']='октябрь';}
	if ($d['1']==11) {$d['1']='ноябрь';}
	if ($d['1']==12) {$d['1']='декабрь';}
	
	return $d['1'].' '.$d['0'];
}

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<FONT size=7>
    <table width=100%>
        <tr align=center>
            <td width=2100px>
                <b><?php echo(strlen(trim($akt_vypolnenyh_rabot)) == 0 ? "Есеп шот фактура / Счет-фактура" : $akt_vypolnenyh_rabot); ?> №</b> <?php
                if (strlen($schet2) == 0) {
                    echo($schetfactura_date->id);
                } else {
                    echo $schet2;
                }
                echo " от ";
                if (strlen($data_schet) == 0) {
                    echo datetostring($schetfactura_date->date);
                } else {
                    echo datetostring($data_schet);
                } ?>
            </td>
            <td align=left> (1)</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td width=2100px><b>Жеткiзyші <br/>Поставщик</b> <u><?php echo $org->org_name; ?></u>
            </td>
            <td> (2)</td>
        </tr>
        <tr>
            <td width=2100px>Жеткізушінің БСН мен мекенжайы<br/>БИН и адрес поставщика
                <u>  <?php echo "БИН " . $org->bin . ", " . $org->address; ?> Свидетельство о постановке на
                    регистрационный учет по НДС серия 03001 № 0007114 от 17.09.2012</u></td>
        </tr>
        <tr>
            <td width=2100px>Жеткізушінің ЖСК<br/>ИИК поставщика <u>  <?php echo $org->IIK; ?>
                    КБЕ-<?php echo $org->Bank_kbe; ?> КНП-<?php echo $org->bank_knp; ?> <?php echo $org->bank_name; ?>
                    <br/>
                    БИК <?php echo $org->bank_bik; ?>  </u></td>
            <td> (2б)</td>
        </tr>
        <tr>
            <td width=2100px>Тауарлар (жұмыстар, қызметтер) жеткізуге арналған шарт (келісім-шарт)<br/>Договор(контракт)
                на поставку товаров (работ, услуг) №<u>  <?php if (strlen($dog2) == '') {
                        echo $firm->dogovor . "   </u> от <u>    " . datetostring($firm->dogovor_date);
                    } else echo $dog2; ?></u></td>
            <td> (3)</td>
        </tr>
        <tr>
            <td width=2100px>Шарт (келісім-шарт) бойынша төлем шарттары<br/>Условия оплаты по договору
                (контракту)<u>    <?php echo $edit1; ?>                                                                                                                             </u>
            </td>
            <td> (4)</td>
        </tr>
        <tr>
            <td width=2100px>Жеткізілетін тауарларды (жұмыстар,құзметтерді) белгіленген пункті<br/>Пункт назначения
                поставляемых товаров (работ,
                услуг)<u>   <?php echo $edit2; ?>                                                                                                                                           </u>
            </td>
        </tr>
        <tr align=right>
            <td width=2100px><i><FONT size=5>(мемлекет,өңір,облыс,қала,аудан/государство, регион, область, город,
                        район) </font> </i></td>
            <td></td>
        </tr>
        <tr>
            <td width=2100px>Тауарды жеткізу (қызметтерді, жұмыстарды) келесі сенімхат бойынша жүзеге асырылды<br/>
                Поставка товаров (работ,услуг) осуществлена по доверенности
                №<u>   <?php echo $edit3; ?>                                                       </u></td>
            <td> (5)</td>
        </tr>
        <tr>
            <td width=2100px>Жіберу тәсілі<br/>Способ отправления
                <u>  <?php echo $edit4; ?>                                                                                                                                                    </u>
            </td>
            <td> (6)</td>
        </tr>
        <tr>
            <td width=2100px>Тауар-көлік жөнелтпе құжатты <br/>Товарно-транспортная накладная
                _____________________________________________________________________
            </td>
            <td> (7)</td>
        </tr>
        <tr>
            <td width=2100px>Жүк жіберуші<br/> Грузоотправитель <u>филиал ТОО "Кокшетау Энерго Центр" Горэлектросети БИК
                    NURSKZKX КБЕ 17 БИН 091241017203 АО "Нурбанк" </u></td>
            <td> (8)</td>
        </tr>
        <tr align=right>
            <td width=2100px><i><FONT size=5>(БСН, атауы және мекенжайы/БИН, наименование и адрес) </font></i></td>
            <td></td>
        </tr>
        <tr>
            <td width=2100px>Жүк алушы БСН<br/>Грузополучатель БИН <u>  <?php if (strlen($edit6) == 0) {
                        echo $firm->bin . ", " . $firm->name . ", " . $firm->address;
                    } else {
                        echo $edit6;;
                    } ?>                                                                                                                 </u>
            </td>
            <td> (9)</td>
        </tr>
        <tr align=right>
            <td width=2100px><i><FONT size=5>(СТН,атауы және мекенжайы/РНН, наименование и адрес) </font></i></td>
            <td></td>
        </tr>
        <tr>
            <td width=2100px><b>Сатып алушы<br/>Покупатель</b> <u>        <?php if (strlen($edit7) == 0) {
                        echo "{$firm->name}";
                    } else {
                        echo $edit7;
                    } ?>                </u></td>
            <td> (10)</td>
        </tr>
        <tr>
            <td width=2100px>Сатып алушының БСН<br/>БИН и адрес нахождения покупателя
                <u> <?php if (strlen($edit8) == 0) {
                        echo "БИН " . $firm->bin . ", " . $firm->address;
                    } else {
                        echo $edit8;;
                    } ?></u></td>
        </tr>
        <tr>
            <td width=2100px>Алушының ЖСК<br/> ИИК получателя<u>   <?php echo $firm->raschetnyy_schet; ?></u> в банке
                <u><?php echo $bank->name; ?> </u> БИК <u><?php echo $bank->mfo; ?></u></td>
            <td> (10б)</td>
        </tr>
    </table>
    <br>

    <table border="1px" width="100%" align="center">
        <tr>
            <td rowspan="2" width="60px">№<br/>Р/с</td>
            <td rowspan="2" width="400px">Тауарлар (жұмыстар,<br/>қызметтер)<br/>атауы/Наименование<br/> товаров(работ,
                услуг)
            </td>
            <td rowspan="2" width="100px">Өлшем<br/>бірлігі/<br/>Ед.<br/>изм.</td>
            <td rowspan="2" width="200px">Саны/Кол-<br/>во(обьем)</td>
            <td rowspan="2" width="150px">Бағасы/Цена<br/>тенге</td>
            <td rowspan="2" width="300px">ҚҚС-сыз <br/>құны/Стоимост<br/>ь без НДС</td>
            <td colspan="2" width="310px">ҚҚС/НДС</td>
            <td rowspan="2" width="400px">Cатудың<br/>барлық құны<br/>құны/Всего<br/>стоимость<br/>реализации</td>
            <td colspan="2" width="210px">Акциз</td>
        </tr>
        <tr>
            <td width="110px" valign="middle">Cтавкасы<br/>Ставка</td>
            <td width="200px" valign="middle">Сомасы<br/>Сумма</td>
            <td width="110px" valign="middle">Ставкасы<br/>Ставка</td>
            <td width="100px" valign="middle">Сомасы<br/>Сумма</td>
        </tr>
        <tr>
            <td width="60px">1</td>
            <td width="400px">2</td>
            <td width="100px">3</td>
            <td width="200px">4</td>
            <td width="150px">5</td>
            <td width="300px">6</td>
            <td width="110px">7</td>
            <td width="200px">8</td>
            <td width="400px">9</td>
            <td width="110px">10</td>
            <td width="100px">11</td>
        </tr>
        <?php
        $sum_bez_nds = 0;
        $sum_nds = 0;
        $sum = 0;
        $i = 1;

        $i_t = $itog->itog_tenge;
        $i_nds = $itog->itogo_nds;
        $i_itogo = $itog->itogo_with_nds;

        foreach ($s as $s2):
            ?>
            <tr align="center">
                <td width="60px"><?php echo $i++; ?></td>
                <td width="400px">Электроэнергия за <br/> <?php echo datetostring2($schetfactura_date->date); ?> года
                </td>
                <td width="100px">КВТ.Ч</td>
                <td width="200px"><?php echo f_d($s2->kvt); ?></td>
                <td width="150px"><?php echo f_d3($s2->tariff_value); ?></td>
                <td width="300px" align="right">
                    <?php
                    if ($i_t - $s2->tenge > 1) {
                        echo f_d($s2->tenge);
                        $sum_bez_nds += $s2->tenge;
                        $i_t -= f_d($s2->tenge);
                    } else echo $i_t;
                    ?></td>
                <td width="110px">
                    12%
                </td>
                <td width="200px" align="right">
                    <?php
                    if ($i_nds - $s2->nds_value > 1) {
                        echo f_d($s2->nds_value);
                        $sum_nds += $s2->nds_value;
                        $i_nds -= f_d($s2->nds_value);
                    } else
                        echo f_d($i_nds);
                    ?>
                </td>
                <td width="400px" align="right">
                    <?php
                    if ($i_itogo - $s2->with_nds > 1) {
                        echo f_d($s2->with_nds);
                        $sum += $s2->with_nds;
                        $i_itogo -= f_d($s2->with_nds);
                    } else
                        echo f_d($i_itogo);
                    ?>
                </td>
                <td width="110px">0</td>
                <td width="100px">0</td>
            </tr>
        <?php endforeach; ?>

        <?php if ($fine_value > 0): ?>
            <tr>
                <td width="60px" class="border-td"><?php echo $i++; ?></td>
                <td width="400px">Пеня</td>
                <td width="100px">тенге</td>
                <td width="200px">1</td>
                <td width="150px" align="right"><?php echo sprintf("%22.3f", $fine_value); ?></td>
                <td width="300px" align="right"><?php echo sprintf("%22.3f", $fine_value); ?></td>
                <td width="110px">Без НДС</td>
                <td width="200px"></td>
                <td width="400px" align="right"><?php echo sprintf("%22.3f", $fine_value); ?></td>
                <td width="110px" align="center">0</td>
                <td width="100px" align="center">0</td>
            </tr>
        <?php endif; ?>

        <tr align="center">
            <td width="910px" colspan="5"><b align="left">Барлыгы шот бойынша<br/>Всего по счету:<></b></td>
            <td width="300px" align="right"><b><?php echo f_d($itog->itog_tenge + $fine_value); ?></b></td>
            <td width="110px" bgColor=#c0c0c0></td>
            <td width="200px" align="right"><b><?php echo f_d($itog->itogo_nds); ?></b></td>
            <td width="400px" align="right"><b><?php echo f_d($itog->itogo_with_nds + $fine_value); ?></b></td>
            <td width="110px" bgColor=#c0c0c0></td>
            <td width="100px"></td>
        </tr>

    </table>

    <br><br>

    <!--saldo string-->
        <b>
            <table>
                <tbody>
                    <?php if ($do_fine): ?>
                        <tr>
                            <td>Сальдо пени на начало месяца <?php echo $fine_saldo; ?>; начислено пени <?php echo $fine_value; ?>;оплачено пени <?php echo $fine_oplata; ?>; сальдо пени на конец месяца <?php echo $fine_saldo + $fine_value - $fine_oplata; ?>.
                            </td>
                        </tr>

                    <tr>
                        <td>Сальдо на начало месяца <?php echo sprintf("%22.2f", $saldo); ?>; сальдо на конец месяца <?php echo sprintf("%22.2f", $saldo - $oplata + $itog->itogo_with_nds); ?>.
                        </td>
                    </tr>
                    <?php endif; ?>                    
                </tbody>
            </table>
        </b>
    <!--saldo string-->

</font>
<br />
<br />
 <FONT size=7>
 <table width="100%">
 <tr>
 <td align="left"><b>Уполномоченный подписывать счета фактуры за руководителя согласно приказа №64 от 31.12.2015г.:<br/> <?php  echo 'Ташкенова С.С.'; ?></b></td>
 <td></td>
 <td align="right"><b>БЕРДІ (жеткізушінің жауапты тұлғасы)<br/>ВЫДАЛ (ответственное лицо поставщика) </b></td>
  </tr>
 <tr>
 <td> </td>
 <td align= "center">
     <table width="30%"border="2"cellpadding="0" cellspacing="0" align="center">
		
         <font size="10"><br/>МО/МП<br/></font>
   
	 </table>

  </td>
 <td> </td>
 </tr>
  <tr>
 <td align="left"> _______________________________________________</td>
 <td></td>
 <td align="right"> ______________________________________</td>
 </tr>
  <tr>
 <td align="left"><font size=6>(Қолы,аты,фамилиясы/Подпись,имя,фамилия)</font></td>
 <td></td>
 <td align="right"><font size=6>(лауазымы/должность)</font></td>
 </tr>
   <tr>
 <td align="center"></td>
 <td align="center"></td>
 </tr>
 <tr>
 <td align="left"><b>Уполномоченный подписывать счета фактуры за главного бухгалтера согласно приказа №64 от 31.12.2015 г.:<br/> <?php echo "Ташкенова С.С."; ?> </b></td>
 <td></td>
 <td align="right"></td>
 </tr>
 <tr>
 <td align="left"> </td>
 <td align="right"> </td>
 </tr>
  <tr>
 <td align="left">_____________________________</td>
 <td></td>
 <td align="right">____________________________</td>
 </tr>
  <tr>
 <td align="left"><font size=6>(Қолы,аты,фамилиясы/Подпись,имя,фамилия)</font></td>
 <td></td>
 <td align="right"><font size=6>(Қолы,аты,фамилиясы/Подпись,имя,фамилия)</font></td>
</tr>  
  <tr>
 <td align="center"></td>
 <td align="center"></td>
 </tr>

 <tr align=left>
	<td width=2100px>Ескерту: Мөрсіз жарамсыз. Түпнұсқа (бірінші данасы) - сатып алушыға. Көшірмесі (екінші дана) - жеткізушіге<br/>
	Примечание. Без печати недействительно. Оригинал (первый экземпляр)-покупателю. Копия (второй экземпляр)-поставщику.</td>
	</tr>
 </table>
  </FONT>
</body>
</html>