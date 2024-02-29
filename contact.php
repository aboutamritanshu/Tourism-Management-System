<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php'); ?>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style_contact.css">
    <title><?php echo $settings_r['site_title'] ?> - CONTACT</title>


    <style>
        .hr {
            margin: auto;
            width: 80%;
            margin-top: 1rem;
            margin-bottom: 1rem;
            border: 0;
            border-top: 1px solid white;
        }
    </style>

</head>

<body>

    <?php require('inc/header.php'); ?>

    <div class="contact-home">
        <div class="contact-us">
            <h2 class="fw-bold h-font text-center">CONTACT US</h2>
            <div class="hr"></div>
            <p class="text-center mt-3" style="text-transform: none;">
            If you have any questions or queries, a member of staff will always be happy to help. <br>Feel free to contact us and we will be sure to get back to you as soon as possible.
            </p>
        </div>
        <div class="home">
            <div class="wrapper">
                <form class="frm" method="POST" style="width: 38rem; margin-right: 3rem;">
                    <div class="modal-header" style="justify-content: center;">
                        <h5 class="mt-2">
                            Send a Message
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 mt-2">
                            <label class="form-label">Name</label>
                            <input name="name" required type="text" class="form-control shadow-none">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input name="email" required type="email" class="form-control shadow-none">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input name="subject" required type="text" class="form-control shadow-none">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" style="border-radius: 2.5rem;" required class="form-control shadow-none" rows="2" style="resize: none;"></textarea>
                        </div>
                        <div class="text-center mt-4">
                            <button name="send" type="submit" class="btn btn-outline-* shadow-none">SEND</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['send'])) {
        $frm_data = filteration($_POST);
        $q = "INSERT INTO `user_queries`(`name`, `email`, `subject`, `message`) VALUES (?,?,?,?)";
        $values = [$frm_data['name'], $frm_data['email'], $frm_data['subject'], $frm_data['message']];
        $res = insert($q, $values, 'ssss');
        if($res = 1){
            alert('success', 'Mail sent!');
        } else {
            alert('error', 'Server down! Try again later.');
        }
    }
    ?>
    <?php require('inc/footer.php'); ?>

</body>

</html>