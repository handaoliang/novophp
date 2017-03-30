<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>访问出错 - NovoPHP.com - Error</title>

    <style type="text/css">

    ::selection { background-color: #E13300; color: white; }
    ::-moz-selection { background-color: #E13300; color: white; }

    body {
        background-color: #fff;
        margin: 40px;
        font: 13px/20px normal Helvetica, Arial, sans-serif;
        color: #4F5155;
    }

    a {
        color: #003399;
        background-color: transparent;
        font-weight: normal;
    }

    h1 {
        color: #444;
        background-color: transparent;
        border-bottom: 1px solid #D0D0D0;
        font-size: 19px;
        font-weight: normal;
        margin: 0 0 14px 0;
        padding: 14px 15px 10px 15px;
    }

    code {
        font-family: Consolas, Monaco, Courier New, Courier, monospace;
        font-size: 12px;
        background-color: #f9f9f9;
        border: 1px solid #D0D0D0;
        color: #002166;
        display: block;
        margin: 14px 0 14px 0;
        padding: 12px 10px 12px 10px;
    }

    #body {
        margin: 0 15px 0 15px;
    }

    p.footer {
        text-align: right;
        font-size: 11px;
        border-top: 1px solid #D0D0D0;
        line-height: 32px;
        padding: 0 10px 0 10px;
        margin: 20px 0 0 0;
    }

    #container {
        margin: 10px;
        border: 1px solid #D0D0D0;
        box-shadow: 0 0 8px #D0D0D0;
    }
    </style>
</head>
<body>

<div id="container">
    <h1>{{if isset($error_msg)}}{{$error_msg}}{{else}}对不起，访问出错了，请联系官方客服人员反馈{{/if}}</h1>

    <div id="body">
        <p></p>
        {{if isset($redirect_info)}}
        <code>
            系统将于&nbsp;<span id="rd_time">8</span>&nbsp;秒钟后自动跳转到&nbsp;{{$redirect_info.name}}，或&nbsp;<a href="{{$redirect_info.uri}}">点击这里直接跳转»</a>
        </code>
        {{else}}
        <code>
            系统将于&nbsp;<span id="rd_time">8</span>&nbsp;秒钟后自动返回上一页，或&nbsp;<a href="javascript:back_pre_page();">点击这里直接跳转»</a>
        </code>
        {{/if}}
        <p></p>
    </div>

    <p class="footer">Power by <strong>NovoPHP</strong></p>
</div>
<script type="text/javascript">
    function windowRedirect(sec, uri, eid)
    {
        for(var i=sec; i>=0; i--) { 
            window.setTimeout('updateTimeInfo('+i+',"'+uri+'","'+eid+'")', (sec-i)*1000); 
        } 
    }
    function updateTimeInfo(num, redirectURI, showTimeElementId)
    {
        document.getElementById(showTimeElementId).innerHTML = num;
        if(num == 0) {
            if(redirectURI != ""){
                window.location.href = redirectURI;
            }else{
                window.history.go(-1);
            }
        }
    }
    function back_pre_page(){
         window.history.go(-1);
    }

    var redirect_time = 8;
    {{if isset($redirect_info)}}
    windowRedirect(redirect_time, '{{$redirect_info.uri}}', 'rd_time');
    {{else}}
    windowRedirect(redirect_time, "", 'rd_time');
    {{/if}}
</script>

</body>
</html>
