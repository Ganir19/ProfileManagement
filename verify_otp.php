<?php
session_start();

if (!isset($_GET['email'])) {
    echo "Invalid request.";
    exit;
}

include 'db.php';  
include 'nav.php';

$email = $_GET['email'];


$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND otp_expires_at > NOW()+60");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
  
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $otp_entered = $_POST['otp'];
        $new_password = $_POST['password'];
        $new_password_repeat = $_POST['password_repeat'];

      
        if ($otp_entered != $user['otp']) {
            die("Invalid OTP entered.");
        }

      
        if ($new_password !== $new_password_repeat) {
            die("Passwords do not match.");
        }

       
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ?, otp = NULL, otp_expires_at = NULL WHERE email = ?");
        $stmt->bind_param("ss", $password_hash, $email);
        $stmt->execute();

        echo "Password has been successfully updated. You can now log in.";
    }
} else {
    echo "OTP has expired or is invalid.";
}
?>
<html>
<head>
    <title>Verify Otp</title>
 <style>
        body {
            font-family: Arial, sans-serif;
           
            height: 100vh;
            margin: 0;
            background-color: #f4f4f9;
        }
        
        input[type="text"], input[type="password"] {
            width: 30%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 30%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
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
        .center1{
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

 <body>
<center>
<div>
<form method="POST">
    <input type="text" name="otp" placeholder="Enter OTP" required><br>
    <input type="password" name="password" placeholder="Enter your new password" required><br>
    <input type="password" name="password_repeat" placeholder="Repeat your new password" required><br>
    <button type="submit">Reset Password</button>
</form>
</div></center>
 </body>
</html>