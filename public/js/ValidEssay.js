// Clientside Validierung - Add Task
window.addEventListener("load", function() {
    this.document.querySelector("#essay").addEventListener('submit', function(evt) {
        var errors = false;
        var warnings = document.querySelectorAll(".warning");
        var value = document.querySelector('#essay_content').value;
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
        if (document.querySelector('#essay_content') != null) {
            if (document.querySelector('#essay_content').value.trim() === '') {
                document.querySelector('#essay_content').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please write an essay!</label>");
                errors = true;
            } else if (value.length < 5000) {
                document.querySelector('#essay_content').insertAdjacentHTML("afterend", "<label class=\"warning\"> Please enter at least 5000 characters!</label>");
                errors = true;
            }
        }
        if (errors) {
            evt.preventDefault();
        }
    });
});