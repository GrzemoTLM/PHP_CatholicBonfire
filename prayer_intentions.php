<?php
require_once 'check_session.php';
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
    <div class="header-section">
        <h1>Prayer Intentions</h1>
        <img src="multimedia/prayer_image.png" alt="Prayer Image" class="prayer-image">
    </div>
    <p class="subtitle">Share your intentions or pray for others</p>

    <div class="back-button">
        <a href="mainboard.php" class="btn btn-back">Back to Main Board</a>
    </div>

    <div class="intentions-form">
        <h2>Add a Prayer Intention</h2>
        <form id="addIntentionForm">
            <input type="text" id="intentionTitle" placeholder="Enter intention title" class="input-field">
            <textarea id="intentionDescription" placeholder="Describe your intention..." class="textarea-field"></textarea>
            <button type="button" class="btn" onclick="submitIntention()">Submit</button>
        </form>
    </div>

    <div class="intentions-list">
        <h2>All Intentions</h2>
        <div id="intentionsContainer">
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2025 Catholic Campfire. All rights reserved.</p>
</footer>

<script>
    function submitIntention() {
        const title = document.getElementById('intentionTitle').value;
        const description = document.getElementById('intentionDescription').value;

        if (!title.trim() || !description.trim()) {
            alert('Both title and description are required.');
            return;
        }

        const intentionsContainer = document.getElementById('intentionsContainer');
        const newIntention = document.createElement('div');
        newIntention.className = 'intention-card';
        newIntention.innerHTML = `
            <h3>${title}</h3>
            <p>${description}</p>
            <button class="btn-small" onclick="promisePrayer()">Pray for this</button>
        `;
        intentionsContainer.appendChild(newIntention);

        document.getElementById('intentionTitle').value = '';
        document.getElementById('intentionDescription').value = '';
    }

    function promisePrayer() {
        alert('Thank you for your prayer promise!');
    }
</script>
</body>
</html>
