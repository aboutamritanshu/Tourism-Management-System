
function get_bookings(search='') {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/new_bookings.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        document.getElementById('table-data').innerHTML = this.responseText;
    }

    xhr.send('get_bookings&search='+search);
}

function assign_destination(id) {
    let data = new FormData();
    data.append('booking_id', id);
    data.append('assign_destination', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/new_bookings.php", true);

    xhr.onload = function () {

        if(this.responseText == 1){
            alert('success', 'Booking Approved!')
            get_bookings();
        } else {
            alert('error', 'Server Down!')
        }
    }
    xhr.send(data);
}

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