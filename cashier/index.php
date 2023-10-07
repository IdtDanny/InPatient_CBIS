<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../public/config/connection.php';

    # Getting Information of Signed in User
    $cashier_username = $_SESSION['sessionToken']->cashier_username;
    $caID = $_SESSION['sessionToken']->caID;
    $cashier_name = $_SESSION['sessionToken']->cashier_name;
    $cashier_pin = $_SESSION['sessionToken']->cashier_pin;

    # error and success alerts
    $photo_errorMessage = "";
    $photo_successMessage = "";
    $errorMessage = "";
    $successMessage = "";
    $key_errorMessage = "";
    $key_successMessage = "";
    $patient_errorMessage = "";
    $patient_deleteErrorMessage  = "";
    $patient_successMessage   = "";
    $patient_deleteSuccessMessage  = "";
    $update_successMessage  = "";
    $update_errorMessage   = "";

    # Calculating Each Number of Users, Cards, cashier, cashiers and so on...
    $sql_cashier = 'SELECT * FROM cashier';
    $sql_patient = 'SELECT * FROM patient';
    $sql_notify = 'SELECT * FROM `notification_all` WHERE `receiver_id` =:cashier_pin OR `sender_id`';
    $statement_notify = $pdo->prepare($sql_notify);
    $statement_notify -> execute([ 'cashier_pin' => $cashier_pin ]); 
    
    $statement = $pdo->prepare($sql_cashier);
    $statement->execute();

    $statement_patient = $pdo->prepare($sql_patient);
    $statement_patient -> execute();

    # Getting The number of cashiers, Cards, cashier...
    $cashiersCount = $statement->rowCount();
    $registered_patient = $statement_patient->rowCount();
    $registered_notify = $statement_notify->rowCount();

    # Fetching cashier info ...

    $cashier_FetchQuery = 'SELECT * FROM `cashier` ORDER BY `created_at` DESC';
    $cashier_FetchStatement = $pdo->prepare($cashier_FetchQuery);
    $cashier_FetchStatement->execute();
    $cashier_Result = $cashier_FetchStatement->fetchAll();

    # Getting cashier Info. for update form...

    $cashierFetchQuery = 'SELECT * FROM `cashier` WHERE `caID` = :cashierid';
    $cashierFetchStatement = $pdo->prepare($cashierFetchQuery);
    $cashierFetchStatement->execute([
        'cashierid' => $caID
    ]);
    $cashierResults = $cashierFetchStatement->fetch();

    # Getting user notifications

    $cashier_notifyFetchQuery = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :cashier_pin OR `sender_id` = :scashier_pin ORDER BY `date_sent` AND `time_sent` DESC';
    $cashier_notifyFetchStatement = $pdo->prepare($cashier_notifyFetchQuery);
    $cashier_notifyFetchStatement->execute([
        'cashier_pin'     => $cashier_pin,
        'scashier_pin'    => $cashier_pin
    ]);
    $cashier_notifyResults = $cashier_notifyFetchStatement->fetchAll();

    # Fetching Patient info ...

    $patient_FetchQuery = 'SELECT * FROM `patient` ORDER BY `created_at` DESC';
    $patient_FetchStatement = $pdo->prepare($patient_FetchQuery);
    $patient_FetchStatement->execute();
    $patient_Result = $patient_FetchStatement->fetchAll();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to continue </span><a href='index.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to see the change </span><a href='index.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";
 

    # Updating cashier Information...

    if (isset($_POST['editinfo'])) {
        $new_cashier_Name = $_POST['cashier-name'];
        $new_cashier_Mail = $_POST['cashier-mail'];
        $new_cashier_Username = $_POST['cashier-username'];
        $cashier_Old_Password = $_POST['old-password'];
        $cashier_New_Password = $_POST['new-password'];
        $cashier_Confirm_password = $_POST['confirm-password'];

        # Checking for Password fields(if they are empty, It will only update the username or name only)...

        if (empty($cashier_Old_Password)) {

            # Updating Query...

            $cashier_Update_Query = 'UPDATE `cashier`
                                    SET `cashier_name` = :cashiername,
                                        `cashier_username` = :cashierusername
                                        `cashier_mail` = :cashier_mail,
                                    WHERE `caID` = :cashierid
            ';

            $cashier_Update_stmt = $pdo->prepare($cashier_Update_Query);
            $cashier_Update_stmt->execute([
                'cashiername'     =>  $new_cashier_Name,
                'cashierusername' =>  $new_cashier_Username,
                'cashier_mail'    =>  $new_cashier_Mail,
                'cashierid'       =>  $caID
            ]);
            $successMessage = " Username Edited Successfully";
        }
        else {

            # Checking if the old password match...

            $hashedpass = md5($cashier_Old_Password);
            
            // $hashedpass = $cashier_Old_Password;

            if ($cashierResults->cashier_password == $hashedpass || $cashierResults->cashier_password == $cashier_Old_Password ) {

                if ($cashier_New_Password == $cashier_Confirm_password) {

                    # Update Query Including Passwords...

                    $cashier_Update_Query = 'UPDATE `cashier`
                                            SET `cashier_name` = :cashiername,
                                                `cashier_username` = :cashierusername,
                                                `cashier_mail` = :cashier_mail,
                                                `cashier_password` = :cashierpassword
                                            WHERE `caID` = :cashierid
                    ';

                    $cashier_Update_stmt = $pdo->prepare($cashier_Update_Query);
                    $cashier_Update_stmt->execute([
                        'cashiername'     =>  $new_cashier_Name,
                        'cashierusername' =>  $new_cashier_Username,
                        'cashier_mail'    =>  $new_cashier_Mail,
                        'cashierpassword' =>  md5($cashier_New_Password),
                        'cashierid'       =>  $caID
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
        $target_file = $target_dir . basename($_FILES["cashier-profile"]["name"]);
        $photo = $_FILES['cashier-profile']['name'];
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        # Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["cashier-profile"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        }
        else {
            $photo_errorMessage = " File is not an image.";
            $uploadOk = 0;
        }
        
        # Check file size
        if ($_FILES["cashier-profile"]["size"] > 4000000) {
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
            if (move_uploaded_file($_FILES["cashier-profile"]["tmp_name"], $target_file)) {        
                
                # Updating cashier profile...
                $profile_update = 'UPDATE `cashier` 
                                    SET `photo` = :photo 
                                    WHERE `caID` = :cashierid
                                ';
        
                $cashier_updateStatement = $pdo->prepare($profile_update);
                $cashier_updateStatement->execute([
                                    'photo'     =>  $photo,
                                    'cashierid'   =>  $cashierResults->caID
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

        # check if cashier pin are same ... 

        if ($cashierResults->cashier_pin == $cpin) {

            # checking if cashier has enough balance to withdraw ...

            $cashier_balance = $cashierResults->cashier_balance;

            if ($cashier_balance <= 0 || $cashier_balance < $amount) {
                $key_errorMessage = " Not enough balance, ". $errorRefreshMessage;
            }

            # otherwise proceed with operation ...

            else {

                # create a request ...

                $request_date = date('Y-m-d');
                $request_time = date('h:i:s');
                $request_type = 'withdraw';
                $user_id = $cpin;
                $amount;
                $activation_key = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$&?", 5)),0 , 10);
                $status = 'waiting';

                # check if he exist in request table ...

                $requestFetchQuery = 'SELECT * FROM `request` WHERE `user_id` = :requestid';
                $requestFetchStatement = $pdo->prepare($requestFetchQuery);
                $requestFetchStatement->execute([
                    'requestid' => $cpin
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

                # create a request row for cashier if he/she does not exist ...

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
    include 'include/index_new.html';
?>