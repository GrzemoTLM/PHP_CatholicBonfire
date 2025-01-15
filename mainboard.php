<?php
require_once 'check_session.php';
require_once 'Threads.php';
require_once 'Comments.php';

$db = Database::getInstance()->getConnection();
$categories = $db->query("SELECT id, name FROM categories")->fetchAll();

$threads = new Threads();
$comments = new Comments();

$postsResponse = $threads->fetchPosts();
$posts = $postsResponse['success'] ? $postsResponse['posts'] : [];
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
        <a href="profil.php" class="btn">Your Profile</a>
        <a href="prayer_intentions.php" class="btn">Prayer Intentions</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin_panel.php" class="btn btn-admin">Admin Panel</a>
        <?php endif; ?>
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
        <div id="posts">
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <div class="post-header">
                        <img src="profile_images/<?= htmlspecialchars($post['profile_image_id']) ?>.png" alt="Profile Picture" class="profile-pic">
                        <div>
                            <strong><?= htmlspecialchars($post['username']) ?></strong>
                            <small><?= htmlspecialchars($post['category_name']) ?></small>
                        </div>
                    </div>
                    <div class="post-content">
                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                        <p><?= htmlspecialchars($post['content']) ?></p>
                        <small>Posted on: <?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($post['created_at']))) ?></small>
                    </div>
                    <div class="comments">
                        <h4>Comments</h4>
                        <div class="comments-list">
                            <?php foreach ($post['comments'] as $comment): ?>
                                <div class="comment">
                                    <div class="comment-details">
                                        <img src="profile_images/<?= htmlspecialchars($comment['profile_image_id']) ?>.png" alt="Profile Picture" class="profile-pic">
                                        <strong class="comment-username"><?= htmlspecialchars($comment['username']) ?></strong>
                                        <span class="comment-text"><?= htmlspecialchars($comment['content']) ?></span>
                                    </div>
                                    <span class="comment-date"><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($comment['created_at']))) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <textarea placeholder="Add a comment..." class="comment-input" data-thread-id="<?= $post['thread_id'] ?>"></textarea>
                        <button class="btn-small" onclick="addComment(<?= $post['thread_id'] ?>)">Comment</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
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
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred. Check console for details.');
            });
    }

    function addComment(threadId) {
        const textarea = document.querySelector(`textarea[data-thread-id="${threadId}"]`);
        const content = textarea.value.trim();

        if (!content) {
            alert('Comment content cannot be empty.');
            return;
        }

        fetch('add_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `thread_id=${encodeURIComponent(threadId)}&content=${encodeURIComponent(content)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Comment added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred. Please try again.');
            });
    }
</script>
</body>
</html>
