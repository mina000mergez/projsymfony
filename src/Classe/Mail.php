<?php
namespace App\Classe;

class Mail
{
    public function send($to_email, $to_name, $subject, $content)
    {
        // Simule l'envoi pour le projet
        file_put_contents(
            __DIR__ . '/../../var/log/mail_test.txt',
            "[" . date('Y-m-d H:i:s') . "] To: $to_email | Subject: $subject\n$content\n\n",
            FILE_APPEND
        );
    }
}