<?php
namespace Exinfinite\Helpers\Mailer;
use function Exinfinite\Helpers\sanitizeStr;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Smtp {
    private $cfg = [
        'host' => '',
        'secure' => '',
        'port' => '',
        'charset' => '',
        'username' => '',
        'password' => '',
    ];
    private $mailer;
    public function __construct(Array $cfg = []) {
        $this->mailer = new PHPMailer(true);
        $this->mailer->IsSMTP();
        $this->mailer->SMTPAuth = true;
        $this->mailer->Timeout = 30;
        $this->mailer->isHTML(true);
        $this->setCfgs($cfg);
    }
    function setCfgs(Array $cfg = []) {
        $this->cfg = array_merge($this->cfg, array_intersect_key($cfg, $this->cfg));
        $this->mailer->Host = $this->cfg['host'];
        $this->mailer->SMTPSecure = $this->cfg['secure'];
        $this->mailer->Port = $this->cfg['port'];
        $this->mailer->CharSet = $this->cfg['charset'];
        $this->mailer->Username = $this->cfg['username'];
        $this->mailer->Password = $this->cfg['password'];
    }
    public function send(Array $from = [], Array $to = [], Array $mail = []) {
        /* session_write_close(); */
        $rst = false;
        try {
            extract([
                "_from_mail" => @key($from),
                "_from_name" => @current($from),
                "_to_email" => @key($to),
                "_to_name" => @current($to),
                "_subject" => @$mail['subject'],
                "_body" => @$mail['body'],
            ]);
            if ($this->validateAddress($_from_mail) && $this->validateAddress($_to_email)) {
                $this->mailer->setFrom($_from_mail, sanitizeStr($_from_name));
                $this->mailer->AddAddress($_to_email, sanitizeStr($_to_name));
                $this->mailer->Subject = sanitizeStr($_subject);
                $this->mailer->Body = $_body;
                $rst = $this->mailer->Send();
            }
        } catch (Exception $e) {}
        $this->ClearAllRecipients();
        return $rst;
    }
    public function setAddress(Array $list = [], $type = 'cc') {
        $map = ['cc' => 'addCC', 'bcc' => 'addBCC'];
        $method = array_key_exists($type, $map) ? $map[$type] : 'cc';
        foreach ($list as $mail => $name) {
            if ($this->validateAddress($mail)) {
                call_user_func([$this->mailer, $method], $mail, sanitizeStr($name));
            }
        }
    }
    public function addReplyTo($address, $name = '') {
        $this->mailer->addReplyTo($address, $name);
    }
    public function ClearAllRecipients() {
        $this->mailer->ClearAllRecipients();
    }
    protected function validateAddress($email) {
        return PHPMailer::validateAddress($email);
    }

}
?>