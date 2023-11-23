<html>
<head>
    <meta charset="utf-8">
    <title>Акт комплектности</title>
    <style>
        body { font-family: 'DeJaVu Sans', sans-serif; font-size: 12px; }
        table.bordered { border-bottom: 1px solid #000000; border-right: 1px solid #000000; }
        table.bordered tr td, table.bordered tr th { border-top: 1px solid #000000; border-left: 1px solid #000000; }
    </style>
</head>
<body>
<p style="text-align: right;">Приложение №1<br />
    к Договору №{{ $data['region_num'] }}-{{ $data['order_num'] }}-{{ $data['car_vin'] }}<br />
    от {{ $data['order_date'] }} года</p>

<p style="text-align: center; margin-top: 25px;"><strong>Акт №{{ $data['region_num'] }}-{{ $data['order_num'] }}-{{ $data['car_vin'] }}-{{ $data['car_num'] }}<br />
    Акт комплектности вышедшего<br />из эксплуатации самоходной сельскохозяйственной техники<br />
    (для самоходной сельскохозяйственной техники категории Трактор)<br />
    VIN: {{ $data['car_vin'] }}</strong></p>

<div style="float: left;">г. {{ $data['region'] }}</div>
<div style="float: right;">{{ $data['order_date'] }}</div>
<div style="clear: both;"></div>

<p style="text-align: justify;">{{ $data['factory_name'] }},
    в лице {{ $data['operator_name_for_docs'] }}, действующего на основании доверенности №{{ $data['operator_base'] }}
    именуемое в дальнейшем «Сторона-1», {{ $data['client_name'] }}, ИИН/БИН {{ $data['client_idnum'] }} именуемый в дальнейшем «Сторона-2»,
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
        <tr><td>1</td> <td>Кабина</td><td>&nbsp;{{ $data['VL_1'] }}</td> <td></td> </tr>
        <tr><td>2</td><td>Двигатель</td><td>&nbsp;{{ $data['VL_2'] }}</td><td>&nbsp;</td></tr>
        <tr><td>3</td><td>Трансмиссия (полурама, задний мост, при наличии передний мост)</td><td>&nbsp;{{ $data['VL_3'] }}</td><td>&nbsp;</td></tr>
        <tr><td>4</td><td>Шины (при наличии)</td><td>&nbsp;{{ $data['VL_4'] }}</td><td>&nbsp;</td></tr>
        <tr><td>5</td><td>Колесные диски (при наличии)</td><td>&nbsp;{{ $data['VL_5'] }}</td><td>&nbsp;</td></tr>
        <tr><td>6</td><td>Аккумуляторные батареи (при наличии)</td><td>&nbsp;{{ $data['VL_6'] }}</td><td>&nbsp;</td></tr>
        <tr><td>7</td><td>Вес:</td><td> </td><td></td></tr>
        <tr><td>7.1</td><td>Килограмм</td><td> {{ $data['weight'] }}</td><td></td></tr>
        <tr><td>7.2</td><td>Процент (%) от заводской массы</td><td> {{ $data['percent_of_original_weight'] }}</td><td></td></tr>
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
            <td style="text-align: center;">{{ $data['vssht_1_cert_sum'] }}</td>
            <td style="text-align: center;">{{ $data['vssht_2_cert_sum'] }}</td>
        </tr>
    </tbody>
</table>

<p>&nbsp;</p>

<table style="width: 100%;">
    <tr>
        <td><strong>Сторона-1</strong></td>
    </tr>
    <tr>
        <td style="width: 50%">{{ $data['factory_name'] }}<br />
            <br>_____________________ <sub style="position: relative;top:8px;margin-left: -90px">Подпись</sub><br />
        </td>
        <td class="text-right" style="width: 50%">{{ $data['client_name'] }} <br />
            <br>_____________________ <sub style="position: relative;top:8px;margin-left: -90px">Подпись</sub><br />
        </td>
    </tr>
</table>

</body>
</html>
