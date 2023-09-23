<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../public/config/connection.php';

    # Getting Information of Signed in User
    $pharmacy_username = $_SESSION['sessionToken']->pharmacy_username;
    $bID = $_SESSION['sessionToken']->bID;
    $pharmacy_name = $_SESSION['sessionToken']->pharmacy_name;
    $pharmacy_tin = $_SESSION['sessionToken']->pharmacy_tin;

    # notification variables ...
    $key_errorMessage = "";
    $key_successMessage = "";
    $successMessage = "";    
    $errorMessage = "";
    $photo_errorMessage = "";    
    $photo_successMessage = "";

    # Calculating Each Number of Users, Cards, pharmacy, agents and so on...
    $sql_agent = 'SELECT * FROM agent';
    $sql_client = 'SELECT * FROM client';
    $sql_pharmacy = 'SELECT * FROM pharmacy';
    $sql_pharmacy_notify = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :pharmacy_tin OR `sender_id` = :npharmacy_tin';
    $sql_pharmacy_record = 'SELECT * FROM `records` WHERE `rID` = :pharmacy_tin';
    // $usedCardsSql = 'SELECT * FROM `client` WHERE `Approve` = :approve';

    $statement = $pdo->prepare($sql_agent);
    $statement->execute();

    $statement_client = $pdo->prepare($sql_client);
    $statement_client -> execute();

    $statement_pharmacy = $pdo->prepare($sql_pharmacy);
    $statement_pharmacy -> execute();

    $statement_pharmacy_notify = $pdo->prepare($sql_pharmacy_notify);
    $statement_pharmacy_notify -> execute([
        'pharmacy_tin'  => $pharmacy_tin,
        'npharmacy_tin' => $pharmacy_tin
    ]);

    $statement_pharmacy_record = $pdo->prepare($sql_pharmacy_record);
    $statement_pharmacy_record -> execute([
        'pharmacy_tin'  => $pharmacy_tin
    ]);

    # Getting The number of Agents, Cards, pharmacy...
    $agentsCount = $statement->rowCount();
    $registered_client = $statement_client->rowCount();
    $registered_pharmacy = $statement_pharmacy -> rowCount();
    $pharmacy_notifyCount = $statement_pharmacy_notify -> rowCount();
    $pharmacy_recordCount = $statement_pharmacy_record -> rowCount();

    # Fetching pharmacy info ...

    $pharmacy_FetchQuery = 'SELECT * FROM `pharmacy` ORDER BY `Date` DESC';
    $pharmacy_FetchStatement = $pdo->prepare($pharmacy_FetchQuery);
    $pharmacy_FetchStatement->execute();
    $pharmacy_Result = $pharmacy_FetchStatement->fetchAll();

    # Getting pharmacy Info. for update form...

    $pharmacyFetchQuery = 'SELECT * FROM `pharmacy` WHERE `bID` = :pharmacyid';
    $pharmacyFetchStatement = $pdo->prepare($pharmacyFetchQuery);
    $pharmacyFetchStatement->execute([
        'pharmacyid' => $bID
    ]);
    $pharmacyResults = $pharmacyFetchStatement->fetch();

    # Getting user notifications

    $pharmacy_notifyFetchQuery = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :pharmacy_tin OR `sender_id` = :spharmacy_tin ORDER BY `date_sent` AND `time_sent` DESC';
    $pharmacy_notifyFetchStatement = $pdo->prepare($pharmacy_notifyFetchQuery);
    $pharmacy_notifyFetchStatement->execute([
        'pharmacy_tin'     => $pharmacy_tin,
        'spharmacy_tin'    => $pharmacy_tin
    ]);
    $pharmacy_notifyResults = $pharmacy_notifyFetchStatement->fetchAll();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to continue </span><a href='index.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to see the change </span><a href='index.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";
 

    # Updating pharmacy Information...

    if (isset($_POST['editinfo'])) {
        $new_pharmacy_Name = $_POST['pharmacy-name'];
        $pharmacy_Old_Password = $_POST['old-password'];
        $pharmacy_New_Password = $_POST['new-password'];
        $pharmacy_Confirm_password = $_POST['confirm-password'];

        # Checking for Password fields(if they are empty, It will only update the username or name only)...

        if (empty($pharmacy_Old_Password)) {

            # Updating Query...

            $pharmacy_Update_Query = 'UPDATE `pharmacy`
                                    SET `pharmacy_name` = :pharmacyname
                                    WHERE `bID` = :pharmacyid
            ';

            $pharmacy_Update_stmt = $pdo->prepare($pharmacy_Update_Query);
            $pharmacy_Update_stmt->execute([
                'pharmacyname'     =>  $new_pharmacy_Name,
                'pharmacyid'       =>  $bID
            ]);
            $successMessage = " Username Edited Successfully";
        }
        else {

            # Checking if the old password match...

            $hashedpass = md5($pharmacy_Old_Password);
            
            // $hashedpass = $pharmacy_Old_Password;

            if ($pharmacyResults->pharmacy_password == $hashedpass || $pharmacyResults->pharmacy_password == $pharmacy_Old_Password ) {

                if ($pharmacy_New_Password == $pharmacy_Confirm_password) {

                    # Update Query Including Passwords...

                    $pharmacy_Update_Query = 'UPDATE `pharmacy`
                                            SET `pharmacy_name` = :pharmacyname,
                                                `pharmacy_password` = :pharmacypassword
                                            WHERE `bID` = :pharmacyid
                    ';

                    $pharmacy_Update_stmt = $pdo->prepare($pharmacy_Update_Query);
                    $pharmacy_Update_stmt->execute([
                        'pharmacyname'     =>  $new_pharmacy_Name,
                        'pharmacypassword' =>  md5($pharmacy_New_Password),
                        'pharmacyid'       =>  $bID
                    ]);
                    $successMessage = " Data Edited Successfully";
                }
                else{
                    $errorMessage = " New Password Does not Match";
                }
            }
            else{
                $errorMessage = " Current Password is Incorrect";
            }
        }
    }

    # Updating profile photo

    if(isset($_POST["submit-profile"])) {
        $target_dir = "../public/profile/";
        $target_file = $target_dir . basename($_FILES["pharmacy-profile"]["name"]);
        $photo = $_FILES['pharmacy-profile']['name'];
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        # Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["pharmacy-profile"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        }
        else {
            $photo_errorMessage = " File is not an image.";
            $uploadOk = 0;
        }
        
        # Check if file already exists
        // if (file_exists($target_file)) {
        //     $photo_errorMessage = " Sorry, file already exists.";
        //     $uploadOk = 0;
        // }
        
        # Check file size
        if ($_FILES["pharmacy-profile"]["size"] > 4000000) {
            $photo_errorMessage = " Sorry, your file is too large.";
            $uploadOk = 0;
        }
        
        # Allow certain file formats
        else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $photo_errorMessage = " Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        
        # Check if $uploadOk is set to 0 by an error
        else if ($uploadOk == 0) {
            $photo_errorMessage = " Sorry, your file was not uploaded.";
            # if everything is ok, try to upload file 
        } 
        else {
            if (move_uploaded_file($_FILES["pharmacy-profile"]["tmp_name"], $target_file)) {        
                
                # Updating pharmacy profile...
                $profile_update = 'UPDATE `pharmacy` 
                                    SET `photo` = :photo 
                                    WHERE `bID` = :pharmacyid
                                ';
        
                $pharmacy_updateStatement = $pdo->prepare($profile_update);
                $pharmacy_updateStatement->execute([
                                    'photo'     =>  $photo,
                                    'pharmacyid'   =>  $pharmacyResults->bID
                                ]);
            
                if ($profile_update) {
                    $photo_successMessage = $successRefreshMessage;
                }
            } 
            else {
                $photo_errorMessage = " Sorry, there was an error uploading your file.";
            }
        }
    }

    # generating activation key passcode

    if (isset($_POST['generateKey'])) {
        $cpin = $_POST['cpin'];
        $amount = $_POST['ramount'];

        # check if agent pin are same ... 

        if ($pharmacyResults->pharmacy_pin == $cpin) {

            # checking if agent has enough balance to withdraw ...

            $pharmacy_balance = $pharmacyResults->balance;
            $pharmacy_tin = $pharmacyResults->pharmacy_tin;

            if ($pharmacy_balance <= 0 || $pharmacy_balance < $amount) {
                $key_errorMessage = " Not enough balance, ". $errorRefreshMessage;
            }

            # otherwise proceed with operation ...

            else {

                # create a request ...

                $request_date = date('Y-m-d');
                $request_time = date('h:i:s');
                $request_type = 'withdraw';
                $user_id = $pharmacy_tin;
                $amount;
                $activation_key = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$&?", 5)),0 , 10);
                $status = 'waiting';

                # check if he exist in request table ...

                $requestFetchQuery = 'SELECT * FROM `request` WHERE `user_id` = :requestid';
                $requestFetchStatement = $pdo->prepare($requestFetchQuery);
                $requestFetchStatement->execute([
                    'requestid' => $user_id
                ]);
                $requestResults = $requestFetchStatement->fetch();

                $requestCount = $requestFetchStatement->rowCount();

                # update request row if he/she already exists ...

                if ($requestCount > 0) {

                    $sql_updateRequest = 'UPDATE `request` SET `request_date`=:request_date,`request_time`=:request_time,`confirmed_date`=:confirmed_date,`confirmed_time`=:confirmed_time,`request_type`=:request_type,`user_id`=:ruser_id,`amount`=:amount,`activation_key`=:activation_key,`status`=:rstatus WHERE `user_id` =:rauser_id';

                    # PDO Prep & Exec..
                    $update_requestStatement = $pdo->prepare($sql_updateRequest);
                    $update_requestStatement->execute([
                        'request_date'   => $request_date,
                        'request_time'   => $request_time,
                        'confirmed_date' => NULL,
                        'confirmed_time' => NULL,
                        'request_type'   => $request_type,
                        'ruser_id'       => $user_id,
                        'amount'         => $amount,
                        'activation_key' => $activation_key,
                        'rstatus'        => $status,
                        'rauser_id'      => $user_id
                    ]);

                    if ($sql_updateRequest) {
                        $key_successMessage = "  Key: ". $activation_key . $errorRefreshMessage;
                    }
                }

                # create a request row for agent if he/she does not exist ...

                else {

                    $sql_insertRequest = 'INSERT INTO `request`(`request_date`, `request_time`, `request_type`, `user_id`, `amount`, `activation_key`, `status`) VALUES (:request_date, :request_time, :request_type, :ruser_id, :amount, :activation_key, :rstatus)';

                    # PDO Prep & Exec..
                    $insert_requestStatement = $pdo->prepare($sql_insertRequest);
                    $insert_requestStatement->execute([
                        'request_date'   => $request_date,
                        'request_time'   => $request_time,
                        'request_type'   => $request_type,
                        'ruser_id'       => $user_id,
                        'amount'         => $amount,
                        'activation_key' => $activation_key,
                        'rstatus'        => $status
                    ]);

                    if ($sql_insertRequest) {
                        $key_successMessage = "  Key: ". $activation_key . $errorRefreshMessage;
                    }
                }
            }
        }

        # otherwise cancel everything

        else {
            $key_errorMessage = " Wrong Pin, ". $errorRefreshMessage;
        }
    }
?>

<?php 
    include 'include/index_front.html';
?>