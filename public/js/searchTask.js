function searchFor() {
    var input, filter, tables, target;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    tables = document.querySelectorAll(".data");
    target = 0;

    tables.forEach(element => {
        var tableMatch = element.id.toUpperCase().indexOf(filter) > -1;
        var rapportMatch = false;

        // Check rapports inside the table
        var rapports = element.querySelectorAll("tr");
        rapports.forEach(row => {
            var cells = row.querySelectorAll("td");
            cells.forEach(cell => {
                var txtValue = cell.textContent || cell.innerText;
                txtValue = txtValue.toUpperCase();
                if (txtValue.indexOf(filter) > -1) {
                    rapportMatch = true;
                }
            });
        });

        if (tableMatch || rapportMatch) {
            element.hidden = false;
            target++;
        } else {
            element.hidden = true;
        }
    });

    /* Check if nothing has been found */
    if (target === 0) {
        document.getElementById("nothingFound").style.display = "block";
    } else {
        document.getElementById("nothingFound").style.display = "none";
    }
}