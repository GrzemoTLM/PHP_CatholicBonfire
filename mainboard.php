<?php
require_once 'check_session.php';
require_once 'db.php';

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
    <div class="main-logo">
        <img src="multimedia/logo2.png" alt="Catholic Campfire Logo" class="main-logo">
    </div>
    <h1>Welcome to Catholic Campfire</h1>
    <p>Choose your destination:</p>
    <div class="button-group">
        <a href="profile.php" class="btn">Your Profile</a>
        <a href="prayer_intentions.php" class="btn">Prayer Intentions</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
    <div class="post-form">
        <h2>Add a Post</h2>
        <form id="addPostForm">
            <label for="categoryId" class="category-label">Select Category:</label>
            <select id="categoryId" class="custom-select">
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="postTitle" placeholder="Enter post title here" class="input-field">
            <textarea id="postContent" placeholder="Here write what you are thinking about" class="textarea-field"></textarea>
            <button type="button" class="btn" onclick="submitPost()">Post</button>
        </form>
    </div>
    <div class="posts-container">
        <h2>Recent Posts</h2>
        <div id="posts"></div>
    </div>
</div>
<footer>
    <p>&copy; 2025 Catholic Campfire. All rights reserved.</p>
</footer>

<script>
    function submitPost() {
        const title = document.getElementById('postTitle').value;
        const content = document.getElementById('postContent').value;
        const categoryId = document.getElementById('categoryId').value;

        if (!title.trim()) {
            alert('Post title cannot be empty.');
            return;
        }

        if (!content.trim()) {
            alert('Post content cannot be empty.');
            return;
        }

        fetch('create_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `title=${encodeURIComponent(title)}&content=${encodeURIComponent(content)}&category_id=${categoryId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Post created successfully!');
                    document.getElementById('postTitle').value = '';
                    document.getElementById('postContent').value = '';
                    document.getElementById('categoryId').value = '1';
                    loadPosts();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred. Check console for details.');
            });
    }

    function loadPosts() {
        fetch('fetch_posts.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const postsContainer = document.getElementById('posts');
                    postsContainer.innerHTML = '';

                    data.posts.forEach(post => {
                        const postElement = document.createElement('div');
                        postElement.className = 'post';

                        postElement.innerHTML = `
                            <div class="post-header">
                                <img src="profile_images/${post.profile_image_id}.png" alt="Profile Picture" class="profile-pic">
                                <div>
                                    <strong>${post.username}</strong>
                                    <small>${post.category_name}</small>
                                </div>
                            </div>
                            <div class="post-content">
                                <h3>${post.title}</h3>
                                <p>${post.content}</p>
                                <small>Posted on: ${new Date(post.created_at).toLocaleString()}</small>
                            </div>
                        `;

                        postsContainer.appendChild(postElement);
                    });
                } else {
                    alert('Error fetching posts: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred while fetching posts.');
            });
    }

    window.onload = loadPosts;
</script>
</body>
</html>
