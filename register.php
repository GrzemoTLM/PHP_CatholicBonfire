<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Catholic Campfire</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="form-container">
    <h1>Register</h1>
    <form action="#" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm-password">Confirm Password:</label>
        <input type="password" id="confirm-password" name="confirm-password" required>

        <button type="submit" class="btn">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Log in</a></p>
</div>
<footer class="footer-form">
    <p>&copy; 2025 Catholic Campfire. All rights reserved.</p>
</footer>
</body>
</html>
