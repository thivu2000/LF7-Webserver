<?php
header('Content-Type: application/json');

$filename = "tasks.json";

if (!file_exists($filename)) {
    echo json_encode([]);
    exit;
}

$tasks = file_get_contents($filename);
echo $tasks;
