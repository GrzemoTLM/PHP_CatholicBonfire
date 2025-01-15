<?php
require_once '../functions/check_session.php';
require_once '../classes/AdminPanel.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: mainboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    $adminPanel = new AdminPanel();

    if ($action === 'changeUserRole') {
        $userId = intval($_POST['userId'] ?? 0);
        $newRole = $_POST['newRole'] ?? '';

        if ($userId && !empty($newRole)) {
            $response = $adminPanel->changeUserRole($userId, $newRole);
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        }
        exit;
    }

    if ($action === 'deleteIntention') {
        $intentionId = intval($_POST['intentionId'] ?? 0);

        if ($intentionId) {
            $response = $adminPanel->deleteIntention($intentionId);
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid intention ID.']);
        }
        exit;
    }
}

$adminPanel = new AdminPanel();
$loggedUsers = $adminPanel->getLoggedUsers();
$posts = $adminPanel->getAllPosts();
$comments = $adminPanel->getAllComments();
$intentions = $adminPanel->getAllIntentions();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
        function logoutUser(sessionId) {
            if (confirm("Are you sure you want to log out this user?")) {
                fetch('../functions/logout_user.php', {
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
                fetch('../functions/delete_post.php', {
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
                fetch('../functions/delete_comment.php', {
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

        function changeUserRole(userId, newRole) {
            if (confirm(`Are you sure you want to change the role of this user to ${newRole}?`)) {
                fetch('admin_panel.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=changeUserRole&userId=${encodeURIComponent(userId)}&newRole=${encodeURIComponent(newRole)}`
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
                        alert('An error occurred while changing the user role.');
                    });
            }
        }

        function deleteIntention(intentionId) {
            if (confirm("Are you sure you want to delete this intention?")) {
                fetch('admin_panel.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `action=deleteIntention&intentionId=${encodeURIComponent(intentionId)}`
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
                        alert('An error occurred while deleting the intention.');
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
        <th>Role</th>
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
            <td><?= htmlspecialchars($user['role'] ?? 'user') ?></td>
            <td><?= htmlspecialchars($user['lastUpdate']) ?></td>
            <td>
                <button onclick="logoutUser('<?= htmlspecialchars($user['sessionId']) ?>')">Log Out</button>
                <button onclick="changeUserRole(<?= $user['user_id'] ?>, 'admin')">Make Admin</button>
                <button onclick="changeUserRole(<?= $user['user_id'] ?>, 'user')">Make User</button>
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
            <td><?= htmlspecialchars($post['username']) ?></td>
            <td><?= htmlspecialchars($post['created_at']) ?></td>
            <td>
                <button onclick="deletePost('<?= htmlspecialchars($post['thread_id']) ?>')">Delete</button>
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
            <td><?= htmlspecialchars($comment['username']) ?></td>
            <td><?= htmlspecialchars($comment['post_title'] ?? 'Unknown') ?></td>
            <td><?= htmlspecialchars($comment['created_at']) ?></td>
            <td>
                <button onclick="deleteComment('<?= htmlspecialchars($comment['comment_id']) ?>')">Delete</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>All Prayer Intentions</h2>
<table border="1">
    <thead>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Description</th>
        <th>Author</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($intentions as $intention): ?>
        <tr>
            <td><?= htmlspecialchars($intention['id']) ?></td>
            <td><?= htmlspecialchars($intention['title']) ?></td>
            <td><?= htmlspecialchars($intention['description']) ?></td>
            <td><?= htmlspecialchars($intention['username']) ?></td>
            <td><?= htmlspecialchars($intention['created_at']) ?></td>
            <td>
                <button onclick="deleteIntention(<?= htmlspecialchars($intention['id']) ?>)">Delete</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
