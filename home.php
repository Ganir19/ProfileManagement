<?php
session_start();
include 'db.php';  
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}


$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $new_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $new_password = $_POST['password'];
    $new_password_repeat = $_POST['password_repeat'];


    if ($new_password !== $new_password_repeat) {
        $error = "Passwords do not match!";
    } else {
     
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

  
        $stmt = $conn->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_email, $password_hash, $user_id);
        $stmt->execute();

     
        $_SESSION['email'] = $new_email;

        $success = "Profile updated successfully!";
    }
}
?>

<?php include 'nav.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 50px auto;
        }
        .profile-info {
            margin-bottom: 20px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
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
        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome!</h2>

  
    <?php if (isset($success)) { echo "<div class='success'>$success</div>"; } ?>
    <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

    <h3>Profile Information</h3>


    <?php if (!empty($user['profile_image'])): ?>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_image']); ?>" alt="Profile Image" class="profile-image">
    <?php else: ?>
        <img src="default-profile.jpg" alt="Default Profile Image" class="profile-image">
    <?php endif; ?>

    <div class="profile-info">
        <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br>
    </div>


</div>

</body>
</html>
