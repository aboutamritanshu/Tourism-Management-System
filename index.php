<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?>
    <title><?php echo $settings_r['site_title'] ?> - Home</title>
</head>


<body>

    <?php require('inc/header.php'); ?>

    <!-- home section starts  -->

    <section class="home" id="home">

        <div class="content">
            <span>follow us</span>
            <h3>to the unknown</h3>
            <p></p>
        </div>

    </section>

    <!-- home section ends  -->

    <!-- book form section starts  -->

    <section class="book-form" id="book-form">

        <form action="destinations.php" method="POST">
            <div class="inputBox">
                <span>Which State you wanna go to?</span>
                <input type="text" name="name" autocomplete="off" placeholder="place name" value="">
            </div>
            <div class="inputBox">
                <span>When?</span>
                <input type="date" autocomplete="off" value="">
            </div>
            <div class="inputBox">
                <span>How many?</span>
                <input type="number" autocomplete="off" placeholder="number of travelers" value="">
            </div>
            <button type="submit" name="submit-search" class="btn shadow-none">Find Now</button>
        </form>

    </section>

    <!-- book form section ends  -->

    <!-- about section starts  -->

    <section class="about" id="about">

        <div class="video-container">
            <video src="images/about-vid-1.mp4" muted autoplay loop class="video"></video>
            <div class="controls">
                <span class="control-btn" data-src="images/about-vid-1.mp4"></span>
                <span class="control-btn" data-src="images/about-vid-2.mp4"></span>
                <span class="control-btn" data-src="images/about-vid-3.mp4"></span>
            </div>
        </div>

        <div class="content">
            <span>why choose us?</span>
            <h3>Nature's Majesty Awaits You</h3>
            <p>​At <?php echo $settings_r['site_title'] ?>, we look into each trip with meticulous preparation, planning, and craft them into your dream vacation. Learn about why you should choose us as your next travel agent.</p>
            <a href="about.php" class="btn">read more</a>
        </div>

    </section>

    <!-- about section ends  -->

    <!-- destination section starts  -->

    <section class="destination" id="destination">

        <div class="heading">
            <span>our destination</span>
            <h1>make destination yours</h1>
        </div>

        <div class="box-container">

            <?php
            $destination_res = select("SELECT * FROM `destinations` WHERE `status`=? AND `removed`=? ORDER BY `id` DESC LIMIT 4", [1, 0], 'ii');

            while ($destination_data = mysqli_fetch_assoc($destination_res)) {

                //get features of destination
                $feq_q = mysqli_query($con, "SELECT f.name FROM `features` f INNER JOIN `destination_features` rfea ON f.id = rfea.features_id WHERE rfea.destination_id = '$destination_data[id]'");

                if (mysqli_num_rows($feq_q) == 0) {
                    $features_data = "";
                } else {
                    $features_data = "<h6 class='mb-1 text-white fs-3'>Features</h6>";
                }

                while ($fea_row = mysqli_fetch_assoc($feq_q)) {
                    $features_data .= "<span class='badge rounded-pill bg-light text-dark text-wrap fs-4 mb-3' style='margin-right:3px;'>$fea_row[name]</span>";
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

                $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review` WHERE `destination_id`='$destination_data[id]' ORDER BY `sr_no` DESC LIMIT 20";

                $rating_res = mysqli_query($con, $rating_q);
                $rating_fetch = mysqli_fetch_assoc($rating_res);

                $rating_data = "";

                if ($rating_fetch['avg_rating'] != NULL) {
                    $rating_data .= "<h6 class='text-white' style='font-size: 1.7rem;'> Rating - ";
                    for ($i = 0; $i < $rating_fetch['avg_rating']; $i++) {
                        $rating_data .= "<i class='bi bi-star-fill text-warning'></i> ";
                    }
                    $rating_data .= "</h6>";
                }

                //print destination card

                echo <<< data
                    <div class="box">
                        <div class="image">
                            <img src="$destination_thumb" alt="">
                        </div>
                        <div class="content">
                            <div class="info">
                                <h3 class="mb-4">$destination_data[name]</h3>
                                <div class="Services">
                                    <span class='badge rounded-pill bg-light text-dark text-wrap fs-4 mb-3' style='margin-right:3px;'>Included: $services_data</span>                                            
                                </div>
                                <div class="Features">                                           
                                    $features_data
                                </div>
                                <div class="duration mb-3">
                                    <span class="badge rounded-pill bg-light text-dark text-wrap" style="font-size: 1.4rem; margin-bottom: 6px">Duration - $destination_data[duration] days</span>
                                </div>
                                <h6 class="mb-3 text-white" style="font-size: 1.7rem;">Package Price - ₹$destination_data[price] per person</h6>

                                <div class='rating'> 
                                    $rating_data
                                </div>

                            </div>

                            <div class="mt-5 mb-3">
                                $book_btn
                                <a href="destination_details.php?id=$destination_data[id]">More Details <i class="fas fa-angle-right"></i></a>
                            </div>
                        </div>
                    </div>
                data;
            }
            ?>
        </div>
        <div class="text-center mt-3 mb-5">
            <a href="destinations.php" class="btn">More destination</a>
        </div>
    </section>

    <!-- destination section ends  -->

    <!-- services section ends  -->

    <section class="services" id="services">
        <div class="heading">
            <span>our services</span>
            <h1>countless expericences</h1>
        </div>
        <div class="box-container">
            <div class="box">
                <i class="fas fa-globe"></i>
                <h3>worldwide</h3>
                <p>You can book a tour from all around the world.</p>
            </div>
            <div class="box">
                <i class="fas fa-hiking"></i>
                <h3>activity</h3>
                <p>We offers wide range of activities in India with a unique blend of thrilling and adventurous activities</p>
            </div>
            <div class="box">
                <i class="fas fa-utensils"></i>
                <h3>restaurant</h3>
                <p>Enjoy the tastiest from different restaurants of your choice</p>
            </div>
            <div class="box">
                <i class="fas fa-hotel"></i>
                <h3>hotels</h3>
                <p>We offers plenty of deals and offers on luxury and budget hotels in India in all the cities, on a daily basis.</p>
            </div>
            <div class="box">
                <i class="fa fa-car"></i>
                <h3>Transport</h3>
                <p>You can find the top-rated car rental services on <?php echo $settings_r['site_title'] ?> that ensure a safe trip to your destination.</p>
            </div>
            <div class="box">
                <i class="fas fa-headset"></i>
                <h3>24/7 services</h3>
                <p>We are available 24/7.<br> If you have any queries, feel free to ask anytime.</p>
            </div>
        </div>
    </section>

    <!-- services section ends  -->

    <!-- gallery section starts  -->

    <section class="gallery" id="gallery">

        <div class="heading">
            <span>our gallery</span>
            <h1>we record memories</h1>
        </div>

        <div class="box-container">

            <div class="box">
                <img src="images/gallery-img-1.jpg" alt="">
            </div>

            <div class="box">
                <img src="images/gallery-img-2.jpg" alt="">
            </div>

            <div class="box">
                <img src="images/gallery-img-3.jpg" alt="">
            </div>

            <div class="box">
                <img src="images/gallery-img-4.jpg" alt="">
            </div>

            <div class="box">
                <img src="images/gallery-img-5.jpg" alt="">
            </div>

            <div class="box">
                <img src="images/gallery-img-6.jpg" alt="">
            </div>

            <div class="box">
                <img src="images/gallery-img-7.jpg" alt="">
            </div>

            <div class="box">
                <img src="images/gallery-img-8.jpg" alt="">
            </div>

            <div class="box">
                <img src="images/gallery-img-9.jpg" alt="">
            </div>

        </div>

    </section>

    <!-- gallery section ends  -->

    <!-- review section starts  -->

    <section class="review">

        <div class="content">
            <span>testimonials</span>
            <h3>good news from our Customers</h3>
            <p>
                Our clients come from very different walks of life and age groups. We’re delighted that many come back to travel on our tours again and again. We feel that their testimonials describe who we are and what we do far better than we ever could. Here’s a small selection.

            </p>

        </div>

        <div class="box-container">


            <?php
            $review_q = "SELECT rr.*, uc.name AS uname, uc.profile, d.name AS dname FROM `rating_review` rr 
            INNER JOIN `user_cred` uc ON rr.user_id = uc.id 
            INNER JOIN `destinations` d ON rr.destination_id = d.id 
            ORDER BY `sr_no` DESC LIMIT 4";

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

                    echo <<< slides
                        <div class="box">
                            <p>$row[review]</p>
                            <div class="user mb-3">
                                <img src="$img_path$row[profile]" style="object-fit: cover;" loading="lazy">
                                <div class="info">
                                    <h3>$row[uname]</h3>
                                    <span>Customer</span>
                                </div>
                            </div>
                            $stars
                        </div>
                    slides;
                }
            }



            ?>

        </div>

    </section>

    <!-- review section ends  -->

    <!-- blogs section starts  -->

    <!-- <section class="blogs" id="blogs">

        <div class="heading">
            <span>blogs</span>
            <h1>we untold stories</h1>
        </div>

        <div class="box-container">
            <div class="box">
                <div class="image">
                    <img src="images/blog-1.jpg" alt="">
                </div>
                <div class="content">
                    <a href="#" class="link">life is a journey, not a destination</a>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat, eaque.</p>
                    <div class="icon">
                        <a href="#"><i class="fas fa-clock"></i> 21st may, 2021</a>
                        <a href="#"><i class="fas fa-user"></i> by admin</a>
                    </div>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/blog-2.jpg" alt="">
                </div>
                <div class="content">
                    <a href="#" class="link">life is a journey, not a destination</a>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat, eaque.</p>
                    <div class="icon">
                        <a href="#"><i class="fas fa-clock"></i> 21st may, 2021</a>
                        <a href="#"><i class="fas fa-user"></i> by admin</a>
                    </div>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/blog-3.jpg" alt="">
                </div>
                <div class="content">
                    <a href="#" class="link">life is a journey, not a destination</a>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat, eaque.</p>
                    <div class="icon">
                        <a href="#"><i class="fas fa-clock"></i> 21st may, 2021</a>
                        <a href="#"><i class="fas fa-user"></i> by admin</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="text-center mt-3 mb-5">
            <a href="#" class="btn">More Blogs</a>
        </div>
    </section> -->

    <!-- blogs section ends  -->

    <!-- banner section starts  -->

    <div class="banner">

        <div class="content">
            <span>start your adventures</span>
            <h3>Let's Explore This World</h3>
            <p></p>
        </div>

    </div>

    <!-- banner section ends  -->

    <?php require('inc/footer.php'); ?>

</body>

</html>