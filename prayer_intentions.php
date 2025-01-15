<?php
require_once 'check_session.php';
require_once 'Intentions.php';

$intentionsClass = new Intentions();

$response = $intentionsClass->getAllIntentions();

if (!$response['success']) {
    die('Error fetching intentions: ' . $response['message']);
}

$intentions = $response['intentions'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prayer Intentions - Catholic Campfire</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="content">
    <h1>Prayer Intentions</h1>
    <div class="add-intention-form">
        <h2>Add a Prayer Intention</h2>
        <form id="addIntentionForm">
            <input type="text" id="intentionTitle" placeholder="Enter title" class="input-field" required>
            <textarea id="intentionDescription" placeholder="Enter description" class="textarea-field" required></textarea>
            <button type="button" class="btn" onclick="submitIntention()">Add Intention</button>
        </form>
    </div>

    <div class="intentions-container">
        <h2>All Prayer Intentions</h2>
        <?php if (empty($intentions)): ?>
            <p>No intentions available.</p>
        <?php else: ?>
            <?php foreach ($intentions as $intention): ?>
                <div class="intention">
                    <h3><?= htmlspecialchars($intention['title']) ?></h3>
                    <p><?= htmlspecialchars($intention['description']) ?></p>
                    <small>Added by: <?= htmlspecialchars($intention['username']) ?> on <?= htmlspecialchars($intention['created_at']) ?></small>
                    <div class="likes">
                        <span><?= htmlspecialchars($intention['likes_count']) ?> Prayers Offered</span>
                        <button onclick="offerPrayer(<?= $intention['id'] ?>)" class="btn-prayer">Offer Prayer</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<footer>
    <p>&copy; 2025 Catholic Campfire. All rights reserved.</p>
</footer>
<script>
    function submitIntention() {
        const title = document.getElementById('intentionTitle').value.trim();
        const description = document.getElementById('intentionDescription').value.trim();

        if (!title || !description) {
            alert('Both title and description are required.');
            return;
        }

        fetch('add_prayer_intention.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Prayer intention added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
            });
    }

    function offerPrayer(intentionId) {
        fetch('offer_prayer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `intention_id=${encodeURIComponent(intentionId)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Prayer offered successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
            });
    }
</script>
</body>
</html>
