<?php echo form_open('billing/manual_sf'); ?>

    <table>
        <tbody>
        <tr>
            <td>
                <select style="width: 200px;" name="firm_id" id="firm_id">
                    <?php foreach ($firms as $firm): ?>
                        <option value="<?php echo $firm->id; ?>"><?php echo $firm->dogovor . ' ' . $firm->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <select name="period_id" id="period_id">
                    <?php foreach ($period as $p): ?>
                        <option value="<?php echo $p->id; ?>"><?php echo $p->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Другой номер счет-фактуры</td>
            <td><input type="text" value="" name="schet2"></td>
        </tr>
        <tr>
            <td>Другая дата счет-фактуры</td>
            <td><input type="text" value="" name="data_schet"></td>
        </tr>
        <tr>
            <td align="right">Условия оплаты по договору</td>
            <td><input name=edit1 value='<?php echo $firm->edit1; ?>'></td>
        </tr>
        <tr>
            <td align="right">Пункт назначения поставляемых <br>товаров (работ, услуг)</td>
            <td><input name=edit2 value='<?php echo $firm->edit2; ?>'></td>
        </tr>
        <tr>
            <td align="right">Поставка товаров осуществлена <br>по доверенности</td>
            <td><input name=edit3 value='<?php echo $firm->edit3; ?>'></td>
        </tr>
        <tr>
            <td align="right">Способ отправления</td>
            <td><input name=edit4 value='<?php echo $firm->edit4; ?>'></td>
        </tr>
        <tr>
            <td align="right">Грузоотправитель</td>
            <td><input name=edit5 value=''></td>
        </tr>
        <tr>
            <td align="right">Грузополучатель</td>
            <td><input name=edit6 value='<?php echo $firm->edit6; ?>'></td>
        </tr>
        <tr>
            <td align="right">Отправитель</td>
            <td><input name=edit7 value='<?php echo $firm->edit7; ?>'></td>
        </tr>
        <tr>
            <td align="right">Адрес отправителя</td>
            <td><input name=edit8 value='<?php echo $firm->edit8; ?>'></td>
        </tr>
        <tr>
            <td align="right">Номер договора и прочее</td>
            <td><input name=dog2 value='<?php echo $firm->dog2; ?>'></td>
        </tr>
        </tbody>
    </table>

    <br><br>

    <table style="border: 1px solid black">
        <caption>Тарифы</caption>
        <thead>
        <tr>
            <th>кВт</th>
            <th>Тариф</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><input type="text" name="kvt1"></td>
            <td><input type="text" name="tarif1"></td>
        </tr>
        <tr>
            <td><input type="text" name="kvt2"></td>
            <td><input type="text" name="tarif2"></td>
        </tr>
        <tr>
            <td><input type="text" name="kvt3"></td>
            <td><input type="text" name="tarif3"></td>
        </tr>
        </tbody>
    </table>
    <br><input type="submit" value="Открыть">
<?php echo form_close(); ?>