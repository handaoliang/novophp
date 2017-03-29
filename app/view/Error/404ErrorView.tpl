{{include file="Share/headerView.tpl"}}
    <title>访问的页面不存在 - 404 Error - NovoPHP.com</title>
    <style type="text/css">
        body{ padding-top:0px; background:#DAD8D9; background-color:#DAD8D9; background-image:none; }
        #error_404_container {
            width:700px; height:550px; overflow:hidden; margin:auto;
            background:url({{$res}}/images/error_404_bg.jpg) no-repeat 0 0;
        }
        #error_404_container div { width:450px; margin:auto; text-align:center; }
        #error_404_container div.e_title { color:#EC2829; padding:370px 0 10px 0; font-size:16px; border-bottom:1px dotted #AAAAAA; }
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
            if(num == 0) { window.location.href = redirectURI; }
        }
    </script>

</head>
<body>
<div id="error_404_container">
    <div class="e_title"></div>
    <div class="e_con">
        系统将于&nbsp;<span id="rd_time">8</span>&nbsp;秒钟后自动跳转到首页，或&nbsp;<a href="/">点击这里直接跳转»</a>
    </div>
</div>
<script type="text/javascript">
windowRedirect(8, "/", 'rd_time');
</script>
</body>
</html>
