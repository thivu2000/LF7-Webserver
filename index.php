<?php
$filename = "tasks.txt";

if (isset($_GET['delete'])) {
    $indexToDelete = (int)$_GET['delete'];
    $lines = file($filename, FILE_IGNORE_NEW_LINES);
    if (isset($lines[$indexToDelete])) {
        unset($lines[$indexToDelete]);
        file_put_contents($filename, implode("\n", $lines) . "\n");
    }
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task = htmlspecialchars($_POST["task"]);
    $date = htmlspecialchars($_POST["date"]);
    $priority = htmlspecialchars($_POST["priority"]);
    $line = "$task|$date|$priority\n";
    file_put_contents($filename, $line, FILE_APPEND);
}

$tasks = [];
if (file_exists($filename)) {
    $lines = file($filename, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        list($task, $date, $priority) = explode("|", $line);
        $tasks[] = [
            "task" => $task,
            "date" => $date,
            "priority" => $priority
        ];
    }
    usort($tasks, fn($a, $b) => strcmp($a['date'], $b['date']));
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>To-Do Liste mit PHP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        .priority-low { color: green; }
        .priority-medium { color: orange; }
        .priority-high { color: red; font-weight: bold; }
        .task-item { margin-bottom: 10px; }
        .delete-link {
            color: red;
            text-decoration: none;
            margin-left: 10px;
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

    <h2>📋 Aufgaben</h2>
    <ul>
        <?php foreach ($tasks as $i => $t): ?>
            <li class="task-item priority-<?= $t['priority'] ?>">
                <?= htmlspecialchars($t["task"]) ?> – Fällig am: <?= $t["date"] ?> –
                Priorität: <?= ucfirst($t["priority"]) ?>
                <a class="delete-link" href="?delete=<?= $i ?>" onclick="return confirm('Diese Aufgabe wirklich löschen?')">❌ Löschen</a>
            </li>
        <?php endforeach; ?>
        <?php if (empty($tasks)): ?>
            <li>Keine Aufgaben vorhanden.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>
