<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body style="background-color:#F1FEFF;">
        <table border="0" cellpadding="0" cellspacing="0" style="font-family: Verdana; font-size: 14px; line-height: 20px; color: #464646; font-weight: normal; width: 680px; padding:20px 50px; background-color: #F1FEFF; ">
            <tr>
                <td valign="top">
                    <p style="font-size: 18px; line-height: 24px;">
                    亲爱的 {{$user_name}}，您好！
                    </p>
                    <p>
                        <span style="font-size: 14px; line-height: 36px; color:#464646; font-weight:bold;">非常欢迎您来到Comnovo.com。</span>
                        <br>
                        <span style="font-size:14px;">在正式开始您愉快的体验旅途之前，为了能为您提供更优质的服务，我们需要验证证您的邮箱。请不吝花费一点点时间激活您的账号：</span>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="color:#333333;">
                        <p style="font-size:18px; line-height:24px; padding:20px; margin-bottom:20px; text-align:center; background-color:#E9FEFF; border:1px solid #BAF0F4;">
                            <a href="{{$active_link}}" target="_blank" style="color:#00979E;">点击这里激活您的账号</a>
                        </p>
                        <p>
                        如果不能点击上面的链接，请复制以下链接，并粘贴到浏览器的地址输入框，回车直接激活：
                        </p>
                        <p>
                        <a href="{{$active_link}}" target="_blank" style="color:#00979E;">{{$active_link}}</a>
                        </p>
                        <p style="margin-top: 10px;">
                        感谢您对Comnovo.com的关注，Comnovo.com致力于为用户创建最好的产品和应用，我们很用心但我们还会有很多不够完善的地方，如果您有任何意见或建议，请发邮件告诉我们，我们会在最短的时间内给您以答复，我们的反馈邮箱是：<a style="font-style: italic; color: #444; text-decoration: underline;" href="mailto:{{$feedback_email}}">{{$feedback_email}}</a>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td height="20"></td>
            </tr>
            <tr>
                <td style="color: #666666; font-size: 11px;">
                    本邮件由Comnovo.com发出，您收到这封邮件，是因为有人在Comnovo.com用：&nbsp;<a style="font-style: italic; color: #666666; text-decoration: none;" href="mailto:{{$user_email}}">{{$user_email}}</a>&nbsp;注册了一个帐号。如果这不是你本人的行为，请不要点击上面的链接，并且请把这封邮件删除，你的邮箱不会被注册。
                </td>
            </tr>
        </table>
    </body>
</html>
