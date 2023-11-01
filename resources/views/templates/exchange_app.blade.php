<html>
<head>
    <meta charset="utf-8">
    <title>Расписка</title>
    <style>
        body {
            font-family: "DeJaVu Sans";
            font-size: 14px;
            line-height: 1.5em;
        }
    </style>
</head>
<body>

<p style="text-align: right; width:50%; float: right;">
    <strong>АО «Жасыл Даму»</strong><br />
    <strong>От:</strong> [CERT_TITLE]<br />
    <strong>ИИН/БИН:</strong> [CERT_IDNUM]<br />
    <strong>Адрес:</strong> [CERT_ADDRESS] <br />
    <strong>Телефон:</strong> [CERT_PHONE] <br />
    <strong>Email:</strong> [CERT_EMAIL]
</p>

<p style="text-align: center; padding-top: 50px; clear: both; text-transform: uppercase;"><strong>Заявление о передаче (перерегистрации) скидочного сертификата</strong></p>

<!--<p style="text-align: left; width: 40%; float: left;">Город [USER_CUSTOM_3]</p>-->
<p style="text-align: right; width: 40%; float: right;">Дата [CURRENT_DATE]</p>
<div style="clear: both;"></div>

<br style="text-align: justify; margin-top: 20px;">Прошу Вас осуществить перерегистрацию скидочного сертификата [CERT_NUM], дата выдачи [CERT_DATE],
принадлежащего [CERT_TITLE] ИИН/БИН: [CERT_IDNUM], на имя [EX_TITLE] ИИН/БИН: [EX_IDNUM].
<p style="text-align: justify; margin-top: 20px;"></p>Копии документов, удостоверяющих личность владельца скидочного сертификата (свидетельства о государственной регистрации юридического лица, документ, подтверждающий полномочия представителя, копия документа, удостоверяющего личность представителя) и лица, которому данный сертификат передается прилагаю на [CERT_PAGE] листах.</p>
<p style="text-align: justify; margin-top: 20px;">Настоящим сообщаю, что за предоставленные данные по переоформлению скидочного сертификата несу персональную ответственность. Сертификат перерегистрируется не в целях предпринимательской деятельности, за перерегистрацию скидочного сертификата не получаю каких-либо денежных средств от лица, на которое производится переоформление скидочного сертификата. Перерегистрацию совершаю по собственному желанию.</p>
<p style="text-align: justify; margin-top: 20px;">С условиями передачи (перерегистрации) скидочного сертификата, использования и сроками его действия ознакомлен и претензий не имею. Даю согласие на сбор и обработку моих персональных данных (данных юридического лица).</p>

<table style="width: 100%; font-size: 14px;" cellspacing="0">
    <tr>
        <td><strong>Владелец</strong></td>
        <td><strong>Получатель</strong></td>
    </tr>
    <tr>
        <td><br />(ФИО) [CERT_TITLE] <br /><br />(Дата) [OWNER_SIGN_TIME] <br /><br />[Z_OWNER_SIGN]
        </td>
        <td><br />(ФИО) [EX_TITLE] <br /><br />(Дата) [RECEIVER_SIGN_TIME] <br /><br />[Z_RECEIVER_SIGN]
        </td>
    </tr>
</table>
<br />
[QR_OWNER_SIGN]<br />[QR_SIGN_INFO]

</body>
</html>
