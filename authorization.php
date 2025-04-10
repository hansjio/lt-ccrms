<?php
session_start();
include 'configs/config.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, username, password_hash, role FROM users WHERE username = ? AND is_active = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        // Verify the password
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct, create session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Update last login time
            $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
            $updateStmt->bind_param("i", $user['user_id']);
            $updateStmt->execute();
            
            // Redirect to dashboard
            header("Location: index.php");
            exit();
        }
    }
    
    // If we get here, login failed
    $error = "Invalid username or password";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lupon Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: rgb(249, 244, 239);
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #db8505;
            margin-bottom: 2rem;
            font-size: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
        }
        input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        input:focus {
            border-color: #db8505;
            outline: none;
        }
        button {
            width: 100%;
            padding: 1rem;
            background: #db8505;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #c67704;
        }
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
