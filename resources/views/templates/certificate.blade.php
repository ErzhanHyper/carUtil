<html style="padding:0; margin: 0;">

<head>
    <meta charset="UTF-8">
    <title>Сертификат на ТС</title>

    <style>
        body {
            font-size: 14px;
            font-family: 'DeJaVu Sans';
            margin: 0;
            padding: 0;
            background: #ffffff url('{{public_path("img/certificate.jpg")}}') top center repeat-y;
            background-size: contain;
            letter-spacing: -0.5px;
            font-weight: 400;
        }

        .page {
            padding: 85px 70px 0 70px;
        }

        h1 {
            font-size: 13px;
            text-align: center;
            margin-bottom: 25px;
        }

        p {
            margin-bottom: 9px;
            font-size: 12px;
        }

        p.center {
            text-align: center;
        }

        p.small {
            font-size: 10px;
            text-align: justify;
        }

        table td, table th {
            border-left: 1px solid #000000;
            border-top: 1px solid #000000;
            padding: 5px;
        }

        table {
            width: 100%;
            border-right: 1px solid #000000;
            border-bottom: 1px solid #000000;
        }

        .left {
            float: left;
            width: 40%;
            text-align: left;
        }

        .right {
            float: right;
            width: 40%;
            text-align: right;
        }

        img {
            border: 1px solid #555;
            margin: 0 1px;
        }
    </style>
</head>
<body>
<div class="page">
    <h1>СКИДОЧНЫЙ СЕРТИФИКАТ</h1>
    <p class="center">{{ $data['z_num'] }}</p>
    <p style="text-align: justify;"><strong>{{ $data['pre'] }}</strong></p>
    <p style="text-align: justify;">{!! $data['detail1'] !!}</p>
    <p style="text-align: justify;">{!! $data['detail2'] !!}</p>
    <p style="text-align: justify;">{!! $data['detail3'] !!}</p>
    <div class="left"><p><strong>{{ $data['z_date'] }}</strong></p></div>
    <div class="right"><p><strong>{{ $data['z_to'] }}</strong></p></div>
    <div style="clear: both;"></div>
    <p class="center" style="margin-top: 50px; margin-bottom: 0px;">{!! $data['z_qr1'] !!} {!! $data['z_qr2'] !!} {!! $data['z_qr3'] !!} {!! $data['z_qr4'] !!}</p>
    <p style="margin: 0 auto; max-width: 85%;font-size: 11px;text-align: justify;line-height: 1.1">{{ $data['text_subtitle'] }}</p>
</div>
</body>
</html>
