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

   
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $profile_image = $_FILES['profile_image']['tmp_name'];
            $image_data = file_get_contents($profile_image); 
        } else {
           
            $image_data = $user['profile_image']; 
        }

        $stmt = $conn->prepare("UPDATE users SET email = ?, password = ?, profile_image = ? WHERE id = ?");
        $stmt->bind_param("sssi", $new_email, $password_hash, $image_data, $user_id);
        $stmt->execute();

        $_SESSION['email'] = $new_email;

        $success = "Profile updated successfully!";
    }
}
?>

<?php include 'nav.php'; ?>

<div class="container">
    <h2>Edit Profile</h2>

    <?php if (isset($success)) { echo "<div class='success'>$success</div>"; } ?>
    <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
        <input type="password" name="password" placeholder="Enter your new password"><br>
        <input type="password" name="password_repeat" placeholder="Repeat your new password"><br>
        <input type="file" name="profile_image" accept="image/*"><br>
        <button type="submit">Update Profile</button>
    </form>


    <h3>Your Current Profile Image</h3>
    <?php if (!empty($user['profile_image'])): ?>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_image']); ?>" alt="Profile Image" class="profile-image">
    <?php else: ?>
        <img src="default-profile.jpg" alt="Default Profile Image" class="profile-image">
    <?php endif; ?>
</div>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
    }
    .container {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 300px;
        margin: 50px auto;
    }
    input[type="email"], input[type="password"], input[type="file"] {
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
        max-width: 100px;
        height: auto;
        border-radius: 50%;
        margin-top: 10px;
    }
</style>

</body>
</html>
