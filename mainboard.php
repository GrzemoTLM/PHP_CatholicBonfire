<?php
require_once 'check_session.php';
require_once 'db.php';

// Pobierz kategorie z bazy danych
$db = new Database();
$categories = $db->query("SELECT id, name FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Board - Catholic Campfire</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="content">
    <h1>Welcome to Catholic Campfire</h1>
    <p>Choose your destination:</p>
    <div class="button-group">
        <a href="profile.php" class="btn">Your Profile</a>
        <a href="prayer_intentions.html" class="btn">Prayer Intentions</a>
    </div>

    <div class="post-creator">
        <label for="categoryId">Select Category:</label>
        <select id="categoryId">
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['id']) ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <textarea id="postContent" placeholder="Here write what you are thinking about"></textarea>
        <button class="btn" onclick="submitPost()">Post</button>
    </div>
</div>
<footer>
    <p>&copy; 2025 Catholic Campfire. All rights reserved.</p>
</footer>

<script>
    function submitPost() {
        const content = document.getElementById('postContent').value;
        const categoryId = document.getElementById('categoryId').value;

        if (!content.trim()) {
            alert('Post content cannot be empty.');
            return;
        }

        fetch('create_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `content=${encodeURIComponent(content)}&category_id=${categoryId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Post created successfully!');
                    document.getElementById('postContent').value = ''; // Wyczyść pole tekstowe
                    document.getElementById('categoryId').value = '1'; // Resetuj kategorię na "General"
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred. Check console for details.');
            });
    }
</script>
</body>
</html>
