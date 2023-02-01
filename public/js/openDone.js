var open = document.getElementById("open");
var done = document.getElementById("done");
done.style.display = "none";

function navSwitch(num) {
    /* Open */
    if (num == 1) {
        done.style.display = "none";
        open.style.display = "block";

        document.getElementById("openButton").style.backgroundColor = "white";
        document.getElementById("doneButton").style.backgroundColor = "black";

        document.getElementById("openButton").style.color = "black";
        document.getElementById("doneButton").style.color = "white";
    }
    /* Done */
    else {
        done.style.display = "block";
        open.style.display = "none";

        document.getElementById("openButton").style.backgroundColor = "black";
        document.getElementById("doneButton").style.backgroundColor = "white";

        document.getElementById("openButton").style.color = "white";
        document.getElementById("doneButton").style.color = "black";
    }
}