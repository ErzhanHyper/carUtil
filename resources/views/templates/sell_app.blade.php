<html>
<head>
    <meta charset="utf-8">
    <title>Расписка</title>
    <style>
        body {
            font-family: "DeJaVu Sans";
            font-size: 14px;
        }
    </style>
</head>
<body>

<p style="text-align: right; width: 50%; float: right;">
    <strong>Кому:</strong> {{ $data['user_for_docs'] }}<br />
    <strong>Представитель: </strong> {{ $data['user_title'] }}<br /><br />
    <strong>От:</strong> {{ $data['cert_title'] }}<br />
    <strong>ИИН / БИН</strong> {{ $data['cert_idnum'] }}
</p>

<p style="text-align: center; padding-top: 100px; clear: both; text-transform: uppercase;"><strong>Анкета на использование скидочного сертификата</strong></p>

<p style="text-align: left; width: 40%; float: left;">Город {{ $data['user_custom_3'] }}</p>
<p style="text-align: right; width: 40%; float: right;">Дата {{ $data['current_date'] }}</p>
<div style="clear: both;"></div>

<p style="text-align: justify; margin-top: 20px;">
    Прошу вас одобрить использование скидочного сертификата №{{ $data['cert_num'] }}, дата выдачи {{ $data['cert_date'] }} соответственно, принадлежащего {{ $data['cert_title'] }}, ИИН / БИН {{ $data['cert_idnum'] }} на покупку автомобильного транспортного средства марки ___________________________________, VIN ___________________________________, год выпуска ______________.
</p>
<p style="text-align: justify; margin-top: 20px;">Настоящим сообщаю, что за предоставленные данные несу персональную ответственность. Скидочный сертификат ранее не использовлся, с условиями покупки, возврата приобретаемого автомобильного транспортного ознакомлен, претензий не имею.</p>
<p style="text-align: justify; margin-top: 20px;">Даю согласие на сбор, обработку и хранение персональных данных.</p>

<p style="text-align: right; margin-top: 100px;"><strong>{{ $data['cert_title'] }}</strong></p>
<p style="text-align: right; margin-top: 40px;">___________________________________</p>
<p style="text-align: right; margin-top: 10px;">М.П. (при наличии)</p>

<p style="text-align: right; margin-top: 50px;"><strong>{{ $data['user_title'] }}</strong></p>
<p style="text-align: right; margin-top: 40px;">___________________________________</p>
<p style="text-align: right; margin-top: 10px;">М.П.</p>
</body>
</html>
