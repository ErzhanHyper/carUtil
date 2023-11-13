<html>
<head>
    <meta charset="utf-8">
    <title>Акт комплектности</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { font-family: sans-serif; font-size: 12px; }
        table.bordered { border-bottom: 1px solid #000000; border-right: 1px solid #000000; }
        table.bordered tr td, table.bordered tr th { border-top: 1px solid #000000; border-left: 1px solid #000000; }
    </style>
</head>
<body>
<p style="text-align: right;">Приложение №1<br />
    к Договору №[REGION_NUM]-[ORDER_NUM]-[CAR_VIN]<br />
    от [ORDER_DATE] года</p>

<p style="text-align: center; margin-top: 25px;"><strong>Акт №[REGION_NUM]-[ORDER_NUM]-[CAR_VIN]-[CAR_NUM]<br />
    Акт комплектности вышедшего<br />из эксплуатации самоходной сельскохозяйственной техники<br />
    (для самоходной сельскохозяйственной техники категории Трактор)<br />
    VIN: [CAR_VIN]</strong></p>

<div style="float: left;">г. [REGION]</div>
<div style="float: right;">[ORDER_DATE]</div>
<div style="clear: both;"></div>

<p style="text-align: justify;">Товарищество с ограниченной ответственностью «Recycling Company»,
    в лице [OPERATOR_NAME_FOR_DOCS], действующего на основании доверенности №[OPERATOR_BASE]
    именуемое в дальнейшем «Сторона-1», [CLIENT_NAME], ИИН/БИН [CLIENT_IDNUM] именуемый в дальнейшем «Сторона-2»,
    составили настоящий акт, подтверждающий комплектацию вышедшего из эксплуатации самоходной сельскохозяйственной техники.</p>

<br/>
<p style="text-align: left;">ВЭССХТ (трактор):</p>
<table class="bordered" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th>№</th>
            <th>Наименование</th>
            <th>Наличие</th>
            <th>Примечания</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>1</td> <td>Кабина</td><td>&nbsp;[VL_1]</td> <td></td> </tr>
        <tr><td>2</td><td>Двигатель</td><td>&nbsp;[VL_2]</td><td>&nbsp;</td></tr>
        <tr><td>3</td><td>Трансмиссия (полурама, задний мост, при наличии передний мост)</td><td>&nbsp;[VL_3]</td><td>&nbsp;</td></tr>
        <tr><td>4</td><td>Шины (при наличии)</td><td>&nbsp;[VL_4]</td><td>&nbsp;</td></tr>
        <tr><td>5</td><td>Колесные диски (при наличии)</td><td>&nbsp;[VL_5]</td><td>&nbsp;</td></tr>
        <tr><td>6</td><td>Аккумуляторные батареи (при наличии)</td><td>&nbsp;[VL_6]</td><td>&nbsp;</td></tr>
        <tr><td>7</td><td>Вес:</td><td> </td><td></td></tr>
        <tr><td>7.1</td><td>Килограмм</td><td> [WEIGHT]</td><td></td></tr>
        <tr><td>7.2</td><td>Процент (%) от заводской массы</td><td> [PROCENT_OF_ORIGINAL_WEIGHT]</td><td></td></tr>
    </tbody>
</table>

<br/>
<p style="text-align: left;">Отметка о подкатегории ВЭССХТ:</p>
<p style="text-align: left;">Необходимое отметить</p>
<table class="bordered" width="100%" cellspacing="0">
    <thead>
        <tr>
            <th>Комплектация 1 ВЭССХТ (трактор)</th>
            <th>Комплектация 2 ВЭССХТ (трактор)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center;">Скидочный сертификат на сумму </td>
            <td style="text-align: center;">Скидочный сертификат на сумму</td>
        </tr>
        <tr>
            <td style="text-align: center;">[VSSHT_1_CERT_SUM]</td>
            <td style="text-align: center;">[VSSHT_2_CERT_SUM]</td>
        </tr>
    </tbody>
</table>

<p>&nbsp;</p>

<table style="width: 100%;" cellspacing="0">
    <tr>
        <td><strong>Сторона-1</strong></td>
    </tr>
    <tr>
        <td><br>[QR_OPERATOR_SIGN] <br />ТОО «Recycling Company»<br />
            <b>[QR_OPERATOR_SIGN_INFO]</b><br />
        </td>
    </tr>
    <tr>
        <td><br><br><strong>Сторона-2</strong></td>
    </tr>
    <tr>
        <td><br>[QR_CLIENT_SIGN] <br />[CLIENT_NAME] <br />
            <b>[QR_CLIENT_SIGN_INFO]</b>
        </td>
    </tr>
</table>

</body>
</html>