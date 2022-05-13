<?php
//This php script is used to delete a document
session_start();
 //if the user is not logged in redirect to the login page
if (!isset($_SESSION['logged_in'])) {
    header("Location: Login.php");
}
require('server.php');
//get the id from the session
$id = $_GET['id'];

//create query
$sql = "DELETE FROM documents WHERE id='$id'";
$result = mysqli_query($db, $sql);
if ($result === TRUE) {
    echo "Record deleted successfully";
  } else {
    echo "Error deleting record: " . $db->error;
  }
    var_dump($result);
    $db->close();
    header('Location: documents.php');
    
?>