let add_destination_form = document.getElementById('add_destination_form');
        add_destination_form.addEventListener('submit', function(e) {
            e.preventDefault();
            add_destination();
        });

        function add_destination() {
            let data = new FormData();
            data.append('add_destination', '');
            data.append('name', add_destination_form.elements['name'].value);
            data.append('duration', add_destination_form.elements['duration'].value);
            data.append('price', add_destination_form.elements['price'].value);
            data.append('quantity', add_destination_form.elements['quantity'].value);
            data.append('desc', add_destination_form.elements['desc'].value);

            let features = [];

            add_destination_form.elements['features'].forEach(el => {
                if (el.checked) {
                    features.push(el.value);
                }
            });

            let services = [];

            add_destination_form.elements['services'].forEach(el => {
                if (el.checked) {
                    services.push(el.value);
                }
            });

            data.append('features', JSON.stringify(features));
            data.append('services', JSON.stringify(services));

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/destinations.php", true);

            xhr.onload = function() {
                var myModal = document.getElementById('add-destination');
                var modal = bootstrap.Modal.getInstance(myModal);
                modal.hide();

                if (this.responseText == 1) {
                    alert('success', 'New destination added!');
                    add_destination_form.reset();
                    get_all_destinations();
                } else {
                    alert('error', 'Server Down!');
                }
            }

            xhr.send(data);

        }

        function get_all_destinations() {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/destinations.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                document.getElementById('destination-data').innerHTML = this.responseText;
            }

            xhr.send('get_all_destinations');
        }

        let edit_destination_form = document.getElementById('edit_destination_form');

        function edit_details(id) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/destinations.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                let data = JSON.parse(this.responseText);

                edit_destination_form.elements['name'].value = data.destinationdata.name;
                edit_destination_form.elements['duration'].value = data.destinationdata.duration;
                edit_destination_form.elements['price'].value = data.destinationdata.price;
                edit_destination_form.elements['quantity'].value = data.destinationdata.quantity;
                edit_destination_form.elements['desc'].value = data.destinationdata.description;
                edit_destination_form.elements['destination_id'].value = data.destinationdata.id;

                edit_destination_form.elements['features'].forEach(el => {
                    if (data.features.includes(Number(el.value))) {
                        el.checked = true;
                    }
                });

                edit_destination_form.elements['services'].forEach(el => {
                    if (data.services.includes(Number(el.value))) {
                        el.checked = true;
                    }
                });

            }

            xhr.send('get_destination=' + id);
        }

        edit_destination_form.addEventListener('submit', function(e) {
            e.preventDefault();
            submit_edit_destination();
        });

        function submit_edit_destination() {
            let data = new FormData();
            data.append('edit_destination', '');
            data.append('destination_id', edit_destination_form.elements['destination_id'].value);
            data.append('name', edit_destination_form.elements['name'].value);
            data.append('duration', edit_destination_form.elements['duration'].value);
            data.append('price', edit_destination_form.elements['price'].value);
            data.append('quantity', edit_destination_form.elements['quantity'].value);
            data.append('desc', edit_destination_form.elements['desc'].value);

            let features = [];

            edit_destination_form.elements['features'].forEach(el => {
                if (el.checked) {
                    features.push(el.value);
                }
            });

            let services = [];

            edit_destination_form.elements['services'].forEach(el => {
                if (el.checked) {
                    services.push(el.value);
                }
            });

            data.append('features', JSON.stringify(features));
            data.append('services', JSON.stringify(services));

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/destinations.php", true);

            xhr.onload = function() {
                var myModal = document.getElementById('edit-destination');
                var modal = bootstrap.Modal.getInstance(myModal);
                modal.hide();

                if (this.responseText == 1) {
                    alert('success', 'Destination edited!');
                    edit_destination_form.reset();
                    get_all_destinations();
                } else {
                    alert('error', 'Server Down!');
                }
            }

            xhr.send(data);
        }

        function toggle_status(id, val) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/destinations.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (this.responseText == 1) {
                    alert('success', 'Status toggled!');
                    get_all_destinations();
                } else {
                    alert('success', 'Serve Down!');
                }
            }

            xhr.send('toggle_status=' + id + '&value=' + val);
        }

        let add_image_form = document.getElementById('add_image_form');
        add_image_form.addEventListener('submit', function(e) {
            e.preventDefault();
            add_image();
        });

        function add_image() {
            let data = new FormData();
            data.append('image', add_image_form.elements['image'].files[0]);
            data.append('destination_id', add_image_form.elements['destination_id'].value);
            data.append('add_image', '');

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/destinations.php", true);

            xhr.onload = function() {
                if (this.responseText == 'inv_img') {
                    alert(' error', 'Only JPG , WEBP or PNG images are allowed!', 'image-alert');
                } else if (this.responseText == 'inv_size') {
                    alert('error', 'Image should be less than 2MB!', 'image-alert');
                } else if (this.responseText == 'upd_failed') {
                    alert('error', 'Image upload failed. Server Down!', 'image-alert');
                } else {
                    alert('success', 'New image added!', 'image-alert');
                    destination_images(add_image_form.elements['destination_id'].value, document.querySelector("#destination-images .modal-title").innerHTML);
                    add_image_form.reset();
                }
            }
            xhr.send(data);
        }

        function destination_images(id, rname){
            document.querySelector("#destination-images .modal-title").innerHTML = rname;
            add_image_form.elements['destination_id'].value = id;
            add_image_form.elements['image'].value = '';

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/destinations.php", true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                document.getElementById('destination-image-data').innerHTML = this.responseText;
            }

            xhr.send('get_destination_images='+id);
        }

        function rem_image(img_id, destination_id){
            let data = new FormData();
            data.append('image_id', img_id);
            data.append('destination_id', destination_id);
            data.append('rem_image', '');

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/destinations.php", true);

            xhr.onload = function() {
                if (this.responseText == 1) {
                    alert('success', 'Image removed!', 'image-alert');
                    destination_images(destination_id, document.querySelector("#destination-images .modal-title").innerHTML);
                } else {
                    alert(' error', 'Image removal failed!', 'image-alert');
                    add_image_form.reset();
                }
            }
            xhr.send(data);

        }

        function thumb_image(img_id, destination_id){
            let data = new FormData();
            data.append('image_id', img_id);
            data.append('destination_id', destination_id);
            data.append('thumb_image', '');

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/destinations.php", true);

            xhr.onload = function() {
                if (this.responseText == 1) {
                    alert('success', 'Image Thumbnail Changed!', 'image-alert');
                    destination_images(destination_id, document.querySelector("#destination-images .modal-title").innerHTML);
                } else {
                    alert(' error', 'Thumbnail update failed!', 'image-alert');
                    add_image_form.reset();
                }
            }
            xhr.send(data);

        }

        function remove_destination(destination_id){
            if(confirm("Are you sure, you want to delete this destination?"))
            {
                let data = new FormData();
                data.append('destination_id', destination_id);
                data.append('remove_destination', '');

                let xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax/destinations.php", true);
    
                xhr.onload = function() {
                    if (this.responseText == 1) {
                        alert('success', 'Destination Removed!');
                        get_all_destinations();
                    } else {
                        alert('error', 'Destination removal failed!');
                    }
                }
                xhr.send(data);
            }
        }

        window.onload = function() {
            get_all_destinations();
        }