<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'connection.php';
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gmail'])) {
    $email = $_POST['gmail'];


    $checkgmail = "SELECT * FROM intern_account WHERE gmail = ?";
    $stmt = $conn->prepare($checkgmail);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0 ) {
       
        $otp = rand(100000, 999999);

        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + (10 * 60);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'louie.jay.castillo@student.pnm.edu.ph';
            $mail->Password   = 'avmmnktcvnyoihse';   
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('louie.jay.castillo@student.pnm.edu.ph', 'Intern Management System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'OTP Verification for Password Reset';
            $mail->Body    = "Your OTP is <b>$otp</b>. It is valid for 10 minutes.";

            $mail->send();
            echo "<script>alert('OTP has been sent to " . htmlspecialchars($email) . "'); window.location.href='verify_otp.php'</script>";
            exit;
        } catch (Exception $e) {
            error_log("Error sending OTP: {$mail->ErrorInfo}");
            echo "There was an issue sending the OTP. Please try again later.";
        }
    } else {
        echo "<script>alert('Email Address not found ! '); window.location.href='ForgotPassword.php';</script>";

    }

    $stmt->close();
} else {
    echo "Invalid request.";
}
?>
