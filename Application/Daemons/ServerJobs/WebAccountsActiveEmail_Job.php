<?php
if(!defined('NOVOPHP_VENDORS_DIR')){
	echo "NOVOPHP_VENDORS_DIR is not defined, WebAccountsActiveEmail.Job";
	exit;
}
require_once NOVOPHP_VENDORS_DIR.'/MailMime/mime.php';

class WebAccountsActiveEmail_Job extends BaseEmailServerJobs
{
    public function perform()
    {
        $mailTitles = $this->args["mail_title"];

        $serverJobsViewsDir = dirname(__FILE__)."/Views";
        $mailBody = $this->getMailBody($serverJobsViewsDir, $this->args, "WebAccountsActiveEmail_Job");

        if($this->emailConnetHandler == NULL){
            $this->AmazonSMTPConnect();
        }
        $this->sendEmailViaAmazon($mailTitles, $mailBody, $this->args);
    }

    public function sendEmailViaAmazon($title, $body, $args)
    {
        $mail_mime = new Mail_mime();
        $mail_mime->setHTMLBody($body);

        $body = $mail_mime->get();
        $headers = $mail_mime->txtHeaders(array('From'=>'Comnovo Web Service<'.SERVICE_EMAIL.'>', 'Reply-To'=>SERVICE_EMAIL, 'Subject'=>"$title"));

        $message = $headers."\r\n".$body;

        $sendResult = $this->emailConnetHandler->send_raw_email(array('Data'=>base64_encode($message)), array('Destinations'=>$args['user_email']));

        if ($sendResult->isOK())
        {
            print("Mail sent; message id is ".(string)$sendResult->body->SendRawEmailResult->MessageId."\n");
        } else {
            print("Mail not sent; error is ".(string) $sendResult->body->Error->Message."\n");
        }
    }
}
