<!-- footer section starts  -->


<section class="footer">

    <div class="box-container">
        <div class="box">
            <a href="index.php" class="logo"><i class="fas fa-paper-plane"></i> <?php echo $settings_r['site_title'] ?> </a>
            <p><?php echo $settings_r['site_about'] ?></p>
            <div class="text-white fs-2" style="margin-left: 10px;">Social Media Links</div>
            <div class="share">
                <a href="<?php echo $contact_r['fb'] ?>" class="fab fa-facebook-f" target="_blank"></a>
                <a href="<?php echo $contact_r['insta'] ?>" class="fab fa-instagram" target="_blank"></a>
                <a href="<?php echo $contact_r['tw'] ?>" class="fab fa-twitter" target="_blank"></a>
                <a href="<?php echo $contact_r['linkd'] ?>" class="fab fa-linkedin" target="_blank"></a>
            </div>
        </div>

        <div class="box">
            <h3>quick links</h3>
            <a href="index.php" class="links"><i class="fas fa-arrow-right"></i> home </a>
            <a href="about.php" class="links"><i class="fas fa-arrow-right"></i> about </a>
            <a href="destinations.php" class="links"><i class="fas fa-arrow-right"></i> destination </a>
            <a href="contact.php" class="links"><i class="fas fa-arrow-right"></i> contact us </a>

        </div>

        <div class="box">
            <h3>contact info</h3>
            <p><i class="fas fa-map"></i> <?php echo $contact_r['address'] ?> </p>
            <p><i class="fas fa-phone"></i> +<?php echo $contact_r['pn1'] ?>
            </p>
            <p style="text-transform: none;"><i class="fas fa-envelope"></i> <?php echo $contact_r['email'] ?> </p>
        </div>

        <!-- <div class="box">
            <h3>newsletter</h3>
            <p>subscribe to latest updates</p>
            <form action="">
                <input type="email" placeholder="enter your email" class="email">
                <input type="submit" value="subscribe" class="btn">
            </form>
        </div> -->
    </div>

</section>

<!-- footer section ends  -->

<!-- credits  -->
<div class="credit">created by <span>Group 254</span> | all rights reserved!</div>

<!-- script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

<script>
    function alert(type, msg, position = 'body') {
        let bs_class = (type == 'success') ? 'alert-success' : 'alert-danger';
        let element = document.createElement('div');
        element.innerHTML = `
            <div class="alert ${bs_class} alert-dismissible fade show" role="alert">
                <strong class="me-3">${msg}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        if (position == 'body') {
            document.body.append(element);
            element.classList.add('custom-alert');
        } else {
            document.getElementById(position).appendChild(element);
        }

        setTimeout(remAlert, 3000);
    }

    function remAlert() {
        document.getElementsByClassName('alert')[0].remove();
    }

    function setActive() {
        let navbar = document.getElementById('dashboard-menu');
        let a_tags = navbar.getElementsByTagName('a');

        for (i = 0; i < a_tags.length; i++) {
            let file = a_tags[i].href.split('/').pop();
            let file_name = file.split('.')[0];
            if (document.location.href.indexOf(file_name) >= 0) {
                a_tags[i].classList.add('active');
            }
        }
    }

    let register_form = document.getElementById('register-form');

    register_form.addEventListener('submit', (e)=>{
        e.preventDefault();

        let data = new FormData();

        data.append('name', register_form.elements['name'].value);
        data.append('email', register_form.elements['email'].value);
        data.append('phonenum', register_form.elements['phonenum'].value);
        data.append('address', register_form.elements['address'].value);
        data.append('pincode', register_form.elements['pincode'].value);
        data.append('dob', register_form.elements['dob'].value);
        data.append('pass', register_form.elements['pass'].value);
        data.append('cpass', register_form.elements['cpass'].value);
        data.append('profile', register_form.elements['profile'].files[0]);
        data.append('register', '');

        var myModal = document.getElementById('registerModal');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/login_register.php", true);

        xhr.onload = function() {
            if (this.responseText == 'pass_mismatch') {
                alert('error', "Password Mismatch!");
            } else if(this.responseText == 'email_already'){
                alert('error', "Email is already registered!");
            } else if(this.responseText == 'phone_already'){
                alert('error', "Phone number is already registered!");
            } else if(this.responseText == 'inv_img'){
                alert('error', "Only JPG, WEBP & PNG images are allowed!");
            } else if(this.responseText == 'upd_failed'){
                alert('error', "Image upload failed!");
            } else if(this.responseText == 'mail_failed'){
                alert('error', "Cannot send confirmation email! Server down!");
            } else if(this.responseText == 'ins_failed'){
                alert('error', "Registration failed! Server down!");
            } else {
                alert('success', "Registration successful. ");
                register_form.reset();
            }
        }
        xhr.send(data);
    });

    let login_form = document.getElementById('login-form');

    login_form.addEventListener('submit', (e)=>{
        e.preventDefault();

        let data = new FormData();

        data.append('email_mob', login_form.elements['email_mob'].value);
        data.append('pass', login_form.elements['pass'].value);

        data.append('login', '');

        var myModal = document.getElementById('loginModal');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/login_register.php", true);

        xhr.onload = function() {
            if (this.responseText == 'inv_email_mob') {
                alert('error', "Invalid Email or Mobile Number!");
            } else if(this.responseText == 'not_verified'){
                alert('error', "Email not verified!");
            } else if(this.responseText == 'inactive'){
                alert('error', "Account Suspended! Please contact Admin!");
            } else if(this.responseText == 'invalid_pass'){
                alert('error', "Incorrect Password!");
            } else {
                let  fileurl = window.location.href.split('/').pop().split('?').shift();
                if(fileurl == 'destination_details.php'){
                    window.location = window.location.href;
                } else {
                    window.location = window.location.pathname;
                }
            }
        }
        xhr.send(data);


    });

    function checkLogininToBook(status, destination_id){
        if(status){
            window.location.href='confirm_booking.php?id='+destination_id;
        } else {
            alert('error', 'Please login to book your destination!');
        }
    }
</script>