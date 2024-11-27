<?php
// Include the database connection file
include 'services/koneksi.php'; 
// Example login code
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the SQL query
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);  // "ss" means two string parameters
    $stmt->execute();

    // Check if the user exists
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // User found, proceed with login
        echo "Login successful!";
    } else {
        // User not found
        echo "Invalid credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="login.css">     
</head>
<body>
    <nav class="navbar">
        <a href="index.php">Home</a>
    </nav>

    <div class="container">
        <div class="login-card">
            <h1>Login Admin</h1>
            <form action="" method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email..." required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password..." required>
                </div>
                <button type="submit" class="login-button">LOGIN</button>
            </form>
        </div>
    </div>
</body>
</html>
