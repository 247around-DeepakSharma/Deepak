/* 
 * This file will be used for adding basic validations in the file
 * @creator : Prity Sharma
 * @date : 05-12-2019
 */

function checkQuote(event) {
    if(event.keyCode == 39 || event.keyCode == 34) {
        alert("Quotes are not allowed in the Model Name");
        event.keyCode = 0;
        return false;
    }
}

function checkInputQuote(input)
{
    var model = $(input).val();
    model = model.replace(/'/g, '');
    model = model.replace(/"/g, '');
    $(input).val(model);
}

