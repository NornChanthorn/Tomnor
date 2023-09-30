$(function () {
    formatNumericFields();
});

function formatNumericFields() {
    $('.integer-input').number(true, 0, '.', '');
    $('.decimal-input').number(true, 2, '.', '');
    $('.decimal-display').number(true, 2);
}
function formatDate(date,addDay=0,format='DD-MM-YYYY') {
    date=moment(date, 'DD-MM-YYYY HH:mm');
    if(addDay){
        date = date.add(addDay, 'days');
    }
    return  date.format(format);
}