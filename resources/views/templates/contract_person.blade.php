<html>
<head>
    <meta charset="utf-8">
    <title>Заявление</title>
    <style>
        body { font-family: 'DeJaVu Sans'; font-size: 12px; }
        table { font-family: 'DeJaVu Sans'; font-size: 11px; }
        p { margin: 0 0 12px 0; }
        .page-breaker {
            clear: both;
            display: block;
            border: 1px solid transparent;
            page-break-after: always;
        }
    </style>
</head>
<body>
<!-- <div class="page-breaker"></div> -->
<!-- <p style="text-align: right;">Приложение №2<br />
к Правилам и условиям выдачи документов,<br />
подтверждающих сдачу на утилизацию вышедшего<br />
из эксплуатации транспортного средства,<br />
в том числе предусматривающего право на получение скидки<br />
на приобретение транспортного средства<br />
на территории Республики Казахстан,<br />
произведенного в Республике Казахстан<br /> -->

<p style="text-align: center; margin-top: 100px;"><strong>Акт приема – передачи вышедшего из эксплуатации транспортного средства и (или) самоходной сельскохозяйственной техники <br />№{{ $data['region_num'] }}-{{ $data['order_num'] }}-{{ $data['car_vin'] }}-{{ $data['car_num'] }}<br />
    </strong></p>

<div style="float: left;">г.&nbsp;{{ $data['region'] }}</div>
<div style="float: right;">{{ $data['order_date'] }}</div>
<div style="clear: both;"></div>

<p style="text-align: justify; margin-top: 10px;">АО "Жасыл Даму", именуемое в дальнейшем «Сторона-1», в лице материально ответственного лица {{ $data['operator_name'] }}, и {{ $data['client_name'] }}, ИИН {{ $data['client_idnum'] }}, удостоверение личности №{{ $data['ud_num'] }} выдано {{ $data['ud_expired'] }} {{ $data['ud_issued'] }}, именуемый(-ая) в дальнейшем «Сторона-2», составили
    настоящий акт, подтверждающий передачу вышедшего из эксплуатации транспортного средства VIN (либо заменяющие его сведения) и (или) самоходной сельскохозяйственной техники {{ $data['car_vin'] }}, категории {{ $data['car_category'] }}, «Стороне-1».</p>

<p><strong>За полноту и достоверность предоставленных при сдаче ВЭТС и (или) ВЭССХТ сведений и документов «Сторона-2» несет полную персональную ответственность.</strong></p>

<p><strong>При одобрении АО "Жасыл Даму" заявки «Стороны-2» на выдачу скидочного сертификата или выплату денежных средств, право собственности на ВЭТС и (или) ВЭССХТ переходит «Стороне-1» и ВЭТС и (или) ВЭССХТ возврату не подлежит.</strong></p>

<table style="width: 100%;" cellspacing="0">
    <tr>
        <td><strong>Сторона-1</strong></td>
    </tr>
    <tr>
        <td><br>[QR_OPERATOR_SIGN] <br />АО "Жасыл Даму"<br />
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
