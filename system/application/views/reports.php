<?php

echo anchor("billing/vih_7_re_form","Оборотная ведомость 7-РЭ")."<br>";
echo anchor("billing/vih_2_re_form","Оборотная ведомость 2-РЭ")."<br>";
echo anchor("billing/pre_dolgi","Должники")."<br>";
echo anchor("billing/pre_list_of_firms","Cписок действующих организаций и предприятий")."<br>";
echo anchor("billing/reported_firms_form","Отчитавшиеся/неотчитавшиеся предприятия")."<br>";
echo anchor("billing/pre_oplata_svod","Свод по оплате")."<br>";
echo anchor("billing/pre_oplata_po_schetam","Оплата за текущий период")."<br>";
echo anchor("billing/pre_svod_po_tp","Свод по тп")."<br>";
echo anchor("billing/pre_svod_saldo_po_ture","Свод сальдо по участкам")."<br>";
echo anchor("billing/pre_energo_24","24 энергетика")."<br>";
echo anchor("billing/pre_poleznyy_otpusk","Полезный отпуск")."<br>";
echo anchor("billing/pre_naryad_zadanie_po_ture","Наряд задание по ТУРЭ")."<br>";
echo anchor("billing/pre_oborotka_with_predoplata","Оборотка с учетом предоплаты")."<br>";
echo anchor("billing/pre_svod_oplat_po_firmam","Свод оплат по фирмам")."<br>";
echo anchor("billing/statisticheskiy_otchet","Статистический отчет")."<br>";
echo anchor("billing/pre_oborotno_svodnaya_vedomost","Оборотно сводная ведомость")."<br>";
echo anchor("billing/pre_diff_tariff_controll","Ведомость по дифф тарифу")."<br>";
echo anchor("billing/pre_diff_tariff_spisok","Ведомость по дифф тарифу (развернутая) ")."<br>";
echo anchor("billing/pre_diff_tariff_controll_3","Ведомость по дифф тарифу ( 3 тарифа ) ")."<br>";
echo anchor("billing/pre_diff_tariff_spisok_3","Ведомость по дифф тарифу ( 3 тарифа ) (развернутая) ")."<br>";
echo anchor("billing/pre_analiz_diff_tarif","Анализ по тарифам (3 тарифа) ")."<br>";
echo anchor("billing/pre_analiz_diff_tarif_spisok","Анализ по тарифам (3 тарифа) развернутая ")."<br>";
echo anchor("billing/pre_platejnye_dokumenty","Платежные документы")."<br>";
echo anchor("billing/pre_report_to_nalogovaya","Отчет в налоговую")."<br>";
echo anchor("billing/pre_multi_tariff_count","Количество многоставочных и одноставочных счетчиков.")."<br>";
echo anchor("billing/pre_nadbavka_info","Информация по надбавкам.")."<br>";
echo anchor("billing/pre_subabonent","Список субабонентов")."<br>";
echo anchor("billing/pre_ne_potrebil","Список фирм с нулевым начислением")."<br>";
echo anchor("billing/pre_svod","Свод потребления кВт за период")."<br>";
echo anchor("billing/statisticheskiy_otchet_new","Статистический отчет (развернутый)")."<br>";
echo anchor("billing/pre_akt_snyatiya_pokazaniy","Акт снятия показаний")."<br>";
echo anchor("billing/pre_holostoy_hod","Холостой ход")."<br>";
echo anchor("billing/dispetcherskaya","Диспетчерская")."<br>";
echo anchor("billing/doljniki_za_period_form","Должники за период")."<br>";
echo anchor("billing/billing_point_info_all","Список счетчиков")."<br>";
echo anchor("billing/pre_analiz_mnogourovneviy_spisok","Анализ по многоуровневому тарифу (развернутый)")."<br>";
echo anchor("billing/pre_analiz_mnogourovneviy","Анализ по многоуровневому тарифу")."<br>";
echo anchor("billing/pre_ip_obshiy","Частники, расчитывающиеся по общему тарифу")."<br>";
echo anchor("billing/svod_kvt_po_godam","Свод киловат по годам")."<br>";
echo anchor("billing/pre_svod_new","Свод киловат за периоды по группам")."<br>";
echo anchor("billing/pre_analiz_obwii","Анализ по общему тарифу")."<br>";
echo anchor("billing/report_budjet","Бюджет")."<br>";
echo anchor("billing/report_prom","Промгруппа")."<br>";
echo anchor("billing/report_neprom","НеПромгруппа")."<br>";
echo anchor("billing/firm_poter","Фирмы с потерями")."<br>";
echo anchor("billing/pre_manual_sf","Счет-фактура")."<br>";
?>
<a href="<?php echo site_url('billing/pre_fine_2_re'); ?>"><?php echo '2-РЭ (пеня)'; ?></a><br>	
<a href="<?php echo site_url('billing/pre_fine_7_re'); ?>"><?php echo '7-РЭ (пеня)'; ?></a><br>
<a href="<?php echo site_url('billing/pre_diff_tariff_year'); ?>"><?php echo 'Годовая ведомость по дифф.тарифу (3 тарифа)'; ?></a><br>
<a href="<?php echo site_url('billing/pre_analiz_m_year'); ?>"><?php echo 'Годовой анализ по многоуровневому тарифу'; ?></a><br>
<a href="<?php echo site_url('billing/pre_kvt_year'); ?>"><?php echo 'Свод кВт по годам в разрезе фирм'; ?></a><br>
<a href="<?php echo site_url('billing/pre_tenge_year'); ?>"><?php echo 'Свод начислений по годам в разрезе фирм'; ?></a><br>
<a href="<?php echo site_url('billing/double_oplata'); ?>"><?php echo 'Дублирующиеся оплаты'; ?></a><br>
<a href="<?php echo site_url('billing/pre_billing_info_dop'); ?>"><?php echo 'Информация по точкам учета по ТУРЭ'; ?></a><br>
<li><a href="<?php echo site_url('billing/kontragent_rek'); ?>"><?php echo 'Список контрагентов с реквизитами'; ?></a></li>
