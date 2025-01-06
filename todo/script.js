var todoList = document.getElementById('todoList');

// Load tasks from Local Storage
var tasks = JSON.parse(localStorage.getItem('tasks')) || [];
tasks.forEach(function(task) {
    addTaskToList(task.text, task.completed);
});

// Event listener for the Add Task button
document.getElementById('addTaskButton').addEventListener('click', function() {
    var taskText = document.getElementById('taskInput').value;
    if (taskText) {
        addTaskToList(taskText, false);
        tasks.push({text: taskText, completed: false});
        saveTasks();
        document.getElementById('taskInput').value = '';
    } else {
        alert('Please enter a task.');
    }
});

document.getElementById('clearCompletedButton').addEventListener('click', function() {
    tasks = tasks.filter(task => !task.completed);
    saveTasks();
    // Refresh the task list
    todoList.innerHTML = '';
    tasks.forEach(function(task) {
        addTaskToList(task.text, task.completed);
    });
});

function addTaskToList(text, completed) {
    var li = document.createElement('li');
    li.textContent = text;

    var checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.checked = completed;
    checkbox.addEventListener('change', function() {
        li.classList.toggle('completed', this.checked);
        var task = tasks.find(task => task.text === text);
        if (task) task.completed = this.checked;
        saveTasks();
    });

    if (completed) li.classList.add('completed');
    li.appendChild(checkbox);
    todoList.appendChild(li);
}

function saveTasks() {
    localStorage.setItem('tasks', JSON.stringify(tasks));
}