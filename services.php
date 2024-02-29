<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel - Services</title>
    <?php require('inc/links.php'); ?>
    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style_services.css">
    <title><?php echo $settings_r['site_title'] ?> - SERVICES</title>


</head>


<body>

    <?php require('inc/header.php'); ?>
    <div class="services-home">
        <div class="my-5 px-4">
            <h2 class="fw-bold h-font text-center"> SERVICES </h2>
            <div class="hr mb-1"></div>
            <p class="text-center mt-3 fs-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tempore nostrum, nam quam consequuntur quis, mollitia temporibus sint deleniti asperiores enim neque consequatur fuga dicta provident magnam. Aliquam sed explicabo dignissimos?</p>
        </div>
    </div>

    <div class="container">
        <div class="row">

            <?php 
                $res = selectAll('services');
                $path = SERVICES_IMG_PATH;

                while($row = mysqli_fetch_assoc($res)){
                    echo <<< data
                        <div class="col-lg-4 col-md-6 mb-5 px-4">
                            <div class="bg-white rounded shadow p-4 pop">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="$path$row[icon]" width="40px">
                                    <h5 class="fs-2 m-0 ms-3">$row[name]</h5>
                                </div>
                                <p class="fs-4">$row[description]</p>
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