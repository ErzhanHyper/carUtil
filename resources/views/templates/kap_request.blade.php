<html>
<head>
  <meta charset="UTF-8">
  <title>Справка запроса в КАП</title>
  <style>
    body {
      font-size: 14px;
      font-family: "DeJaVu Sans";
      margin: 0;
      padding: 0;
    }
    .page {
        margin: 0;
    }
    div.padding {
        padding: 90px 50px 0 50px;
    }
    h1 {
      font-size: 14px;
      text-align: center;
      margin-bottom: 22px;
    }
    p {
      line-height: 1.3em;
      margin-bottom: 8px;
      font-size: 13px;
    }
    p.center {
        text-align: center;
    }
    div.response {
        font-size: 12px;
        text-align: left;
        text-transform: uppercase;
    }
    p.small {
        font-size: 9px;
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
  </style>
</head>
<body>
    <div class="page">
        <div class="padding">
            <h1>{{ $data['header'] }}</h1>
            <p class="center">{!! $data['description'] !!}</p>
            <div class="left"><p>{{ $data['author'] }}</p></div>
            <div class="right"><p>{!! $data['date'] !!}</p></div>
            <div style="clear: both;"></div>
            <div class="response">{!! $data['response'] !!}</div>
            <div>
              <p class="center" style="margin-top: 20px; margin-bottom: 20px;">{!! $data['qr'] !!}</p>
            </div>
        </div>
    </div>
</body>
</html>
