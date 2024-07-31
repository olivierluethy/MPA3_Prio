// Function to handle search
function searchFor() {
    // Get the search query and convert it to lowercase
    const query = document.getElementById('myInput').value.toLowerCase();

    // Get all tasks
    const tasks = document.querySelectorAll('.data');

    tasks.forEach(task => {
        // Get the task title
        const title = task.querySelector('th').innerText.toLowerCase();

        // Check if the title or any rapport contains the search query
        if (title.includes(query) || Array.from(task.querySelectorAll('td')).some(td => td.innerText.toLowerCase().includes(query))) {
            // If query matches, display the task
            task.style.display = '';
        } else {
            // If query does not match, hide the task
            task.style.display = 'none';
        }
    });

    // Show 'Nothing Found' message if no tasks are visible
    const visibleTasks = Array.from(tasks).some(task => task.style.display !== 'none');
    document.getElementById('nothingFound').style.display = visibleTasks ? 'none' : 'block';
}