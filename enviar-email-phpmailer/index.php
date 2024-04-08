<?php
require 'vendor_email/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader

function enviarBoleto($email)
{


    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    header('Content-Type: text/html; charset=UTF-8');
    $corpo = 'conteudo-do-meu-email';

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    $mail->IsSMTP(); // enable SMTP
    $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = "mail.gmail.com"; // utilize servidor de email (gmail, outlook...)
    $mail->Port = 587; // or 587
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = "seu-email";
    $mail->Password = "senha-email";
    $mail->SetFrom("seu-email");
    $mail->Subject = "titulo-email";
    $mail->Body = $corpo;
    $mail->AddAddress($email); // Email que será enviado
    if (!$mail->Send()) {
        return false;
    } else {

        return true;
    }
}


//CRIE sua lógica e utilize a função para realizar mais fácilmente a chamada, podendo expandir para uma lista. 

enviarBoleto("email@teste.com.br");
