<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style_destinations.css">
    <title><?php echo $settings_r['site_title'] ?> - DESTINATION DETAILS</title>

</head>

<body>

    <?php require('inc/header.php'); ?>

    <?php
    if (!isset($_GET['id'])) {
        redirect('destinations.php');
    }

    $data = filteration($_GET);

    $destination_res = select("SELECT * FROM `destinations` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');

    if (mysqli_num_rows($destination_res) == 0) {
        redirect('destinations.php');
    }

    $destination_data = mysqli_fetch_assoc($destination_res);
    ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 my-5 mb-4 px-4">
                <h2 class="fw-bold fs-1"><?php echo $destination_data['name'] ?></h2>
                <div style="font-size: 14px;">
                    <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                    <span class="text-secondary"> > </span>
                    <a href="destinations.php" class="text-secondary text-decoration-none">DESTINATIONS</a>
                </div>
            </div>

            <div class="col-lg-7 col-md-12 px-4">
                <div id="destinationCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                        $destination_img = DESTINATIONS_IMG_PATH . "thumbnail.jpg";
                        $img_q = mysqli_query($con, "SELECT * FROM `destination_images` WHERE `destination_id`='$destination_data[id]'");

                        if (mysqli_num_rows($img_q) > 0) {
                            $active_class = 'active';

                            while ($img_res = mysqli_fetch_assoc($img_q)) {
                                echo "<div class='carousel-item $active_class'>
                                    <img src='" . DESTINATIONS_IMG_PATH . $img_res['image'] . "' class='d-block w-100 rounded'>
                                </div>";
                                $active_class = '';
                            }
                        } else {
                            echo "<div class='carousel-item active'>
                                <img src='$destination_img' class='d-block w-100'>
                            </div>";
                        }

                        ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#destinationCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#destinationCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>

            <div class="col-lg-5 col-md-12 px-4">
                <div class="card mb-4 border-0 shadow-sm rounded-3">
                    <div class="card-body fs-4">
                        <?php

                        echo <<<price
                            <h2>Package Price - ₹$destination_data[price] per person</h4>
                        price;

                        $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review` WHERE `destination_id`='$destination_data[id]' ORDER BY `sr_no` DESC LIMIT 20";

                        $rating_res = mysqli_query($con, $rating_q);
                        $rating_fetch = mysqli_fetch_assoc($rating_res);

                        $rating_data = "";

                        if ($rating_fetch['avg_rating'] != NULL) {
                            for ($i = 0; $i < $rating_fetch['avg_rating']; $i++) {
                                $rating_data .= " <i class='bi bi-star-fill text-warning'></i>";
                            }
                        }

                        echo <<<rating
                            <div class="mb-3">
                                $rating_data
                            </div>
                        rating;

                        $feq_q = mysqli_query($con, "SELECT f.name FROM `features` f INNER JOIN `destination_features` rfea ON f.id = rfea.features_id WHERE rfea.destination_id = '$destination_data[id]'");

                        if (mysqli_num_rows($feq_q) == 0) {
                            $features_data = "";
                        } else {
                            $features_data = "<h6 class='mb-1 text-white fs-3'> Features</h6>";
                        }

                        while ($fea_row = mysqli_fetch_assoc($feq_q)) {
                            $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap fs-5 mb-3' style='margin-right:5px;'>$fea_row[name]</span>";
                        }

                        echo <<<features
                            <div class="Features">                                           
                                $features_data
                            </div>
                        features;

                        $fac_q = mysqli_query($con, "SELECT f.name FROM `services` f INNER JOIN `destination_services` rfac ON f.id = rfac.services_id WHERE rfac.destination_id = '$destination_data[id]'");

                        $services_data = "";
                        while ($fac_row = mysqli_fetch_assoc($fac_q)) {
                            $services_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap fs-5 mb-3' style='margin-right:5px;'>$fac_row[name]</span>";
                        }

                        echo <<<services
                            <div class="Services">
                                <h6 class="mb-1 text-white fs-3"> Included</h6>
                                $services_data
                            </div>
                        services;

                        echo <<< duration
                            <div class="duration mb-3">
                                <h6 class="mb-1 text-white fs-3"> Duration</h6>
                                <span class="badge rounded-pill bg-light text-dark text-wrap" style="font-size: 1.3rem; margin-bottom: 6px"> $destination_data[duration] days</span>
                            </div>
                        duration;

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
                        echo <<< book
                            $book_btn
                        book;

                        ?>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4 px-4">
                <div class="mb-5 mt-5">
                    <h1 class="fw-bold"> Description </h5>
                        <p class="fs-3">
                            <?php echo $destination_data['description']; ?>
                        </p>
                </div>
                <div class="reviews">
                    <h1 class="mb-4 fw-bold">Reviews & Ratings</h1>
                    <?php
                        $review_q = "SELECT rr.*, uc.name AS uname, uc.profile, d.name AS dname FROM `rating_review` rr 
                        INNER JOIN `user_cred` uc ON rr.user_id = uc.id 
                        INNER JOIN `destinations` d ON rr.destination_id = d.id 
                        WHERE rr.destination_id = '$destination_data[id]' 
                        ORDER BY `sr_no` DESC LIMIT 10";

                        $review_res = mysqli_query($con, $review_q);
                        $img_path = USERS_IMG_PATH;

                        if (mysqli_num_rows($review_res) == 0) {
                            echo 'No reviews yet!';
                        } else {
                            while ($row = mysqli_fetch_assoc($review_res)) {
                                $stars = "<i class='bi bi-star-fill text-warning'></i> ";
                                for ($i = 1; $i < $row['rating']; $i++) {
                                    $stars .= " <i class='bi bi-star-fill text-warning'></i>";
                                }

                                echo <<< reviews
                                    <div class="mb-5 fs-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="$img_path$row[profile]" width="30px" height="30px">
                                            <h3 class="m-0 ms-2"> $row[uname]</h3>
                                        </div>
                                        <p class="mb-1">
                                            $row[review]
                                        </p>
                                        <div class="rating">
                                            $stars
                                        </div>
                                    </div>
                                reviews;
                            }
                        }
                    ?>
                    
                </div>
            </div>

            <?php
            // $destination_res = select("SELECT * FROM `destinations` WHERE `status`=? AND `removed`=?", [1, 0], 'ii');

            // while($destination_data = mysqli_fetch_assoc($destination_res))
            // {

            //     //get features of destination
            //     $feq_q = mysqli_query($con, "SELECT f.name FROM `features` f INNER JOIN `destination_features` rfea ON f.id = rfea.features_id WHERE rfea.destination_id = '$destination_data[id]'");

            //     $features_data = "";
            //     while($fea_row = mysqli_fetch_assoc($feq_q)){
            //         $features_data .="<span class='badge rounded-pill bg-light text-dark text-wrap'>$fea_row[name]</span>";
            //     }

            //     //get services of destination
            //     $fac_q = mysqli_query($con, "SELECT f.name FROM `services` f INNER JOIN `destination_services` rfac ON f.id = rfac.services_id WHERE rfac.destination_id = '$destination_data[id]'");

            //     $services_data = "";
            //     while($fac_row = mysqli_fetch_assoc($fac_q)){
            //         $services_data .="<span class='badge rounded-pill bg-light text-dark text-wrap fs-5 mb-3' style='margin-right:3px;'>$fac_row[name]</span>";
            //     }

            //     //get thumbnail of image
            //     $destination_thumb = DESTINATIONS_IMG_PATH."thumbnail.jpg";
            //     $thumb_q = mysqli_query($con, "SELECT * FROM `destination_images` WHERE `destination_id`='$destination_data[id]' AND `thumb`='1'");

            //     if(mysqli_num_rows($thumb_q)>0){
            //         $thumb_res = mysqli_fetch_assoc($thumb_q);
            //         $destination_thumb = DESTINATIONS_IMG_PATH.$thumb_res['image'];
            //     }

            //     //print destination card

            //     echo <<< data

            //         <div class="card mb-4 border-0 text-start">
            //             <div class="row g-0 p-3 align-items-center">
            //                 <div class="image col-md-5 mb-lg-0 mb-3">
            //                     <img src="$destination_thumb" class="img-fluid rounded">
            //                 </div>
            //                 <div class="col-md-5 px-lg-3 px-md-3 px-0">
            //                     <div class="card-body">
            //                         <h5 class="card-title fs-1 text-start mb-3">$destination_data[name]</h5>
            //                         <div class="duration mb-3">
            //                             <span class="badge rounded-pill bg-light text-dark text-wrap" style="font-size: 1.3rem; margin-bottom: 6px">Duration - $destination_data[duration] days</span>
            //                         </div>
            //                         <div class="Services">
            //                             <h6 class="mb-1 text-white fs-3">Included</h6>
            //                             $services_data
            //                         </div>
            //                         <h6 class="mb-2 text-white" style="font-size: 1.7rem;">Package Price - ₹$destination_data[price] per person</h6>
            //                         <p class="card-text fs-4">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
            //                     </div>
            //                 </div>
            //                 <div class="col-md-2 text-center">
            //                     <a href="#" class="btn btn-sm w-100 custom-bg shadown-none mb-2">Book Now</a>
            //                     <a href="destination_details.php?id=$destination_data[id]" class="btn btn-sm w-100 btn-outline-dark shadown-none">More Details</a>
            //                 </div>
            //             </div>
            //         </div>

            //     data;

            // }
            ?>
        </div>
    </div>

    <?php require('inc/footer.php'); ?>

</body>

</html>