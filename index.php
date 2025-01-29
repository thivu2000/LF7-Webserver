<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do Liste mit PHP</title>
    <link rel="stylesheet" href="">
</head>
<body>
<div class="container">
    <h1>To-Do Liste</h1>
    <div class="input-fields">
        <label for="taskInput"></label><input type="text" placeholder="Aufgabe" id="taskInput">
        <label for="dateInput"></label><input type="date" id="dateInput">
        <div class="priority">
            <label>
                <input type="radio" name="priority" value="low"> Niedrig
            </label>
            <label>
                <input type="radio" name="priority" value="medium"> Mittel
            </label>
            <label>
                <input type="radio" name="priority" value="high"> Hoch
            </label>
        </div>
        <button id="addTaskButton">Aufgabe hinzufügen</button>
    </div>
    <div class="task">
        <ul id="taskList">
        </ul>
    </div>
</div>

<script>
    document.getElementById('addTaskButton').addEventListener('click', function() {
        const taskInput = document.getElementById('taskInput').value;
        const dateInput = document.getElementById('dateInput').value;
        const priority = document.querySelector('input[name="priority"]:checked');

        if (taskInput && dateInput && priority) {
            const taskList = document.getElementById('taskList');
            const listItem = document.createElement('li');
            listItem.textContent = `${taskInput} - Fällig am: ${dateInput} - Priorität: ${priority.value}`;
            taskList.appendChild(listItem);

            document.getElementById('taskInput').value = '';
            document.getElementById('dateInput').value = '';
            document.querySelector('input[name="priority"]:checked').checked = false;
        } else {
            alert('Bitte füllen Sie alle Felder aus.');
        }
    });
</script>
</body>
</html>