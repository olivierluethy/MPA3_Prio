<!-- The Modal (kept hidden via inline display:none; toggled by time_recording.js) -->
<div id="myModal" class="modal fixed inset-0 z-[1000] overflow-auto bg-black/60 pt-24" style="display: none;">

    <!-- Modal content -->
    <div class="modal-content mx-auto w-[90%] max-w-lg overflow-hidden rounded-xl border border-solid border-surface-700 bg-surface-800 shadow-2xl">
        <div class="modal-header flex items-center justify-between bg-brand-600 px-5 py-3 text-white">
            <h2 class="text-lg font-semibold">Add Rapport</h2>
            <span class="close cursor-pointer text-2xl font-bold leading-none text-white/80 hover:text-white">&times;</span>
        </div>
        <div class="modal-body p-5">
            <form action="addTimeRecord" method="POST" class="space-y-4">
                <div>
                    <label for="rapport" class="label">Rapport:</label>
                    <input placeholder="What did you achieve?" id="rapport" type="text" name="rapport" class="input" />
                </div>
                <div>
                    <label for="appt-time" class="label">Time:</label>
                    <input id="appt-time" type="time" name="time" step="2" readonly class="input" />
                </div>
                <input style="display: none;" id="taskId" type="text" name="taskId" />
                <button title="Add time to time recording" type="submit" class="btn-primary w-full">Add time</button>
            </form>
        </div>
    </div>
</div>

<script>
// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];
</script>
