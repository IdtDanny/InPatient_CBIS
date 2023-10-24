<?php
    session_start();

    # Checkin if The user logged in...

    if (!isset($_SESSION['sessionToken'])) {
        header("location:../index.php");
    }

    # Includes...
    require_once '../public/config/connection.php';

    # Getting Information of Signed in User
    $admin_username = $_SESSION['sessionToken']->admin_username;
    $admin_ID = $_SESSION['sessionToken']->admin_ID;
    $admin_name = $_SESSION['sessionToken']->admin_name;

    #notification variables ...

    $patient_errorMessage = "";
    $patient_successMessage = "";
    $patient_deleteSuccessMessage = "";
    $patient_deleteErrorMessage = "";
    $update_errorMessage = "";
    $update_successMessage = "";

    # Calculating Each Number of Users, Cards, pharmacy, patients and so on...
    $sql_patient = 'SELECT * FROM patient';
    $sql_patient = 'SELECT * FROM patient';
    $sql_pharmacy = 'SELECT * FROM pharmacy';
    $sql_pharmacy_gas = 'SELECT * FROM `pharmacy` WHERE `pharmacy_type` = :btype';
    $sql_pharmacy_others = 'SELECT * FROM `pharmacy` WHERE `pharmacy_type` = :otype';
    // $usedCardsSql = 'SELECT * FROM `patient` WHERE `Approve` = :approve';

    $statement = $pdo->prepare($sql_patient);
    $statement->execute();

    $statement_patient = $pdo->prepare($sql_patient);
    $statement_patient -> execute();

    $statement_pharmacy = $pdo->prepare($sql_pharmacy);
    $statement_pharmacy -> execute();

    $statement_pharmacy_gas = $pdo->prepare($sql_pharmacy_gas);
    $statement_pharmacy_gas -> execute([
        'btype' => 'gas'
    ]);

    $statement_pharmacy_others = $pdo->prepare($sql_pharmacy_others);
    $statement_pharmacy_others -> execute([
        'otype' => 'others'
    ]);

    # Getting The number of patients, Cards, pharmacy...
    $patientsCount = $statement->rowCount();
    $registered_patient = $statement_patient->rowCount();
    $registered_pharmacy = $statement_pharmacy -> rowCount();
    $gas_pharmacy = $statement_pharmacy_gas -> rowCount();
    $others_pharmacy = $statement_pharmacy_others -> rowCount();

    # Fetching pharmacy info ...

    $pharmacy_FetchQuery = 'SELECT * FROM `pharmacy` ORDER BY `Date` DESC';
    $pharmacy_FetchStatement = $pdo->prepare($pharmacy_FetchQuery);
    $pharmacy_FetchStatement->execute();
    $pharmacy_Result = $pharmacy_FetchStatement->fetchAll();

    # Fetching patients info ...

    $patient_FetchQuery = 'SELECT * FROM `patient` ORDER BY `created_at` DESC';
    $patient_FetchStatement = $pdo->prepare($patient_FetchQuery);
    $patient_FetchStatement->execute();
    $patient_Result = $patient_FetchStatement->fetchAll();

    # Fetching Patient info ...

    $patient_FetchQuery = 'SELECT * FROM `patient` ORDER BY `created_at` DESC';
    $patient_FetchStatement = $pdo->prepare($patient_FetchQuery);
    $patient_FetchStatement->execute();
    $patient_Result = $patient_FetchStatement->fetchAll();

    # Getting Admin Info. for update form...

    $adminFetchQuery = 'SELECT * FROM `admin` WHERE `admin_ID` = :adminid';
    $adminFetchStatement = $pdo->prepare($adminFetchQuery);
    $adminFetchStatement->execute([
        'adminid' => $admin_ID
    ]);
    $adminResults = $adminFetchStatement->fetch();

    # refreshing message ...

    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to continue </span><a href='inpatient.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to see the change </span><a href='inpatient.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

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
            // $target_dir = "../public/profile/";
            // $target_file = $target_dir . basename($_FILES['patient_profile']['name']);
            // $patient_profile = $_FILES['patient_profile']['name'];
            // $uploadOk = 1;
            // $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            
            // # Check if image file is a actual image or fake image
            // $check = getimagesize($_FILES["patient_profile"]["tmp_name"]);
            // if($check !== false) {
            //     $uploadOk = 1;
            // }
            // else {
            //     $patient_errorMessage = " File is not an image.";
            //     $uploadOk = 0;
            // }
            
            // # Check file size
            // if ($_FILES["patient_profile"]["size"] > 400000) {
            //     $patient_errorMessage = " Sorry, your file is too large." . $errorRefreshMessage;
            //     $uploadOk = 0;
            // }
            
            // # Allow certain file formats
            // else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            // && $imageFileType != "gif" ) {
            //     $patient_errorMessage = " Sorry, only JPG, JPEG, PNG & GIF files are allowed." . $errorRefreshMessage;
            //     $uploadOk = 0;
            // }
            
            // # Check if $uploadOk is set to 0 by an error
            // else if ($uploadOk == 0) {
            //     $patient_errorMessage = " Sorry, your file was not uploaded." . $errorRefreshMessage;
            // } 
            // else {
                // if (move_uploaded_file($_FILES["patient_profile"]["tmp_name"], $target_file)) {
                    // echo "moved";
                    
                    # Inserting pharmacy...

                    $sql_insert_patient = " INSERT INTO `patient`(
                        `created_at`, 
                        `patient_id`, 
                        `patient_profile`, 
                        `patient_name`, 
                        `patient_mname`, 
                        `patient_lname`, 
                        `patient_gender`, 
                        `patient_tel`, 
                        `patient_mail`, 
                        `patient_balance`, 
                        `referral_cashier`, 
                        `status`, 
                        `approve`, 
                        `patient_pin`) 
                                                   VALUES(
                                                    :adate, 
                                                    :patient_id, 
                                                    :patient_profile, 
                                                    :patient_name, 
                                                    :patient_mname, 
                                                    :patient_lname, 
                                                    :patient_gender, 
                                                    :patient_tel, 
                                                    :patient_mail, 
                                                    :patient_balance, 
                                                    :referral_cashier, 
                                                    :bstatus, 
                                                    :approve, 
                                                    :patient_pin)";

                    $patient_InsertStatement = $pdo->prepare($sql_insert_patient);
                    $patient_InsertStatement->execute([
                        'adate'             =>  $date_Sent,
                        'patient_id'        =>  $patient_id,
                        'patient_profile'   =>  $patient_profile,
                        'patient_name'      =>  $patient_name,
                        'patient_mname'     =>  $patient_mname,
                        'patient_lname'     =>  $patient_lname,
                        'patient_gender'    =>  $patient_gender,
                        'patient_tel'       =>  $patient_tel,  
                        'patient_mail'      =>  $patient_mail,
                        'patient_balance'   =>  '0',
                        'referral_cashier'  =>  $admin_name,
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
                                $patient_successMessage = " Registered Pin: ". $patient_pin . $successRefreshMessage;
                        }
                    }
                    else {
                        $patient_errorMessage = " Could not register" . $errorRefreshMessage;
                    }
                // } 
                // else {
                //     $patient_errorMessage = " Something went wrong" . $errorRefreshMessage;
                // }
            // }
        }
    }
    
    # Registering new patient

    // if (isset($_POST['registerpatient'])) {

    //     $patient_id = $_POST['patient_id'];
    //     $patient_name = $_POST['patient_name'];
    //     $patient_mail = $_POST['patient_mail'];
    //     $patient_tel = $_POST['patient_tel'];
    //     $patient_district = $_POST['patient_district'];
    //     $patient_sector = $_POST['patient_sector'];
    //     $date_Sent = date('Y-m-d h:i:s');
    //     $patient_pin = rand(10000,99999);
    //     # $password= $patient_uname.'-'.$patient_pin;
    //     # $hashed_Password = md5($password);

    //     # checking if patient exists
    //     $patient_existFetchQuery = 'SELECT * FROM `patient` WHERE `patient_id` =:patient_id';
    //     $patient_existFetchStatement = $pdo->prepare($patient_existFetchQuery);
    //     $patient_existFetchStatement->execute([
    //         'patient_id' => $patient_id
    //     ]);
    //     $patient_existResults = $patient_existFetchStatement->fetch();

    //     # if exist, pop some message
    //     if ($patient_existResults) {
    //         $patient_errorMessage = " Already registered" . $errorRefreshMessage;
    //     }

    //     # otherwise proceed with registration process
    //     else {      
    //         # Inserting patient...
            
    //         $sql_insert_patient = " INSERT INTO `patient`(`created_at`, `patient_id`, `patient_profile`, `patient_name`, `patient_mname`, `patient_lname`, `patient_tel`, `patient_mail`, `patient_balance`, `referral_patient`, `status`, `approve`, `patient_pin`) 
    //         VALUES (:created_at, :patient_id, :patient_name, :patient_mname, :patient_lname, :patient_tel, :patient_mail, :patient_balance, :referral_patient, :bstatus, :approve, :patient_pin)";
            
    //         $patient_InsertStatement = $pdo->prepare($sql_insert_patient);
    //         $patient_InsertStatement->execute([
    //             'created_at'         =>  $date_Sent,
    //             'patient_id'         =>  $patient_id,
    //             'patient_name'       =>  $patient_name,
    //             'patient_tel'        =>  $patient_tel,
    //             'patient_mail'       =>  $patient_mail,
    //             'patient_balance'    =>  '0',
    //             'referral_patient'   =>  $patient_pin,
    //             'bstatus'            =>  'active',
    //             'approve'            =>  'Approved',
    //             'patient_pin'        =>  $patient_pin
    //         ]);
            
    //         if ($sql_insert_patient) {

    //             # Getting patient Info. for update form...

    //             $patient_existFetchQuery = 'SELECT * FROM `patient` WHERE `patient_id` =:patient_id';
    //             $patient_existFetchStatement = $pdo->prepare($patient_existFetchQuery);
    //             $patient_existFetchStatement->execute([
    //                 'patient_id' => $patient_id
    //             ]);
    //             $patient_existResults = $patient_existFetchStatement->fetch();
    //             $pID = $patient_existResults->pID;

    //             $sql_insert_location = "  INSERT INTO `patient_location`(`pID`, `patient_name`, `district`, `sector`) VALUES(:caID, :patient_name, :district, :sector) ";
    //             $location_InsertStatement = $pdo->prepare($sql_insert_location);
    //             $location_InsertStatement->execute([
    //                     'caID'           =>  $pID,
    //                     'patient_name'   =>  $patient_name,
    //                     'district'      =>  $patient_district,
    //                     'sector'        =>  $patient_sector
    //             ]);
    //             if ($sql_insert_location) {
    //                     $patient_successMessage = " patient Registered, Pin: ". $patient_pin . $successRefreshMessage;
    //             }
    //         }
    //         else {
    //             $patient_errorMessage = " Could not register" . $errorRefreshMessage;
    //         }
    //     }
    // }

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

    # topup patient Operation...

    if (isset($_POST['toppatient'])) {

        $confirm_top = $_POST['confirm_top'];
        $patient_id = $_POST['patient_id'];
        $ramount = $_POST['ramount'];

        # confirming patient ...

        $fetch_patientQuery='SELECT * FROM `patient` WHERE `patient_pin` = :patient_pin';
        $fetch_patientStatement = $pdo->prepare($fetch_patientQuery);
        $fetch_patientStatement->execute([
            'patient_pin' => $confirm_top
        ]);

        $patient_Info = $fetch_patientStatement -> fetch();

        $patientCount = $fetch_patientStatement->rowCount();

        # if patient is confirmed 

        if ($patientCount > 0 ) {

            # check the float patient balance to top up ...

            $patient_balance = $patient_Info->patient_balance;

            # if patient does not have enough balance to top up

            if ($patient_balance <= 0 || $patient_balance < $ramount) {
                $update_errorMessage = " Not enough balance" . $errorRefreshMessage;
            }

            # otherwise patient can top up

            else {

                # Checking for patient to top ...

                $fetch_UserQuery='SELECT * FROM `patient` WHERE `patient_id` = :patient_id';
                $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
                $fetch_UserStatement->execute([
                    'patient_id' => $patient_id
                ]);

                $patient_Info = $fetch_UserStatement -> fetch();

                $patientCount = $fetch_UserStatement->rowCount();

                # if patient found

                if ($patientCount > 0 ) {

                    # Modifying patient ...

                    $patient_name = $patient_Info->patient_name;

                    $patient_pin = $patient_Info->patient_pin;

                    $patient_balance -= $ramount;

                    $patient_UpdateQuery = ' UPDATE `patient`
                                        SET `patient_balance` = :patient_balance
                                        WHERE `patient_pin` = :patient_pin
                    ';

                    $patient_UpdateStatement = $pdo->prepare($patient_UpdateQuery);
                    $patient_UpdateStatement->execute([
                        'patient_balance'   =>  $patient_balance,
                        'patient_pin'        =>  $patient_pin
                    ]);

                    # Modifying patient ...

                    $patient_name = $patient_Info->patient_name;

                    $balance = $patient_Info->patient_balance;

                    $balance += $ramount;

                    $patient_UpdateQuery = ' UPDATE `patient`
                                        SET `patient_balance` = :patient_balance
                                        WHERE `patient_id` = :patient_id
                    ';

                    $patient_UpdateStatement = $pdo->prepare($patient_UpdateQuery);
                    $patient_UpdateStatement->execute([
                        'patient_balance'   =>  $balance,
                        'patient_id'        =>  $patient_id
                    ]);

                    # done top up

                    if ($patient_UpdateQuery) {

                        
                        $sender_id = $confirm_top;
                        $receiver_id = $patient_id;
                        $amount = $ramount;
                        $date_Sent = date('Y-m-d h:i:s');
                        $time_Sent = date('h:i:s');

                        # record keeping

                        $record_insertQuery = ' INSERT INTO `records`(`rdate`, `rtime`, `rID`, `patient_id`, `patient_name`, `amount`, `action`, `status`) 
                        VALUES(:rdate, :rtime, :rID, :patient_id, :patient_name, :amount, :raction, :rstatus)';
    
                        $record_insertStatement = $pdo->prepare($record_insertQuery);
                        $record_insertStatement->execute([
                            'rdate'         =>  $date_Sent,
                            'rtime'         =>  $time_Sent,
                            'rID'           =>  $confirm_top,
                            'patient_id'     =>  $patient_id,
                            'patient_name'   =>  $patient_name,
                            'amount'        =>  $pamount,
                            'raction'       =>  'topup',
                            'rstatus'       =>  'approved'
                        ]);

                        # notification

                        $sql_insert_notification = " INSERT INTO `notification_all`(`date_sent`, `time_sent`, `receiver_id`, `sender_id`, `amount`, `action`, `status`) VALUES (:date_sent, :time_sent, :receiver_id, :sender_id, :amount, :naction, :astatus)";

                        $notification_InsertStatement = $pdo->prepare($sql_insert_notification);
                        $notification_InsertStatement->execute([
                            'date_sent'     =>  $date_Sent,
                            'time_sent'     =>  $time_Sent,
                            'receiver_id'   =>  $receiver_id,
                            'sender_id'     =>  $sender_id,
                            'amount'        =>  $amount,
                            'naction'       =>  'topup',
                            'astatus'       =>  'unread'
                        ]);

                        if ($sql_insert_notification){

                            # notification via email

                            $patient_mail = $patient_Info->patient_mail;

                            # check if email is not optional@smtp.com

                            if ($patient_mail == 'optional@tps.com') {
                                $update_successMessage = " Recharged Successful" . $successRefreshMessage;
                            }

                            # else send email to actual email

                            else {

                                $notify_message = "Tps M-Card: You have been recharged with ". number_format($ramount)." RWF from ". $patient_name ." at ". $date_Sent . ". Your new balance: ". number_format($balance) ." RWF.";


                                $mail = new PHPMailer();
                                $mail->isSMTP();
                    
                                $mail->SMTPDebug = 0;
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->SMTPSecure = 'tls';
                                $mail->Port = '587';
                                $mail->Username = 'tap.pay.holder@gmail.com';
                                $mail->Password = 'zkwulxaitrxcovfh';
                    
                                $mail->isHTML(true);
                                $mail->Subject = 'TOP UP CONFIRMATION';
                                $mail->setFrom('tap.pay.holder@gmail.com', 'Tap and Pay');
                                $mail->addAddress($patient_mail);
                    
                                $mail->Body = $notify_message;

                                # if email sent
                    
                                if ($mail->Send()) {
                                    $update_successMessage = " Recharged Successful" . $successRefreshMessage;
                                } 

                                # email not sent

                                else {
                                    $update_errorMessage = " Could not send" . $errorRefreshMessage . $mail->ErrorInfo;
                                }
                            }
                        } 
                    }
                }
                else {
                    $update_errorMessage = " Unknown patient" . $errorRefreshMessage;
                }
            }
        }
        else {
            $update_errorMessage = " Incorrect Pin" . $errorRefreshMessage;
        }
    }

    # withdraw patient Operation...

    if (isset($_POST['withdrawpatient'])) {

        $patient_confirm = $_POST['patient_confirm'];
        $confirm_top = $_POST['confirm_top'];
        $patient_id = $_POST['patient_id'];
        $ramount = $_POST['ramount'];

        # confirming patient ...

        $fetch_patientQuery='SELECT * FROM `patient` WHERE `patient_pin` = :patient_pin';
        $fetch_patientStatement = $pdo->prepare($fetch_patientQuery);
        $fetch_patientStatement->execute([
            'patient_pin' => $confirm_top
        ]);

        $patient_Info = $fetch_patientStatement -> fetch();

        $patientCount = $fetch_patientStatement->rowCount();

        # if patient is confirmed 

        if ($patientCount > 0 ) {

            # patient info

            $patient_balance = $patient_Info->patient_balance;
            $patient_pin = $patient_Info->patient_pin;

            # Checking for patient to top ...

            $fetch_UserQuery='SELECT * FROM `patient` WHERE `patient_id` = :patient_id AND `patient_pin` = :patient_pin';
            $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
            $fetch_UserStatement->execute([
                'patient_id' => $patient_id,
                'patient_pin' => $patient_confirm
            ]);

            $patient_Info = $fetch_UserStatement -> fetch();

            $patientCount = $fetch_UserStatement->rowCount();

            # when patient is found

            if ($patientCount > 0 ) {

                # checking if patient has enough money to withdraw

                $patient_balance = $patient_Info->patient_balance;

                if ($patient_balance <= 0 || $patient_balance < $ramount) {
                    $update_errorMessage = " Not enought balance" . $errorRefreshMessage;
                }

                # else patient having enough balance to top up

                else {

                    # modifying patient ...

                    $patient_balance += $ramount;

                    $patient_UpdateQuery = ' UPDATE `patient`
                                        SET `patient_balance` = :patient_balance
                                        WHERE `patient_pin` = :patient_pin
                    ';

                    $patient_UpdateStatement = $pdo->prepare($patient_UpdateQuery);
                    $patient_UpdateStatement->execute([
                        'patient_balance' =>  $patient_balance,
                        'patient_pin'     =>  $patient_pin
                    ]);

                    # Modifying patient ...

                    $balance = $patient_Info->patient_balance;

                    $balance -= $ramount;

                    $patient_UpdateQuery = ' UPDATE `patient`
                                        SET `patient_balance` = :patient_balance
                                        WHERE `patient_id` = :patient_id
                    ';

                    $patient_UpdateStatement = $pdo->prepare($patient_UpdateQuery);
                    $patient_UpdateStatement->execute([
                        'patient_balance'   =>  $balance,
                        'patient_id'        =>  $patient_id
                    ]);

                    # if patient and patient update is done

                    if ($patient_UpdateQuery && $patient_UpdateQuery) {

                        
                        $sender_id = $confirm_top;
                        $receiver_id = $patient_id;
                        $amount = $ramount;
                        $date_Sent = date('Y-m-d h:i:s');
                        $time_Sent = date('h:i:s');

                        # record keeping

                        $record_insertQuery = ' INSERT INTO `records`(`rdate`, `rtime`, `rID`, `patient_id`, `patient_name`, `amount`, `action`, `status`) 
                        VALUES(:rdate, :rtime, :rID, :patient_id, :patient_name, :amount, :raction, :rstatus)';
    
                        $record_insertStatement = $pdo->prepare($record_insertQuery);
                        $record_insertStatement->execute([
                            'rdate'         =>  $date_Sent,
                            'rtime'         =>  $time_Sent,
                            'rID'           =>  $confirm_top,
                            'patient_id'     =>  $patient_id,
                            'patient_name'   =>  $patient_name,
                            'amount'        =>  $pamount,
                            'raction'       =>  'withdraw',
                            'rstatus'       =>  'approved'
                        ]);

                        # notification

                        $sql_insert_notification = " INSERT INTO `notification_all`(`date_sent`, `time_sent`, `receiver_id`, `sender_id`, `amount`, `action`, `status`) VALUES (:date_sent, :time_sent, :receiver_id, :sender_id, :amount, :naction, :astatus)";

                        $notification_InsertStatement = $pdo->prepare($sql_insert_notification);
                        $notification_InsertStatement->execute([
                            'date_sent'     =>  $date_Sent,
                            'time_sent'     =>  $time_Sent,
                            'receiver_id'   =>  $receiver_id,
                            'sender_id'     =>  $sender_id,
                            'amount'        =>  $amount,
                            'naction'       =>  'withdraw',
                            'astatus'       =>  'unread'
                        ]);

                        if ($sql_insert_notification){

                            # notification via email

                            $patient_mail = $patient_Info->patient_mail;

                            # check if email is not optional@smtp.com

                            if ($patient_mail == 'optional@tps.com') {
                                $update_successMessage = " Withdraw Successful" . $successRefreshMessage;
                            }

                            # else send email to actual email

                            else {

                                $notify_message = "Tps M-Card: ". number_format($ramount)." RWF withdrawn from your account ". $patient_id ." at ". $date_Sent . ". Your new balance: ". number_format($balance) ." RWF.";


                                $mail = new PHPMailer();
                                $mail->isSMTP();

                                $mail->SMTPDebug = 0;
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->SMTPSecure = 'tls';
                                $mail->Port = '587';
                                $mail->Username = 'tap.pay.holder@gmail.com';
                                $mail->Password = 'zkwulxaitrxcovfh';

                                $mail->isHTML(true);
                                $mail->Subject = 'TPS WITHDRAW';
                                $mail->setFrom('tap.pay.holder@gmail.com', 'Tap and Pay');
                                $mail->addAddress($patient_mail);

                                $mail->Body = $notify_message;

                                # if email sent

                                if ($mail->Send()) {
                                    $update_successMessage = " Withdraw Successful" . $successRefreshMessage;
                                } 

                                # email not sent

                                else {
                                    $update_errorMessage = " Could not send" . $errorRefreshMessage . $mail->ErrorInfo;
                                }
                            }
                        }
                        else {
                            $update_errorMessage = " Could not notify" . $errorRefreshMessage;
                        }
                    }
                    else {
                        $update_errorMessage = " Could not update patient or patient" . $errorRefreshMessage;
                    }
                }
            }

            # when patient is not found

            else {
                $update_errorMessage = " Unknown patient" . $errorRefreshMessage;
            }
        }

        # when patient is not confirmed

        else {
            $update_errorMessage = " Incorrect Pin" . $errorRefreshMessage;
        }
    }

    # modifying current patient

    if (isset($_POST['modifypatient'])) {

        $patient_id = $_POST['patient_id'];
        $patient_name = $_POST['patient_name'];
        $patient_mail = $_POST['patient_mail'];
        $patient_tel = $_POST['patient_tel'];
        $patient_district = $_POST['patient_district'];
        $patient_sector = $_POST['patient_sector'];
        $date_Sent = date('Y-m-d h:i:s');
        # $password= $patient_uname.'-'.$patient_pin;
        # $hashed_Password = md5($password);

        # checking if patient exists

        $patient_existFetchQuery = 'SELECT * FROM `patient` WHERE `patient_id` =:patient_id';
        $patient_existFetchStatement = $pdo->prepare($patient_existFetchQuery);
        $patient_existFetchStatement->execute([
            'patient_id' => $patient_id
        ]);
        $patient_existResults = $patient_existFetchStatement->fetch();

        # if exist, update patient with provided credentials ...

        if ($patient_existResults) {

            # updating patient...

            $sql_update_patient = " UPDATE `patient` SET `patient_name` =:patient_name, `patient_tel` =:patient_tel, `patient_mail` =:patient_mail WHERE `patient_id` =:patient_id";
                        
            $patient_updateStatement = $pdo->prepare($sql_update_patient);
            $patient_updateStatement->execute([
                'patient_name'       =>  $patient_name,
                'patient_tel'        =>  $patient_tel,
                'patient_mail'       =>  $patient_mail,
                'patient_id'         =>  $patient_id
            ]);

            # after updating patient

            if ($sql_update_patient) {

                # updating patient location ...

                $pID = $patient_existResults->pID;

                $sql_update_location = "  UPDATE `patient_location` SET `patient_name` =:patient_name, `district` =:district, `sector` =:sector WHERE `pID` =:pID";
                $location_updateStatement = $pdo->prepare($sql_update_location);
                $location_updateStatement->execute([
                    'patient_name'   =>  $patient_name,
                    'district'      =>  $patient_district,
                    'sector'        =>  $patient_sector,
                    'pID'           =>  $pID
                ]);
                if ($sql_update_location) {
                        $patient_successMessage = " patient Updated, ". $successRefreshMessage;
                }
            }
            else {
                $patient_errorMessage = " Could not register" . $errorRefreshMessage;
            }
        }

        # otherwise they should register first

        else {      
            $patient_errorMessage = " Not registered" . $errorRefreshMessage;
        }
    }
?>

<?php 
    include 'include/patient_front.html';
?>