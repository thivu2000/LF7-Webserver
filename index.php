<?php
$filename = "tasks.json";

function loadTasks($filename) {
    if (!file_exists($filename)) return [];
    $json = file_get_contents($filename);
    return json_decode($json, true) ?? [];
}

function saveTasks($filename, $tasks) {
    file_put_contents($filename, json_encode($tasks, JSON_PRETTY_PRINT));
}

$tasks = loadTasks($filename);

if (isset($_GET['delete'])) {
    $idToDelete = $_GET['delete'];
    $tasks = array_filter($tasks, fn($task) => $task["id"] !== $idToDelete);
    saveTasks($filename, array_values($tasks));
    header("Location: index.php?sort=" . ($_GET['sort'] ?? 'date'));
    exit;
}

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
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>To-Do Liste mit IDs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        .priority-low { color: green; }
        .priority-medium { color: orange; }
        .priority-high { color: red; font-weight: bold; }
        .task-item { margin-bottom: 20px; }
        .delete-link {
            color: red;
            text-decoration: none;
            margin-left: 10px;
        }
        .progress-bar {
            background-color: #ddd;
            border-radius: 5px;
            overflow: hidden;
            width: 100%;
            height: 15px;
            margin-top: 5px;
        }
        .progress-fill {
            background-color: #4caf50;
            height: 100%;
            transition: width 0.3s;
        }
        input[type="range"] {
            width: 100%;
        }
        .inline-form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📝 To-Do Liste</h1>

    <form method="POST" action="">
        <label for="taskInput">Aufgabe:</label><br>
        <input type="text" name="task" id="taskInput" required><br><br>

        <label for="dateInput">Fälligkeitsdatum:</label><br>
        <input type="date" name="date" id="dateInput" required><br><br>

        <label>Priorität:</label><br>
        <label><input type="radio" name="priority" value="low" required> Niedrig</label>
        <label><input type="radio" name="priority" value="medium"> Mittel</label>
        <label><input type="radio" name="priority" value="high"> Hoch</label><br><br>

        <button type="submit">Aufgabe hinzufügen</button>
    </form>

    <hr>

    <form method="GET" style="margin-bottom: 20px;">
        <label for="sortSelect">Sortieren nach:</label>
        <select name="sort" id="sortSelect" onchange="this.form.submit()">
            <option value="date" <?= $sortBy === 'date' ? 'selected' : '' ?>>Fälligkeitsdatum</option>
            <option value="priority" <?= $sortBy === 'priority' ? 'selected' : '' ?>>Priorität</option>
            <option value="progress" <?= $sortBy === 'progress' ? 'selected' : '' ?>>Fortschritt</option>
        </select>
    </form>

    <h2>📋 Aufgaben</h2>
    <ul>
        <?php foreach ($tasks as $t): ?>
            <li class="task-item priority-<?= $t['priority'] ?>">
                <?= htmlspecialchars($t["task"]) ?> – Fällig am: <?= $t["date"] ?> –
                Priorität: <?= ucfirst($t["priority"]) ?>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $t["progress"] * 10 ?>%"></div>
                </div>
                <form method="POST" class="inline-form">
                    <input type="hidden" name="id" value="<?= $t["id"] ?>">
                    <input type="range" name="progress" min="0" max="10" value="<?= $t["progress"] ?>" oninput="this.nextElementSibling.value=this.value">
                    <output><?= $t["progress"] ?></output>/10
                    <button type="submit" name="update_progress">💾</button>
                </form>
                <a class="delete-link" href="?delete=<?= $t["id"] ?>&sort=<?= $sortBy ?>" onclick="return confirm('Diese Aufgabe wirklich löschen?')">❌ Löschen</a>
            </li>
        <?php endforeach; ?>
        <?php if (empty($tasks)): ?>
            <li>Keine Aufgaben vorhanden.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>
