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
        clock_element.src = "images/clock on.png";
        start(id);
        startCounter++;
        clock_element.title = "Stop recording";
    } else if (startCounter == 1) {
        clock_element.src = "images/clock off.png";
        stop();
        location.href = "addTimeRecord?timeRecord=" + time_element.innerHTML + "&id=" + id;
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

function stop() {
    clearInterval(interval);
    interval = null;
}