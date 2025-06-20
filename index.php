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
    file_put_contents($filename, json_encode($tasks, JSON_PRETTY_PRINT));
}

$tasks = loadTasks($filename);

// Aufgabe löschen
if (isset($_GET['delete'])) {
    $idToDelete = $_GET['delete'];
    $tasks = array_filter($tasks, fn($task) => $task["id"] !== $idToDelete);
    saveTasks($filename, array_values($tasks));
    header("Location: index.php?sort=" . ($_GET['sort'] ?? 'date'));
    exit;
}

// Neue Aufgabe hinzufügen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["task"])) {
    $newTask = [
        "id" => uniqid(),
        "task" => htmlspecialchars($_POST["task"]),
        "date" => htmlspecialchars($_POST["date"]),
        "priority" => htmlspecialchars($_POST["priority"]),
        "status" => "offen" // Startstatus
    ];

    $tasks[] = $newTask;
    saveTasks($filename, $tasks);
}

// Status aktualisieren
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_status"])) {
    $idToUpdate = $_POST["id"];
    $newStatus = $_POST["status"];

    foreach ($tasks as &$task) {
        if ($task["id"] === $idToUpdate) {
            $task["status"] = $newStatus;
            break;
        }
    }
    saveTasks($filename, $tasks);
    header("Location: index.php?sort=" . ($_GET['sort'] ?? 'date'));
    exit;
}

// Sortierung
$sortBy = $_GET['sort'] ?? 'date';

usort($tasks, function ($a, $b) use ($sortBy) {
    if ($sortBy === 'priority') {
        $order = ['high' => 1, 'medium' => 2, 'low' => 3];
        return $order[$a['priority']] <=> $order[$b['priority']];
    } elseif ($sortBy === 'status') {
        $order = ['offen' => 1, 'in_bearbeitung' => 2, 'erledigt' => 3];
        return $order[$a['status']] <=> $order[$b['status']];
    } else {
        return strcmp($a['date'], $b['date']);
    }
});

include 'template.php';
