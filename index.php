<?php
$filename = "tasks.json";

// Aufgaben aus Datei laden
function loadTasks($filename) {
    if (!file_exists($filename)) return [];

    $json = file_get_contents($filename);

    return json_decode($json, true) ?? [];
}

// Aufgaben in Datei speichern
function saveTasks($filename, $tasks): void {
    // Aufgaben als JSON speichern
    file_put_contents($filename, json_encode($tasks, JSON_PRETTY_PRINT));
}

$tasks = loadTasks($filename);

// Aufgabe löschen, wenn "delete" per GET gesendet wurde
if (isset($_GET['delete'])) {
    // ID der Aufgabe lesen
    $idToDelete = $_GET['delete'];

    $tasks = array_filter($tasks, fn($task) => $task["id"] !== $idToDelete);

    saveTasks($filename, array_values($tasks));

    header("Location: index.php?sort=" . ($_GET['sort'] ?? 'date'));
    exit;
}

// Neue Aufgabe wurde per POST gesendet
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["task"])) {
    $newTask = [
        "id" => uniqid(),
        "task" => htmlspecialchars($_POST["task"]),
        "date" => htmlspecialchars($_POST["date"]),
        "priority" => htmlspecialchars($_POST["priority"]),
        "progress" => 0
    ];

    $tasks[] = $newTask;

    saveTasks($filename, $tasks);
}

// Fortschritt wurde aktualisiert
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_progress"])) {
    $idToUpdate = $_POST["id"];
    $newProgress = (int)$_POST["progress"];

    foreach ($tasks as &$task) {
        if ($task["id"] === $idToUpdate) {
            $task["progress"] = $newProgress;
            break;
        }
    }

    saveTasks($filename, $tasks);

    header("Location: index.php?sort=" . ($_GET['sort'] ?? 'date'));
    exit;
}

$sortBy = $_GET['sort'] ?? 'date';

// Aufgaben sortieren nach ausgewähltem Kriterium
usort($tasks, function ($a, $b) use ($sortBy) {
    if ($sortBy === 'priority') {
        $order = ['high' => 1, 'medium' => 2, 'low' => 3];
        return $order[$a['priority']] <=> $order[$b['priority']];
    } elseif ($sortBy === 'progress') {
        return $a['progress'] <=> $b['progress'];
    } else {
        return strcmp($a['date'], $b['date']);
    }
});

include 'template.php';
