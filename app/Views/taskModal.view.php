<!-- Reusable Create / Edit Task modal (in-page).
     Uses the same POST endpoints as the standalone pages:
       - Create: action="add_task"
       - Edit:   action="edit_task?id={id}"  (set by taskModal.js)
     taskModal.js handles open/close/populate/validation + CKEditor. -->
<div id="taskModal" class="fixed inset-0 z-[1000] hidden overflow-y-auto bg-black/60 p-4 sm:p-8"
     role="dialog" aria-modal="true" aria-labelledby="taskModalTitle">
    <div class="mx-auto mt-6 w-full max-w-2xl rounded-xl border border-solid border-surface-700 bg-surface-800 shadow-2xl sm:mt-12">
        <div class="flex items-center justify-between border-0 border-b border-solid border-surface-700 px-5 py-3">
            <h2 id="taskModalTitle" class="text-lg font-semibold text-white">Add Task</h2>
            <button type="button" data-task-close aria-label="Close dialog"
                    class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg border-0 bg-transparent text-surface-300 transition-colors hover:bg-surface-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                <?= icon('x', 'h-5 w-5') ?>
            </button>
        </div>

        <form id="taskForm" method="POST" action="add_task" class="space-y-4 p-5">
            <div>
                <label for="task_title" class="label">Title:</label>
                <input type="text" id="task_title" name="title" class="input" autocomplete="off">
            </div>
            <div>
                <label for="task_description" class="label">Description:</label>
                <textarea id="task_description" name="description"></textarea>
            </div>
            <div>
                <label for="task_motivation" class="label">Motivation:</label>
                <textarea id="task_motivation" name="motivation"></textarea>
            </div>
            <div>
                <label for="task_deadline" class="label">Deadline:</label>
                <input type="date" id="task_deadline" name="deadline" class="input">
            </div>
            <div>
                <label for="task_priority" class="label">Priority:</label>
                <select id="task_priority" name="priority" class="input">
                    <?php foreach (($possiblePriorities ?? [1]) as $p): ?>
                        <option value="<?= (int) $p ?>"><?= (int) $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" data-task-close class="btn-secondary">Cancel</button>
                <button type="submit" id="taskSubmit" class="btn-primary">Add task</button>
            </div>
        </form>
    </div>
</div>
