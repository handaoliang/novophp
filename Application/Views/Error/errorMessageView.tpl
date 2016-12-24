{{include file="Share/headerView.tpl"}}
    <title>访问出错 - NovoPHP.com - Error</title>
    <style type="text/css">
        body{ padding-top:0px; background:#DAD8D9; background-color:#DAD8D9; background-image:none; }
        #error_404_container {
            width:700px; height:550px; overflow:hidden; margin:auto;
            background:url({{$res}}/images/web_error_bg.jpg) no-repeat 0 0;
        }
        #error_404_container div { width:700px; text-align:center; }
        #error_404_container div.e_title { color:#EC2829; padding:350px 0 10px 0; font-size:18px; border-bottom:1px dotted #AAAAAA; }
        #error_404_container div.e_con { padding-top:10px; font-size:14px; }
        #error_404_container div.e_con span { color:#D06302; font-size:14px; font-weight:bold; }
    </style>
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
    </script>

</head>
<body>
<div id="error_404_container">
    <div class="e_title">{{if isset($error_msg)}}{{$error_msg}}{{else}}对不起，访问出错！{{/if}}</div>
    {{if isset($redirect_info)}}
    <div class="e_con">
        系统将于&nbsp;<span id="rd_time">8</span>&nbsp;秒钟后自动跳转到&nbsp;{{$redirect_info.name}}，或&nbsp;<a href="{{$redirect_info.uri}}">点击这里直接跳转»</a>
    </div>
    {{else}}
    <div class="e_con">
        系统将于&nbsp;<span id="rd_time">8</span>&nbsp;秒钟后自动返回上一页，或&nbsp;<a href="javascript:back_pre_page();">点击这里直接跳转»</a>
    </div>
    {{/if}}
</div>
<script type="text/javascript">
var redirect_time = 8;
{{if isset($redirect_info)}}
windowRedirect(redirect_time, '{{$redirect_info.uri}}', 'rd_time');
{{else}}
windowRedirect(redirect_time, "", 'rd_time');
{{/if}}
</script>
</body>
</html>
