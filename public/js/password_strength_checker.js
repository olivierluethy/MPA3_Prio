const indicator = document.querySelector(".indicator");
const input = document.getElementById("password_register");
const weak = document.querySelector(".weak").style;
const medium = document.querySelector(".medium").style;
const strong = document.querySelector(".strong").style;
const text = document.querySelector(".text");
let isPasswordReady = false;

function trigger() {
    var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
    var mediumRegex = new RegExp("^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})");

    if (strongRegex.test(input.value)) {
        weak.backgroundColor = "white";
        medium.backgroundColor = "white";
        strong.backgroundColor = "green";
        isPasswordReady = true;
        text.innerHTML = "Your Password Is Strong";
    } else if (mediumRegex.test(input.value)) {
        weak.backgroundColor = "white";
        medium.backgroundColor = "orange";
        strong.backgroundColor = "white";
        isPasswordReady = false;
        text.innerHTML = "Your Password Is Medium";
    } else if (input.value.length == 0) {
        text.innerHTML = "Enter A Password";
        weak.backgroundColor = "white";
        medium.backgroundColor = "white";
        strong.backgroundColor = "white";
        isPasswordReady = false;
    } else {
        weak.backgroundColor = "red";
        medium.backgroundColor = "white";
        strong.backgroundColor = "white";
        text.innerHTML = "Your Password Is Weak";
        isPasswordReady = false;
    }
}