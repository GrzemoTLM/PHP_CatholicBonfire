<?php
require_once 'Threads.php';

header('Content-Type: application/json');

$threads = new Threads();
$response = $threads->fetchPosts();

echo json_encode($response);
?>
