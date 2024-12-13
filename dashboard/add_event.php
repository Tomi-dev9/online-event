<?php
    if (isset($_POST['add_event'])) {
        $event_name = $_POST['event_name'];
        $event_date = $_POST['event_date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $quota = $_POST['quota'];
        $image_name = null;

        if (!empty($_FILES['event_image']['name'])) {
            $image_name = time() . '_' . $_FILES['event_image']['name'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($image_name);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            $check = getimagesize($_FILES['event_image']['tmp_name']);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }
            if ($uploadOk == 0) {
                echo "Sorry, your file is not an image.";
            } else {
                if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target_file)) {
                    $stmt = $conn->prepare("INSERT INTO event (event_name, event_date, start_time, end_time, quota, image) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $event_name, $event_date, $start_time, $end_time, $quota, $image_name);
                    $stmt->execute();
                    header("Location:event.php");
                    exit();
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
            } 
            
    }
    
?>