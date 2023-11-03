<html style="padding:0; margin: 0;">

<head>
    <meta charset="UTF-8">
    <title>Расписка</title>
    <style>
        body {
            font-size: 13px;
            font-family: 'DeJaVu Sans';
            padding: 30px 45px;
        }
    </style>
</head>

<body>
<div class="page">
    <p style="text-align: right; width:50%; float: right;">
        <strong>АО «Жасыл Даму»</strong><br />
        <strong>От:</strong> {{ $data['cert_title'] }} <br />
        <strong>ИИН/БИН:</strong> {{ $data['cert_idnum'] }}<br />
        <strong>Адрес:</strong> {{ $data['cert_address'] }} <br />
        <strong>Телефон:</strong> {{ $data['cert_phone'] }} <br />
        <strong>Email:</strong> {{ $data['cert_email'] }}
    </p>

<p style="text-align: center; padding-top: 50px; clear: both; text-transform: uppercase;font-size: 14px"><strong>Заявление о передаче (перерегистрации) скидочного сертификата</strong></p>

<!--<p style="text-align: left; width: 40%; float: left;">Город [USER_CUSTOM_3]</p>-->
<p style="text-align: right; width: 40%; float: right;">Дата {{ $data['current_date'] }}</p>
<div style="clear: both;"></div>

<br style="text-align: justify; margin-top: 20px;">

    <p>
        Прошу Вас осуществить перерегистрацию скидочного сертификата {{ $data['cert_num'] }}, дата выдачи {{ $data['cert_date'] }}, принадлежащего {{ $data['cert_title'] }} ИИН/БИН: {{ $data['cert_idnum'] }}, на имя {{ $data['ex_title'] }} ИИН/БИН: {{ $data['ex_idnum'] }}.
    </p>

    <p style="text-align: justify;">
        Копии документов, удостоверяющих личность владельца скидочного сертификата (свидетельства о государственной регистрации юридического лица, документ, подтверждающий полномочия представителя, копия документа, удостоверяющего личность представителя) и лица, которому данный сертификат передается прилагаю на {{ $data['cert_page'] }} листах.
    </p>

    <p style="text-align: justify;">
        Настоящим сообщаю, что за предоставленные данные по переоформлению скидочного сертификата несу персональную ответственность. Сертификат перерегистрируется не в целях предпринимательской деятельности, за перерегистрацию скидочного сертификата не получаю каких-либо денежных средств от лица, на которое производится переоформление скидочного сертификата. Перерегистрацию совершаю по собственному желанию.
    </p>

    <p style="text-align: justify;">
        С условиями передачи (перерегистрации) скидочного сертификата, использования и сроками его действия ознакомлен и претензий не имею. Даю согласие на сбор и обработку моих персональных данных (данных юридического лица).
    </p>

<table style="width: 100%; font-size: 14px;">
    <thead>
    <tr>
        <td><strong>Владелец</strong></td>
        <td><strong>Получатель</strong></td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><br />(ФИО) {{ $data['cert_title'] }} <br /><br />
            (Дата) {{ $data['owner_sign_time'] }} <br /><br />
            {{ $data['owner_sign'] }}
        </td>
        <td><br />(ФИО) {{ $data['ex_title'] }} <br /><br />
            (Дата) {{ $data['receiver_sign_time'] }} <br /><br />
            {{ $data['receiver_sign'] }}
        </td>
    </tr>
    </tbody>
</table>
<br />
    <br />
    <div style="text-align: center">{!! $data['owner_sign_qr']  !!}</div>
    <br />
    <div style="text-align: center">{!! $data['receiver_sign_qr'] !!}</div>
    <br />
    {{ $data['sign_info_qr'] }}
</div>
</body>
</html>
