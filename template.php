<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>To-Do Liste mit Status</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
/** @var array $tasks */
/** @var string $sortBy */
?>

<div class="container">
    <h1>To-Do Liste</h1>

    <!-- Neue Aufgabe hinzufügen -->
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

    <!-- Sortierung -->
    <form method="GET" style="margin-bottom: 20px;">
        <label for="sortSelect">Sortieren nach:</label>
        <select name="sort" id="sortSelect" onchange="this.form.submit()">
            <option value="date" <?= $sortBy === 'date' ? 'selected' : '' ?>>Fälligkeitsdatum</option>
            <option value="priority" <?= $sortBy === 'priority' ? 'selected' : '' ?>>Priorität</option>
            <option value="status" <?= $sortBy === 'status' ? 'selected' : '' ?>>Status</option>
        </select>
    </form>

    <!-- Aufgabenliste -->
    <h2>Aufgaben</h2>
    <ul>
        <?php foreach ($tasks as $t): ?>
            <li class="task-item priority-<?= $t['priority'] ?> status-<?= $t['status'] ?>">
                <strong><?= htmlspecialchars($t["task"]) ?></strong><br>
                Fällig am: <?= htmlspecialchars($t["date"]) ?> |
                Priorität: <?= ucfirst($t["priority"]) ?> |
                Status: <strong><?= ucfirst(str_replace('_', ' ', $t["status"])) ?></strong>

                <form method="POST" class="inline-form">
                    <input type="hidden" name="id" value="<?= $t["id"] ?>">
                    <select name="status">
                        <?php
                        $statusOptions = [
                            "offen" => "Offen",
                            "in_bearbeitung" => "In Bearbeitung",
                            "erledigt" => "Erledigt"
                        ];
                        foreach ($statusOptions as $value => $label):
                            $selected = $value === $t['status'] ? 'selected' : '';
                            echo "<option value=\"$value\" $selected>$label</option>";
                        endforeach;
                        ?>
                    </select>
                    <button type="submit" name="update_status">💾 Speichern</button>
                    <a class="delete-link" href="?delete=<?= $t["id"] ?>&sort=<?= $sortBy ?>" onclick="return confirm('Diese Aufgabe wirklich löschen?')">❌ Löschen</a>
                </form>
            </li>
        <?php endforeach; ?>
        <?php if (empty($tasks)): ?>
            <li>Keine Aufgaben vorhanden.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>
