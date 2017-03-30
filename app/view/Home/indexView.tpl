{{include file="Share/headerView.tpl"}}
    <title>Comnovo.com</title>
</head>

<body>
    {{$home_data}}<br />

    Controller Name: {{$controller}}<br />
    Actions Name: {{$actions}}<br />
    Charset: {{$charset}}<br />
    Language: {{$lang}}<br />
    Web Root Path: {{$web}}<br />
    Resource Root Path: {{$res}}<br />
    Files Path: {{$files}}<br />
    String:{{CommonFunc::mbString($test_string,3)}}
</body>
</html>
