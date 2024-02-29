<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style_destinations.css">
    <title><?php echo $settings_r['site_title'] ?> - CONFIRM BOOKING</title>

    <style>
        ::-webkit-calendar-picker-indicator {
            filter: invert(0) !important;
        }
    </style>


</head>

<body>

    <?php require('inc/header.php'); ?>

    <?php

    /*    
        check destination id from url is present or not
        shutdown mode is active or not
        user if logged in or not
    */
    if (!isset($_GET['id']) || $settings_r['shutdown'] == true) {
        redirect('destinations.php');
    } else if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
        redirect('destinations.php');
    }

    // filter and get destination and user data
    $data = filteration($_GET);

    $destination_res = select("SELECT * FROM `destinations` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');

    if (mysqli_num_rows($destination_res) == 0) {
        redirect('destinations.php');
    }

    $destination_data = mysqli_fetch_assoc($destination_res);

    $_SESSION['destination'] = [
        "id" => $destination_data['id'],
        "name" => $destination_data['name'],
        "price" => $destination_data['price'],
        "payment" => null,
        "available" => false,
        "days" => $destination_data['duration'],
    ];

    $user_res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
    $user_data = mysqli_fetch_assoc($user_res);
    ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 my-5 mb-4 px-4">
                <h2 class="fw-bold fs-1">CONFIRM BOOKING</h2>
                <div style="font-size: 14px;">
                    <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                    <span class="text-secondary"> > </span>
                    <a href="destinations.php" class="text-secondary text-decoration-none">DESTINATIONS</a>
                    <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                    <span class="text-secondary"> > </span>
                    <a href="#" class="text-secondary text-decoration-none">CONFIRM</a>
                </div>
            </div>

            <div class="col-lg-7 col-md-12 px-4">
                <?php
                //get thumbnail of image
                $destination_thumb = DESTINATIONS_IMG_PATH . "thumbnail.jpg";
                $thumb_q = mysqli_query($con, "SELECT * FROM `destination_images` WHERE `destination_id`='$destination_data[id]' AND `thumb`='1'");

                if (mysqli_num_rows($thumb_q) > 0) {
                    $thumb_res = mysqli_fetch_assoc($thumb_q);
                    $destination_thumb = DESTINATIONS_IMG_PATH . $thumb_res['image'];
                }

                echo <<< data
                    <div class="card p-3 shadow-sm rounded">
                        <img src="$destination_thumb" class="img-fluid rounded mb-3">
                        <h1 class="mb-3 fs-bold" style="font-size: 3.5rem">$destination_data[name]</h1>
                        <h2>₹$destination_data[price] per person</h2>
                        <h2>$destination_data[duration] days Duration</h2>
                    </div>
                data;
                ?>
            </div>

            <div class="col-lg-5 col-md-12 px-4">
                <div class="card mb-4 border-0 shadow-sm rounded-3">
                    <div class="card-body fs-4">
                        <form action="pay_now.php" method="POST" id="booking_form">
                            <h1 class="mb-3 fs-bold" style="font-size: 3.5rem">BOOKING DETAILS</h1>
                            <div class="row fs-1">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Name</label>
                                    <input style="font-size: 1.8rem;" name="name" value="<?php echo $user_data['name'] ?>" type="text" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label">Phone Number</label>
                                    <input style="font-size: 1.8rem;" name="phonenum" value="<?php echo $user_data['phonenum'] ?>" type="number" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-12 mb-4">
                                    <label class="form-label">Address</label>
                                    <textarea style="font-size: 1.8rem;" name="address" class="form-control shadow-none" rows="2" required><?php echo $user_data['address'] ?></textarea>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">No. of travelers</label>
                                    <input style="font-size: 1.8rem;" name="no_person" oninput="refresh_amount()" type="number" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label class="form-label">Departure Date</label>
                                    <input style="font-size: 1.8rem; text-transform: none;" onchange="check_availability()" name="ddate" type="date" class="form-control shadow-none" required>
                                </div>
                                <div class="col-12">
                                    <div class="spinner-border text-light mb-4 d-none" id="info_loader" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <h3 class="text-danger text-center" id="pay_info">Provide Departure Date & No. of travelers!</h3>
                                    <button name="pay_now" class="btn w-100 mb-1" disabled>Pay Now</button>
                                </div>
                                <input name="duration" value="<?php echo $destination_data['duration'] ?>" hidden>
                                <input name="price" value="<?php echo $destination_data['price'] ?>" hidden>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php require('inc/footer.php'); ?>
    <script>
        let booking_form = document.getElementById('booking_form');
        let info_loader = document.getElementById('info_loader');
        let pay_info = document.getElementById('pay_info');
        let days = 0;


        function refresh_amount(){
            pay_info.innerHTML = "No. of Days: "+days+"<br>Total Amount to Pay: ₹"+booking_form.elements['no_person'].value*booking_form.elements['price'].value;
        }

        function check_availability() {
            let ddate_val = booking_form.elements['ddate'].value;
            let no_person = booking_form.elements['no_person'].value;
            let duration = booking_form.elements['duration'].value;

            booking_form.elements['pay_now'].setAttribute('disabled', true);

            if (ddate_val != '') {
                pay_info.classList.add('d-none');
                pay_info.classList.replace('text-dark', 'text-danger');
                pay_info.classList.remove('d-none');

                let data = new FormData();

                data.append('check_availability', '');
                data.append('ddate', ddate_val);
                data.append('no_person', no_person);
                data.append('duration', duration)

                let xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax/confirm_booking.php", true);

                xhr.onload = function() {
                    let data = JSON.parse(this.responseText);
                    
                    if(data.status == 'unavailable'){
                        pay_info.innerText = "Destination not available for this date";
                    } else {
                        days = data.days;
                        pay_info.innerHTML = "No. of Days: "+days+"<br>Total Amount to Pay: ₹"+data.payment;
                        pay_info.classList.replace('text-danger', 'text-white');
                        booking_form.elements['pay_now'].removeAttribute('disabled');
                    }

                    pay_info.classList.remove('d-none');
                    info_loader.classList.add('d-none');
                }
                xhr.send(data);

            }
        }
    </script>

</body>

</html>