<?php
session_start();
include 'db.php';  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Retrieve the user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Check if the user is blocked
        $failed_attempts = $user['failed_attempts'];
        $last_failed_attempt = strtotime($user['last_failed_attempt']);
        $current_time = time();

        // Block user for 5 minutes after 3 failed attempts
        if ($failed_attempts >= 3 && ($current_time - $last_failed_attempt) < 300) {
            $login_error = "Your account is locked. Please try again after 5 minutes.";
        } else {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Reset failed attempts on successful login
                $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, last_failed_attempt = NULL WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();

                // Set session data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['username'] = $user['username'];

                header("Location: home.php");
                exit;
            } else {
                // Increment failed attempts
                $failed_attempts++;
                $stmt = $conn->prepare("UPDATE users SET failed_attempts = ?, last_failed_attempt = NOW() WHERE email = ?");
                $stmt->bind_param("is", $failed_attempts, $email);
                $stmt->execute();

                $login_error = "Invalid email or password.";
            }
        }
    } else {
        $login_error = "Invalid email or password.";
    }
}
?>

<?php include 'nav.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            background-color: #4CAF50;
            padding: 15px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 15px;
        }

        .navbar a:hover {
            background-color: #45a049;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin: auto;
            margin-top: 50px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s;
        }

        input[type="email"]:focus, input[type="password"]:focus {
            border-color: #007bff;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }

        .footer-links {
            text-align: center;
            margin-top: 20px;
        }

        .footer-links a {
            text-decoration: none;
            color: #007bff;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Login</h2>

        <?php if (isset($login_error)) { echo "<div class='error'>$login_error</div>"; } ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required><br>
            <input type="password" name="password" placeholder="Enter your password" required><br>
            <button type="submit">Log In</button>
        </form>

        <div class="footer-links">
            <p>Forgot your password? <a href="forgot_password.php">Click here</a></p>
            <p>No Account? <a href="signup.php">Register here</a></p>
        </div>
    </div>

</body>
</html>
