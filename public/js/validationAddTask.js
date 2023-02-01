// Clientside Validierung - Add Task
window.addEventListener("load", function() {
    this.document.querySelector("form").addEventListener('submit', function(evt) {
        var errors = false;
        var warnings = document.querySelectorAll(".warning");
        var value = document.querySelector('#motivation').value;
        var value = document.querySelector('#description').value;
        if (warnings != null) {
            warnings.forEach(element => {
                element.remove();
            });
        }
        if (document.querySelector('#title') != null) {
            if (document.querySelector('#title').value.trim() === '') {
                document.querySelector('#title').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please enter a title!</label>");
                errors = true;
            }
        }
        if (document.querySelector('#description') != null) {
            if (document.querySelector('#description').value.trim() === '') {
                document.querySelector('#description').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please enter a description!</label>");
                errors = true;
            } else if (value.length < 60) {
                document.querySelector('#description').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please enter at least 60 characters!</label>");
                errors = true;
            }
        }
        if (document.querySelector('#motivation') != null) {
            if (document.querySelector('#motivation').value.trim() === '') {
                document.querySelector('#motivation').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please enter a motivation!</label>");
                errors = true;
            } else if (value.length < 60) {
                document.querySelector('#motivation').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please enter at least 60 characters!</label>");
                errors = true;
            }
        }
        if (document.querySelector('#deadline') != null) {
            if (document.querySelector('#deadline').value.trim() === '') {
                document.querySelector('#deadline').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please enter a deadline!</label>");
                errors = true;
            } else if (new Date(document.querySelector('#deadline').value) > Date()) {
                document.querySelector('#deadline').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please select a valid date!</label>");
                errors = true;
            }
        }
        if (document.querySelector('#priority') != null) {
            if (document.querySelector('#priority').value.trim() === '') {
                document.querySelector('#priority').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please enter a priority!</label>");
                errors = true;
            }
        }
        if (errors) {
            evt.preventDefault();
        }
    });
});