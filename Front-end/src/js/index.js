/*
 * Shows/hides USA and International specific fields
 * by toggling css classes
 */
function toggleAddressType() {
    var div = document.getElementById("customerInfo");

    if (div.classList.contains('usa')) {
        div.classList.remove('usa');
        div.classList.add('international');
    } else {
        div.classList.remove('international');
        div.classList.add('usa');
    }
}