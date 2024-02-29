<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style_destinations.css">
    <title><?php echo $settings_r['site_title'] ?> - DESTINATIONS</title>




</head>

<body>

    <?php require('inc/header.php'); ?>

    <div class="destinations-home">
        <div class="destinations-us">
            <h2 class="fw-bold h-font text-center">OUR DESTINATIONS</h2>
            <div class="hr mb-4"></div>
        </div>
    </div>

    <section class="book-form" id="book-form">

        <form action="destinations.php" method="POST">
            <div class="inputBox">
                <span>Which State you wanna go to?</span>
                <input type="text" name="name" autocomplete="off" placeholder="place name" value="">
            </div>
            <button type="submit" name="submit-search" class="btn shadow-none">Find Now</button>
        </form>

    </section>

    <div class="container-fluid" style="margin-top: 2rem!important;">
        <div class="main-row">

            <?php
            if (isset($_POST['submit-search'])) {
                $search = mysqli_real_escape_string($con, $_POST['name']);
                $destination_res = select("SELECT * FROM `destinations` WHERE `status`=? AND `removed`=? AND `name`LIKE '%$search%' ORDER BY `id` DESC", [1, 0], 'ii');
            } else {
                $destination_res = select("SELECT * FROM `destinations` WHERE `status`=? AND `removed`=? ORDER BY `id` DESC", [1, 0], 'ii');
            }

            while ($destination_data = mysqli_fetch_assoc($destination_res)) {

                //get features of destination
                $feq_q = mysqli_query($con, "SELECT f.name FROM `features` f INNER JOIN `destination_features` rfea ON f.id = rfea.features_id WHERE rfea.destination_id = '$destination_data[id]'");

                if (mysqli_num_rows($feq_q) == 0) {
                    $features_data = "";
                } else {
                    $features_data = "<h6 class='mb-1 text-white fs-3'>Features</h6>";
                }

                while ($fea_row = mysqli_fetch_assoc($feq_q)) {
                    $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap fs-5 mb-3' style='margin-right:3px;'>$fea_row[name]</span>";
                }

                //get services of destination
                $fac_q = mysqli_query($con, "SELECT f.name FROM `services` f INNER JOIN `destination_services` rfac ON f.id = rfac.services_id WHERE rfac.destination_id = '$destination_data[id]'");

                $services_data = "";
                while ($fac_row = mysqli_fetch_assoc($fac_q)) {
                    $services_data .= "$fac_row[name], ";
                }

                //get thumbnail of image
                $destination_thumb = DESTINATIONS_IMG_PATH . "thumbnail.jpg";
                $thumb_q = mysqli_query($con, "SELECT * FROM `destination_images` WHERE `destination_id`='$destination_data[id]' AND `thumb`='1'");

                if (mysqli_num_rows($thumb_q) > 0) {
                    $thumb_res = mysqli_fetch_assoc($thumb_q);
                    $destination_thumb = DESTINATIONS_IMG_PATH . $thumb_res['image'];
                }

                $book_btn = "";
                if (!$settings_r['shutdown']) {
                    $login = 0;
                    if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                        $login = 1;
                    }
                    $book_btn = "<button onclick='checkLogininToBook($login, $destination_data[id])' class='btn btn-sm w-100 custom-bg shadown-none mb-4'>Book Now</button>";
                } else {
                    $book_btn = "<button class='btn btn-sm w-100 custom-bg shadown-none mb-4 disabled'>Book Now</button>";
                }
                //print destination card

                echo <<< data
    
                            <div class="card mb-4 border-0 text-start">
                                <div class="row g-0 p-3 align-items-center">
                                    <div class="image col-md-5 mb-lg-0 mb-3">
                                        <img src="$destination_thumb" class="img-fluid rounded">
                                    </div>
                                    <div class="col-md-5 px-lg-3 px-md-3 px-0">
                                        <div class="card-body">
                                            <h5 class="card-title fs-1 text-start mb-3">$destination_data[name]</h5>
                                            <div class="duration mb-3">
                                                <span class="badge rounded-pill bg-light text-dark text-wrap" style="font-size: 1.3rem; margin-bottom: 6px">Duration - $destination_data[duration] days</span>
                                            </div>
                                            <div class="Services">
                                                <span class='badge rounded-pill bg-light text-dark text-wrap fs-5 mb-3' style='margin-right:3px;'>Included: $services_data</span>                                            
                                            </div>
                                            <div class="Features">                                           
                                                $features_data
                                            </div>
                                            <h6 class="mb-2 text-white" style="font-size: 1.7rem;">Package Price - ₹$destination_data[price] per person</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        $book_btn
                                        <a href="destination_details.php?id=$destination_data[id]" class="btn btn-sm w-100 btn-outline-dark shadown-none">More Details</a>
                                    </div>
                                </div>
                            </div>
                        
                        data;
            }
            ?>

        </div>
    </div>

    <?php require('inc/footer.php'); ?>

</body>

</html>