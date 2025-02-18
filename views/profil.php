<?php
require_once '../functions/check_session.php';
require_once '../classes/Profile.php';

$profile = new Profile();
$userId = $_SESSION['user_id'];

try {
    $user = $profile->getUserData($userId);

    $postCount = $profile->getPostCount($userId);

    $likesGiven = $profile->getLikesGiven($userId);

    $profileImage = "../profile_images/" . $user['profile_image_id'] . ".png";
} catch (Exception $e) {
    die("Error fetching profile data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - Catholic Campfire</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="content">
    <h1>Your Profile</h1>
    <div class="profile-image">
        <img src="<?= $profileImage ?>" alt="Profile Picture" onclick="openImageSelection()"
             style="cursor: pointer; width: 150px; border-radius: 50%;">
    </div>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Number of Posts:</strong> <?= $postCount ?></p>
    <p><strong>Prayers Offered (Likes Given):</strong> <?= $likesGiven ?></p>
    <a href="mainboard.php" class="btn">Back to Main Board</a>
</div>

<div id="imageModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2>Select Profile Picture</h2>
        <div class="image-grid">
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <img src="../profile_images/<?= $i ?>.png" alt="Image <?= $i ?>" class="profile-option"
                     onclick="updateProfileImage(<?= $i ?>)">
            <?php endfor; ?>
        </div>
        <button onclick="closeImageSelection()" class="btn">Close</button>
    </div>
</div>

<footer>
    <p>&copy; 2025 Catholic Campfire. All rights reserved.</p>
</footer>

<script>
    function openImageSelection() {
        document.getElementById('imageModal').style.display = 'block';
    }

    function closeImageSelection() {
        document.getElementById('imageModal').style.display = 'none';
    }

    function updateProfileImage(imageId) {
        fetch('../functions/update_profile_image.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({image_id: imageId})
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating profile picture.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
</script>
</body>
</html>
