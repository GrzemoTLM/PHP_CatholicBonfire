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

<!-- Modal for Editing Posts -->
<div id="editModal" class="modal-edit">
    <div class="modal-edit-content">
        <span class="close-edit" onclick="closeEditModal()">&times;</span>
        <h2>Edit Post</h2>
        <form id="editPostForm">
            <input type="hidden" id="editPostId">
            <label for="editPostTitle">Title:</label>
            <input type="text" id="editPostTitle" class="input-edit-field" required>
            <label for="editPostContent">Content:</label>
            <textarea id="editPostContent" class="textarea-edit-field" required></textarea>
            <button type="button" class="btn-edit" onclick="submitEdit()">Save Changes</button>
        </form>
    </div>
</div>

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

                        const commentsHTML = post.comments.map(comment => `
                        <div class="comment">
                            <div class="comment-details">
                                <img src="profile_images/${comment.profile_image_id}.png" alt="Profile Picture" class="profile-pic">
                                <strong class="comment-username">${comment.username}</strong>
                                <span class="comment-text">${comment.content}</span>
                            </div>
                            <span class="comment-date">${new Date(comment.created_at).toLocaleString()}</span>
                        </div>
                    `).join('');

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
                        <div class="comments">
                            <h4>Comments</h4>
                            <div class="comments-list">
                                ${commentsHTML}
                            </div>
                            <textarea placeholder="Add a comment..." class="comment-input" data-thread-id="${post.thread_id}"></textarea>
                            <button class="btn-small" onclick="addComment(${post.thread_id})">Comment</button>
                            <button class="btn-small btn-edit-post" onclick="openEditModal(${post.id}, '${post.title}', '${post.content}')">Edit</button>
                        </div>
                    `;
                        postsContainer.appendChild(postElement);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred while loading posts.');
            });
    }

    function openEditModal(postId, title, content) {
        document.getElementById('editModal').style.display = 'flex';
        document.getElementById('editPostId').value = postId;
        document.getElementById('editPostTitle').value = title;
        document.getElementById('editPostContent').value = content;
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function submitEdit() {
        const postId = document.getElementById('editPostId').value;
        const title = document.getElementById('editPostTitle').value;
        const content = document.getElementById('editPostContent').value;

        if (!title.trim() || !content.trim()) {
            alert('Title and content cannot be empty.');
            return;
        }

        fetch('edit_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${postId}&title=${encodeURIComponent(title)}&content=${encodeURIComponent(content)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Post updated successfully!');
                    closeEditModal();
                    loadPosts();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
            });
    }

    window.onload = loadPosts;
</script>
</body>
</html>
