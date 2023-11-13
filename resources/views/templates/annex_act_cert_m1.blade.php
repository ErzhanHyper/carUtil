<html>
<head>
	<meta charset="utf-8">
	<title>Заявление</title>
	<style>
	body {
        font-family: 'DeJaVu Sans', sans-serif; font-size: 12px; }
	table {
        font-family: 'DeJaVu Sans', sans-serif; font-size: 12px; }
	table.bordered { border-bottom: 1px solid #000000; border-right: 1px solid #000000; }
	table.bordered tr td, table.bordered tr th { border-top: 1px solid #000000; border-left: 1px solid #000000; }
	</style>
</head>
<body>
<p style="text-align: right;">Приложение №1<br />
к Договору №[REGION_NUM]-{{ $data['order_num'] }}-[CAR_VIN]<br />
от [ORDER_DATE] года</p>

<p style="text-align: center; margin-top: 25px;"><strong>Акт №[REGION_NUM]-[ORDER_NUM]-[CAR_VIN]-[CAR_NUM]<br />
Акт комплектности вышедшего<br />из эксплуатации транспортного средства<br />
(для легковых автомобилей категории M1)<br />
VIN: [CAR_VIN]</strong></p>

<div style="float: left;">г. [REGION]</div>
<div style="float: right;">[ORDER_DATE]</div>
<div style="clear: both;"></div>

<p style="text-align: justify;">Товарищество с ограниченной ответственностью «Recycling Company»,
в лице [OPERATOR_NAME_FOR_DOCS], действующего на основании доверенности №[OPERATOR_BASE] именуемое в
дальнейшем «Сторона-1», [CLIENT_NAME], ИИН/БИН [CLIENT_IDNUM] именуемый в дальнейшем «Сторона-2»,
составили настоящий акт, подтверждающий комплектацию вышедшего из эксплуатации транспортного средства.</p>

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
	<tr>
		<td>1</td>
		<td>Кузов (шасси)</td>
		<td>[VL_1]</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>2</td>
		<td>Крышка капота</td>
		<td>[VL_2]</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>3</td>
		<td>Крышка багажника</td>
		<td>[VL_3]</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>4</td>
		<td>Двери</td>
		<td>[VL_4]</td>
		<td>(штатное количество) -</td>
	</tr>
	<tr>
		<td>5</td>
		<td>Колеса</td>
		<td>[VL_5]</td>
		<td>(штатное количество) -</td>
	</tr>
	<tr>
		<td>6</td>
		<td>Ограждающие покрытия колес (крылья)</td>
		<td>[VL_6]</td>
		<td>(штатное количество) -</td>
	</tr>
	<tr>
		<td>7</td>
		<td>Двигатель с генератором, стартером, карбюратором/системой впрыска</td>
		<td>[VL_7]</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>8</td>
		<td>Радиатор</td>
		<td>[VL_8]</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>9</td>
		<td>Редуктор</td>
		<td>[VL_9]</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>10</td>
		<td>Аккумулятор</td>
		<td>[VL_10]</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>11</td>
		<td>Коробка передач</td>
		<td>[VL_11]</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>12</td>
		<td>Раздаточная коробка, мосты и редуктора мостов, карданные валы</td>
		<td>[VL_12]</td>
		<td>(в случае если предусмотрена штатным оснащением изготовителя, установлено на штатных местах).</td>
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
