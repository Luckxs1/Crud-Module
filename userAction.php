<?php

// Start session
session_start();

// Load and initialize database class
require_once 'DB.class.php';

$db = new DB();

$tblName = 'users';

// Set default redirect url
$redirectURL = 'index.php';

if(isset($_POST['userSubmit'])){

    // Get submitted data
    $name   = $_POST['name'];
    $email  = $_POST['email'];
    $phone  = $_POST['phone'];
    $id     = $_POST['id'];
    
    // Submitted user data
    $userData = array(
        'name'  => $name,
        'email' => $email,
        'phone' => $phone
    );
    
    // Store submitted data into session
    $sessData['postData'] = $userData;
    $sessData['postData']['id'] = $id;
    
    // ID query string
    $idStr = !empty($id)?'?id='.$id:'';
    
    // If the data is not empty
    if(!empty($name) && !empty($email) && !empty($phone)){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            if(!empty($id)){
                // Update data
                $condition = array('id' => $id);
                $update = $db->update($tblName, $userData, $condition);
                
                if($update){
                    $sessData['postData'] = '';
                    $sessData['status']['type'] = 'success';
                    $sessData['status']['msg']  = 'Data has been updated successfully.';
                }else{
                    $sessData['status']['type'] = 'error';
                    $sessData['status']['msg']  = 'Some problem occurred, please try again.';
                    
                    // Set redirect url
                    $redirectURL = 'addEdit.php'.$idStr;
                }
            }else{
                // Insert data
                $insert = $db->insert($tblName, $userData);
                
                if($insert){
                    $sessData['postData'] = '';
                    $sessData['status']['type'] = 'success';
                    $sessData['status']['msg']  = 'Data has been added successfully.';
                }else{

                    $sessData['status']['type'] = 'error';
                    $sessData['status']['msg']  = 'Some problem occurred, please try again.';
                    
                    // Set redirect url
                    $redirectURL = 'addEdit.php';
                }
            }
        }else{
            $sessData['status']['type'] = 'error';
            $sessData['status']['msg']  = 'Please enter a valid email address.';
            
            // Set redirect url
            $redirectURL = 'addEdit.php'.$idStr;
        }
    }else{
        $sessData['status']['type'] = 'error';
        $sessData['status']['msg']  = 'All fields are mandatory, please fill all the fields.';
        
        // Set redirect url
        $redirectURL = 'addEdit.php'.$idStr;
    }
    
    // Store status into the session
    $_SESSION['sessData'] = $sessData;
}elseif(($_REQUEST['action_type'] == 'delete') && !empty($_GET['id'])){
    // Delete data
    $condition = array('id' => $_GET['id']);
    $delete = $db->delete($tblName, $condition);
    if($delete){
        $sessData['status']['type'] = 'success';
        $sessData['status']['msg']  = 'Data has been deleted successfully.';
    }else{
        $sessData['status']['type'] = 'error';
        $sessData['status']['msg']  = 'Some problem occurred, please try again.';
    }
    
    // Store status into the session
    $_SESSION['sessData'] = $sessData;
}

// Redirect the user
header("Location: ".$redirectURL);
exit();