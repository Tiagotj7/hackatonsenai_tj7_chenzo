<?php
// config/mailer.php
require_once __DIR__ . '/config.php';

function send_email($to, $subject, $body) {
  if (!EMAIL_ENABLED) return false;
  $headers = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM . ">\r\n" .
             "MIME-Version: 1.0\r\n" .
             "Content-Type: text/html; charset=UTF-8\r\n";
  return @mail($to, $subject, $body, $headers);
}