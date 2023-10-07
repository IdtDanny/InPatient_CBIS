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
    $photo = $_SESSION['sessionToken']->photo;

    # error and success alerts

    $notify_updateSuccessMessage = "";
    $notify_updateErrorMessage = "";
    $notify_deleteSuccessMessage = "";
    $notify_deleteErrorMessage = "";

    # Getting cashier Info. for update form...

    $cashierFetchQuery = 'SELECT * FROM `cashier` WHERE `caID` = :caID';
    $cashierFetchStatement = $pdo->prepare($cashierFetchQuery);
    $cashierFetchStatement->execute([
        'caID' => $caID
    ]);
    $cashierResults = $cashierFetchStatement->fetch();

    # Calculating Each Number of Users, Cards, cashier, cashiers and so on...
    
    $sql_cashier = 'SELECT * FROM cashier';
    $sql_patient = 'SELECT * FROM patient';
    $sql_cashier = 'SELECT * FROM cashier';

    $statement = $pdo->prepare($sql_cashier);
    $statement->execute();

    $statement_patient = $pdo->prepare($sql_patient);
    $statement_patient -> execute();

    $statement_cashier = $pdo->prepare($sql_cashier);
    $statement_cashier -> execute();

    # Getting The number of cashiers, Cards, cashier...
    $cashiersCount = $statement->rowCount();
    $registered_patient = $statement_patient->rowCount();
    $registered_cashier = $statement_cashier -> rowCount();

    # Fetching cashier info ...

    $cashier_FetchQuery = 'SELECT * FROM `cashier` ORDER BY `created_at` DESC';
    $cashier_FetchStatement = $pdo->prepare($cashier_FetchQuery);
    $cashier_FetchStatement->execute();
    $cashier_Result = $cashier_FetchStatement->fetchAll();

    # Getting user notifications

    $cashier_notifyFetchQuery = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :cashier_pin OR `sender_id` =:scashier_pin ORDER BY `date_sent` AND `time_sent` DESC';
    $cashier_notifyFetchStatement = $pdo->prepare($cashier_notifyFetchQuery);
    $cashier_notifyFetchStatement->execute([
        'cashier_pin'     => $cashier_pin,
        'scashier_pin'    => $cashier_pin
    ]);
    $cashier_notifyResults = $cashier_notifyFetchStatement->fetchAll();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to continue </span><a href='notification.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to see the change </span><a href='notification.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # getting cashier delete response
    if (isset($_GET['vnID'])) {
        $dnID = $_GET['vnID'];
        $sql_nupdate = 'UPDATE `notification` SET `status` =:nread WHERE `nID` = :nid';

        # PDO Prep & Exec..
        $update_notify = $pdo->prepare($sql_nupdate);
        $update_notify->execute([
            'nread'    => 'read',
            'nid'       =>  $dnID
        ]);

        if ($sql_nupdate) {
            $notify_updateSuccessMessage = " Viewed" . $successRefreshMessage;
        }
        else {
            $notify_updateErrorMessage = " Could not update, check cashier id" . $errorRefreshMessage;
        }

    }

    # getting notification delete response

    if (isset($_GET['dnID'])) {
        $dnID = $_GET['dnID'];
        $sql_ndelete = 'DELETE FROM `notification_all` WHERE nID = :nid';

        # PDO Prep & Exec..
        $delete_notify = $pdo->prepare($sql_ndelete);
        $delete_notify->execute([
            'nid'  =>  $dnID
        ]);

        if ($sql_ndelete) {
            $notify_deleteSuccessMessage = " Deleted Successful" . $successRefreshMessage;
        }
        else {
            $notify_deleteErrorMessage = " Could not delete, check cashier id" . $errorRefreshMessage;
        }
    }

    # view notification response

    if (isset($_GET['vnID'])) {
        $vnID = $_GET['vnID'];
        $sql_nview = 'UPDATE `notification_all` SET `status` = :nstatus WHERE `nID` = :nid';

        # PDO Prep & Exec..
        $view_notify = $pdo->prepare($sql_nview);
        $view_notify->execute([
            'nstatus'  =>  'read',
            'nid'      =>  $vnID
        ]);

        if ($sql_nview) {
            $notify_viewSuccessMessage = " Viewed Successful" . $successRefreshMessage;
        }
        else {
            $notify_viewErrorMessage = " Could not view, check id" . $errorRefreshMessage;
        }
    }
    
?>

<?php 
    include 'include/notification_front.html';
?>