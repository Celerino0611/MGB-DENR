<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "to_inventory");

$error_msg = "";

if (isset($_POST['login_btn'])) {
    
    $user_input = mysqli_real_escape_string($conn, $_POST['username']);
    $pass_input = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt  = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $user_input);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if ($pass_input == $user['password']) {
            
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
                exit();
            } elseif ($user['role'] == 'chief') {
                header("Location: chief/index.php");
                exit();
            } else {
                $error_msg = "Your account has no assigned role. Contact support.";
            }

        } else {
            $error_msg = "Invalid username or password.";
        }
    } else {
        $error_msg = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { 
            font-family: sans-serif; 
            background: #f4f7f6; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .login-box { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1); 
            width: 320px; 
        }
        h2 { 
            text-align: center; 
            color: #333; 
        }
        input { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            box-sizing: border-box; 
        }
        button { 
            width: 100%; 
            padding: 12px; 
            background: #007bff; 
            border: none; 
            color: white; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
        }
        button:hover { 
            background: #0056b3; }
        .error { 
            color: #d9534f; 
            background: #f2dede; 
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 10px; 
            font-size: 14px; 
            text-align: center; 
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login</h2>
    
    <?php if($error_msg): ?>
        <div class="error"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login_btn">Login</button>
    </form>
</div>
</body>
</html>