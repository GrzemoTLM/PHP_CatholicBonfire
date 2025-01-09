<?php
require_once 'check_session.php';
require_once 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: mainboard.php');
    exit;
}

$db = new Database();
$sqlLoggedUsers = "
    SELECT users.id AS user_id, users.username, users.email, logged_in_users.lastUpdate, logged_in_users.sessionId
    FROM logged_in_users
    JOIN users ON logged_in_users.userId = users.id
    ORDER BY logged_in_users.lastUpdate DESC
";
$loggedUsers = $db->query($sqlLoggedUsers)->fetchAll();
$sqlPosts = "
    SELECT threads.id AS thread_id, threads.title, threads.content, threads.created_at, 
           users.username AS author
    FROM threads
    JOIN users ON threads.user_id = users.id
    ORDER BY threads.created_at DESC
";
$posts = $db->query($sqlPosts)->fetchAll();
$sqlComments = "
    SELECT comments.id AS comment_id, comments.content, comments.created_at, 
           users.username AS author, threads.title AS post_title
    FROM comments
    JOIN users ON comments.user_id = users.id
    JOIN threads ON comments.thread_id = threads.id
    ORDER BY comments.created_at DESC
";
$comments = $db->query($sqlComments)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script>
        function logoutUser(sessionId) {
            if (confirm("Are you sure you want to log out this user?")) {
                fetch('logout_user.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `sessionId=${encodeURIComponent(sessionId)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while logging out the user.');
                    });
            }
        }

        function deletePost(postId) {
            if (confirm("Are you sure you want to delete this post?")) {
                fetch('delete_post.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `postId=${encodeURIComponent(postId)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the post.');
                    });
            }
        }

        function deleteComment(commentId) {
            if (confirm("Are you sure you want to delete this comment?")) {
                fetch('delete_comment.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `commentId=${encodeURIComponent(commentId)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the comment.');
                    });
            }
        }
    </script>
</head>
<body>
<h1>Admin Panel</h1>

<h2>Currently Logged-in Users</h2>
<table border="1">
    <thead>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Last Active</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($loggedUsers as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['user_id']) ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['lastUpdate']) ?></td>
            <td>
                <button onclick="logoutUser('<?= htmlspecialchars($user['sessionId']) ?>')">WYLOGUJ</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>All Posts</h2>
<table border="1">
    <thead>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Content</th>
        <th>Author</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= htmlspecialchars($post['thread_id']) ?></td>
            <td><?= htmlspecialchars($post['title']) ?></td>
            <td><?= htmlspecialchars($post['content']) ?></td>
            <td><?= htmlspecialchars($post['author']) ?></td>
            <td><?= htmlspecialchars($post['created_at']) ?></td>
            <td>
                <button onclick="deletePost('<?= htmlspecialchars($post['thread_id']) ?>')">USUŃ</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>All Comments</h2>
<table border="1">
    <thead>
    <tr>
        <th>ID</th>
        <th>Content</th>
        <th>Author</th>
        <th>Post Title</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($comments as $comment): ?>
        <tr>
            <td><?= htmlspecialchars($comment['comment_id']) ?></td>
            <td><?= htmlspecialchars($comment['content']) ?></td>
            <td><?= htmlspecialchars($comment['author']) ?></td>
            <td><?= htmlspecialchars($comment['post_title']) ?></td>
            <td><?= htmlspecialchars($comment['created_at']) ?></td>
            <td>
                <button onclick="deleteComment('<?= htmlspecialchars($comment['comment_id']) ?>')">USUŃ</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
