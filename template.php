<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>To-Do Liste mit IDs</title>
    <link rel="stylesheet" href="styles.css">
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
                    <label>
                        <input type="range" name="progress" min="0" max="10" value="<?= $t["progress"] ?>" oninput="this.nextElementSibling.value=this.value">
                    </label>
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
