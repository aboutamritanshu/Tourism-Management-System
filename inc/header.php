<?php
// require('admin/inc/db_config.php');
// require('admin/inc/essentials.php');


// $contact_q = "SELECT * FROM `contact_details` WHERE `sr_no`=?"; 
// $settings_q = "SELECT * FROM `settings` WHERE `sr_no`=?"; 
// $values = [1];
// $contact_r = mysqli_fetch_assoc(select($contact_q, $values, 'i'));
// $settings_r = mysqli_fetch_assoc(select($settings_q, $values, 'i'));
?>

<!-- header section starts  -->
<?php
if ($settings_r['shutdown']) {
    echo <<< alertbar
                <div class='bg-danger text-center p-2 fw-bold' style='margin-top: 8rem; position: fixed; width: 100%; font-size: 1.5rem; z-index: 111;'>
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Bookings are temporarily closed!
                </div>
            alertbar;
}
?>
<header class="header">

    <a href="index.php" class="logo"> <i class="fas fa-paper-plane"></i><?php echo $settings_r['site_title'] ?> </a>

    <nav class="navbar">
        <a class="nav-link" href="index.php">home</a>
        <a class="nav-link" href="about.php">about</a>
        <a class="nav-link" href="destinations.php">destination</a>
        <a class="nav-link" href="contact.php">contact us</a>
    </nav>
    <div style="color: white;">
        <?php
        if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
            $path = USERS_IMG_PATH;
            echo <<< data
                    <div class="btn-group">
                        <button type="button" class="btn dropdown-toggle shadow-none" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                            <img src="$path$_SESSION[uPic]" style="width: 25px; height: 25px; object-fit: cover;" class="rounded-circle me-1">
                            $_SESSION[uName]
                        </button>
                        <ul class="dropdown-menu dropdown-menu-lg-end">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="bookings.php">Bookings</a></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                data;
        } else {
            echo <<< data
                <button type="button" class="btn btn-outline-* shadow-none me-lg-3 me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                    Login
                </button>
                <button type="button" class="btn btn-outline-* shadow-none" data-bs-toggle="modal" data-bs-target="#registerModal">
                    Register
                </button>
            data;
        }
        ?>
    </div>
</header>


<!-- LOGIN MODAL -->
<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="login-form" class="frm">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-person-circle"></i>
                        User Login
                    </h5>
                    <button type="reset" class="bi bi-x" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 mt-4">
                        <label class="form-label">Email / Mobile</label>
                        <input type="text" name="email_mob" required class="form-control shadow-none">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="pass" required class="form-control shadow-none">
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3 mt-5">
                        <button type="submit" class="btn btn-outline-* shadow-none">LOGIN</button>
                        <a href="javascript: void(0)" class="btn text-decoration-none">Forgot Password</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- REGISTER MODAL -->
<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="register-form" class="frm">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-person-lines-fill"></i>
                        User Registration
                    </h5>
                    <button type="reset" class="bi bi-x" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <span class="badge rounded-pill bg-light text-dark mb-3 mt-2 text-wrap lh-base me-3">Note: Your
                            details
                            must match your ID (Aadhaar card, passport, driving license, etc)
                        </span>
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <input name="name" type="text" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email address</label>
                                <input name="email" type="email" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input name="phonenum" type="number" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Picture</label>
                                <input name="profile" type="file" accept=".jpg, .jpeg, .png, .webp" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control shadow-none" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pincode</label>
                                <input name="pincode" type="number" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of birth</label>
                                <input name="dob" type="date" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input name="pass" type="password" class="form-control shadow-none" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input name="cpass" type="password" class="form-control shadow-none" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-center my-1">
                        <button type="submit" class="btn btn-outline-* shadow-none">REGISTER</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- header section ends  -->