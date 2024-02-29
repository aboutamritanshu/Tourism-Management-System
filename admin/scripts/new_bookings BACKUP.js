
function get_bookings(search='') {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/new_bookings.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        document.getElementById('table-data').innerHTML = this.responseText;
    }

    xhr.send('get_bookings&search='+search);
}

let assign_destination_form = document.getElementById('assign_destination_form');
function assign_destination(id){
    assign_destination_form.elements['booking_id'].value=id;
}

assign_destination_form.addEventListener('submit', function(e){
    e.preventDefault();

    let data = new FormData();
    data.append('destination_no', assign_destination_form.elements['destination_no'].value);
    data.append('booking_id', assign_destination_form.elements['booking_id'].value);
    data.append('assign_destination', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/new_bookings.php", true);

    xhr.onload = function () {
        var myModal = document.getElementById('assign-destination');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText == 1){
            alert('success', 'Destination Number Alloted! Booking Finalized!')
            assign_destination_form.reset();
            get_bookings();
        } else {
            alert('error', 'Server Down!')
        }
    }
    xhr.send(data);
})

function cancel_booking(id){
    if (confirm("Are you sure, you want to cancel this booking?")) {
        let data = new FormData();
        data.append('booking_id', id);
        data.append('cancel_booking', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/new_bookings.php", true);

        xhr.onload = function () {
            if (this.responseText == 1) {
                alert('success', 'Booking Removed!');
                get_bookings();
            } else {
                alert('error', 'Server Down!');
            }
        }
        xhr.send(data);
    }
}

window.onload = function () {
    get_bookings();
}