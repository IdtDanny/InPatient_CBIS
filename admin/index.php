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

    # Updating Admin Information...

    if (isset($_POST['editinfo'])) {
        $new_Admin_Name = $_POST['admin-name'];
        $new_Admin_Mail = $_POST['admin-mail'];
        $new_Admin_Username = $_POST['admin-username'];
        $admin_Old_Password = $_POST['old-password'];
        $admin_New_Password = $_POST['new-password'];
        $admin_Confirm_password = $_POST['confirm-password'];

        # Checking for Password fields(if they are empty, It will only update the username or name only)...

        if (empty($admin_Old_Password)) {

            # Updating Query...

            $admin_Update_Query = 'UPDATE `admin`
                                    SET `admin_name` = :adminname,
                                        `admin_username` = :adminusername,
                                        `admin_email` = :adminmail
                                    WHERE `admin_ID` = :adminid
            ';

            $admin_Update_stmt = $pdo->prepare($admin_Update_Query);
            $admin_Update_stmt->execute([
                'adminname'     =>  $new_Admin_Name,
                'adminusername' =>  $new_Admin_Username,
                'adminmail'     =>  $new_Admin_Mail,
                'adminid'       =>  $admin_ID
            ]);
            $successMessage = " Username Edited Successfully";
        }

        else {

            # Checking if the old password match...

            $hashedpass = md5($admin_Old_Password);
            
            // $hashedpass = $admin_Old_Password;

            if ($adminResults->admin_password == $hashedpass || $adminResults->admin_password == $admin_Old_Password ) {

                if ($admin_New_Password == $admin_Confirm_password) {

                    # Update Query Including Passwords...

                    $admin_Update_Query = 'UPDATE `admin`
                                            SET `admin_name` = :adminname,
                                                `admin_username` = :adminusername,
                                                `admin_email` = :adminmail,
                                                `admin_password` = :adminpassword
                                            WHERE `admin_ID` = :adminid
                    ';

                    $admin_Update_stmt = $pdo->prepare($admin_Update_Query);
                    $admin_Update_stmt->execute([
                        'adminname'     =>  $new_Admin_Name,
                        'adminusername' =>  $new_Admin_Username,
                        'adminmail'     =>  $new_Admin_Mail,
                        'adminpassword' =>  md5($admin_New_Password),
                        'adminid'       =>  $admin_ID
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
        $target_file = $target_dir . basename($_FILES["admin-profile"]["name"]);
        $photo = $_FILES['admin-profile']['name'];
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        # Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["admin-profile"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        }
        else {
            $photo_errorMessage = " File is not an image.";
            $uploadOk = 0;
        }
        
        # Check if file already exists
        // if (file_exists($target_file)) {
        //     $photo_errorMessage = " Sorry, file already exists.";
        //     $uploadOk = 0;
        // }
        
        # Check file size
        if ($_FILES["admin-profile"]["size"] > 4000000) {
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
            if (move_uploaded_file($_FILES["admin-profile"]["tmp_name"], $target_file)) {        
                
                # Updating admin profile...
                $profile_update = 'UPDATE `admin` 
                                    SET `admin_profile` = :photo 
                                    WHERE `admin_ID` = :adminid
                                ';
        
                $admin_updateStatement = $pdo->prepare($profile_update);
                $admin_updateStatement->execute([
                                    'photo'     =>  $photo,
                                    'adminid'   =>  $adminResults->admin_ID
                                ]);
            
                if ($profile_update) {
                    $photo_successMessage = " Profile Edited";
                }
            } 
            else {
                $photo_errorMessage = " Sorry, there was an error uploading your file.";
            }
        }
    }
?>

<?php 
    include 'include/index_front.html';
?>