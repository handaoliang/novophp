{{include file="common/header.tpl"}}
    <title>NovoPHP.com</title>
</head>

<body>
    {{$home_data}}<br />

    Controller Name: {{$controller}}<br />
    Actions Name: {{$actions}}<br />
    Charset: {{$charset}}<br />
    Language: {{$lang}}<br />
    Web Root Path: {{$web}}<br />
    Resource Root Path: {{$res}}<br />
    String:{{CommonFunc::mbString($test_string,3)}}
</body>
</html>
