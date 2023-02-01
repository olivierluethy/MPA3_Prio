// Clientside Validierung - Add Task
window.addEventListener("load", function() {
    this.document.querySelector("form").addEventListener('submit', function(evt) {
        var errors = false;
        var warnings = document.querySelectorAll(".warning");
        if (warnings != null) {
            warnings.forEach(element => {
                element.remove();
            });
        }
        if (document.querySelector('#rapport') != null) {
            if (document.querySelector('#rapport').value.trim() === '') {
                document.querySelector('#rapport').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please enter a rapport!</label>");
                errors = true;
            }
        }
        if (document.querySelector('#appt-time') != null) {
            if (document.querySelector('#appt-time').value.trim() === '') {
                document.querySelector('#appt-time').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please enter a time!</label>");
                errors = true;
            }
        }
        if (errors) {
            evt.preventDefault();
        }
    });
});