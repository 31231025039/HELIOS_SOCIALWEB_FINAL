<?php
// File: config/Mail.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail {
    public static function send($to, $subject, $body) {
        $mail = new PHPMailer(true);

        try {
            // Cấu hình Server
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // SMTP server của bạn, ví dụ Gmail
            $mail->SMTPAuth   = true;
            $mail->Username   = 'nguyenthanhthao0728@gmail.com'; // Email của bạn
            $mail->Password   = 'ryyh ipsv yatu lyte';    // Mật khẩu ứng dụng của Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Người gửi và người nhận
            $mail->setFrom('nguyenthanhthao0728@gmail.com', 'Helios');
            $mail->addAddress($to);

            // Nội dung
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Ghi lỗi ra log để debug
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}