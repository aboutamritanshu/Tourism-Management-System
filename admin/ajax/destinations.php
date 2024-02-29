<?php
require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['add_destination'])) {
    $features = filteration((json_decode($_POST['features'])));
    $services = filteration((json_decode($_POST['services'])));

    $frm_data = filteration($_POST);
    $flag = 0;

    $q1 = "INSERT INTO `destinations`(`name`, `duration`, `price`, `quantity`, `description`) VALUES (?,?,?,?,?)";
    $values = [$frm_data['name'], $frm_data['duration'], $frm_data['price'], $frm_data['quantity'], $frm_data['desc']];

    if (insert($q1, $values, 'siiis')) {
        $flag = 1;
    }


    $destination_id = mysqli_insert_id($con);

    $q2 = "INSERT INTO `destination_services`(`destination_id`, `services_id`) VALUES (?,?)";
    if ($stmt = mysqli_prepare($con, $q2)) {
        foreach ($services as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $destination_id, $f);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $flag = 0;
        die('query cannot be prepared - insert');
    }

    $q3 = "INSERT INTO `destination_features`(`destination_id`, `features_id`) VALUES (?,?)";
    if ($stmt = mysqli_prepare($con, $q3)) {
        foreach ($features as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $destination_id, $f);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $flag = 0;
        die('query cannot be prepared - insert');
    }

    if ($flag) {
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['get_all_destinations'])) {
    $res =  select("SELECT * FROM `destinations` WHERE `removed`=?", [0], 'i');
    $i = 1;

    $data = "";
    while ($row = mysqli_fetch_assoc($res)) {

        if ($row['status'] == 1) {
            $status = "<button onclick='toggle_status($row[id], 0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
        } else {
            $status = "<button onclick='toggle_status($row[id], 1)' class='btn btn-warning btn-sm shadow-none'>inactive</button>";
        }
        $data .= "
                <tr class='align-middle'>
                    <td>$i</td>
                    <td>$row[name]</td>
                    <td>$row[duration]</td>
                    <td>₹$row[price]</td>
                    <td>$row[quantity]</td>
                    <td>$status</td>
                    <td>
                        <button type='button' onclick='edit_details($row[id])' class='btn btn-primary shadow-non btn-sm' data-bs-toggle='modal' data-bs-target='#edit-destination'>
                            <i class='bi bi-pencil-square'></i>
                        </button>
                        <button type='button' onclick=\"destination_images($row[id],'$row[name]')\" class='btn btn-info shadow-non btn-sm' data-bs-toggle='modal' data-bs-target='#destination-images'>
                            <i class='bi bi-images'></i>
                        </button>
                        <button type='button' onclick='remove_destination($row[id])' class='btn btn-danger shadow-non btn-sm'>
                            <i class='bi bi-trash'></i>
                        </button>
                    </td>
                </tr>
            ";
        $i++;
    }

    echo $data;
}

if (isset($_POST['get_destination'])) {
    $frm_data = filteration($_POST);

    $res1 = select("SELECT * FROM `destinations` WHERE `id`=?", [$frm_data['get_destination']], 'i');
    $res2 = select("SELECT * FROM `destination_features` WHERE `destination_id`=?", [$frm_data['get_destination']], 'i');
    $res3 = select("SELECT * FROM `destination_services` WHERE `destination_id`=?", [$frm_data['get_destination']], 'i');

    $destinationdata = mysqli_fetch_assoc($res1);
    $features = [];
    $services = [];


    if (mysqli_num_rows($res2) > 0) {
        while ($row = mysqli_fetch_assoc($res2)) {
            array_push($features, $row['features_id']);
        }
    }

    if (mysqli_num_rows($res3) > 0) {
        while ($row = mysqli_fetch_assoc($res3)) {
            array_push($services, $row['services_id']);
        }
    }

    $data = ["destinationdata" => $destinationdata, "features" => $features, "services" => $services];
    $data = json_encode($data);

    echo $data;
}

if (isset($_POST['edit_destination'])) {
    $features = filteration((json_decode($_POST['features'])));
    $services = filteration((json_decode($_POST['services'])));

    $frm_data = filteration($_POST);
    $flag = 0;

    $q1 = "UPDATE `destinations` SET `name`=?,`duration`=?,`price`=?,`quantity`=?,`description`=? WHERE `id`=?";
    $values = [$frm_data['name'], $frm_data['duration'], $frm_data['price'], $frm_data['quantity'], $frm_data['desc'], $frm_data['destination_id']];

    if (update($q1, $values, 'siiisi')) {
        $flag = 1;
    }

    $del_features = delete("DELETE FROM `destination_features` WHERE `destination_id`=?", [$frm_data['destination_id']], 'i');
    $del_services = delete("DELETE FROM `destination_services` WHERE `destination_id`=?", [$frm_data['destination_id']], 'i');

    if ($del_features && $del_services) {
        $flag = 0;
    }

    $q2 = "INSERT INTO `destination_services`(`destination_id`, `services_id`) VALUES (?,?)";
    if ($stmt = mysqli_prepare($con, $q2)) {
        foreach ($services as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $frm_data['destination_id'], $f);
            mysqli_stmt_execute($stmt);
        }
        $flag = 1;
        mysqli_stmt_close($stmt);
    } else {
        $flag = 0;
        die('query cannot be prepared - insert');
    }

    $q3 = "INSERT INTO `destination_features`(`destination_id`, `features_id`) VALUES (?,?)";
    if ($stmt = mysqli_prepare($con, $q3)) {
        foreach ($features as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $frm_data['destination_id'], $f);
            mysqli_stmt_execute($stmt);
        }
        $flag = 1;
        mysqli_stmt_close($stmt);
    } else {
        $flag = 0;
        die('query cannot be prepared - insert');
    }

    if ($flag) {
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['toggle_status'])) {
    $frm_data = filteration($_POST);

    $q = "UPDATE `destinations` SET `status`=? WHERE `id`=?";
    $v = [$frm_data['value'], $frm_data['toggle_status']];

    if (update($q, $v, 'ii')) {
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['add_image'])) {
    $frm_data = filteration(($_POST));

    $img_r = uploadImage($_FILES['image'], DESTINATIONS_FOLDER);

    if ($img_r == 'inv_img') {
        echo $img_r;
    } else if ($img_r == 'inv_size') {
        echo $img_r;
    } else if ($img_r == 'upd_failed') {
        echo $img_r;
    } else {
        $q = "INSERT INTO `destination_images`(`destination_id`, `image`) VALUES (?,?)";
        $values = [$frm_data['destination_id'], $img_r];
        $res = insert($q, $values, 'is');
        echo $res;
    }
}

if (isset($_POST['get_destination_images'])) {
    $frm_data = filteration(($_POST));
    $res = select("SELECT * FROM `destination_images` WHERE `destination_id`=?", [$frm_data['get_destination_images']], 'i');

    $path = DESTINATIONS_IMG_PATH;

    while ($row = mysqli_fetch_assoc($res)) {
        if ($row['thumb'] == 1) {
            $thumb_btn = "<i class='bi bi-check-lg text-light bg-success px-2 py-1 rounded fs-5'></i>";
        } else {
            $thumb_btn = "<button onclick='thumb_image($row[sr_no], $row[destination_id])' class='btn btn-secondary shadow-none'>
                <i class='bi bi-check-lg'></i>
            </button>";
        }
        echo <<< data
                <tr class='align-middle'>
                    <td><img src='$path$row[image]' class='img-fluid'></td>
                    <td>$thumb_btn</td>
                    <td>
                        <button onclick='rem_image($row[sr_no], $row[destination_id])' class='btn btn-danger shadow-none'>
                            <i class='bi bi-trash'></i>
                        </button>
                    </td>
                </tr>
            data;
    }
}

if (isset($_POST['rem_image'])) {
    $frm_data = filteration($_POST);
    $values = [$frm_data['image_id'], $frm_data['destination_id']];

    $pre_q = "SELECT * FROM `destination_images` WHERE `sr_no`=? AND `destination_id`=?";
    $res = select($pre_q, $values, 'ii');
    $img = mysqli_fetch_assoc($res);

    if (deleteImage($img['image'], DESTINATIONS_FOLDER)) {
        $q = "DELETE FROM `destination_images` WHERE `sr_no`=? AND `destination_id`=?";
        $res = delete($q, $values, 'ii');
        echo $res;
    } else {
        echo 0;
    }
}

if (isset($_POST['thumb_image'])) {

    $frm_data = filteration($_POST);

    $pre_q = "UPDATE `destination_images` SET `thumb`=? WHERE `destination_id`=?";
    $pre_v = [0, $frm_data['destination_id']];
    $pre_res = update($pre_q, $pre_v, 'ii');
    
    $q = "UPDATE `destination_images` SET `thumb`=? WHERE `sr_no`=? AND `destination_id`=?";
    $v = [1, $frm_data['image_id'], $frm_data['destination_id']];
    $res = update($q, $v, 'iii');

    echo $res;
}

if (isset($_POST['remove_destination'])) {

    $frm_data = filteration($_POST);

    $res1 = select("SELECT * FROM `destination_images` WHERE `destination_id`=?", [$frm_data['destination_id']], 'i');

    while($row = mysqli_fetch_assoc($res1))
    {
        deleteImage($row['image'], DESTINATIONS_FOLDER);
    }

    $res2 = delete("DELETE FROM `destination_images` WHERE `destination_id`=?", [$frm_data['destination_id']], 'i');
    $res3 = delete("DELETE FROM `destination_features` WHERE `destination_id`=?", [$frm_data['destination_id']], 'i');
    $res4 = delete("DELETE FROM `destination_services` WHERE `destination_id`=?", [$frm_data['destination_id']], 'i');
    $res5 = update("UPDATE `destinations` SET `removed`=? WHERE `id`=?", [1, $frm_data['destination_id']], 'ii');

    if($res2 || $res3 || $res4 || $res5) {
        echo 1;
    } else {
        echo 0;
    }
}

?>