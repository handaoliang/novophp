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
                        <span style="font-size:14px;">本邮件由Comnovo.com发出，您收到此邮件是因为我们刚刚收到了您要求重置您在Comnovo.com的账号密码的指示，请点击以下链接进入到重置密码页重新设置您的密码：</span>
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="color:#333333;">
                        <p style="font-size:18px; line-height:24px; padding:20px; margin-bottom:20px; text-align:center; background-color:#E9FEFF; border:1px solid #BAF0F4;">
                            <a href="{{$reset_pwd_token_uri}}" target="_blank" style="color:#00979E;">点击这里重新设置您的密码</a>
                        </p>
                        <p>
                        如果上面的链接不能点击，请复制以下链接，并粘贴到浏览器的地址输入框，回车进入到重置密码页面：
                        </p>
                        <p>
                        <a href="{{$reset_pwd_token_uri}}" target="_blank" style="color:#00979E;">{{$reset_pwd_token_uri}}</a>
                        </p>
                        <p style="margin-top: 10px; font-size:14px;">
                        感谢您使用Comnovo.com重置密码功能，Comnovo.com致力于为用户创建最好的产品和应用，如果您有任何意见或建议，请发邮件告诉我们，我们会在最短的时间内给您以答复，我们的反馈邮箱是：<a style="font-style: italic; color: #444; text-decoration: underline;" href="mailto:{{$feedback_email}}">{{$feedback_email}}</a>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td height="20"></td>
            </tr>
            <tr>
                <td style="color: #666666; font-size: 11px;">
                    本邮件由Comnovo.com发出，您收到这封邮件，是因为有人在Comnovo.com使用了密码重置功能。如果这不是你本人的行为，请不要点击上面的链接，并且请把这封邮件删除，你在Comnovo.com的账号密码不会被重置。
                </td>
            </tr>
        </table>
    </body>
</html>
