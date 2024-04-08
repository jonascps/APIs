<?php
require 'vendor_email/autoload.php'; // Inclui o autoload do PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Função para enviar boleto por e-mail
function enviarBoleto($email)
{
    // Instância do PHPMailer
    $mail = new PHPMailer(true);

    // Configuração do servidor SMTP
    $mail->IsSMTP(); // Habilita SMTP
    $mail->SMTPDebug = 1; // Depuração: 1 = erros e mensagens, 2 = apenas mensagens
    $mail->SMTPAuth = true; // Autenticação SMTP ativada
    $mail->SMTPSecure = 'tls'; // Transferência segura ativada, necessário para Gmail
    $mail->Host = "mail.gmail.com"; // Servidor de e-mail (gmail, outlook, etc.)
    $mail->Port = 587; // Porta SMTP
    $mail->IsHTML(true); // Define que o e-mail será em HTML
    $mail->CharSet = 'UTF-8'; // Define o conjunto de caracteres como UTF-8
    $mail->Username = "seu-email"; // Seu e-mail
    $mail->Password = "senha-email"; // Sua senha de e-mail
    $mail->SetFrom("seu-email"); // Define o e-mail remetente
    $mail->Subject = "titulo-email"; // Assunto do e-mail

    // Corpo do e-mail
    $corpo = 'conteudo-do-meu-email';
    $mail->Body = $corpo;

    $mail->AddAddress($email); // Adiciona o e-mail do destinatário

    // Envia o e-mail
    if (!$mail->Send()) {
        return false; // Retorna falso se houver erro no envio
    } else {
        return true; // Retorna verdadeiro se o e-mail for enviado com sucesso
    }
}

// Chamada da função enviarBoleto com um e-mail de exemplo
enviarBoleto("email@teste.com.br");
?>
