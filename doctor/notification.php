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
    $photo = $_SESSION['sessionToken']->photo;

    # error and success alerts

    $notify_updateSuccessMessage = "";
    $notify_updateErrorMessage = "";
    $notify_deleteSuccessMessage = "";
    $notify_deleteErrorMessage = "";

    # Getting authorized Info. for update form...

    $authorizedFetchQuery = 'SELECT * FROM `authorized` WHERE `auID` = :auID';
    $authorizedFetchStatement = $pdo->prepare($authorizedFetchQuery);
    $authorizedFetchStatement->execute([
        'auID' => $auID
    ]);
    $authorizedResults = $authorizedFetchStatement->fetch();

    # Calculating Each Number of Users, Cards, authorized, authorizeds and so on...
    
    $sql_authorized = 'SELECT * FROM authorized';
    $sql_patient = 'SELECT * FROM patient';
    $sql_authorized = 'SELECT * FROM authorized';

    $statement = $pdo->prepare($sql_authorized);
    $statement->execute();

    $statement_patient = $pdo->prepare($sql_patient);
    $statement_patient -> execute();

    $statement_authorized = $pdo->prepare($sql_authorized);
    $statement_authorized -> execute();

    # Getting The number of authorizeds, Cards, authorized...
    $authorizedsCount = $statement->rowCount();
    $registered_patient = $statement_patient->rowCount();
    $registered_authorized = $statement_authorized -> rowCount();

    # Fetching authorized info ...

    $authorized_FetchQuery = 'SELECT * FROM `authorized` ORDER BY `created_at` DESC';
    $authorized_FetchStatement = $pdo->prepare($authorized_FetchQuery);
    $authorized_FetchStatement->execute();
    $authorized_Result = $authorized_FetchStatement->fetchAll();

    # Getting user notifications

    $authorized_notifyFetchQuery = 'SELECT * FROM `notification_all` WHERE `receiver_id` = :authorized_pin OR `sender_id` =:sauthorized_pin ORDER BY `date_sent` AND `time_sent` DESC';
    $authorized_notifyFetchStatement = $pdo->prepare($authorized_notifyFetchQuery);
    $authorized_notifyFetchStatement->execute([
        'authorized_pin'     => $authorized_pin,
        'sauthorized_pin'    => $authorized_pin
    ]);
    $authorized_notifyResults = $authorized_notifyFetchStatement->fetchAll();

    # refreshing message
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to continue </span><a href='notification.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>&nbsp; Refresh to see the change </span><a href='notification.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # getting authorized delete response
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
            $notify_updateErrorMessage = " Could not update, check authorized id" . $errorRefreshMessage;
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
            $notify_deleteErrorMessage = " Could not delete, check authorized id" . $errorRefreshMessage;
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