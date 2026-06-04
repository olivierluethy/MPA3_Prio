var open = document.getElementById("open");
var done = document.getElementById("done");
done.style.display = "none";

function navSwitch(num) {
    /* Open */
    if (num == 1) {
        done.style.display = "none";
        open.style.display = "block";

        // Dark-theme active/inactive colors (brand accent vs. muted surface)
        document.getElementById("openButton").style.backgroundColor = "#4f46e5";
        document.getElementById("doneButton").style.backgroundColor = "#1e293b";

        document.getElementById("openButton").style.color = "#ffffff";
        document.getElementById("doneButton").style.color = "#94a3b8";
    }
    /* Done */
    else {
        done.style.display = "block";
        open.style.display = "none";

        document.getElementById("openButton").style.backgroundColor = "#1e293b";
        document.getElementById("doneButton").style.backgroundColor = "#4f46e5";

        document.getElementById("openButton").style.color = "#94a3b8";
        document.getElementById("doneButton").style.color = "#ffffff";
    }
}