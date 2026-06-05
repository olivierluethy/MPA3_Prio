<!-- Shared time-record edit + delete modals (used by Time records and Calendar).
     Driven by public/js/timeRecords.js. -->
<div id="editTimeModal" class="fixed inset-0 z-[1000] hidden overflow-y-auto bg-black/60 p-4 sm:p-8"
     role="dialog" aria-modal="true" aria-labelledby="editTimeTitle">
    <div class="mx-auto mt-10 w-full max-w-lg rounded-xl border border-solid border-surface-700 bg-surface-800 shadow-2xl">
        <div class="flex items-center justify-between border-0 border-b border-solid border-surface-700 px-5 py-3">
            <h2 id="editTimeTitle" class="text-lg font-semibold text-white">Edit time record</h2>
            <button type="button" data-time-close aria-label="Close dialog"
                    class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg border-0 bg-transparent text-surface-300 transition-colors hover:bg-surface-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                <?= icon('x', 'h-5 w-5') ?>
            </button>
        </div>
        <form id="editTimeForm" class="space-y-4 p-5">
            <div>
                <span class="label">Date</span>
                <p id="editTime_date" class="text-sm text-surface-300">—</p>
            </div>
            <div>
                <label for="editTime_report" class="label">Report</label>
                <input type="text" id="editTime_report" name="rapport" class="input" autocomplete="off">
            </div>
            <div>
                <label for="editTime_duration" class="label">Duration (hh:mm:ss)</label>
                <input type="time" step="1" id="editTime_duration" name="time" class="input">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" data-time-close class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteTimeModal" class="fixed inset-0 z-[1000] hidden overflow-y-auto bg-black/60 p-4 sm:p-8"
     role="dialog" aria-modal="true" aria-labelledby="deleteTimeTitle">
    <div class="mx-auto mt-24 w-full max-w-md rounded-xl border border-solid border-surface-700 bg-surface-800 p-6 text-center shadow-2xl">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-500/15 text-red-400"><?= icon('trash', 'h-6 w-6') ?></div>
        <h2 id="deleteTimeTitle" class="text-lg font-semibold text-white">Delete time record?</h2>
        <p class="mt-2 text-sm text-surface-400">This permanently removes this report and its time. This action cannot be undone.</p>
        <div class="mt-6 flex justify-center gap-3">
            <button type="button" data-time-close class="btn-secondary">Cancel</button>
            <button type="button" id="confirmDeleteTime"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-lg border-0 bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500">
                <?= icon('trash', 'h-4 w-4') ?> Delete
            </button>
        </div>
    </div>
</div>
