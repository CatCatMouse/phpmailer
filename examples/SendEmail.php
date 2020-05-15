<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2020/5/14
 * Time: 16:44
 */

if (!function_exists('classAutoLoadFunction')) {
    function classAutoLoadFunction($class)
    {
        $path = str_replace('\\',DIRECTORY_SEPARATOR, $class);
        $search = '/^PHPMailer\\'.DIRECTORY_SEPARATOR.'PHPMailer\\'.DIRECTORY_SEPARATOR.'/' ;
        $replace = '..'.DIRECTORY_SEPARATOR .'src'. DIRECTORY_SEPARATOR;
        $path = preg_replace( [$search],  [$replace] , $path);

        $file = __DIR__ . DIRECTORY_SEPARATOR . $path .'.php';

        if(file_exists($file)){
            require_once($file);
        }

    }
}
spl_autoload_register('classAutoLoadFunction');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class SendEmail extends PHPMailer
{
    public function __construct($config)
    {
        $this->isSMTP();
        $this->Debugoutput = SMTP::DEBUG_SERVER;
        $this->Host = $config['host']??'smtp.163.com';//邮箱服务器host:smtp.163.com
        $this->Port = $config['port']??'25';//邮箱服务器端口:25
        $this->SMTPAuth = true;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //tls

        //配置ssl
//        $this->SMTPSecure = EPM::ENCRYPTION_SMTPS; //  ssl
//        $this->SMTPOptions = array(
//            'ssl' => [
//                'verify_peer' => true,
//                'verify_depth' => 3,
//                'allow_self_signed' => true,
//                'peer_name' => 'smtp.example.com',
//                'cafile' => '/etc/ssl/ca_cert.pem',
//            ],
//        );
        /* 可登录163邮箱开启smtp服务获取 */
        $this->Username = $config['user_name'];//用户名 ***@163.com
        $this->Password = $config['pwd']; //授权密码
        //邮箱来自谁
        $this->setFrom($this->Username,$config['from_name']??'服务器');

    }

    public function send_email($title, $content, $to, $to_name)
    {

        //发送的邮箱的地址及名字
        $this->addAddress($to,$to_name);
        //发送的主题
        $this->Subject = $title;

        //邮件内容
        $content = htmlspecialchars_decode($content);
        $this->msgHTML($content);
        //发送
        $msg = "[".date('Y-m-d H:i:s')."] email:{$to}, clientName:{$to_name}, subject:{$title}, response:";
        if (!$this->send()) {
            $msg .= 'Mailer Error: ' . $this->ErrorInfo;
        }else{
            //避免重复发送
            $this->clearAddresses();
            $msg .= "Successs!";
        }
        echo $msg . "\r\n";
    }

}
//163邮箱配置
$config = [
    'host' => 'smtp.163.com',
    'port' => '25',
    'user_name' => 'your email server',
    'pwd' => 'your authorization password',
    'from_name' => 'Sender name',
];
$mail = new SendEmail($config);

/* 发送整理 */
$title = '163stmp测试'; //标题
$content = '测试';   //内容主题
$to = 'xx@qq.com';//接收方邮箱
$to_name = 'xx';//接受方名字

$mail->send_email($title, $content, $to, $to_name);








