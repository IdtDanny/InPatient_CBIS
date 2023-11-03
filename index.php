<?php

    session_start();

    # Including The Connection...
    require_once 'public/config/connection.php';

    # Variable Declaration...
    $sessionToken ='';
    $error_message ='';
    $user_details ='';
    $user_balance ='';
    $user_detail ='';
    $patientResults = '';
    $patientLocation = '';
    $busy_successMessage = '';
    $cashier_errorMessage = '';
    $cashier_deleteErrorMessage = '';
    $update_errorMessage = '';
    $cashier_successMessage = '';
    $cashier_successMessage = '';
    $cashier_deleteSuccessMessage = '';
    $update_successMessage = '';
    $busy_errorMessage = '';

    # Getting Data From Form...

    if (isset($_POST['signin'])) {

        # Form Variables...
        $username = $_POST['username'];
        $password = $_POST['password'];

        # Getting The Hashed Password...
        $hashedPassword = md5($password);

        $sessionToken = addslashes($sessionToken);

        # Checking for User Existence...
        $query = 'SELECT * FROM `admin` WHERE `admin_username` = :username';

        # PDO Prepare & Execution of the query...
        $statement = $pdo->prepare($query);
        $statement->execute([
            'username' => $username
        ]);
        $usersCount = $statement->rowCount();

        # if admin is found

        if ($usersCount > 0) {
            $admin = $statement->fetch();
            if ($username == $admin->admin_username && ($password == $admin->admin_password || $hashedPassword == $admin->admin_password)) {
                $page = 'admin/#dashboard';
                $_SESSION['sessionToken'] = $admin;
                header('location:'.$page);
            }
            else {
                $error_message=" Incorrect Username or Password";
            }
        }

        # when not found, check in cashiers ...

        else if ($usersCount == 0) {

            # Checking for cashier Account Existence...

            $query_2 = 'SELECT * FROM `cashier` WHERE `cashier_username` = :username AND `role` = :brole';

            # PDO Prepare & Execution of the query...
            $statement = $pdo->prepare($query_2);
            $statement->execute([
                'username'  => $username,
                'brole'     => 'cashier'
            ]);
            $cashierCount = $statement->rowCount();

            # cashier is found 

            if ($cashierCount > 0) {
                $cashier = $statement->fetch();

                # check if cashier account is activated

                if ($cashier->status == 'active') {

                    if ($username == $cashier->cashier_username && ($password == $cashier->cashier_password || $hashedPassword == $cashier->cashier_password)) {
                        $page = 'cashier/#dashboard';
                        $_SESSION['sessionToken'] = $cashier;
                        header('location:'.$page);
                    }
                    else {
                        $error_message=" Incorrect Username or Password";
                    }
                }

                # otherwise contact for activation ...

                else {
                    $error_message=" Request for Activation";
                }
            }

            # if cashier is not found, look in receptionist ...

            else {

                # Checking for receptionist Account Existence...

                $query_r = 'SELECT * FROM `cashier` WHERE `cashier_username` = :username AND `role` = :brole';

                # PDO Prepare & Execution of the query...
                $statement = $pdo->prepare($query_r);
                $statement->execute([
                    'username'  => $username,
                    'brole'     => 'receptionist'
                ]);
                $receptionistCount = $statement->rowCount();

                # receptionist is found 

                if ($receptionistCount > 0) {

                    $receptionist = $statement->fetch();

                    # check if receptionist account is activated ...

                    if ($receptionist->status == 'active') {

                        if ($username == $receptionist->cashier_username && ($password == $receptionist->cashier_password || $hashedPassword == $receptionist->cashier_password)) {
                            $page = 'receptionist/#dashboard';
                            $_SESSION['sessionToken'] = $receptionist;
                            header('location:'.$page);
                        }
                        else {
                            $error_message=" Incorrect Username or Password";
                        }
                    }

                    # otherwise contact for activation ...

                    else {
                        $error_message=" Request for Activation";
                    }
                }

                # if receptionist is not found, look in pharmacist ...

                else {

                    # Checking for pharmacist Account Existence...

                    $query_3 = 'SELECT * FROM `pharmacy` WHERE `pharmacy_tin` = :pharmacytin';
                    
                    $pharmacyname = $username; 

                    # PDO Prepare & Execution of the query...
                    $statement = $pdo->prepare($query_3);
                    $statement->execute([
                        'pharmacytin' => $pharmacyname
                    ]);
                    $pharmacyCount = $statement->rowCount();

                    # if pharmacist is found ...
                
                    if ($pharmacyCount > 0) {

                        $pharmacy = $statement->fetch();

                        # check if pharmacy is approved ...

                        if ($pharmacy->approved_by == 'N/A') {
                            $error_message=" Request for pharmacy Approval";
                        }

                        # otherwise proceed with pharmacy login ...

                        else {

                            if ($username == $pharmacy -> pharmacy_tin && ($password == $pharmacy -> pharmacy_password || $hashedPassword == $pharmacy -> pharmacy_password)) {
                                $page = 'pharmacy/#dashboard';
                                $_SESSION['sessionToken'] = $pharmacy;
                                header('location:'.$page);
                            }
                            else {
                                $error_message=" Incorrect TIN or Password";
                            }
                        }
                    }

                    # if pharmacist is not found, look in doctors ...

                    else {

                        # Checking for doctor Account Existence...

                        $query_4 = 'SELECT * FROM `authorized` WHERE `role` = :doctor AND `authorized_username` = :username';
                        
                        $doctor_username = $username; 

                        # PDO Prepare & Execution of the query...
                        $statement = $pdo->prepare($query_4);
                        $statement->execute([
                            'doctor'    => 'doctor',
                            'username'  => $doctor_username
                        ]);
                        $authorizedCount = $statement->rowCount();

                        # if doctor is found ...
                    
                        if ($authorizedCount > 0) {

                            $authorized = $statement->fetch();

                            # otherwise proceed with authorized login ...

                            if ($username == $authorized -> authorized_username && ($password == $authorized -> authorized_password || $hashedPassword == $authorized -> authorized_password)) {
                                $page = 'doctor/#dashboard';
                                $_SESSION['sessionToken'] = $authorized;
                                header('location:'.$page);
                            }
                            else {
                                $error_message=" Incorrect Username or Password";
                            }
                        }

                        # if doctor is not found, look in nurse ...

                        else {

                            # Checking for doctor Account Existence...
        
                            $query_5 = 'SELECT * FROM `authorized` WHERE `role` = :nurse AND `authorized_username` = :username';
                            
                            $nurse_username = $username; 
        
                            # PDO Prepare & Execution of the query...
                            $statement = $pdo->prepare($query_5);
                            $statement->execute([
                                'nurse'     => 'nurse',
                                'username'  => $nurse_username
                            ]);
                            $authorizedCount = $statement->rowCount();
        
                            # if doctor is found ...
                        
                            if ($authorizedCount > 0) {
        
                                $authorized = $statement->fetch();
        
                                # otherwise proceed with authorized login ...
        
                                if ($username == $authorized -> authorized_username && ($password == $authorized -> authorized_password || $hashedPassword == $authorized -> authorized_password)) {
                                    $page = 'nurse/#dashboard';
                                    $_SESSION['sessionToken'] = $authorized;
                                    header('location:'.$page);
                                }

                                else {
                                    $error_message=" Incorrect Username or Password";
                                }
                            }
        
                            # if nurse is not found, not found ...
        
                            else {
                                $error_message=" User not found";
                            }
                        }
                    }
                }
            }   
        }
    }

    # refreshing message ...
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to continue </span><a href='index.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to see the change </span><a href='index.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # new cashier application form ...

    if (isset($_POST['cashierApply'])) {

        $cashier_name = $_POST['cashier_name'];
        $cashier_uname = $_POST['cashier_uname'];
        $cashier_mail = $_POST['cashier_mail'];
        $cashier_tel = $_POST['cashier_tel'];
        $cashier_gender = $_POST['agender'];
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
            $target_dir = "public/profile/";
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

                    $sql_insert_cashier = " INSERT INTO `cashier`(`created_at`, `cashier_name`, `cashier_gender`, `cashier_username`, `cashier_tel`, `cashier_mail`, `cashier_password`, `cashier_pin`, `photo`, `cashier_balance`, `status`) VALUES(:adate, :cashier_name, :cashier_gender, :cashier_uname, :cashier_tel, :cashier_mail, :cashier_password, :cashier_pin, :photo, :balance, :bstatus)";

                    $cashier_InsertStatement = $pdo->prepare($sql_insert_cashier);
                    $cashier_InsertStatement->execute([
                        'adate'             =>  $date_Sent,
                        'cashier_name'        =>  $cashier_name,
                        'cashier_gender'      =>  $cashier_gender,
                        'cashier_uname'       =>  $cashier_uname,
                        'cashier_tel'         =>  $cashier_tel,  
                        'cashier_mail'        =>  $cashier_mail,
                        'cashier_password'    =>  $hashed_Password,
                        'cashier_pin'         =>  $cashier_pin,
                        'photo'             =>  $cashier_profile,
                        'balance'           =>  '0',
                        'bstatus'           =>  'inactive'
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
                                $cashier_successMessage = " Registered Pin: ". $cashier_pin . $successRefreshMessage;
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

    # Registering new pharmacy ...

    if (isset($_POST['pharmacyApply'])) {
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

        # checking if pharmacy exists
        $pharmacy_existFetchQuery = 'SELECT * FROM `pharmacy` WHERE `pharmacy_name` = :pharmacy_name';
        $pharmacy_existFetchStatement = $pdo->prepare($pharmacy_existFetchQuery);
        $pharmacy_existFetchStatement->execute([
            'pharmacy_name' => $pharmacy_name
        ]);
        $pharmacy_existResults = $pharmacy_existFetchStatement->fetch();

        # if exist, pop some message
        if ($pharmacy_existResults) {
            $busy_errorMessage = " Already registered" . $errorRefreshMessage;
        }

        # if not, proceed with application pharmacy
        else {

            $target_dir = "public/profile/";
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
            if ($_FILES["pharmacy_profile"]["size"] > 4000000) {
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

                    $sql_insert_pharmacy = " INSERT INTO `pharmacy`(`Date`, `pharmacy_name`, `pharmacy_tin`, `pharmacy_mail`, `pharmacy_password`, `pharmacy_pin`, `pharmacy_type`, `balance`, `status`, `photo`, `approved_by`) VALUES(:bdate, :pharmacy_name, :pharmacy_tin, :pharmacy_mail, :pharmacy_password, :pharmacy_pin, :pharmacy_type, :balance, :bstatus, :photo, :approved_by)";

                    $pharmacy_InsertStatement = $pdo->prepare($sql_insert_pharmacy);
                    $pharmacy_InsertStatement->execute([
                        'bdate'             =>  $date_Sent,
                        'pharmacy_name'     =>  $pharmacy_name,
                        'pharmacy_tin'      =>  $pharmacy_tin,
                        'pharmacy_mail'     =>  $pharmacy_mail,
                        'pharmacy_password' =>  $hashed_Password,
                        'pharmacy_pin'      =>  $pharmacy_pin,
                        'pharmacy_type'     =>  $pharmacy_type,
                        'balance'           =>  '0',
                        'bstatus'           =>  'Inactive',
                        'photo'             =>  $pharmacy_profile,
                        'approved_by'       =>  'N/A'
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
                                $busy_successMessage = " Registered, TIN: ". $pharmacy_tin . " Refresh!";
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
    }

    # Checking user_balance ...

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

?>

<?php 
    include 'include/index_front.html';
?>