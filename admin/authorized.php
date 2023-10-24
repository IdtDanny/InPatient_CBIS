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

    # Calculating Each Number of Users, Cards, pharmacy, authorizeds and so on...
    $sql_authorized = 'SELECT * FROM authorized';
    $sql_patient = 'SELECT * FROM patient';
    $sql_pharmacy = 'SELECT * FROM pharmacy';
    $sql_pharmacy_gas = 'SELECT * FROM `pharmacy` WHERE `pharmacy_type` = :btype';
    $sql_pharmacy_others = 'SELECT * FROM `pharmacy` WHERE `pharmacy_type` = :otype';
    // $usedCardsSql = 'SELECT * FROM `patient` WHERE `Approve` = :approve';

    $statement = $pdo->prepare($sql_authorized);
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

    # Getting The number of authorizeds, Cards, pharmacy...
    $authorizedsCount = $statement->rowCount();
    $registered_patient = $statement_patient->rowCount();
    $registered_pharmacy = $statement_pharmacy -> rowCount();
    $gas_pharmacy = $statement_pharmacy_gas -> rowCount();
    $others_pharmacy = $statement_pharmacy_others -> rowCount();

    # Fetching pharmacy info ...

    $pharmacy_FetchQuery = 'SELECT * FROM `pharmacy` ORDER BY `Date` DESC';
    $pharmacy_FetchStatement = $pdo->prepare($pharmacy_FetchQuery);
    $pharmacy_FetchStatement->execute();
    $pharmacy_Result = $pharmacy_FetchStatement->fetchAll();

    # Fetching authorizeds info ...

    $authorized_FetchQuery = 'SELECT * FROM `authorized` ORDER BY `created_at` DESC';
    $authorized_FetchStatement = $pdo->prepare($authorized_FetchQuery);
    $authorized_FetchStatement->execute([]);
    $authorized_Result = $authorized_FetchStatement->fetchAll();

    # Fetching authorizeds info ...

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

    # Registering new authorized

    if (isset($_POST['authorizedApply'])) {

        $authorized_name = $_POST['authorized_name'];
        $authorized_uname = $_POST['authorized_uname'];
        $authorized_tel = $_POST['authorized_tel'];
        $authorized_gender = $_POST['agender'];
        $authorized_mail = $_POST['authorized_mail'];
        $brole = $_POST['brole'];
        $date_Sent = date('Y-m-d h:i:s');
        $authorized_pin = rand(1000,9999);
        $password= $authorized_uname.'-'.$authorized_pin;
        $hashed_Password = md5($password);

        # checking if authorized exists
        $authorized_existFetchQuery = 'SELECT * FROM `authorized` WHERE `authorized_name` =:authorized_name';
        $authorized_existFetchStatement = $pdo->prepare($authorized_existFetchQuery);
        $authorized_existFetchStatement->execute([
            'authorized_name' => $authorized_name
        ]);
        $authorized_existResults = $authorized_existFetchStatement->fetch();

        # if exist, pop some message ...

        if ($authorized_existResults) {
            $authorized_errorMessage = " Already registered" . $errorRefreshMessage;
        }

        # otherwise proceed with registration process ...

        else {
            # Inserting pharmacy...

            $sql_insert_authorized = " INSERT INTO `authorized` (
                `created_at`,
                `authorized_name`, 
                `authorized_gender`, 
                `authorized_username`, 
                `authorized_tel`, 
                `authorized_mail`, 
                `authorized_password`, 
                `authorized_pin`, 
                `photo`, 
                `status`,
                `role`) 
                                            VALUES(
                                                :adate, 
                                                :auth_name, 
                                                :auth_gender, 
                                                :auth_username, 
                                                :auth_tel, 
                                                :auth_mail, 
                                                :auth_password, 
                                                :auth_pin, 
                                                :photo, 
                                                :bstatus,
                                                :brole)";

            $authorized_InsertStatement = $pdo->prepare($sql_insert_authorized);
            $authorized_InsertStatement->execute([
                'adate'          =>  $date_Sent,
                'auth_name'      =>  $authorized_name,
                'auth_gender'    =>  $authorized_gender,
                'auth_username'  =>  $authorized_uname,
                'auth_tel'       =>  $authorized_tel,
                'auth_mail'      =>  $authorized_mail,
                'auth_password'  =>  $hashed_Password,
                'auth_pin'       =>  $authorized_pin,
                'photo'          =>  'optional',
                'bstatus'        =>  'active',
                'brole'          =>  $brole
            ]);

            if ($sql_insert_authorized) {
                        $authorized_successMessage = " Receptionist Registered, Pin: ". $authorized_pin . $successRefreshMessage;
            }
            else {
                $authorized_errorMessage = " Could not register" . $errorRefreshMessage;
            }
        }
    }

    # getting authorized delete response
    if (isset($_GET['dauID'])) {
        $dauID = $_GET['dauID'];
        $sql_adelete = 'DELETE FROM `authorized` WHERE auID = :auID';

        # PDO Prep & Exec..
        $delete_authorized = $pdo->prepare($sql_adelete);
        $delete_authorized->execute([
            'auID'  =>  $dauID
        ]);

        if ($sql_adelete) {
            $authorized_deleteSuccessMessage = " Deleted Successful" . $successRefreshMessage;
        }
        else {
            $authorized_deleteErrorMessage = " Could not delete, check authorized id" . $errorRefreshMessage;
        }

    }

    # Recharge authorized Operation...

    if (isset($_POST['rechargeauthorized'])) {

        $cpin = $_POST['cpin'];
        $authorized_username = $_POST['authorized_username'];
        $ramount = $_POST['ramount'];

        # Checking for pharmacyTin ...

        $fetch_UserQuery='SELECT * FROM `authorized` WHERE `authorized_username` = :authorized_name AND `authorized_pin` = :cpin';
        $fetch_UserStatement = $pdo->prepare($fetch_UserQuery);
        $fetch_UserStatement->execute([
            'authorized_name' => $authorized_username,
            'cpin'       => $cpin
        ]);

        $authorized_Info = $fetch_UserStatement -> fetch();

        $authorizedCount = $fetch_UserStatement->rowCount();

        if ($authorizedCount > 0 ) {

            # Modifying authorized ...

            $balance = $authorized_Info->authorized_balance;

            $balance += $ramount;

            $authorized_UpdateQuery = ' UPDATE `authorized`
                                SET `authorized_balance` = :authorized_balance
                                WHERE `authorized_pin` = :authorized_pin
            ';

            $authorized_UpdateStatement = $pdo->prepare($authorized_UpdateQuery);
            $authorized_UpdateStatement->execute([
                'authorized_balance'   =>  $balance,
                'authorized_pin'       =>  $cpin
            ]);

            if ($authorized_UpdateQuery) {
                $update_successMessage = " Recharged Successful" . $successRefreshMessage;
            }
        }
        else {
            $update_errorMessage = " Unknown Pin" . $errorRefreshMessage;
        }

    }

    # getting authorized activation response

    if (isset($_GET['ApID'])) {
        $dauID = $_GET['ApID'];
        $sql_active = 'UPDATE `patient` SET `status` =:active WHERE pID = :auID';

        # PDO Prep & Exec..
        $active_authorized = $pdo->prepare($sql_active);
        $active_authorized->execute([
            'active' => 'active',
            'auID'    =>  $dauID
        ]);

        if ($sql_active) {
            $update_successMessage = " Activated Successful" . $successRefreshMessage;
        }
        else {
            $update_errorMessage = " Could not activate, check authorized id" . $errorRefreshMessage;
        }

    }
?>

<?php 
    include 'include/authorized_front.html';
?>