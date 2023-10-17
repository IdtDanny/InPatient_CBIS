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

    $cashier_FetchQuery = 'SELECT * FROM `cashier` WHERE `role` = :roles ORDER BY `created_at` DESC';
    $cashier_FetchStatement = $pdo->prepare($cashier_FetchQuery);
    $cashier_FetchStatement->execute([ 'roles' => 'receptionist' ]);
    $cashier_Result = $cashier_FetchStatement->fetchAll();

    # Fetching cashiers info ...

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

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to continue </span><a href='authorized.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to see the change </span><a href='authorized.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # Registering new cashier

    if (isset($_POST['cashierApply'])) {

        $cashier_name = $_POST['cashier_name'];
        $cashier_uname = $_POST['cashier_uname'];
        $cashier_tel = $_POST['cashier_tel'];
        $cashier_gender = $_POST['agender'];
        $cashier_mail = $_POST['cashier_mail'];
        $cashier_district = $_POST['cashier_district'];
        $cashier_sector = $_POST['cashier_sector'];
        $date_Sent = date('Y-m-d h:i:s');
        $cashier_pin = rand(1000,9999);
        $password= $cashier_uname.'-'.$cashier_pin;
        $hashed_Password = md5($password);

        # checking if cashier exists
        $cashier_existFetchQuery = 'SELECT * FROM `cashier` WHERE `cashier_name` = :cashier_name';
        $cashier_existFetchStatement = $pdo->prepare($cashier_existFetchQuery);
        $cashier_existFetchStatement->execute([
            'cashier_name' => $cashier_name
        ]);
        $cashier_existResults = $cashier_existFetchStatement->fetch();

        # if exist, pop some message
        if ($cashier_existResults) {
            $cashier_errorMessage = " Already registered" . $errorRefreshMessage;
        }

        # otherwise proceed with registration process
        else {
            $target_dir = "../public/profile/";
            $target_file = $target_dir . basename($_FILES['cashier_profile']['name']);
            $cashier_profile = $_FILES['cashier_profile']['name'];
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            
            # Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["cashier_profile"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            }
            else {
                $cashier_errorMessage = " File is not an image.";
                $uploadOk = 0;
            }
            
            # Check file size
            if ($_FILES["cashier_profile"]["size"] > 400000) {
                $cashier_errorMessage = " Sorry, your file is too large." . $errorRefreshMessage;
                $uploadOk = 0;
            }
            
            # Allow certain file formats
            else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                $cashier_errorMessage = " Sorry, only JPG, JPEG, PNG & GIF files are allowed." . $errorRefreshMessage;
                $uploadOk = 0;
            }
            
            # Check if $uploadOk is set to 0 by an error
            else if ($uploadOk == 0) {
                $cashier_errorMessage = " Sorry, your file was not uploaded." . $errorRefreshMessage;
            } 
            else {
                if (move_uploaded_file($_FILES["cashier_profile"]["tmp_name"], $target_file)) {

                    # Inserting pharmacy...

                    $sql_insert_cashier = " INSERT INTO `cashier`(`created_at`, `cashier_name`, `cashier_username`, `cashier_gender`, `cashier_tel`, `cashier_mail`, `cashier_password`, `cashier_pin`, `photo`, `cashier_balance`, `status`, `role`) VALUES(:adate, :cashier_name, :cashier_uname, :cashier_gender, :cashier_tel, :cashier_mail, :cashier_password, :cashier_pin, :photo, :balance, :bstatus, :roles)";

                    $cashier_InsertStatement = $pdo->prepare($sql_insert_cashier);
                    $cashier_InsertStatement->execute([
                        'adate'             =>  $date_Sent,
                        'cashier_name'      =>  $cashier_name,
                        'cashier_uname'     =>  $cashier_uname,
                        'cashier_gender'    =>  $cashier_gender,
                        'cashier_tel'       =>  $cashier_tel,
                        'cashier_mail'      =>  $cashier_mail,
                        'cashier_password'  =>  $hashed_Password,
                        'cashier_pin'       =>  $cashier_pin,
                        'photo'             =>  $cashier_profile,
                        'balance'           =>  '0',
                        'bstatus'           =>  'active',
                        'roles'             =>  'receptionist'
                    ]);

                    if ($sql_insert_cashier) {

                        # Getting Admin Info. for update form...

                        $cashier_locationFetchQuery = 'SELECT * FROM `cashier` WHERE `cashier_pin` = :apin';
                        $cashier_locationFetchStatement = $pdo->prepare($cashier_locationFetchQuery);
                        $cashier_locationFetchStatement->execute([
                            'apin' => $cashier_pin
                        ]);
                        $cashier_locationResults = $cashier_locationFetchStatement->fetch();
                        $caID = $cashier_locationResults->caID;

                        $sql_insert_location = "  INSERT INTO `cashier_location`(`caID`, `cashier_name`, `district`, `sector`) VALUES(:caID, :cashier_name, :district, :sector) ";
                        $location_InsertStatement = $pdo->prepare($sql_insert_location);
                        $location_InsertStatement->execute([
                                'caID'           =>  $caID,
                                'cashier_name'    =>  $cashier_name,
                                'district'      =>  $cashier_district,
                                'sector'        =>  $cashier_sector
                        ]);
                        if ($sql_insert_cashier && $sql_insert_location) {
                                $cashier_successMessage = " Receptionist Registered, Pin: ". $cashier_pin . $successRefreshMessage;
                        }
                    }
                    else {
                        $cashier_errorMessage = " Could not register" . $errorRefreshMessage;
                    }
                } 
                else {
                    $cashier_errorMessage = " Something went wrong" . $errorRefreshMessage;
                }
            }
        }
    }

    # getting cashier delete response
    if (isset($_GET['dpID'])) {
        $dcaID = $_GET['dpID'];
        $sql_adelete = 'DELETE FROM `patient` WHERE pID = :pID';
        $sql_lodelete = 'DELETE FROM `patient_location` WHERE pID = :pID';

        # PDO Prep & Exec..
        $delete_patient = $pdo->prepare($sql_adelete);
        $delete_patient->execute([
            'pID'  =>  $dcaID
        ]);

        $delete_patient_location = $pdo->prepare($sql_lodelete);
        $delete_patient_location->execute([
            'pID'  =>  $dcaID
        ]);

        if ($sql_adelete && $sql_lodelete) {
            $patient_deleteSuccessMessage = " Deleted Successful" . $successRefreshMessage;
        }
        else {
            $patient_deleteErrorMessage = " Could not delete, check cashier id" . $errorRefreshMessage;
        }

    }

    # Recharge cashier Operation...

    if (isset($_POST['rechargecashier'])) {

        $cpin = $_POST['cpin'];
        $cashier_username = $_POST['cashier_username'];
        $ramount = $_POST['ramount'];

        # Checking for pharmacyTin ...

        $fetch_UserQuery='SELECT * FROM `cashier` WHERE `cashier_username` = :cashier_name AND `cashier_pin` = :cpin';
        $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
        $fetch_UserStatement->execute([
            'cashier_name' => $cashier_username,
            'cpin'       => $cpin
        ]);

        $cashier_Info = $fetch_UserStatement -> fetch();

        $cashierCount = $fetch_UserStatement->rowCount();

        if ($cashierCount > 0 ) {

            # Modifying cashier ...

            $balance = $cashier_Info->cashier_balance;

            $balance += $ramount;

            $cashier_UpdateQuery = ' UPDATE `cashier`
                                SET `cashier_balance` = :cashier_balance
                                WHERE `cashier_pin` = :cashier_pin
            ';

            $cashier_UpdateStatement = $pdo->prepare($cashier_UpdateQuery);
            $cashier_UpdateStatement->execute([
                'cashier_balance'   =>  $balance,
                'cashier_pin'       =>  $cpin
            ]);

            if ($cashier_UpdateQuery) {
                $update_successMessage = " Recharged Successful" . $successRefreshMessage;
            }
        }
        else {
            $update_errorMessage = " Unknown Pin" . $errorRefreshMessage;
        }

    }

    # getting cashier activation response

    if (isset($_GET['ApID'])) {
        $dcaID = $_GET['ApID'];
        $sql_active = 'UPDATE `patient` SET `status` =:active WHERE pID = :caID';

        # PDO Prep & Exec..
        $active_cashier = $pdo->prepare($sql_active);
        $active_cashier->execute([
            'active' => 'active',
            'caID'    =>  $dcaID
        ]);

        if ($sql_active) {
            $update_successMessage = " Activated Successful" . $successRefreshMessage;
        }
        else {
            $update_errorMessage = " Could not activate, check cashier id" . $errorRefreshMessage;
        }

    }
?>

<?php 
    include 'include/authorized_front.html';
?>