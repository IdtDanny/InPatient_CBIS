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
    $bill_errorMessage = "";
    $bill_updateMessage = "";
    $user_detail = "";
    $busy_successMessage = "";
    $busy_errorMessage = "";
    $patientResults = "";

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

    # apply for patient ...

    if (isset($_POST['patientApply'])) {

        $patient_id        = $_POST['patient_id'];
        $patient_name      = $_POST['patient_fname'];
        $patient_mname     = $_POST['patient_mname'];
        $patient_lname     = $_POST['patient_lname'];
        $patient_mail      = $_POST['patient_mail'];
        $patient_profile   = 'optional';
        $patient_tel       = $_POST['patient_tel'];
        $patient_gender    = $_POST['patient_gender'];
        $patient_district  = $_POST['patient_district'];
        $patient_sector    = $_POST['patient_sector'];
        $patient_cell      = $_POST['patient_cell'];
        $patient_village   = $_POST['patient_village'];
        $date_Sent         = date('Y-m-d h:i:s');
        $patient_pin       = rand(1000,9999);
        $referral          = $cashier_username;
        // $password= $patient_uname.'-'.$patient_pin;
        // $hashed_Password = md5($password);

        # checking if patient exists
        $patient_existFetchQuery = 'SELECT * FROM `patient` WHERE `patient_name` = :patient_name';
        $patient_existFetchStatement = $pdo->prepare($patient_existFetchQuery);
        $patient_existFetchStatement->execute([
            'patient_name' => $patient_name
        ]);
        $patient_existResults = $patient_existFetchStatement->fetch();

        # if exist, pop some message
        if ($patient_existResults) {
            $patient_errorMessage = " Already registered" . $errorRefreshMessage;
        }

        # otherwise proceed with registration process

        else {
                    
            # Inserting patient ...

            $sql_insert_patient = " INSERT INTO `patient`(`created_at`, `patient_id`, `patient_profile`, `patient_name`, `patient_mname`, `patient_lname`, `patient_gender`, `patient_tel`, `patient_mail`, `patient_balance`, `referral_cashier`, `status`, `approve`, `patient_pin`) 
                                                    VALUES(:created_at, :patient_id, :patient_profile, :patient_name, :patient_mname, :patient_lname, :patient_gender, :patient_tel, :patient_mail, :patient_balance, :referral_cashier, :bstatus, :approve, :patient_pin)";

            $patient_InsertStatement = $pdo->prepare($sql_insert_patient);
            $patient_InsertStatement->execute([
                'created_at'        =>  $date_Sent,
                'patient_id'        =>  $patient_id,
                'patient_profile'   =>  $patient_profile,
                'patient_name'      =>  $patient_name,
                'patient_mname'     =>  $patient_mname,
                'patient_lname'     =>  $patient_lname,
                'patient_gender'    =>  $patient_gender,
                'patient_tel'       =>  $patient_tel,  
                'patient_mail'      =>  $patient_mail,
                'patient_balance'   =>  '0',
                'referral_cashier'  =>  $referral,
                'bstatus'           =>  'active',
                'approve'           =>  'Admitted',
                'patient_pin'       =>  $patient_pin
            ]);

            if ($sql_insert_patient) {

                # Getting Admin Info. for update form...

                $patient_locationFetchQuery = 'SELECT * FROM `patient` WHERE `patient_pin` = :apin';
                $patient_locationFetchStatement = $pdo->prepare($patient_locationFetchQuery);
                $patient_locationFetchStatement->execute([
                    'apin' => $patient_pin
                ]);
                $patient_locationResults = $patient_locationFetchStatement->fetch();
                $pID = $patient_locationResults->pID;

                $sql_insert_location = "  INSERT INTO `patient_location`(`pID`, `patient_name`, `district`, `sector`, `cell`, `village`) VALUES(:pID, :patient_name, :district, :sector, :cell, :village) ";
                $location_InsertStatement = $pdo->prepare($sql_insert_location);
                $location_InsertStatement->execute([
                        'pID'           =>  $pID,
                        'patient_name'  =>  $patient_name,
                        'district'      =>  $patient_district,
                        'sector'        =>  $patient_sector,
                        'cell'          =>  $patient_cell,
                        'village'       =>  $patient_village
                ]);
                if ($sql_insert_patient && $sql_insert_location) {
                        $patient_successMessage = " Registered! ". $successRefreshMessage;
                }
            }
            else {
                $patient_errorMessage = " Could not register" . $errorRefreshMessage;
            }
        }
    }

    # getting patient delete response

    if (isset($_GET['dpID'])) {
        $dpID = $_GET['dpID'];
        $sql_adelete = 'DELETE FROM `patient` WHERE pID = :pID';
        $sql_lodelete = 'DELETE FROM `patient_location` WHERE pID = :pID';

        # PDO Prep & Exec..
        $delete_patient = $pdo->prepare($sql_adelete);
        $delete_patient->execute([
            'pID'  =>  $dpID
        ]);

        $delete_patient_location = $pdo->prepare($sql_lodelete);
        $delete_patient_location->execute([
            'pID'  =>  $dpID
        ]);

        if ($sql_adelete && $sql_lodelete) {
            $patient_deleteSuccessMessage = " Deleted Successful" . $successRefreshMessage;
        }
        else {
            $patient_deleteErrorMessage = " Could not delete, check patient id" . $errorRefreshMessage;
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

    # Checking patient existance ...

    if (isset($_POST['patient_id'])){
        $patient_id = $_POST['patient_id'];

        # checking if patient exists ...

        $patient_existFetchQuery = 'SELECT * FROM `patient` WHERE `patient_id` = :patient_id';
        $patient_existFetchStatement = $pdo->prepare($patient_existFetchQuery);
        $patient_existFetchStatement->execute([
            'patient_id' => $patient_id
        ]);
        $patientResults = $patient_existFetchStatement->fetch();

        # if user is found then fetch the balance ...

        if ($patientResults) {

            $pID = $patientResults->pID;

            # retrieving patient location if patient exists ...

            $patient_locationFetchQuery = 'SELECT * FROM `patient_location` WHERE `pID` = :pID';
            $patient_locationFetchStatement = $pdo->prepare($patient_locationFetchQuery);
            $patient_locationFetchStatement->execute([
                'pID' => $pID
            ]);
            $patientLocation = $patient_locationFetchStatement->fetch();

            if ($patientLocation) {
                $busy_successMessage = ' Found'. $successRefreshMessage;
                // $user_details = $patient_existResults->patient_balance;
            }
        }

        # otherwise user not found

        else {
            $busy_errorMessage = " Unknown ID" . $errorRefreshMessage;
        }
    }

    # confirm paid bill ...

    if (isset($_POST['confirm_patient_id'])) {
        
        $patient_id = $_POST['confirm_patient_id'];

        # Getting refer_id to approve for consult_form ...

        $consult_FetchQuery = 'SELECT * FROM `consult_form` WHERE `patient_id` = :apin AND `status`= :bstatus';
        $consult_FetchStatement = $pdo->prepare($consult_FetchQuery);
        $consult_FetchStatement->execute([
            'apin'    => $patient_id,
            'bstatus' => 'unpaid'
        ]);
        $consult_Results = $consult_FetchStatement->fetch();

        # if there is unpaid form from consult_form with sent patient_id ...

        if ($consult_Results) {

            $refer_id = $consult_Results->coID;

            # Update consult form ...
    
            $consult_UpdateQuery = 'UPDATE `consult_form` SET `status`= :bstatus WHERE `coID`= :refer_id AND `patient_id` = :patient_id AND `status` = :fstatus';
            $consult_UpdateStatement = $pdo->prepare($consult_UpdateQuery);
            $consult_UpdateStatement->execute([
                'bstatus'     =>  'paid',
                'refer_id'    =>  $refer_id,
                'patient_id'  =>  $patient_id,
                'fstatus'     =>  'unpaid'
            ]);
    
            # Update records - consultation row with refer_id from consult_form ...
    
            $recordConsult_UpdateQuery = 'UPDATE `records` SET `status`= :bstatus WHERE `refer_id`= :refer_id AND `patient_id` = :patient_id AND `action`= :baction AND `status` = :fstatus';
            $recordConsult_UpdateStatement = $pdo->prepare($recordConsult_UpdateQuery);
            $recordConsult_UpdateStatement->execute([
                'bstatus'     =>  'approved',
                'refer_id'    =>  $refer_id,
                'patient_id'  =>  $patient_id,
                'baction'     =>  'consultation',
                'fstatus'     =>  'pending'
            ]);

            # Inform about payment confirmation ...

            if ($consult_UpdateQuery && $recordConsult_UpdateQuery) {
                $bill_updateMessage = '<br>Consultation: Paid';
            }

            # otherwise ...

            else {
                $bill_errorMessage = 'Could not confirm payment';
            }
        }

        # Otherwise there is not consultation to pay ...

        else {
            $bill_updateMessage = '<br>Consultation: None';
        }

        # ------------------------ Diagnosis ------------------------- #

        # Getting refer_id to approve for consult_form ...

        $diagnose_FetchQuery = 'SELECT * FROM `diagnose_form` WHERE `patient_id` = :apin AND `status`= :bstatus';
        $diagnose_FetchStatement = $pdo->prepare($diagnose_FetchQuery);
        $diagnose_FetchStatement->execute([
            'apin'    => $patient_id,
            'bstatus' => 'unpaid'
        ]);
        $diagnose_Results = $diagnose_FetchStatement->fetch();

        # if there is unpaid form from diagnose_form with sent patient_id ...

        if ($diagnose_Results) {

            $diagnose_id = $diagnose_Results->dgID;

            # Update diagnosis form ...
    
            $diagnose_UpdateQuery = 'UPDATE `diagnose_form` SET `status`=:bstatus WHERE `dgID`= :diagnose_id AND `patient_id` = :patient_id AND `status` = :fstatus';
            $diagnose_UpdateStatement = $pdo->prepare($diagnose_UpdateQuery);
            $diagnose_UpdateStatement->execute([
                'bstatus'     =>  'paid',
                'diagnose_id' =>  $diagnose_id,
                'patient_id'  =>  $patient_id,
                'fstatus'     =>  'unpaid'
            ]);
    
            # Update records - diagnosis row with refer_id from diagnose_form ...
    
            $recordDiagnose_UpdateQuery = 'UPDATE `records` SET `status`= :bstatus WHERE `refer_id`= :refer_id AND `patient_id` = :patient_id AND `action`= :baction AND `status` = :fstatus';
            $recordDiagnose_UpdateStatement = $pdo->prepare($recordDiagnose_UpdateQuery);
            $recordDiagnose_UpdateStatement->execute([
                'bstatus'     =>  'approved',
                'refer_id'    =>  $diagnose_id,
                'patient_id'  =>  $patient_id,
                'baction'     =>  'diagnosis',
                'fstatus'     =>  'pending'
            ]);

            # Inform about payment confirmation ...

            if ($diagnose_UpdateQuery && $recordDiagnose_UpdateQuery) {
                $bill_updateMessage = $bill_updateMessage . '<br>Diagnosis: Paid';
            }

            # otherwise ...

            else {
                $bill_errorMessage = $bill_errorMessage . '<br>Could not confirm payment';
            }
        }

        # otherwise nothing to pay for diagnosis ...

        else {
            $bill_updateMessage = $bill_updateMessage . '<br>Diagnosis: None';
        }

        # ------------------------ Surgery ------------------------- #

        # Getting refer_id to approve for consult_form ...

        $surgery_FetchQuery = 'SELECT * FROM `surgery_form` WHERE `patient_id` = :apin AND `status`= :bstatus';
        $surgery_FetchStatement = $pdo->prepare($surgery_FetchQuery);
        $surgery_FetchStatement->execute([
            'apin'    => $patient_id,
            'bstatus' => 'unpaid'
        ]);
        $surgery_Results = $surgery_FetchStatement->fetch();

        # if there is unpaid form from surgery_form with sent patient_id ...

        if ($surgery_Results) {

            $surgery_id = $surgery_Results->suID;

            # Update surgery form ...
    
            $surgery_UpdateQuery = 'UPDATE `surgery_form` SET `status`=:bstatus WHERE `suID`= :surgery_id AND `patient_id` = :patient_id AND `status` = :fstatus';
            $surgery_UpdateStatement = $pdo->prepare($surgery_UpdateQuery);
            $surgery_UpdateStatement->execute([
                'bstatus'     =>  'paid',
                'surgery_id'  =>  $surgery_id,
                'patient_id'  =>  $patient_id,
                'fstatus'     =>  'unpaid'
            ]);
    
            # Update records - surgery row with refer_id from surgery_form ...
    
            $recordSurgery_UpdateQuery = 'UPDATE `records` SET `status`= :bstatus WHERE `refer_id`= :refer_id AND `patient_id` = :patient_id AND `action`= :baction AND `status` = :fstatus';
            $recordSurgery_UpdateStatement = $pdo->prepare($recordSurgery_UpdateQuery);
            $recordSurgery_UpdateStatement->execute([
                'bstatus'     =>  'approved',
                'refer_id'    =>  $surgery_id,
                'patient_id'  =>  $patient_id,
                'baction'     =>  'surgery',
                'fstatus'     =>  'pending'
            ]);

            # Inform about payment confirmation ...

            if ($surgery_UpdateQuery && $recordSurgery_UpdateQuery) {
                $bill_updateMessage = $bill_updateMessage . '<br>Surgery: Paid!'. $successRefreshMessage;
            }

            # otherwise ...

            else {
                $bill_errorMessage = $bill_errorMessage . '<br>Could not confirm payment!'. $errorRefreshMessage;
            }
        }

        # otherwise nothing to pay for surgery ...

        else {
            $bill_updateMessage = $bill_updateMessage . '<br>Surgery: None!'. $successRefreshMessage;
        }
    }
?>

<?php 
    include 'include/index_new.html';
?>