<?php
session_start();

include 'nav.php';
include 'db.php'; 
$mail = include 'mailer.php'; 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        
        $otp = rand(100000, 999999);
        $expires_at = date("Y-m-d H:i:s", strtotime("+10 minutes"));  

        
        $stmt = $conn->prepare("UPDATE users SET otp = ?, otp_expires_at = ? WHERE id = ?");
        $stmt->bind_param("ssi", $otp, $expires_at, $user['id']);
        $stmt->execute();

 
        try {
            $mail->setFrom('ganeshs1pro@gmail.com', 'OTP Test');
            $mail->addAddress($email);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body = "Hi,<br><br>Your OTP to reset your password is provided below.<br><br>Please click on the link to verify your OTP and reset your password:<br><br><a href='verify_otp.php?email=" . urlencode($email) . "'>Click here to verify OTP</a>";

         
            if ($mail->send()) {
                echo "OTP has been sent to your email.";
                echo '<br><a href="verify_otp.php?email=' . urlencode($email) . '">Click here to verify OTP</a>';
            } else {
                echo "Error sending OTP email.";
            }
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        echo "No user found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
           
        }

        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 0 auto;
        }

        input[type="email"], button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .message {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>Forgot Password?</h1>
    
    <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required><br>
        <button type="submit">Send OTP</button>
    </form>

    <div class="message">
        <p>Remembered your password? <a href="login.php">Login here</a>.</p>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
