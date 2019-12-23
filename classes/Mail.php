<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'classes/Exception.php';
require 'classes/PHPMailer.php';
require 'classes/SMTP.php';

class Mail{
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        // Instantiation and passing `true` enables exceptions
        //Server settings
        $this->mail->SMTPDebug = Config::Mail('debug');                      // Enable verbose debug output
        $this->mail->isSMTP();                                            // Send using SMTP
        $this->mail->Host       = Config::Mail('host');            // Set the SMTP server to send through
        $this->mail->SMTPAuth   = Config::Mail('SMTPAuth');        // Enable SMTP authentication
        $this->mail->Username   = Config::Mail('username');        // SMTP username
        $this->mail->Password   = Config::Mail('password');        // SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $this->mail->Port       = Config::Mail('port');
//        $this->mail->smtpConnect(
//            array(
//                "ssl" => array(
//                    "verify_peer" => false,
//                    "verify_peer_name" => false,
//                    "allow_self_signed" => true
//                )
//            )
//        );
    }
    
    /**
     * @param string $sender
     * @param string $recipient
     * @param string $subject email subject
     * @param string $body email message
     * @throws Exception if message has not been send
     */
    public function sendMail($sender, $recipient, $subject, $body){
        try {
            //Recipients
            $this->mail->setFrom($sender);
            $this->mail->addAddress($recipient);     // Add a recipient Name is optional
            //$this->mail->addReplyTo($sender);
            //$this->mail->addCC('cc@example.com');
            //$this->mail->addBCC('bcc@example.com');
    
            // Attachments
            //$this->mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$this->mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            
            // Content
            $this->mail->isHTML(true);                                  // Set email format to HTML
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            //$this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
            $this->mail->send();
        }catch (Exception $e){
            throw new Exception("Could not send email: {$e->getMessage()}");
        }
    }

}
