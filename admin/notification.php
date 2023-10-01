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

    # Calculating Each Number of Users, Cards, pharmacy, cashiers and so on...
    $sql_cashier = 'SELECT * FROM cashier';
    $sql_patient = 'SELECT * FROM patient';
    # error and success alerts

    $notify_updateSuccessMessage = "";
    $notify_updateErrorMessage = "";
    $notify_deleteSuccessMessage = "";
    $notify_deleteErrorMessage = "";

    $statement = $pdo->prepare($sql_cashier);
    $statement->execute();

    $statement_patient = $pdo->prepare($sql_patient);
    $statement_patient -> execute();

    # Getting The number of cashiers, Cards, cashier...
    $cashiersCount = $statement->rowCount();
    $patientCount = $statement_patient->rowCount();

    # Getting Admin Info. for update form...

    $adminFetchQuery = 'SELECT * FROM `admin` WHERE `admin_ID` = :adminid';
    $adminFetchStatement = $pdo->prepare($adminFetchQuery);
    $adminFetchStatement->execute([
        'adminid' => $admin_ID
    ]);
    $adminResults = $adminFetchStatement->fetch();

    # Getting user notifications

    $admin_notifyFetchQuery = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :nadmin OR `sender_id` =:nsadmin ORDER BY `date_sent` AND `time_sent` DESC';
    $admin_notifyFetchStatement = $pdo->prepare($admin_notifyFetchQuery);
    $admin_notifyFetchStatement->execute([
        'nadmin'     => 'admin',
        'nsadmin'    => 'admin'
    ]);
    $admin_notifyResults = $admin_notifyFetchStatement->fetchAll();

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