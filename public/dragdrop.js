document.addEventListener('DOMContentLoaded', () => {
    if (typeof userRole === 'undefined') {
        return;
    }

    if (userRole === 'owner' || userRole === 'editor') {
        initTaskDragAndDrop();
    }
});

function initTaskDragAndDrop() {
    const taskItems = document.querySelectorAll('.task-item');

    taskItems.forEach(task => {
        task.setAttribute('draggable', 'true');

        task.addEventListener('dragstart', e => {
            e.dataTransfer.effectAllowed = 'move';
            task.classList.add('dragging-task');
        });

        task.addEventListener('dragend', e => {
            task.classList.remove('dragging-task');
        });
    });

    const groupBodies = document.querySelectorAll('.task-group-body');

    groupBodies.forEach(body => {
        body.addEventListener('dragover', e => {
            e.preventDefault();
            const afterElement = getTaskAfterElement(body, e.clientY);
            const draggingTask = document.querySelector('.dragging-task');
            if (!draggingTask) return;

            if (afterElement == null) {
                body.appendChild(draggingTask);
            } else {
                body.insertBefore(draggingTask, afterElement);
            }
        });

        body.addEventListener('drop', e => {
            const groupEl = body.closest('.task-group');
            if (!groupEl) return;
            const groupId = groupEl.dataset.groupId;

            updateTaskOrder(groupId);
        });
    });
}


function getTaskAfterElement(container, y) {
    const taskItems = [...container.querySelectorAll('.task-item:not(.dragging-task)')];

    return taskItems.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - (box.height / 2);

        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

function updateTaskOrder(newGroupId) {
    const groupEl = document.querySelector(`.task-group[data-group-id="${newGroupId}"]`);
    if (!groupEl) return;

    const taskItems = [...groupEl.querySelectorAll('.task-item')];

    const newOrder = taskItems.map((task, index) => {
        return {
            taskId: task.dataset.taskId,
            position: index
        };
    });

    fetch('update_task_position.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ groupId: newGroupId, newOrder })
    })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                console.error('Błąd zapisu kolejności zadań:', data.message);
            }
        })
        .catch(err => console.error(err));
}
