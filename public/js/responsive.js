function responsive() {
    var x = document.getElementById("nav");
    if (x.className === "part2") {
        x.className += " responsive";
    } else {
        x.className = "part2";
    }
}