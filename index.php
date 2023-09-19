<?php

    session_start();

    # Including The Connection...
    require_once 'public/config/connection.php';

    # Variable Declaration...
    $sessionToken='';
    $error_message='';
    $user_balance='';

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

        # when not found, check in agents ...

        else if ($usersCount == 0) {

            # Checking for Agent Account Existence...

            $query_2 = 'SELECT * FROM `agent` WHERE `agent_username` = :username';

            # PDO Prepare & Execution of the query...
            $statement = $pdo->prepare($query_2);
            $statement->execute([
                'username' => $username
            ]);
            $agentCount = $statement->rowCount();

            # agent is found 

            if ($agentCount > 0) {
                $agent = $statement->fetch();

                # check if agent account is activated

                if ($agent->status == 'active') {

                    if ($username == $agent->agent_username && ($password == $agent->agent_password || $hashedPassword == $agent->agent_password)) {
                        $page = 'agent/#dashboard';
                        $_SESSION['sessionToken'] = $agent;
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

            # if agent is not found, look in business

            else {

                # Checking for Business Account Existence...

                $query_3 = 'SELECT * FROM `business` WHERE `business_tin` = :businesstin';
                
                $businessname = $username; 

                # PDO Prepare & Execution of the query...
                $statement = $pdo->prepare($query_3);
                $statement->execute([
                    'businesstin' => $businessname
                ]);
                $businessCount = $statement->rowCount();
            
                if ($businessCount > 0) {

                    $business = $statement->fetch();

                    # check if business is approved ...

                    if ($business->approved_by == 'N/A') {
                        $error_message=" Request for Business Approval";
                    }

                    # otherwise proceed with business login ...

                    else {

                        if ($username == $business -> business_tin && ($password == $business -> business_password || $hashedPassword == $business -> business_password)) {
                            $page = 'business/#dashboard';
                            $_SESSION['sessionToken'] = $business;
                            header('location:'.$page);
                        }
                        else {
                            $error_message=" Incorrect TIN or Password";
                        }
                    }
                }
                else {
                    $error_message=" User not found";
                }
            }   
        }
    }

    # refreshing message ...
    $errorRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to continue </span><a href='index.php' class='float-end fw-bold text-danger'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    $successRefreshMessage = "<span class='d-md-inline-block d-none'>, Refresh to see the change </span><a href='index.php' class='float-end fw-bold text-success'><i class='bi bi-arrow-clockwise me-3'></i></a>";

    # new agent application form ...

    if (isset($_POST['agentApply'])) {

        $agent_name = $_POST['agent_name'];
        $agent_uname = $_POST['agent_uname'];
        $agent_mail = $_POST['agent_mail'];
        $agent_tel = $_POST['agent_tel'];
        $agent_gender = $_POST['agender'];
        $agent_district = $_POST['agent_district'];
        $agent_sector = $_POST['agent_sector'];
        $date_Sent = date('Y-m-d h:i:s');
        $agent_pin = rand(1000,9999);
        $password= $agent_uname.'-'.$agent_pin;
        $hashed_Password = md5($password);

        # checking if agent exists
        $agent_existFetchQuery = 'SELECT * FROM `agent` WHERE `agent_name` = :agent_name';
        $agent_existFetchStatement = $pdo->prepare($agent_existFetchQuery);
        $agent_existFetchStatement->execute([
            'agent_name' => $agent_name
        ]);
        $agent_existResults = $agent_existFetchStatement->fetch();

        # if exist, pop some message
        if ($agent_existResults) {
            $agent_errorMessage = " Already registered" . $errorRefreshMessage;
        }

        # otherwise proceed with registration process

        else {
            $target_dir = "public/profile/";
            $target_file = $target_dir . basename($_FILES['agent_profile']['name']);
            $agent_profile = $_FILES['agent_profile']['name'];
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            
            # Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["agent_profile"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            }
            else {
                $agent_errorMessage = " File is not an image.";
                $uploadOk = 0;
            }
            
            # Check file size
            if ($_FILES["agent_profile"]["size"] > 400000) {
                $agent_errorMessage = " Sorry, your file is too large." . $errorRefreshMessage;
                $uploadOk = 0;
            }
            
            # Allow certain file formats
            else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                $agent_errorMessage = " Sorry, only JPG, JPEG, PNG & GIF files are allowed." . $errorRefreshMessage;
                $uploadOk = 0;
            }
            
            # Check if $uploadOk is set to 0 by an error
            else if ($uploadOk == 0) {
                $agent_errorMessage = " Sorry, your file was not uploaded." . $errorRefreshMessage;
            } 
            else {
                if (move_uploaded_file($_FILES["agent_profile"]["tmp_name"], $target_file)) {
                    
                    # Inserting Business...

                    $sql_insert_agent = " INSERT INTO `agent`(`created_at`, `agent_name`, `agent_gender`, `agent_username`, `agent_tel`, `agent_mail`, `agent_password`, `agent_pin`, `photo`, `agent_balance`, `status`) VALUES(:adate, :agent_name, :agent_gender, :agent_uname, :agent_tel, :agent_mail, :agent_password, :agent_pin, :photo, :balance, :bstatus)";

                    $agent_InsertStatement = $pdo->prepare($sql_insert_agent);
                    $agent_InsertStatement->execute([
                        'adate'             =>  $date_Sent,
                        'agent_name'        =>  $agent_name,
                        'agent_gender'      =>  $agent_gender,
                        'agent_uname'       =>  $agent_uname,
                        'agent_tel'         =>  $agent_tel,  
                        'agent_mail'        =>  $agent_mail,
                        'agent_password'    =>  $hashed_Password,
                        'agent_pin'         =>  $agent_pin,
                        'photo'             =>  $agent_profile,
                        'balance'           =>  '0',
                        'bstatus'           =>  'inactive'
                    ]);

                    if ($sql_insert_agent) {

                        # Getting Admin Info. for update form...

                        $agent_locationFetchQuery = 'SELECT * FROM `agent` WHERE `agent_pin` = :apin';
                        $agent_locationFetchStatement = $pdo->prepare($agent_locationFetchQuery);
                        $agent_locationFetchStatement->execute([
                            'apin' => $agent_pin
                        ]);
                        $agent_locationResults = $agent_locationFetchStatement->fetch();
                        $aID = $agent_locationResults->aID;

                        $sql_insert_location = "  INSERT INTO `agent_location`(`aID`, `agent_name`, `district`, `sector`) VALUES(:aid, :agent_name, :district, :sector) ";
                        $location_InsertStatement = $pdo->prepare($sql_insert_location);
                        $location_InsertStatement->execute([
                                'aid'           =>  $aID,
                                'agent_name'    =>  $agent_name,
                                'district'      =>  $agent_district,
                                'sector'        =>  $agent_sector
                        ]);
                        if ($sql_insert_agent && $sql_insert_location) {
                                $agent_successMessage = " Registered Pin: ". $agent_pin . $successRefreshMessage;
                        }
                    }
                    else {
                        $agent_errorMessage = " Could not register" . $errorRefreshMessage;
                    }
                } 
                else {
                    $agent_errorMessage = " Something went wrong" . $errorRefreshMessage;
                }
            }
        }
    }

    # Registering new business ...

    if (isset($_POST['businessApply'])) {
        $business_name = $_POST['business_name'];
        $business_mail = $_POST['business_mail'];
        $business_type = $_POST['business_type'];
        $business_tin = $_POST['business_tin'];
        $business_district = $_POST['business_district'];
        $business_sector = $_POST['business_sector'];
        $password = $business_tin;
        $hashed_Password = md5($password);
        $date_Sent = date('Y-m-d h:i:s');
        $business_pin = rand(1000,9999);

        # checking if business exists
        $business_existFetchQuery = 'SELECT * FROM `business` WHERE `business_name` = :business_name';
        $business_existFetchStatement = $pdo->prepare($business_existFetchQuery);
        $business_existFetchStatement->execute([
            'business_name' => $business_name
        ]);
        $business_existResults = $business_existFetchStatement->fetch();

        # if exist, pop some message
        if ($business_existResults) {
            $busy_errorMessage = " Already registered" . $errorRefreshMessage;
        }

        # if not, proceed with application business
        else {

            $target_dir = "public/profile/";
            $target_file = $target_dir . basename($_FILES['business_profile']['name']);
            $business_profile = $_FILES['business_profile']['name'];
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            
            # Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["business_profile"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            }
            else {
                $busy_errorMessage = " File is not an image.";
                $uploadOk = 0;
            }
            
            # Check file size
            if ($_FILES["business_profile"]["size"] > 4000000) {
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
                if (move_uploaded_file($_FILES["business_profile"]["tmp_name"], $target_file)) {

                    # Inserting Business...

                    $sql_insert_business = " INSERT INTO `business`(`Date`, `business_name`, `business_tin`, `business_mail`, `business_password`, `business_pin`, `business_type`, `balance`, `status`, `photo`, `approved_by`) VALUES(:bdate, :business_name, :business_tin, :business_mail, :business_password, :business_pin, :business_type, :balance, :bstatus, :photo, :approved_by)";

                    $business_InsertStatement = $pdo->prepare($sql_insert_business);
                    $business_InsertStatement->execute([
                        'bdate'             =>  $date_Sent,
                        'business_name'     =>  $business_name,
                        'business_tin'      =>  $business_tin,
                        'business_mail'     =>  $business_mail,
                        'business_password' =>  $hashed_Password,
                        'business_pin'      =>  $business_pin,
                        'business_type'     =>  $business_type,
                        'balance'           =>  '0',
                        'bstatus'           =>  'Inactive',
                        'photo'             =>  $business_profile,
                        'approved_by'       =>  'N/A'
                    ]);

                    if ($sql_insert_business) {

                        # Getting Admin Info. for update form...

                        $busy_locationFetchQuery = 'SELECT * FROM `business` WHERE `business_tin` = :businesstin';
                        $busy_locationFetchStatement = $pdo->prepare($busy_locationFetchQuery);
                        $busy_locationFetchStatement->execute([
                            'businesstin' => $business_tin
                        ]);
                        $busy_locationResults = $busy_locationFetchStatement->fetch();
                        $bID = $busy_locationResults->bID;

                        $sql_insert_location = "  INSERT INTO `business_location`(`bID`, `business_tin`, `district`, `sector`) VALUES(:bid, :businesstin, :district, :sector) ";
                        $location_InsertStatement = $pdo->prepare($sql_insert_location);
                        $location_InsertStatement->execute([
                                'bid'           =>  $bID,
                                'businesstin'   =>  $business_tin,
                                'district'      =>  $business_district,
                                'sector'        =>  $business_sector
                        ]);
                        if ($sql_insert_business && $sql_insert_location) {
                                $busy_successMessage = " Registered, TIN: ". $business_tin . " Refresh!";
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

    if (isset($_POST['client_id'])){
        $client_id = $_POST['client_id'];

        # checking if client exists ...

        $client_existFetchQuery = 'SELECT * FROM `client` WHERE `client_ID` = :client_id';
        $client_existFetchStatement = $pdo->prepare($client_existFetchQuery);
        $client_existFetchStatement->execute([
            'client_id' => $client_id
        ]);
        $client_existResults = $client_existFetchStatement->fetch();

        # if user is found then fetch the balance ...

        if ($client_existResults) {
            $user_balance = $client_existResults->client_balance;
        }
        else {
            $busy_errorMessage = " Unknown ID" . $errorRefreshMessage;
        }
    }

?>

<?php 
    include 'include/index_front.html';
?>