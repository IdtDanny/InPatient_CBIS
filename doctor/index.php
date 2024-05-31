<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../public/config/connection.php';

    # Getting Information of Signed in User
    $authorized_username = $_SESSION['sessionToken']->authorized_username;
    $auID = $_SESSION['sessionToken']->auID;
    $authorized_name = $_SESSION['sessionToken']->authorized_name;
    $authorized_pin = $_SESSION['sessionToken']->authorized_pin;
    $_GET['drID'] = 0;

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

    # Fetching records done by authorized personnel info ...

    $records_FetchQuery = 'SELECT * FROM `records` WHERE `rID` = :rID ORDER BY `rdate` AND `rtime` DESC';
    $records_FetchStatement = $pdo->prepare($records_FetchQuery);
    $records_FetchStatement->execute([
        'rID' => $auID
    ]);
    $records_Result = $records_FetchStatement->fetchAll();

    # Fetching authorized info ...

    $authorized_FetchQuery = 'SELECT * FROM `authorized` ORDER BY `created_at` DESC';
    $authorized_FetchStatement = $pdo->prepare($authorized_FetchQuery);
    $authorized_FetchStatement->execute();
    $authorized_Result = $authorized_FetchStatement->fetchAll();

    # Getting authorized Info. for update form...

    $authorizedFetchQuery = 'SELECT * FROM `authorized` WHERE `auID` = :authorizedid';
    $authorizedFetchStatement = $pdo->prepare($authorizedFetchQuery);
    $authorizedFetchStatement->execute([
        'authorizedid' => $auID
    ]);
    $authorizedResults = $authorizedFetchStatement->fetch();

    # Getting user notifications

    $authorized_notifyFetchQuery = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :authorized_pin OR `sender_id` = :sauthorized_pin ORDER BY `date_sent` AND `time_sent` DESC';
    $authorized_notifyFetchStatement = $pdo->prepare($authorized_notifyFetchQuery);
    $authorized_notifyFetchStatement->execute([
        'authorized_pin'     => $authorized_pin,
        'sauthorized_pin'    => $authorized_pin
    ]);
    $authorized_notifyResults = $authorized_notifyFetchStatement->fetchAll();

    # Fetching Patient info ...

    $patient_FetchQuery = 'SELECT * FROM `patient` ORDER BY `created_at` DESC';
    $patient_FetchStatement = $pdo->prepare($patient_FetchQuery);
    $patient_FetchStatement->execute();
    $patient_Result = $patient_FetchStatement->fetchAll();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to continue </span><a href='index.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to see the change </span><a href='index.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";
 

    # Updating authorized Information...

    if (isset($_POST['editinfo'])) {
        $new_authorized_Name = $_POST['authorized-name'];
        $new_authorized_Mail = $_POST['authorized-mail'];
        $new_authorized_Username = $_POST['authorized-username'];
        $authorized_Old_Password = $_POST['old-password'];
        $authorized_New_Password = $_POST['new-password'];
        $authorized_Confirm_password = $_POST['confirm-password'];

        # Checking for Password fields(if they are empty, It will only update the username or name only)...

        if (empty($authorized_Old_Password)) {

            # Updating Query...

            $authorized_Update_Query = 'UPDATE `authorized`
                                    SET `authorized_name` = :authorizedname,
                                        `authorized_username` = :authorizedusername
                                        `authorized_mail` = :authorized_mail,
                                    WHERE `auID` = :authorizedid
            ';

            $authorized_Update_stmt = $pdo->prepare($authorized_Update_Query);
            $authorized_Update_stmt->execute([
                'authorizedname'     =>  $new_authorized_Name,
                'authorizedusername' =>  $new_authorized_Username,
                'authorized_mail'    =>  $new_authorized_Mail,
                'authorizedid'       =>  $auID
            ]);
            $successMessage = " Username Edited Successfully";
        }
        else {

            # Checking if the old password match...

            $hashedpass = md5($authorized_Old_Password);
            
            // $hashedpass = $authorized_Old_Password;

            if ($authorizedResults->authorized_password == $hashedpass || $authorizedResults->authorized_password == $authorized_Old_Password ) {

                if ($authorized_New_Password == $authorized_Confirm_password) {

                    # Update Query Including Passwords...

                    $authorized_Update_Query = 'UPDATE `authorized`
                                            SET `authorized_name` = :authorizedname,
                                                `authorized_username` = :authorizedusername,
                                                `authorized_mail` = :authorized_mail,
                                                `authorized_password` = :authorizedpassword
                                            WHERE `auID` = :authorizedid
                    ';

                    $authorized_Update_stmt = $pdo->prepare($authorized_Update_Query);
                    $authorized_Update_stmt->execute([
                        'authorizedname'     =>  $new_authorized_Name,
                        'authorizedusername' =>  $new_authorized_Username,
                        'authorized_mail'    =>  $new_authorized_Mail,
                        'authorizedpassword' =>  md5($authorized_New_Password),
                        'authorizedid'       =>  $auID
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
        $target_file = $target_dir . basename($_FILES["authorized-profile"]["name"]);
        $photo = $_FILES['authorized-profile']['name'];
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        # Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["authorized-profile"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        }
        else {
            $photo_errorMessage = " File is not an image.";
            $uploadOk = 0;
        }
        
        # Check file size
        if ($_FILES["authorized-profile"]["size"] > 4000000) {
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
            if (move_uploaded_file($_FILES["authorized-profile"]["tmp_name"], $target_file)) {        
                
                # Updating authorized profile...
                $profile_update = 'UPDATE `authorized` 
                                    SET `photo` = :photo 
                                    WHERE `auID` = :authorizedid
                                ';
        
                $authorized_updateStatement = $pdo->prepare($profile_update);
                $authorized_updateStatement->execute([
                                    'photo'     =>  $photo,
                                    'authorizedid'   =>  $authorizedResults->auID
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

    # Consult form ...

    if(isset($_POST["submit_consult"])) {
        $patient_id = $_POST['patient_id'];
        $consult_date = $_POST['consult_date'];
        $consult_time = $_POST['consult_time'];
        $consult_complains = $_POST['consult_complains'];
        $consult_findings = $_POST['consult_findings'];
        $consult_treatment = $_POST['consult_treatment'];
        $consult_medecine = $_POST['consult_medecine'];
        $consult_allergy = $_POST['consult_allergy'];
        $filled_by = $authorized_username;

        # checking patient existance ...

        $patient_existFetchQuery = 'SELECT * FROM `patient` WHERE `patient_id` =:patient_id';
        $patient_existFetchStatement = $pdo->prepare($patient_existFetchQuery);
        $patient_existFetchStatement->execute([
            'patient_id' => $patient_id
        ]);
        $patient_existResults = $patient_existFetchStatement->fetch();

        # if they exists, proceed with consultation ...

        if ($patient_existResults) {

            # checking if there is unpaid consultation existance ...

            $consult_existFetchQuery = 'SELECT * FROM `consult_form` WHERE `patient_id` =:patient_id AND `status`= :bstatus';
            $consult_existFetchStatement = $pdo->prepare($consult_existFetchQuery);
            $consult_existFetchStatement->execute([
                'patient_id' => $patient_id,
                'bstatus'    => 'unpaid'
            ]);
            $consult_existResults = $consult_existFetchStatement->fetch();

            # if exist ...

            if ($consult_existResults) {
                $errorMessage = " There is unpaid form! Pay First". $errorRefreshMessage;
            }

            # otherwise proceed with consult_form fill ...

            else {

                # if there is no prescribed treatments, medecine nor allergy ...

                if (empty($consult_treatment) || empty($consult_medecine) || empty($consult_allergy)) {

                    # Fill consult form ...

                    $sql_insert_consult = " INSERT INTO `consult_form`(`created_on`, `created_at`, `patient_id`, `complains`, `findings`, `treatment`, `medecine`, `allergy`, `filled_by`, `status`) 
                                                                    VALUES (:created_on, :created_at, :patient_id, :complains, :findings, :treatment, :medecine, :allergy, :filled_by, :bstatus)";

                    $consult_InsertStatement = $pdo->prepare($sql_insert_consult);
                    $consult_InsertStatement->execute([
                        'created_on'    =>  $consult_date,
                        'created_at'    =>  $consult_time,
                        'patient_id'    =>  $patient_id,
                        'complains'     =>  $consult_complains,
                        'findings'      =>  $consult_findings,
                        'treatment'     =>  'none',
                        'medecine'      =>  'none',
                        'allergy'       =>  'none',
                        'filled_by'     =>  $filled_by,
                        'bstatus'       =>  'unpaid'
                    ]);

                    # record the action ...

                    $rID = $auID;
                    $patient_name = $patient_existResults->patient_name;

                    # retrieving refer_id from consult_form ...

                    $referConsult_FetchQuery = 'SELECT * FROM `consult_form` WHERE `patient_id` =:patient_id AND `status`= :bstatus';
                    $referConsult_FetchStatement = $pdo->prepare($referConsult_FetchQuery);
                    $referConsult_FetchStatement->execute([
                        'patient_id' => $patient_id,
                        'bstatus'    => 'unpaid'
                    ]);
                    $referConsult_Results = $referConsult_FetchStatement->fetch();

                    $refer_id = $referConsult_Results->coID;

                    # retrieving fees information ...

                    $fees_existFetchQuery = 'SELECT * FROM `fees_list` WHERE `fees_name` =:fees_name';
                    $fees_existFetchStatement = $pdo->prepare($fees_existFetchQuery);
                    $fees_existFetchStatement->execute([
                        'fees_name' => 'consultation'
                    ]);
                    $fees_existResults = $fees_existFetchStatement->fetch();

                    $fees_name = $fees_existResults->fees_name;
                    $fees_amount = $fees_existResults->amount;

                    $sql_insertRecord = " INSERT INTO `records`(`rdate`, `rtime`, `rID`, `patient_id`, `patient_name`, `amount`, `action`, `status`, `refer_id`) 
                                                        VALUES (:rdate, :rtime, :rID, :patient_id, :patient_name, :amount, :baction, :bstatus, :refer_id)";

                    $record_InsertStatement = $pdo->prepare($sql_insertRecord);
                    $record_InsertStatement->execute([
                        'rdate'         =>  $consult_date,
                        'rtime'         =>  $consult_time,
                        'rID'           =>  $rID,
                        'patient_id'    =>  $patient_id,
                        'patient_name'  =>  $patient_name,
                        'amount'        =>  $fees_amount,
                        'baction'       =>  $fees_name,
                        'bstatus'       =>  'pending',
                        'refer_id'      =>  $refer_id
                    ]);

                    if ($sql_insert_consult && $sql_insertRecord) {
                                $successMessage = " Filled Successfully!". $successRefreshMessage;
                    }
                    else {
                        $errorMessage = " Could not fill" . $errorRefreshMessage;
                    }
                }

                # otherwise there are ...

                else {
                    # Fill consult form ...

                    $sql_insert_consult = " INSERT INTO `consult_form`(`created_on`, `created_at`, `patient_id`, `complains`, `findings`, `treatment`, `medecine`, `allergy`, `filled_by`, `status`) 
                    VALUES (:created_on, :created_at, :patient_id, :complains, :findings, :treatment, :medecine, :allergy, :filled_by, :bstatus)";

                    $consult_InsertStatement = $pdo->prepare($sql_insert_consult);
                    $consult_InsertStatement->execute([
                        'created_on'    =>  $consult_date,
                        'created_at'    =>  $consult_time,
                        'patient_id'    =>  $patient_id,
                        'complains'     =>  $consult_complains,
                        'findings'      =>  $consult_findings,
                        'treatment'     =>  $consult_treatment,
                        'medecine'      =>  $consult_medecine,
                        'allergy'       =>  $consult_allergy,
                        'filled_by'     =>  $filled_by,
                        'bstatus'       =>  'unpaid'
                    ]);

                    # record the action ...

                    $rID = $auID;
                    $patient_name = $patient_existResults->patient_name;

                    # retrieving refer_id from consult_form ...

                    $referConsult_FetchQuery = 'SELECT * FROM `consult_form` WHERE `patient_id` =:patient_id AND `status`= :bstatus';
                    $referConsult_FetchStatement = $pdo->prepare($referConsult_FetchQuery);
                    $referConsult_FetchStatement->execute([
                        'patient_id' => $patient_id,
                        'bstatus'    => 'unpaid'
                    ]);
                    $referConsult_Results = $referConsult_FetchStatement->fetch();

                    $refer_id = $referConsult_Results->coID;

                    # retrieving fees information ...

                    $fees_existFetchQuery = 'SELECT * FROM `fees_list` WHERE `fees_name` =:fees_name';
                    $fees_existFetchStatement = $pdo->prepare($fees_existFetchQuery);
                    $fees_existFetchStatement->execute([
                        'fees_name' => 'consultation'
                    ]);
                    $fees_existResults = $fees_existFetchStatement->fetch();

                    $fees_name = $fees_existResults->fees_name;
                    $fees_amount = $fees_existResults->amount;

                    $sql_insertRecord = " INSERT INTO `records`(`rdate`, `rtime`, `rID`, `patient_id`, `patient_name`, `amount`, `action`, `status`, `refer_id`) 
                                                        VALUES (:rdate, :rtime, :rID, :patient_id, :patient_name, :amount, :baction, :bstatus, :refer_id)";

                    $record_InsertStatement = $pdo->prepare($sql_insertRecord);
                    $record_InsertStatement->execute([
                        'rdate'         =>  $consult_date,
                        'rtime'         =>  $consult_time,
                        'rID'           =>  $rID,
                        'patient_id'    =>  $patient_id,
                        'patient_name'  =>  $patient_name,
                        'amount'        =>  $fees_amount,
                        'baction'       =>  $fees_name,
                        'bstatus'       =>  'pending',
                        'refer_id'      =>  $refer_id
                    ]);

                    if ($sql_insert_consult && $sql_insertRecord) {
                    $successMessage = " Filled Successfully!".$successRefreshMessage;
                    }
                    else {
                    $errorMessage = " Could not fill" . $errorRefreshMessage;
                    }
                }
            }
        }

        # otherwise pop up some alerts ...

        else {
            $errorMessage = " Invalid User!". $errorRefreshMessage;
        }
    }

    # Diagnose form ...

    if(isset($_POST["submit_diagnose"])) {
        $patient_id = $_POST['patient_id'];
        $diagnose_date = $_POST['diagnose_date'];
        $diagnose_time = $_POST['diagnose_time'];
        $diagnose_name = $_POST['diagnose_name'];
        $diagnose_description = $_POST['diagnose_description'];
        $diagnose_complication = $_POST['diagnose_complication'];
        $diagnose_allergy = $_POST['diagnose_allergy'];
        $filled_by = $authorized_username;

        # checking patient existance ...

        $patient_existFetchQuery = 'SELECT * FROM `patient` WHERE `patient_id` =:patient_id';
        $patient_existFetchStatement = $pdo->prepare($patient_existFetchQuery);
        $patient_existFetchStatement->execute([
            'patient_id' => $patient_id
        ]);
        $patient_existResults = $patient_existFetchStatement->fetch();

        # if they exists, proceed with consultation ...

        if ($patient_existResults) {

            # checking patient existance ...

            $diagnose_FetchQuery = 'SELECT * FROM `diagnose_form` WHERE `patient_id` =:patient_id AND `status`=:bstatus';
            $diagnose_FetchStatement = $pdo->prepare($diagnose_FetchQuery);
            $diagnose_FetchStatement->execute([
                'patient_id' => $patient_id,
                'bstatus'    => 'unpaid'
            ]);
            $diagnose_Results = $diagnose_FetchStatement->fetch();

            # checking if there is unpaid diagnose fee ...

            if ($diagnose_Results) {
                $errorMessage = " There is unpaid fee! Pay first". $errorRefreshMessage;
            }

            # otherwise proceed with diagnose_form ...

            else {

                # if there is neither complication nor allergy ...

                if (empty($diagnose_complication) || empty($diagnose_allergy)) {

                    # Fill diagnosis form ...

                    $sql_insert_diagnose = " INSERT INTO `diagnose_form`(`created_on`, `created_at`, `patient_id`, `diagnose_name`, `description`, `complications`, `allergy`, `filled_by`, `status`) 
                                                                VALUES (:created_on, :created_at, :patient_id, :diagnose_name, :ddescription, :complications, :allergy, :filled_by, :bstatus)";

                    $diagnose_InsertStatement = $pdo->prepare($sql_insert_diagnose);
                    $diagnose_InsertStatement->execute([
                        'created_on'    =>  $diagnose_date,
                        'created_at'    =>  $diagnose_time,
                        'patient_id'    =>  $patient_id,
                        'diagnose_name' =>  $diagnose_name,
                        'ddescription'  =>  $diagnose_description,
                        'complications' =>  'none',
                        'allergy'       =>  'none',
                        'filled_by'     =>  $filled_by,
                        'bstatus'       =>  'unpaid'
                    ]);

                    # record the action ...

                    $rID = $auID;
                    $patient_name = $patient_existResults->patient_name;

                    # retrieving refer_id from consult_form ...

                    $referConsult_FetchQuery = 'SELECT * FROM `diagnose_form` WHERE `patient_id` =:patient_id AND `status`= :bstatus';
                    $referConsult_FetchStatement = $pdo->prepare($referConsult_FetchQuery);
                    $referConsult_FetchStatement->execute([
                        'patient_id' => $patient_id,
                        'bstatus'    => 'unpaid'
                    ]);
                    $referConsult_Results = $referConsult_FetchStatement->fetch();

                    $refer_id = $referConsult_Results->dgID;

                    # retrieving fees information ...

                    $fees_existFetchQuery = 'SELECT * FROM `fees_list` WHERE `fees_name` =:fees_name';
                    $fees_existFetchStatement = $pdo->prepare($fees_existFetchQuery);
                    $fees_existFetchStatement->execute([
                        'fees_name' => 'diagnosis'
                    ]);
                    $fees_existResults = $fees_existFetchStatement->fetch();

                    $fees_name = $fees_existResults->fees_name;
                    $fees_amount = $fees_existResults->amount;

                    $sql_insertRecord = " INSERT INTO `records`(`rdate`, `rtime`, `rID`, `patient_id`, `patient_name`, `amount`, `action`, `status`, `refer_id`) 
                                                        VALUES (:rdate, :rtime, :rID, :patient_id, :patient_name, :amount, :baction, :bstatus, :refer_id)";

                    $record_InsertStatement = $pdo->prepare($sql_insertRecord);
                    $record_InsertStatement->execute([
                        'rdate'         =>  $diagnose_date,
                        'rtime'         =>  $diagnose_time,
                        'rID'           =>  $rID,
                        'patient_id'    =>  $patient_id,
                        'patient_name'  =>  $patient_name,
                        'amount'        =>  $fees_amount,
                        'baction'       =>  $fees_name,
                        'bstatus'       =>  'pending',
                        'refer_id'      =>  $refer_id
                    ]);

                    if ($sql_insert_diagnose && $sql_insertRecord) {
                                $successMessage = " Filled Successfully!".$successRefreshMessage;
                    }
                    else {
                        $errorMessage = " Could not fill" . $errorRefreshMessage;
                    }
                }

                # otherwise there are complication or allergy ...

                else {

                    # Fill diagnosis form ...

                    $sql_insert_diagnose = " INSERT INTO `diagnose_form`(`created_on`, `created_at`, `patient_id`, `diagnose_name`, `description`, `complications`, `allergy`, `filled_by`, `status`) 
                                                                VALUES (:created_on, :created_at, :patient_id, :diagnose_name, :ddescription, :complications, :allergy, :filled_by, :bstatus)";

                    $diagnose_InsertStatement = $pdo->prepare($sql_insert_diagnose);
                    $diagnose_InsertStatement->execute([
                        'created_on'    =>  $diagnose_date,
                        'created_at'    =>  $diagnose_time,
                        'patient_id'    =>  $patient_id,
                        'diagnose_name' =>  $diagnose_name,
                        'ddescription'  =>  $diagnose_description,
                        'complications' =>  $diagnose_complication,
                        'allergy'       =>  $diagnose_allergy,
                        'filled_by'     =>  $filled_by,
                        'bstatus'       =>  'unpaid'
                    ]);

                    # record the action ...

                    $rID = $auID;
                    $patient_name = $patient_existResults->patient_name;

                    # retrieving refer_id from consult_form ...

                    $referConsult_FetchQuery = 'SELECT * FROM `diagnose_form` WHERE `patient_id` =:patient_id AND `status`= :bstatus';
                    $referConsult_FetchStatement = $pdo->prepare($referConsult_FetchQuery);
                    $referConsult_FetchStatement->execute([
                        'patient_id' => $patient_id,
                        'bstatus'    => 'unpaid'
                    ]);
                    $referConsult_Results = $referConsult_FetchStatement->fetch();

                    $refer_id = $referConsult_Results->dgID;

                    # retrieving fees information ...

                    $fees_existFetchQuery = 'SELECT * FROM `fees_list` WHERE `fees_name` =:fees_name';
                    $fees_existFetchStatement = $pdo->prepare($fees_existFetchQuery);
                    $fees_existFetchStatement->execute([
                        'fees_name' => 'diagnosis'
                    ]);
                    $fees_existResults = $fees_existFetchStatement->fetch();

                    $fees_name = $fees_existResults->fees_name;
                    $fees_amount = $fees_existResults->amount;

                    $sql_insertRecord = " INSERT INTO `records`(`rdate`, `rtime`, `rID`, `patient_id`, `patient_name`, `amount`, `action`, `status`, `refer_id`) 
                                                        VALUES (:rdate, :rtime, :rID, :patient_id, :patient_name, :amount, :baction, :bstatus, :refer_id)";

                    $record_InsertStatement = $pdo->prepare($sql_insertRecord);
                    $record_InsertStatement->execute([
                        'rdate'         =>  $diagnose_date,
                        'rtime'         =>  $diagnose_time,
                        'rID'           =>  $rID,
                        'patient_id'    =>  $patient_id,
                        'patient_name'  =>  $patient_name,
                        'amount'        =>  $fees_amount,
                        'baction'       =>  $fees_name,
                        'bstatus'       =>  'pending',
                        'refer_id'      =>  $refer_id
                    ]);

                    if ($sql_insert_diagnose && $sql_insertRecord) {
                                $successMessage = " Filled Successfully!".$successRefreshMessage;
                    }
                    else {
                        $errorMessage = " Could not fill" . $errorRefreshMessage;
                    }
                }
            }
        }

        # otherwise pop up some alerts ...

        else {
            $errorMessage = " Invalid User!". $errorRefreshMessage;
        }
    }

    # Surgery form ...

    if(isset($_POST["submit_surgery"])) {
        $patient_id = $_POST['patient_id'];
        $surgery_date = $_POST['surgery_date'];
        $surgery_time = $_POST['surgery_time'];
        $surgery_description = $_POST['surgery_description'];
        $surgery_complication = $_POST['surgery_complication'];
        $surgery_allergy = $_POST['surgery_allergy'];
        $filled_by = $authorized_username;

        # checking patient existance ...

        $patient_existFetchQuery = 'SELECT * FROM `patient` WHERE `patient_id` =:patient_id';
        $patient_existFetchStatement = $pdo->prepare($patient_existFetchQuery);
        $patient_existFetchStatement->execute([
            'patient_id' => $patient_id
        ]);
        $patient_existResults = $patient_existFetchStatement->fetch();

        # if they exists, proceed with consultation ...

        if ($patient_existResults) {

            # checking surgery_form existance ...

            $surgery_FetchQuery = 'SELECT * FROM `surgery_form` WHERE `patient_id` =:patient_id AND `status`=:bstatus';
            $surgery_FetchStatement = $pdo->prepare($surgery_FetchQuery);
            $surgery_FetchStatement->execute([
                'patient_id' => $patient_id,
                'bstatus'    => 'unpaid'
            ]);
            $surgery_Results = $surgery_FetchStatement->fetch();   
            
            # if there is unpaid bill of surgery ...

            if ($surgery_Results) {
                $errorMessage = " There is unpaid bill! Pay First". $errorRefreshMessage;
            }

            # otherwise proceed with surgery_form ...

            else {

                # if there is neither complication nor allergy ...

                if (empty($surgery_complication) || empty($surgery_allergy)) {

                    # Fill surgery form ...

                    $sql_insert_surgery = " INSERT INTO `surgery_form`(`created_on`, `created_at`, `patient_id`, `description`, `complications`, `allergy`, `filled_by`, `status`) 
                                                                VALUES (:created_on, :created_at, :patient_id, :ddescription, :complications, :allergy, :filled_by, :bstatus)";

                    $surgery_InsertStatement = $pdo->prepare($sql_insert_surgery);
                    $surgery_InsertStatement->execute([
                        'created_on'    =>  $surgery_date,
                        'created_at'    =>  $surgery_time,
                        'patient_id'    =>  $patient_id,
                        'ddescription'  =>  $surgery_description,
                        'complications' =>  'none',
                        'allergy'       =>  'none',
                        'filled_by'     =>  $filled_by,
                        'bstatus'       =>  'unpaid'
                    ]);

                    # record the action ...

                    $rID = $auID;
                    $patient_name = $patient_existResults->patient_name;

                    # retrieving refer_id from surgery_form ...

                    $referSurgery_FetchQuery = 'SELECT * FROM `surgery_form` WHERE `patient_id` =:patient_id AND `status`=:bstatus';
                    $referSurgery_FetchStatement = $pdo->prepare($referSurgery_FetchQuery);
                    $referSurgery_FetchStatement->execute([
                        'patient_id' => $patient_id,
                        'bstatus'    => 'unpaid'
                    ]);
                    $referSurgery_Results = $referSurgery_FetchStatement->fetch();
                    
                    $refer_id = $referSurgery_Results->suID;

                    # retrieving fees information ...

                    $fees_existFetchQuery = 'SELECT * FROM `fees_list` WHERE `fees_name` =:fees_name';
                    $fees_existFetchStatement = $pdo->prepare($fees_existFetchQuery);
                    $fees_existFetchStatement->execute([
                        'fees_name' => 'surgery'
                    ]);
                    $fees_existResults = $fees_existFetchStatement->fetch();

                    $fees_name = $fees_existResults->fees_name;
                    $fees_amount = $fees_existResults->amount;

                    $sql_insertRecord = " INSERT INTO `records`(`rdate`, `rtime`, `rID`, `patient_id`, `patient_name`, `amount`, `action`, `status`, `refer_id`) 
                                                        VALUES (:rdate, :rtime, :rID, :patient_id, :patient_name, :amount, :baction, :bstatus, :refer_id)";

                    $record_InsertStatement = $pdo->prepare($sql_insertRecord);
                    $record_InsertStatement->execute([
                        'rdate'         =>  $surgery_date,
                        'rtime'         =>  $surgery_time,
                        'rID'           =>  $rID,
                        'patient_id'    =>  $patient_id,
                        'patient_name'  =>  $patient_name,
                        'amount'        =>  $fees_amount,
                        'baction'       =>  $fees_name,
                        'bstatus'       =>  'pending',
                        'refer_id'      =>  $refer_id
                    ]);

                    if ($sql_insert_surgery && $sql_insertRecord) {
                        $successMessage = " Filled Successfully!".$successRefreshMessage;
                    }
                    else {
                        $errorMessage = " Could not fill" . $errorRefreshMessage;
                    }
                }

                # otherwise there are complication or allergy ...

                else {

                    # Fill surgery form ...

                    $sql_insert_surgery = " INSERT INTO `surgery_form`(`created_on`, `created_at`, `patient_id`, `description`, `complications`, `allergy`, `filled_by`, `status`) 
                                                                VALUES (:created_on, :created_at, :patient_id, :ddescription, :complications, :allergy, :filled_by, :bstatus)";

                    $surgery_InsertStatement = $pdo->prepare($sql_insert_surgery);
                    $surgery_InsertStatement->execute([
                        'created_on'    =>  $surgery_date,
                        'created_at'    =>  $surgery_time,
                        'patient_id'    =>  $patient_id,
                        'ddescription'  =>  $surgery_description,
                        'complications' =>  $surgery_complication,
                        'allergy'       =>  $surgery_allergy,
                        'filled_by'     =>  $filled_by,
                        'bstatus'       =>  'unpaid'
                    ]);

                    # record the action ...

                    $rID = $auID;
                    $patient_name = $patient_existResults->patient_name;

                    # retrieving refer_id from surgery_form ...

                    $referSurgery_FetchQuery = 'SELECT * FROM `surgery_form` WHERE `patient_id` =:patient_id AND `status`=:bstatus';
                    $referSurgery_FetchStatement = $pdo->prepare($referSurgery_FetchQuery);
                    $referSurgery_FetchStatement->execute([
                        'patient_id' => $patient_id,
                        'bstatus'    => 'unpaid'
                    ]);
                    $referSurgery_Results = $referSurgery_FetchStatement->fetch();
                    
                    $refer_id = $referSurgery_Results->suID;

                    # retrieving fees information ...

                    $fees_existFetchQuery = 'SELECT * FROM `fees_list` WHERE `fees_name` =:fees_name';
                    $fees_existFetchStatement = $pdo->prepare($fees_existFetchQuery);
                    $fees_existFetchStatement->execute([
                        'fees_name' => 'surgery'
                    ]);
                    $fees_existResults = $fees_existFetchStatement->fetch();

                    $fees_name = $fees_existResults->fees_name;
                    $fees_amount = $fees_existResults->amount;

                    $sql_insertRecord = " INSERT INTO `records`(`rdate`, `rtime`, `rID`, `patient_id`, `patient_name`, `amount`, `action`, `status`, `refer_id`) 
                                                        VALUES (:rdate, :rtime, :rID, :patient_id, :patient_name, :amount, :baction, :bstatus, :refer_id)";

                    $record_InsertStatement = $pdo->prepare($sql_insertRecord);
                    $record_InsertStatement->execute([
                        'rdate'         =>  $surgery_date,
                        'rtime'         =>  $surgery_time,
                        'rID'           =>  $rID,
                        'patient_id'    =>  $patient_id,
                        'patient_name'  =>  $patient_name,
                        'amount'        =>  $fees_amount,
                        'baction'       =>  $fees_name,
                        'bstatus'       =>  'pending',
                        'refer_id'      =>  $refer_id
                    ]);

                    if ($sql_insert_surgery && $sql_insertRecord) {
                        $successMessage = " Filled Successfully!".$successRefreshMessage;
                    }
                    else {
                        $errorMessage = " Could not fill" . $errorRefreshMessage;
                    }
                }
            }
        }

        # otherwise pop up some alerts ...

        else {
            $errorMessage = " Invalid User!". $errorRefreshMessage;
        }
    }

    # generating activation key passcode

    if (isset($_POST['generateKey'])) {
        $cpin = $_POST['cpin'];
        $amount = $_POST['ramount'];

        # check if authorized pin are same ... 

        if ($authorizedResults->authorized_pin == $cpin) {

            # checking if authorized has enough balance to withdraw ...

            $authorized_balance = $authorizedResults->authorized_balance;

            if ($authorized_balance <= 0 || $authorized_balance < $amount) {
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

                # create a request row for authorized if he/she does not exist ...

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

    # deleting record ...

    if ($_GET['drID']) {
        $record_no = $_GET['drID'];

        # Fetching Records info to delete ...

        $record_FetchQuery = 'SELECT * FROM `records` WHERE `no` =:record_no';
        $record_FetchStatement = $pdo->prepare($record_FetchQuery);
        $record_FetchStatement->execute([
            'record_no' => $record_no
        ]);
        $record_Result = $record_FetchStatement->fetch();

        $patient_id = $record_Result->patient_id;

        $record_action = $record_Result->action;

        $refer_id = $record_Result->refer_id;

        # check if it is consultation action ...

        if ($record_action == 'Consultation') {

            $delete_consult = 'DELETE FROM `consult_form` WHERE `coID` =:refer_id AND `patient_id`=:patient_id';

            # PDO Prep & Exec..
            $consult_delete = $pdo->prepare($delete_consult);
            $consult_delete->execute([
                'refer_id'   =>  $refer_id,
                'patient_id' =>  $patient_id
            ]);

            # delete records of consult ...
            
            $delete_record = 'DELETE FROM `records` WHERE `no` =:record_no';

            $record_delete = $pdo->prepare($delete_record);
            $record_delete->execute([
                'record_no'   =>  $record_no
            ]);

            if ($delete_consult) {
                $patient_deleteSuccessMessage = 'Deleted successful!' . $successRefreshMessage;
            }
        }

        # check if it is diagnosis action ...

        else if ($record_action == 'diagnosis') {

            $delete_diagnose = 'DELETE FROM `diagnose_form` WHERE `dgID` =:refer_id AND `patient_id`=:patient_id';

            # PDO Prep & Exec..
            $diagnose_delete = $pdo->prepare($delete_diagnose);
            $diagnose_delete->execute([
                'refer_id'   =>  $refer_id,
                'patient_id' =>  $patient_id
            ]);

            # delete records of diagnose ...
            
            $delete_record = 'DELETE FROM `records` WHERE `no` =:record_no';
            
            $record_delete = $pdo->prepare($delete_record);
            $record_delete->execute([
                'record_no'   =>  $record_no
            ]);

            if ($delete_diagnose) {
                $patient_deleteSuccessMessage = 'Deleted successful!' . $successRefreshMessage;
            }
        }

        # check if it is surgery action ...

        else if ($record_action == 'surgery') {

            $delete_surgery = 'DELETE FROM `surgery_form` WHERE `suID` =:refer_id AND `patient_id`=:patient_id';

            # PDO Prep & Exec..
            $surgery_delete = $pdo->prepare($delete_surgery);
            $surgery_delete->execute([
                'refer_id'   =>  $refer_id,
                'patient_id' =>  $patient_id
            ]);

            # delete records of surgery ...
            
            $delete_record = 'DELETE FROM `records` WHERE `no` =:record_no';
            
            $record_delete = $pdo->prepare($delete_record);
            $record_delete->execute([
                'record_no'   =>  $record_no
            ]);

            if ($delete_surgery) {
                $patient_deleteSuccessMessage = 'Deleted successful!' . $successRefreshMessage;
            }
        }

        # there is no action available ...

        else {
            $patient_deleteErrorMessage = 'Could not delete!' . $errorRefreshMessage;
        }
    }
?>

<?php 
    include 'include/index_new.html';
?>