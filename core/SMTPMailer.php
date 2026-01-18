<?php
// core/SMTPMailer.php

class SMTPMailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $timeout = 30;

    public function __construct($host, $port, $username, $password) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        // Nettoyage agressif
        $this->password = preg_replace('/\s+/', '', $password);
    }

    public function send($to, $subject, $body, $fromName = 'Notification Absences') {
        try {
            $socket = fsockopen(($this->port == 465 ? "ssl://" : "") . $this->host, $this->port, $errno, $errstr, $this->timeout);
            if (!$socket) throw new Exception("Erreur connexion SMTP: $errstr ($errno)");

            $this->read($socket); // Welcome message

            $this->cmd($socket, "EHLO " . $_SERVER['SERVER_NAME']);
            
            if ($this->port == 587) {
                $this->cmd($socket, "STARTTLS");
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                $this->cmd($socket, "EHLO " . $_SERVER['SERVER_NAME']);
            }

            $this->cmd($socket, "AUTH LOGIN");
            $this->cmd($socket, base64_encode($this->username));
            $this->cmd($socket, base64_encode($this->password));

            $this->cmd($socket, "MAIL FROM: <" . $this->username . ">");
            $this->cmd($socket, "RCPT TO: <$to>");
            $this->cmd($socket, "DATA");

            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: $fromName <" . $this->username . ">\r\n";
            $headers .= "To: $to\r\n";
            $headers .= "Subject: $subject\r\n";

            $this->cmd($socket, $headers . "\r\n" . $body . "\r\n.");
            $this->cmd($socket, "QUIT");

            fclose($socket);
            return true;
        } catch (Exception $e) {
            error_log("SMTP Error: " . $e->getMessage());
            return false;
        }
    }

    private function cmd($socket, $cmd) {
        fwrite($socket, $cmd . "\r\n");
        $response = $this->read($socket);
        
        if (!preg_match('/^[23]/', $response)) {
             // error_log("SMTP Error ($cmd): $response"); 
             return false;
        }
        return $response;
    }

    private function read($socket) {
        $response = "";
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == " ") break;
        }
        return $response;
    }
}
