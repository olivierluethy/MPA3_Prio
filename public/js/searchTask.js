// Filter task reporting cards by title or any reported text.
// Structure-agnostic: matches against the whole card's text content, so it
// keeps working regardless of the card markup. Hooks preserved: #myInput,
// .data (each task card), #nothingFound.
function searchFor() {
    const query = document.getElementById('myInput').value.toLowerCase();
    const tasks = document.querySelectorAll('.data');
    let anyVisible = false;

    tasks.forEach(task => {
        const matches = task.innerText.toLowerCase().includes(query);
        task.style.display = matches ? '' : 'none';
        if (matches) anyVisible = true;
    });

    const nothing = document.getElementById('nothingFound');
    if (nothing) nothing.style.display = anyVisible ? 'none' : 'block';
}
