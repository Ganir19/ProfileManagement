<?php
session_start();
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];

    
    if ($password !== $password_repeat) {
        $error = "Passwords do not match!";
    } else {
       
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Email already exists!";
        } else {
           
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

           
            $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $password_hash);
            $stmt->execute();

            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['email'] = $email;

           
            header("Location: home.php");
            exit;
        }
    }
}
?>
<?php include 'nav.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
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
        <h2>Sign Up</h2>

        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required><br>
            <input type="password" name="password" placeholder="Enter your password" required><br>
            <input type="password" name="password_repeat" placeholder="Repeat your password" required><br>
            <button type="submit">Sign Up</button>
        </form>

        <div class="footer-links">
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </div>

</body>
</html>
