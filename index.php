<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CatholicCampfire</title>
    <!-- Favicon -->
    <link rel="icon" href="multimedia/favicon.ico" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'MyCustomFont';
            src: url('fonts/King Arthur Legend.ttf') format('truetype');
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'MyCustomFont', serif;
            overflow: hidden;
        }

        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        .center-box {
            text-align: center;
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
            border: 2px solid #6c757d;
            padding: 30px;
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .center-box h1 {
            font-size: 3rem;
            color: #343a40;
        }

        .center-box .btn {
            font-size: 1.8rem;
            padding: 15px 40px;
        }

        .logo {
            height: 190px;
        }

        .mute-button, .unmute-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 10;
            width: 50px;
            height: 50px;
            cursor: pointer;
        }

        .unmute-button {
            display: none;
        }
    </style>
</head>
<body>
<video class="video-background" autoplay muted loop>
    <source src="multimedia/indexFilm.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="center-box">
    <img src="multimedia/logo.png" alt="Logo" class="logo">
    <div>
        <h1>Catholic Campfire</h1>
        <a href="home.php" class="btn btn-primary mt-2">Begin</a>
    </div>
</div>

<img src="multimedia/mute.png" class="mute-button" id="muteButton" alt="Mute Button">
<img src="multimedia/unmute.png" class="unmute-button" id="unmuteButton" alt="Unmute Button">

<audio id="backgroundMusic" autoplay loop>
    <source src="multimedia/backgroundMusic.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const music = document.getElementById('backgroundMusic');
        const muteButton = document.getElementById('muteButton');
        const unmuteButton = document.getElementById('unmuteButton');

        music.muted = false;

        music.play().catch(error => {
            console.log("Music playback was prevented by the browser:", error);
        });

        muteButton.addEventListener('click', () => {
            music.muted = true;
            muteButton.style.display = 'none';
            unmuteButton.style.display = 'block';
        });

        unmuteButton.addEventListener('click', () => {
            music.muted = false;
            music.play();
            unmuteButton.style.display = 'none';
            muteButton.style.display = 'block';
        });
    });
</script>
</body>
</html>
