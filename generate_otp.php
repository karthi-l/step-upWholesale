<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("db_connect.php"); // Include database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

function generateOTP() {
    return rand(100000, 999999); // Generate a 6-digit OTP
}
function sendOTPEmail($recipient_email, $otp, $name) {
    $mail = new PHPMailer(true);
    
    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->Username   = '';                     //SMTP username
        $mail->Password   = '';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            // ENCRYPTION_SMTPS - Enable implicit TLS encryption
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('stepupwholesale.247@gmail.com', 'step-upWholesale Verifictaion');
        $mail->addAddress($recipient_email, 'Hello Retailer !');     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Email Verification for Step-upWholesale';
        $mail->Body    = '
            <h1>Hello, '. $name . '</h1>
            <h3>Here is your OTP for Login/Registration</h3>
            <h4>OTP : '. $otp . '</h4>
        ';
        if($mail->send()){
            $_SESSION['status'] = "OTP has been mailed";
        } else{
            if($_SESSION['authType'] == 'login'){
                $_SESSION['status'] ="Mail has not been sent. Mailer Error:{$mail->ErrorInfo}";
            }elseif($_SESSION['iauthType'] == 'register'){
                $_SESSION['status'] ="Mail has not been sent. Mailer Error:{$mail->ErrorInfo}";
            }else{
                $_SESSION['status'] = "Mail has not been sent.Mailer Error:{$mail->ErrorInfo}"; 
            }

        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

?>
