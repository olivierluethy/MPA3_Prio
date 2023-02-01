function myFunction() {
    var input, filter, tables, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase().replace(/\s/g, "");
    tables = document.querySelectorAll(".data");
    console.log("tables", tables);
    console.log("filter", filter);

    var target = 0;
    tables.forEach(element => {
        if(element.id.toUpperCase().indexOf(filter) > -1){
            element.hidden = false;
            console.log(element.id);
            target++;
        }else{
            element.hidden = true;
            console.log(element.id);
        }
    });

    /* Check if nothing has been found */
    if(target == 0){
        document.getElementById("nothingFound").style="display: block;";
    }else {
        document.getElementById("nothingFound").style="display: none;";
    }
    console.log("target", target);
}