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

    # notification variables ...

    $busy_errorMessage = "";
    $busy_successMessage = "";
    $busy_deleteErrorMessage = "";
    $busy_deleteSuccessMessage = "";
    $update_errorMessage = "";
    $update_successMessage = "";
    $busy_updateErrorMessage = "";
    $busy_updateSuccessMessage = "";

    # Calculating Each Number of Users, Cards, pharmacy, cashiers and so on...
    $sql_cashier = 'SELECT * FROM cashier';
    $sql_patient = 'SELECT * FROM patient';
    $sql_pharmacy = 'SELECT * FROM pharmacy';
    $sql_pharmacy_gas = 'SELECT * FROM `pharmacy` WHERE `pharmacy_type` = :btype';
    $sql_pharmacy_others = 'SELECT * FROM `pharmacy` WHERE `pharmacy_type` = :otype';
    // $usedCardsSql = 'SELECT * FROM `patient` WHERE `Approve` = :approve';

    $statement = $pdo->prepare($sql_cashier);
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

    # Getting The number of cashiers, Cards, pharmacy...
    $cashiersCount = $statement->rowCount();
    $registered_patient = $statement_patient->rowCount();
    $registered_pharmacy = $statement_pharmacy -> rowCount();
    $gas_pharmacy = $statement_pharmacy_gas -> rowCount();
    $others_pharmacy = $statement_pharmacy_others -> rowCount();

    # Fetching pharmacy info ...

    $pharmacy_FetchQuery = 'SELECT * FROM `pharmacy` ORDER BY `Date` DESC';
    $pharmacy_FetchStatement = $pdo->prepare($pharmacy_FetchQuery);
    $pharmacy_FetchStatement->execute();
    $pharmacy_Result = $pharmacy_FetchStatement->fetchAll();

    # Fetching cashiers info ...

    $cashier_FetchQuery = 'SELECT * FROM `cashier` ORDER BY `created_at` DESC';
    $cashier_FetchStatement = $pdo->prepare($cashier_FetchQuery);
    $cashier_FetchStatement->execute();
    $cashier_Result = $cashier_FetchStatement->fetchAll();


    # Getting Admin Info. for update form...

    $adminFetchQuery = 'SELECT * FROM `admin` WHERE `admin_ID` = :adminid';
    $adminFetchStatement = $pdo->prepare($adminFetchQuery);
    $adminFetchStatement->execute([
        'adminid' => $admin_ID
    ]);
    $adminResults = $adminFetchStatement->fetch();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to continue </span><a href='pharmacy.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to see the change </span><a href='pharmacy.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # Registering new pharmacy

    if (isset($_POST['registerpharmacy'])) {
        $pharmacy_name = $_POST['pharmacy_name'];
        $pharmacy_mail = $_POST['pharmacy_mail'];
        $pharmacy_type = $_POST['pharmacy_type'];
        $pharmacy_tin = $_POST['pharmacy_tin'];
        $pharmacy_district = $_POST['pharmacy_district'];
        $pharmacy_sector = $_POST['pharmacy_sector'];
        $password = $pharmacy_tin;
        $hashed_Password = md5($password);
        $date_Sent = date('Y-m-d h:i:s');
        $pharmacy_pin = rand(1000,9999);

        $target_dir = "../public/profile/";
        $target_file = $target_dir . basename($_FILES['pharmacy_profile']['name']);
        $pharmacy_profile = $_FILES['pharmacy_profile']['name'];
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        # Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["pharmacy_profile"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        }
        else {
            $busy_errorMessage = " File is not an image.";
            $uploadOk = 0;
        }
        
        # Check file size
        if ($_FILES["pharmacy_profile"]["size"] > 400000) {
            $busy_errorMessage = " Sorry, your file is too large." . $errorRefreshMessage;
            $uploadOk = 0;
        }
        
        # Allow certain file formats
        else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $busy_errorMessage = " Sorry, only JPG, JPEG, PNG & GIF files are allowed." . $errorRefreshMessage;
            $uploadOk = 0;
        }
        
        # Check if $uploadOk is set to 0 by an error
        else if ($uploadOk == 0) {
            $busy_errorMessage = " Sorry, your file was not uploaded." . $errorRefreshMessage;
        } 
        else {
            if (move_uploaded_file($_FILES["pharmacy_profile"]["tmp_name"], $target_file)) {

                # Inserting pharmacy...

                $sql_insert_pharmacy = " INSERT INTO `pharmacy`(`Date`, `pharmacy_name`, `pharmacy_tin`, `pharmacy_mail`, `pharmacy_password`, `pharmacy_pin`, `pharmacy_type`, `balance`, `status`, `photo`) VALUES(:bdate, :pharmacyname, :pharmacytin, :pharmacymail, :pharmacypass, :pharmacypin, :pharmacytype, :balance, :bstatus, :photo)";

                $pharmacy_InsertStatement = $pdo->prepare($sql_insert_pharmacy);
                $pharmacy_InsertStatement->execute([
                    'bdate'          =>  $date_Sent,
                    'pharmacyname'   =>  $pharmacy_name,
                    'pharmacytin'    =>  $pharmacy_tin,
                    'pharmacymail'   =>  $pharmacy_mail,
                    'pharmacypass'   =>  $hashed_Password,
                    'pharmacypin'    =>  $pharmacy_pin,
                    'pharmacytype'   =>  $pharmacy_type,
                    'balance'        =>  '0',
                    'bstatus'        =>  'Active',
                    'photo'          =>  $pharmacy_profile
                ]);

                if ($sql_insert_pharmacy) {

                    # Getting Admin Info. for update form...

                    $busy_locationFetchQuery = 'SELECT * FROM `pharmacy` WHERE `pharmacy_tin` = :pharmacytin';
                    $busy_locationFetchStatement = $pdo->prepare($busy_locationFetchQuery);
                    $busy_locationFetchStatement->execute([
                        'pharmacytin' => $pharmacy_tin
                    ]);
                    $busy_locationResults = $busy_locationFetchStatement->fetch();
                    $phID = $busy_locationResults->phID;

                    $sql_insert_location = "  INSERT INTO `pharmacy_location`(`phID`, `pharmacy_tin`, `district`, `sector`) VALUES(:phID, :pharmacytin, :district, :sector) ";
                    $location_InsertStatement = $pdo->prepare($sql_insert_location);
                    $location_InsertStatement->execute([
                            'phID'           =>  $phID,
                            'pharmacytin'   =>  $pharmacy_tin,
                            'district'      =>  $pharmacy_district,
                            'sector'        =>  $pharmacy_sector
                    ]);
                    if ($sql_insert_pharmacy && $sql_insert_location) {
                            $busy_successMessage = " pharmacy Registered, TIN: ". $pharmacy_tin . $successRefreshMessage;
                    }
                }
                else {
                    $busy_errorMessage = " Could not register" . $errorRefreshMessage;
                }
            } 
            else {
                $busy_errorMessage = " Something went wrong" . $errorRefreshMessage;
            }
        }
    }

    # getting pharmacy delete response

    if (isset($_GET['dphID'])) {
        $dphID = $_GET['dphID'];
        $sql_bdelete = 'DELETE FROM `pharmacy` WHERE phID = :phID';
        $sql_ldelete = 'DELETE FROM `pharmacy_location` WHERE phID = :phID';

        # PDO Prep & Exec..
        $delete_pharmacyR = $pdo->prepare($sql_bdelete);
        $delete_pharmacyR->execute([
            'phID'  =>  $dphID
        ]);

        $delete_pharmacyL = $pdo->prepare($sql_ldelete);
        $delete_pharmacyL->execute([
            'phID'  =>  $dphID
        ]);

        if ($sql_bdelete && $sql_ldelete) {
            $busy_deleteSuccessMessage = " Deleted Successful" . $successRefreshMessage;
        }
        else {
            $busy_deleteErrorMessage = " Could not delete, check pharmacy id" . $errorRefreshMessage;
        }

    }

    # getting pharmacy approve response

    if (isset($_GET['AphID'])) {
        $dphID = $_GET['AphID'];
        $sql_update = 'UPDATE `pharmacy` SET `approved_by` = :approved_by WHERE `pharmacy`.`phID` = :phID';

        # PDO Prep & Exec..
        $update_pharmacy = $pdo->prepare($sql_update);
        $update_pharmacy->execute([
            'approved_by' => 'admin',
            'phID'         =>  $dphID
        ]);

        if ($sql_update) {
            $update_successMessage = " Approved Successful" . $successRefreshMessage;
        }
        else {
            $update_errorMessage = " Could not update, check pharmacy id" . $errorRefreshMessage;
        }

    }

    # Update pharmacy Operation...

    if (isset($_POST['updatepharmacy'])) {

        $old_btin = $_POST['old_btin'];
        $npharmacy_name = $_POST['npharmacy_name'];
        $npharmacy_type = $_POST['npharmacy_type'];
        $npharmacy_tin = $_POST['npharmacy_tin'];
        $npharmacy_district = $_POST['npharmacy_district'];
        $npharmacy_sector = $_POST['npharmacy_sector'];

        # Checking for pharmacyTin ...

        $fetch_UserQuery='SELECT * FROM `pharmacy` WHERE `pharmacy_tin` = :pin';
        $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
        $fetch_UserStatement->execute([
            'pin'       => $old_btin
        ]);

        $pharmacy_Info = $fetch_UserStatement -> fetch();

        $pharmacyCount = $fetch_UserStatement->rowCount();

        if ($pharmacyCount > 0 ) {

            # Modifying pharmacy ...

            $pharmacy_UpdateQuery = ' UPDATE `pharmacy`
                                SET `pharmacy_name` = :pharmacy_NewName,
                                    `pharmacy_tin` = :pharmacy_NewTin,
                                    `pharmacy_type` = :pharmacy_NewType
                                WHERE `pharmacy_tin` = :pharmacytin
            ';

            $location_UpdateQuery = ' UPDATE `pharmacy_location`
                                SET `pharmacy_tin` = :pharmacy_NewTin,
                                    `district` = :npharmacy_district,
                                    `sector` = :npharmacy_sector
                                WHERE `pharmacy_tin` = :pharmacytin
            ';

            $location_UpdateStatement = $pdo->prepare($location_UpdateQuery);
            $location_UpdateStatement->execute([
                'pharmacy_NewTin'       =>  $npharmacy_tin,
                'npharmacy_district'    =>  $npharmacy_district,
                'npharmacy_sector'      =>  $npharmacy_sector,
                'pharmacytin'           =>  $old_btin
            ]);

            $pharmacy_UpdateStatement = $pdo->prepare($pharmacy_UpdateQuery);
            $pharmacy_UpdateStatement->execute([
                'pharmacy_NewName'      =>  $npharmacy_name,
                'pharmacy_NewTin'       =>  $npharmacy_tin,
                'pharmacy_NewType'      =>  $npharmacy_type,
                'pharmacytin'           =>  $old_btin
            ]);

            if ($pharmacy_UpdateQuery && $location_UpdateQuery) {
                $update_successMessage = " Updated Successful" . $successRefreshMessage;
            }
        }
        else {
            $update_errorMessage = " Unknown Tin" . $errorRefreshMessage;
        }

    }

    # withdraw cashier Operation...

    if (isset($_POST['withdrawpharmacy'])) {

        $cpin = $_POST['cpin'];
        $ramount = $_POST['ramount'];

        # checking cashier activation key from request made ...

        $requestFetchQuery = 'SELECT * FROM `request` WHERE `activation_key` = :cpin AND `amount` = :ramount';
        $requestFetchStatement = $pdo->prepare($requestFetchQuery);
        $requestFetchStatement->execute([
            'cpin'    => $cpin,
            'ramount' => $ramount
        ]);
        $requestResults = $requestFetchStatement->fetch();

        # once activation key confirmed ...

        if ($requestFetchQuery) {

            # checking if it is not confirmed ...

            if ($requestResults->status == 'confirmed') {
                $update_errorMessage = " No request made" . $errorRefreshMessage;
            }

            # otherwise proceed with operation ...

            else {

                # checking if 24 hours haven't passed ...

                $request_date = $requestResults->request_date . ' ' . $requestResults->request_time;

                $now = strtotime(date('Y-m-d h:i:s'));
                $cdate = strtotime($request_date);
                $day_diff = $now - $cdate;
                $hours = floor($day_diff / 3600);
                
                if ($hours >= 24) {
                    $update_errorMessage = " Request expired" . $errorRefreshMessage;
                }

                # otherwise proceed with operation ...

                else {

                    # getting cashier info from request ...

                    $user_id = $requestResults->user_id;
                    
                    # Checking for cashier existing and his id meet with request ...

                    $fetch_UserQuery='SELECT * FROM `pharmacy` WHERE `pharmacy_tin` = :pharmacy_tin';
                    $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
                    $fetch_UserStatement->execute([
                        'pharmacy_tin' => $user_id
                    ]);

                    $pharmacy_Info = $fetch_UserStatement -> fetch();

                    $pharmacyCount = $fetch_UserStatement->rowCount();

                    # proceed with withdraw if cashier info meet with request ...

                    if ($pharmacyCount > 0 ) {

                        # cashier balance ...

                        $pharmacy_balance = $pharmacy_Info->balance;

                        # checking cashier balance to withdraw ...

                        if ($pharmacy_balance <= 0 || $pharmacy_balance < $ramount) {
                            $update_errorMessage = " Not enough balance" . $errorRefreshMessage;
                        }
                        
                        # with enough balance to top up ...

                        else {

                            # modifying admin balance ...

                            $admin_balance = $adminResults->Balance;

                            $admin_pin = $adminResults->admin_pin;

                            $admin_balance += $ramount;

                            $admin_UpdateQuery = ' UPDATE `admin`
                                                SET `Balance` = :admin_balance
                                                WHERE `admin_pin` = :admin_pin
                            ';

                            $admin_UpdateStatement = $pdo->prepare($admin_UpdateQuery);
                            $admin_UpdateStatement->execute([
                                'admin_balance'   =>  $admin_balance,
                                'admin_pin'       =>  $admin_pin
                            ]);

                            # Modifying pharmacy ...

                            $balance = $pharmacy_Info->balance;

                            $balance -= $ramount;

                            $pharmacy_UpdateQuery = ' UPDATE `pharmacy`
                                                SET `balance` = :pharmacy_balance
                                                WHERE `pharmacy_tin` = :pharmacy_tin
                            ';

                            $pharmacy_UpdateStatement = $pdo->prepare($pharmacy_UpdateQuery);
                            $pharmacy_UpdateStatement->execute([
                                'pharmacy_balance' =>  $balance,
                                'pharmacy_tin'     =>  $user_id
                            ]);

                            if ($pharmacy_UpdateQuery && $admin_UpdateQuery) {

                                $sender_id = 'admin';
                                $receiver_id = $pharmacy_Info->pharmacy_tin;
                                $amount = $ramount;
                                $date_Sent = date('Y-m-d h:i:s');
                                $time_Sent = date('h:i:s');

                                # confirming the request ...

                                $sql_confirm_request = " UPDATE `request` SET `confirmed_date` = :confirm_date, 
                                                                            `confirmed_time` =:confirm_time, 
                                                                            `status` =:bstatus
                                                                        WHERE `activation_key` = :activation_key";

                                $request_confirmStatement = $pdo->prepare($sql_confirm_request);
                                $request_confirmStatement->execute([
                                    'confirm_date'   =>  $date_Sent,
                                    'confirm_time'   =>  $time_Sent,
                                    'bstatus'        =>  'confirmed',
                                    'activation_key' =>  $cpin
                                ]);

                                # notifications ...

                                $sql_insert_notification = " INSERT INTO `notification_all`(`date_sent`, `time_sent`, `receiver_id`, `sender_id`, `amount`, `action`, `status`) VALUES (:date_sent, :time_sent, :receiver_id, :sender_id, :amount, :naction, :astatus)";

                                $notification_InsertStatement = $pdo->prepare($sql_insert_notification);
                                $notification_InsertStatement->execute([
                                    'date_sent'     =>  $date_Sent,
                                    'time_sent'     =>  $time_Sent,
                                    'receiver_id'   =>  $receiver_id,
                                    'sender_id'     =>  $sender_id,
                                    'amount'        =>  $amount,
                                    'naction'       =>  'transfer',
                                    'astatus'       =>  'unread'
                                ]);

                                if ($sql_insert_notification && $sql_confirm_request) {
                                    $update_successMessage = " Withdraw Successful" . $successRefreshMessage;
                                }

                                else {
                                    $update_errorMessage = " Failed to confirm" . $errorRefreshMessage;
                                }
                            }

                            else {
                                $update_errorMessage = " Failed to withdraw" . $errorRefreshMessage;
                            }
                        }
                    }

                    # otherwise cancel the process ...

                    else {
                        $update_errorMessage = " Mismatch" . $errorRefreshMessage;
                    }
                }
            }
        }

        # otherwise wrong activation key 

        else {
            $update_errorMessage = " No request made" . $errorRefreshMessage;
        }
    }
?>

<?php 
    include 'include/pharmacy_front.html';
?>