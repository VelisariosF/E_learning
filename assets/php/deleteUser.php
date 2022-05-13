<?php
//This php script is used to delete a User
session_start();
 //if the user is not logged in redirect to the login page
if (!isset($_SESSION['logged_in'])) {
    header("Location: Login.php");
}

require('server.php');
//get the loginame from the session
$loginame = $_GET['loginame'];

//create query
$sql = "DELETE FROM users WHERE loginame='$loginame'";
$result = mysqli_query($db, $sql);
if ($result === TRUE) {
    echo "Record deleted successfully";
  } else {
    echo "Error deleting record: " . $db->error;
  }
    var_dump($result);
    $db->close();
    header('Location: user_management.php');
    
?>