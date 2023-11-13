<html>
<head>
    <meta charset="utf-8">
    <title>Заявление</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { font-family: sans-serif; font-size: 11px; }
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

<p style="text-align: center; margin-top: 100px;"><strong>Акт приема – передачи вышедшего из эксплуатации транспортного средства и (или) самоходной сельскохозяйственной техники <br />№[REGION_NUM]-[ORDER_NUM]-[CAR_VIN]-[CAR_NUM]<br />
    </strong></p>

<div style="float: left;">г.&nbsp;[REGION]</div>
<div style="float: right;">[ORDER_DATE]</div>
<div style="clear: both;"></div>

<p style="text-align: justify; margin-top: 10px;">ТОО «RecyclingCompany», именуемое в дальнейшем «Сторона-1», в лице материально ответственного лица [OPERATOR_NAME], и Юридическое лицо [CLIENT_NAME], БИН [CLIENT_IDNUM], действующее на основании [PROXY], именуемое в дальнейшем «Сторона-2», составили
    настоящий акт, подтверждающий передачу вышедшего из эксплуатации транспортного средства VIN (либо заменяющие его сведения) или самоходной сельскохозяйственной техники номерной агрегат [CAR_VIN], категории [CAR_CATEGORY], «Стороне-1».</p>

<p><strong>За полноту и достоверность предоставленных при сдаче ВЭТС и (или) ВЭССХТ сведений и документов «Сторона-2» несет полную персональную ответственность.</strong></p>

<p><strong>При одобрении ТОО "Оператор РОП" заявки «Стороны-2» на выдачу скидочного сертификата или выплату денежных средств, право собственности на ВЭТС и (или) ВЭССХТ переходит «Стороне-1» и ВЭТС и (или) ВЭССХТ возврату не подлежит.</strong></p>

<table style="width: 100%;" cellspacing="0">
    <tr>
        <td><strong>Сторона-1</strong></td>
    </tr>
    <tr>
        <td>ТБО<br />
            <br>_____________________ <sub style="position: relative;top:8px;margin-left: -90px">Подпись</sub><br />
        </td>
        <td class="text-right">{{ $data['client_name'] }} <br />
            <br>_____________________ <sub style="position: relative;top:8px;margin-left: -90px">Подпись</sub><br />
        </td>
    </tr>
</table>
</body>
</html>
