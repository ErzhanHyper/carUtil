<html>
<head>
	<meta charset="utf-8">
	<title>Заявление</title>
	<style>
        body { font-family: 'DeJaVu Sans', sans-serif; font-size: 12px; }
	table.bordered { border-bottom: 1px solid #000000; border-right: 1px solid #000000; }
	table.bordered tr td, table.bordered tr th { border-top: 1px solid #000000; border-left: 1px solid #000000; }
	</style>
</head>
<body>
<p style="text-align: right;">Приложение №1<br />
к Договору №{{ $data['region_num'] }}-{{ $data['order_num'] }}-{{ $data['car_vin'] }}<br />
от [ORDER_DATE] года</p>

<p style="text-align: center; margin-top: 25px;"><strong>Акт №{{ $data['region_num'] }}-{{ $data['order_num'] }}-{{ $data['car_vin'] }}-{{ $data['car_num'] }}<br />
Акт комплектности вышедшего<br />из эксплуатации транспортного средства<br />
(для транспортных средств, используемых для перевозки пассажиров категории N1, N2, N3)<br />
VIN: {{ $data['car_vin'] }}</strong></p>

<div style="float: left;">г. {{ $data['region'] }}</div>
<div style="float: right;">{{ $data['order_date'] }}</div>
<div style="clear: both;"></div>

<p style="text-align: justify;">{{ $data['factory_name'] }},
в лице {{ $data['operator_name_for_docs'] }}, действующего на основании доверенности №{{ $data['operator_base'] }} именуемое в
дальнейшем «Сторона-1», {{ $data['client_name'] }}, ИИН/БИН {{ $data['client_idnum'] }} именуемый в дальнейшем «Сторона-2»,
составили настоящий акт, подтверждающий комплектацию вышедшего из эксплуатации транспортного средства.</p>

<table class="bordered" width="100%" cellspacing="0">
<thead><tr><th>№</th><th>Наименование</th><th>Наличие</th><th>Примечания</th></tr></thead><tbody>
 <tr><td>1</td><td>Кузов/ фургон/ платформа/ специальное оборудование на шасси (в случае если они предусмотрены штатным оснащением изготовителя)</td><td>&nbsp;{{ $data['VL_1'] }}</td><td>&nbsp;</td></tr>
 <tr><td>2</td><td>Кабина</td><td>&nbsp;{{ $data['VL_2'] }}</td><td>&nbsp;</td></tr>
 <tr><td>3</td><td>Шасси</td><td>&nbsp;{{ $data['VL_3'] }}</td><td>&nbsp;</td></tr>
 <tr><td>4</td><td>Двери</td><td>&nbsp;{{ $data['VL_4'] }}</td><td>(штатное количество) - {{ $data['doors_count'] }}</td></tr>
 <tr><td>5</td><td>Колеса</td><td>&nbsp;{{ $data['VL_5'] }}</td><td>(штатное количество) - {{ $data['wheels_count'] }}</td></tr>
 <tr><td>6</td><td>Двигатель с генератором, стартером, карбюратором/системой впрыска</td><td>&nbsp;{{ $data['VL_6'] }}</td><td>&nbsp;</td></tr>
 <tr><td>7</td><td>Радиатор</td><td>&nbsp;{{ $data['VL_7'] }}</td><td>&nbsp;</td></tr>
 <tr><td>8</td><td>Аккумулятор</td><td>&nbsp;{{ $data['VL_8'] }}</td><td>&nbsp;</td></tr>
 <tr><td>9</td><td>Коробка передач</td><td>&nbsp;{{ $data['VL_9'] }}</td><td>&nbsp;</td></tr>
 <tr><td>10</td><td>Раздаточная коробка, мосты и редукторы мостов, крупноузловые детали, подвески ходовой части, карданные валы</td><td>&nbsp;{{ $data['VL_10'] }}</td><td>(в случае предусмотрения штатным оснащением изготовителя) </td></tr>
</tbody></table>

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
