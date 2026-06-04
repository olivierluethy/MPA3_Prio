var login = document.getElementById("login");
var register = document.getElementById("register");
var title = document.getElementById("title");
register.style.display = "none";

function navSwitch(num) {
    /* Register */
    if (num == 1) {
        login.style.display = "none";
        title.innerHTML = "Register";
        // Dark-theme active/inactive colors (brand accent vs. muted surface)
        document.getElementById("registerButton").style.backgroundColor = "#4f46e5";
        document.getElementById("loginButton").style.backgroundColor = "#1e293b";

        document.getElementById("registerButton").style.color = "#ffffff";
        document.getElementById("loginButton").style.color = "#94a3b8";
        register.style.display = "block";
    } else {
        login.style.display = "block";
        register.style.display = "none";
        title.innerHTML = "Login";

        document.getElementById("registerButton").style.backgroundColor = "#1e293b";
        document.getElementById("loginButton").style.backgroundColor = "#4f46e5";

        document.getElementById("registerButton").style.color = "#94a3b8";
        document.getElementById("loginButton").style.color = "#ffffff";
    }
}

// Clientside Validierung - Login
window.addEventListener("load", function() {
    this.document.getElementById("login").addEventListener('submit', function(evt) {
        var errors = false;
        var warnings = document.querySelectorAll(".warning");
        if (warnings != null) {
            warnings.forEach(element => {
                element.remove();
            });
        }
        if (document.querySelector('#email_login') != null) {
            if (document.querySelector('#email_login').value.trim() === '') {
                document.querySelector('#email_login').insertAdjacentHTML("afterend", "<label class=\"warning\"> Bitte geben Sie Ihre E-Mail ein</label>");
                errors = true;
            }
        }
        if (document.querySelector('#password_login') != null) {
            if (document.querySelector('#password_login').value.trim() === '') {
                document.querySelector('#password_login').insertAdjacentHTML("afterend", "<label class=\"warning\"> Bitte geben Sie Ihr Passwort ein</label>");
                errors = true;
            } else if (document.querySelector('#password_login').value.length < 6) {
                document.querySelector('#password_login').insertAdjacentHTML("afterend", "<label class=\"warning\"> Passwort muss mindestens 6 Zeichen enthalten</label>");
                errors = true;
            }
        }
        if (errors) {
            evt.preventDefault();
        }
    });
});

// Clientside Validierung - Register
window.addEventListener("load", function() {
    this.document.getElementById("register").addEventListener('submit', function(evt) {
        var errors = false;
        var warnings = document.querySelectorAll(".warning");
        if (warnings != null) {
            warnings.forEach(element => {
                element.remove();
            });
        }
        if (document.querySelector('#email_register') != null) {
            if (document.querySelector('#email_register').value.trim() === '') {
                document.querySelector('#email_register').insertAdjacentHTML("afterend", "<label class=\"warning\"> Bitte geben Sie Ihre E-Mail ein</label>");
                errors = true;
            }
        }
        if (document.querySelector('#password_register') != null) {
            if (document.querySelector('#password_register').value.trim() === '') {
                document.querySelector('#password_register').insertAdjacentHTML("afterend", "<label class=\"warning\"> Bitte geben Sie ein Passwort ein</label>");
                errors = true;
            } else if (document.querySelector('#password_register').value.length < 6) {
                document.querySelector('#password_register').insertAdjacentHTML("afterend", "<label class=\"warning\"> Passwort muss mindestens 6 Zeichen enthalten</label>");
                errors = true;
            }
        }
        if (document.querySelector('#verypass') != null) {
            if (document.querySelector('#verypass').value.trim() === '') {
                document.querySelector('#verypass').insertAdjacentHTML("afterend", "<label class=\"warning\"> Bitte Passwort erneut eingeben</label>");
                errors = true;
            } else if (document.querySelector('#password_register').value != document.querySelector('#verypass').value) {
                document.querySelector('#verypass').insertAdjacentHTML("afterend", "<label class=\"warning\"> Nicht das gleiche Passwort</label>");
                errors = true;
            }
        }
        if (isPasswordReady == false) {
            document.querySelector('.password_strength_area').insertAdjacentHTML("afterend", "<label class=\"warning\"> Bitte stärkeres Passwort eingeben</label>");
            errors = true;
        }
        if (errors) {
            evt.preventDefault();
        }
    });
});