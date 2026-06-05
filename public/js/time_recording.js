let seconds = 0;
let interval = null;
let startCounter = 0;
let time_id = 0;
let clock_id = 0;
let clock_element;
let time_element;

function start_recording(id) {
    time_id = clock_id = id;
    clock_element = document.querySelector('#start' + id);
    time_element = document.querySelector('#active_time' + id);
    if (startCounter == 0) {
        clock_element.classList.add("is-recording");
        clock_element.title = "Stop recording";
        clock_element.setAttribute("aria-label", "Stop recording");
        start(id);
        startCounter++;
    } else if (startCounter == 1) {
        clock_element.classList.remove("is-recording");
        clock_element.title = "Start recording";
        clock_element.setAttribute("aria-label", "Start recording");
        stop(id);
        // location.href = "addTimeRecord?timeRecord=" + time_element.innerHTML + "&id=" + id;
        modal.style.display = "block";
        var rd = document.getElementById("reportDuration");
        if (rd && rd.durationEditor) {
            var p = String(time_element.innerHTML).split(":");
            var secs = (parseInt(p[0], 10) || 0) * 3600 + (parseInt(p[1], 10) || 0) * 60 + (parseInt(p[2], 10) || 0);
            rd.durationEditor.setDuration(secs);
        }
        document.getElementById("taskId").value = id;

        startCounter = 0;
    }
}

// Update the timer
function timer() {
    seconds++;

    // Format our time
    let hrs = Math.floor(seconds / 3600);
    let mins = Math.floor((seconds - (hrs * 3600)) / 60);
    let secs = seconds % 60;

    if (secs < 10) secs = '0' + secs;
    if (mins < 10) mins = "0" + mins;
    if (hrs < 10) hrs = "0" + hrs;
    time_element.innerHTML = `${hrs}:${mins}:${secs}`;
}

function start(id) {
    if (interval) {
        return
    }

    interval = setInterval(timer, 1000);
}

function stop(id) {
    clearInterval(interval);
    interval = null;

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
        document.querySelector('#active_time' + id).innerHTML = "00:00:00";
        var startBtn = document.querySelector('#start' + id);
        startBtn.classList.remove("is-recording");
        startBtn.title = "Start recording";
        startBtn.setAttribute("aria-label", "Start recording");
    }
}