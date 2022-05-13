<?php

session_start();
//server connect data
$servername = "localhost";
$userServerName = "velisarios";
$passwordServer = "pataros16";
$dbname = "student3117partb";
//intitalize errors array
$errors = array(); 

// connect to the database
$db = mysqli_connect($servername, $userServerName, $passwordServer, $dbname);
mysqli_query($db,"SET NAMES 'utf8mb4'");
mysqli_query($db,"SET CHARACTER SET 'utf8'");


//================ Log in User ===================\\
//check if the button in the login.php form is pressed
if (isset($_POST['login_user'])) {
  //get the loginame from the form
  $loginame = mysqli_real_escape_string($db, $_POST['loginame']);
  //get the password
  $psw = mysqli_real_escape_string($db, $_POST['psw']);


  //check if they are empty
  if (empty($loginame)) {
  	array_push($errors, "Απαιτείται email για την είσοδο.");
  }
  if (empty($psw)) {
  	array_push($errors, "Απαιτείται κωδικός για την είσοδο.");
  }

   //if not check if they exist inside the database
   if(count($errors) == 0){
  	$query = "SELECT * FROM users WHERE loginame = '$loginame' AND password='$psw'";
    $result = mysqli_query($db, $query);
    
    //if they exist then the result rows is > 0
     if ($result && $result->num_rows > 0) { 
         while ($row = $result->fetch_assoc()) {
           //set the SESSION"S variables with the users data
              $_SESSION['logged_in'] = true;
              $_SESSION['loginame'] = $row["loginame"];
              $_SESSION['firstname'] = $row["first_name"];
              $_SESSION['lastname'] = $row["last_name"];
              $_SESSION['role'] = $row["role"];
              $_SESSION['loginame'] = $row["loginame"];
              //direct to the index.php
              header('Location:index.php');
          }
    } else {
         //if they don't exist inside the database then show an error message
          array_push($errors, "Λάθος όνομα χρήστη ή κωδικός!"); 
     }
   }
   
   $db->close();
}

//================ Announcements ===================\\
//Insert new announcement
//check if the button in the insertAnnouncement.php form is pressed
if (isset($_POST['insertNewAnnouncement'])) {
 
  //get the data from the form
  $date = mysqli_real_escape_string($db, $_POST['date']);
  $topic = mysqli_real_escape_string($db, $_POST['topic']);
  $mainContent = mysqli_real_escape_string($db, $_POST['mainContent']);
 
  //check if they are empty
  if (empty($date)) {
  	array_push($errors, "Απαιτείται εισαγωγή ημερομηνίας.");
  }
  if (empty($topic)) {
  	array_push($errors, "Απαιτείται εισαγωγή θέματος.");
  }

  if (empty($mainContent)) {
  	array_push($errors, "Απαιτείται εισαγωγή κυρίως κειμένου.");
  }
  
  //if not then insert them in the database
  if(count($errors) == 0){
  	$query = "INSERT INTO announcements (date, topic, mainContent) 
  			  VALUES('$date', '$topic', '$mainContent')";
   mysqli_query($db, $query);
    //reset the insert data
    $_POST['date'] = '';
   $_POST['topic'] = '';
    $_POST['mainContent'] = '';
   
    //redirect to the announcement 
      header('Location: announcement.php');  
   
    
    
     
    }
    $db->close();
  }




//Update announcement

//check if the button in the updateAnnouncement.php form is pressed
if (isset($_POST['updateAnnouncement'])) {
  //get the announcements id
  $id = $_SESSION['updateAnnouncementId'];
  //get the data from the form
  $date = mysqli_real_escape_string($db, $_POST['date']);
  $topic = mysqli_real_escape_string($db, $_POST['topic']);
  $mainContent = mysqli_real_escape_string($db, $_POST['mainContent']);
  
  //check if they are empty
  if (empty($date)) {
  	array_push($errors, "Απαιτείται εισαγωγή ημερομηνίας.");
  }
  if (empty($topic)) {
  	array_push($errors, "Απαιτείται εισαγωγή θέματος.");
  }

  if (empty($mainContent)) {
  	array_push($errors, "Απαιτείται εισαγωγή κυρίως κειμένου.");
  }

  //if they are not empty update the data of the specific announcement
  if(count($errors) == 0){
  $query = "UPDATE announcements SET date ='$date', topic='$topic', mainContent='$mainContent' WHERE id='$id'";
  mysqli_query($db, $query);
    //reset the data
    $_POST['date'] = '';
    $_POST['topic'] = '';
    $_POST['mainContent'] = '';
    unset($_SESSION['updateAnnouncementId']);
    header('Location: announcement.php');
    
  }
  $db->close();
}

//================ Email ===================\\
//Send email through web form
//check if the sendEmail button from the email form is pressed
if (isset($_POST['sendEmail'])) {

  //get the data from the form
  $sender = mysqli_real_escape_string($db, $_POST['sender']);
  $emailTopic = mysqli_real_escape_string($db, $_POST['emailTopic']);
  $emailContent = mysqli_real_escape_string($db, $_POST['emailContent']);


  //check if they are empty
  if (empty($sender)) {
  	array_push($errors, "Απαιτείται αποστολέας.");
  }
  if (empty($emailTopic)) {
  	array_push($errors, "Απαιτείται εισαγωγή θέματος.");
  }

  if (empty($emailContent)) {
  	array_push($errors, "Απαιτείται εισαγωγή κυρίως κειμένου.");
  }
  //if not get the emails of the tutor users from the database and send email to every tutor user
  if(count($errors) == 0){
     $emails = []; 
     $query = "SELECT * FROM users WHERE role='Tutor'";
     $result = $db->query($query) or die(mysqli_error($db));
     if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
           $emails[] = $row['loginame'];
         
       }
     }

    if (sizeof($emails) > 0) {
        mail(implode(",", $emails), $emailTopic, $emailContent, 'From:'. $sender);
    }
  
 
    header("Location: communication.php");
  
    }
    $db->close();
}

//================ Documents ===================\\
//documenInsertion
//check if the button in the insertNewDocument.php form is pressed 
if (isset($_POST['documentInsertion'])) {
 
  //get the data from the form 
  $documentTitle = mysqli_real_escape_string($db, $_POST['documentTitle']);
  $description = mysqli_real_escape_string($db, $_POST['description']);
  $documentPath = mysqli_real_escape_string($db, $_POST['documentPath']);
  //check if they are empty
  if (empty($documentTitle)) {
  	array_push($errors, "Απαιτείται εισαγωγή τίτλου.");
  }
  if (empty($description)) {
  	array_push($errors, "Απαιτείται εισαγωγή περιγραφής.");
  }

  if (empty($documentPath)) {
  	array_push($errors, "Απαιτείται εισαγωγή θέσης αρχείου.");
  }
//check if they are empty  indert the new document's data to the database
  if(count($errors) == 0){
  	$query = "INSERT INTO documents (documentTitle, description, documentPath) 
  			  VALUES(' $documentTitle', '$description', '$documentPath')";
  	mysqli_query($db, $query);
    $_POST['documentTitle'] = '';
   $_POST['description'] = '';
    $_POST['documentPath'] = '';
   
      header('Location: documents.php');
    
    }
    $db->close();
  }


//update document
//check if the button in the updateDocument.php form is pressed
if (isset($_POST['updateDocument'])) {
  //get the id of the document
  $id = $_SESSION['updateDocId'];
  //get the from the form
  $documentTitle = mysqli_real_escape_string($db, $_POST['documentTitle']);
  $description = mysqli_real_escape_string($db, $_POST['description']);
  $documentPath = mysqli_real_escape_string($db, $_POST['documentPath']);

  //check if they are empty
  if (empty($documentTitle)) {
    array_push($errors, "Απαιτείται εισαγωγή τίτλου.");
  
  }
  if (empty($description)) {
  	array_push($errors, "Απαιτείται εισαγωγή περιγραφής.");
  }

  if (empty($documentPath)) {
  	array_push($errors, "Απαιτείται εισαγωγή θέσης αρχείου.");
  }
  //if not update the data of the specific document
  if(count($errors) == 0){
  $query = "UPDATE documents SET documentTitle='$documentTitle', description='$description', documentPath='$documentPath' WHERE id='$id'";
   mysqli_query($db, $query);
  
    $_POST['documentTitle'] = '';
    $_POST['description'] = '';
    $_POST['documentPath'] = '';
    unset($_SESSION['updateDocId']);
      header('Location: documents.php');
  
   
  
 }
 $db->close();
}
//================ User ===================\\

//insert new user
//chec if the button from the insertNewUser.php form is pressed
if (isset($_POST['insertUser'])) {
 
  //get the data from the form
  $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
  $role = mysqli_real_escape_string($db, $_POST['role']);
  $loginame = mysqli_real_escape_string($db, $_POST['loginame']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  //check if they are empty
  if (empty($firstname)) {
    array_push($errors, "Απαιτείται εισαγωγή oνόματος.");
  
  }
  if (empty($lastname)) {
  	array_push($errors, "Απαιτείται εισαγωγή επιθέτου.");
  }

  if (empty($role)) {
  	array_push($errors, "Απαιτείται εισαγωγή ρόλου.");
  }else if($role!= 'Student' && $role!='Tutor'){
    array_push($errors, "O ρόλος πρέπει να είναι Student ή Tutor.");
  }

  if (empty($loginame)) {
  	array_push($errors, "Απαιτείται εισαγωγή loginame.");
  }else{
    //if the loginame is not empty check if it already exists in database
    $searchLoginameQuery = "SELECT loginame FROM users WHERE loginame='$loginame'";
    $result =  mysqli_query($db, $searchLoginameQuery);
    if ($result && $result->num_rows > 0) { 
      array_push($errors, "To email(loginame) που δωσατε υπάρχει ήδη. Παρακαλώ εισάγετε διαφορετικό email(loginame)");
    }


  }

  if (empty($password)) {
  	array_push($errors, "Απαιτείται εισαγωγή κωδικού.");
  }
  //if not insert the new user data inside the database
  if(count($errors) == 0){
  	$query = "INSERT INTO users (firstname, lastname, loginame, password, role) 
  			  VALUES(' $firstname', '$lastname', '$loginame', '$password', '$role')";
  	mysqli_query($db, $query);
    $_POST['firstname'] = '';
    $_POST['lastname'] = '';
    $_POST['role'] = '';
    $_POST['loginame'] = '';
    $_POST['password'] = '';

   
      header('Location: usersControl.php');
     
    }
    $db->close();
  }



//update user
//check if the button from the updateUser.php form is pressed
if (isset($_POST['updateUser'])) {
  //get the id of the speceific user
  $loginame =  $_SESSION['updateUserLoginame'];
  //get the data from the form
  $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
  $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
  $role = mysqli_real_escape_string($db, $_POST['role']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  
  if (empty($firstname)) {
    array_push($errors, "Απαιτείται εισαγωγή oνόματος.");
  
  }
  if (empty($lastname)) {
  	array_push($errors, "Απαιτείται εισαγωγή επιθέτου.");
  }

  if (empty($role)) {
  	array_push($errors, "Απαιτείται εισαγωγή ρόλου.");
  }else if($role!= 'Student' && $role!='Tutor'){
    array_push($errors, "O ρόλος πρέπει να είναι Student ή Tutor.");
  }

  

  if (empty($password)) {
  	array_push($errors, "Απαιτείται εισαγωγή κωδικού.");
  }
   //if not empty update the user's data
  if(count($errors) == 0){
  $query = "UPDATE users SET firstname='$firstname', lastname='$lastname', role = '$role', password = '$password' WHERE loginame='$loginame'";
   mysqli_query($db, $query);
  
    $_POST['firstname'] = '';
    $_POST['lastname'] = '';
    $_POST['role'] = '';
    $_POST['password'] = '';
    unset($_SESSION['updateUserLoginame']);
      header('Location: usersControl.php');
  
   
  
 }
 $db->close();
}

//================ Homework ===================\\
//insert new homework
//check if the button from the insertNewHomework.php form is pressed
 if (isset($_POST['insertNewHomework'])) {
  
  //get the data from the form
  $goals = mysqli_real_escape_string($db, $_POST['goals']);
  $path = mysqli_real_escape_string($db, $_POST['path']);
  $homeworkToDeliver = mysqli_real_escape_string($db, $_POST['homeworkToDeliver']);
  $deliveryDate = mysqli_real_escape_string($db, $_POST['deliveryDate']);

  if (empty($goals)) {
    array_push($errors, "Απαιτείται εισαγωγή στόχων.");
  
  }
  if (empty($path)) {
  	array_push($errors, "Απαιτείται εισαγωγή ονόματος/θέσης αρχείου.");
  }

  if (empty($homeworkToDeliver)) {
  	array_push($errors, "Απαιτείται εισαγωγή παραδοτέων.");
  }

  if (empty($deliveryDate)) {
  	array_push($errors, "Απαιτείται εισαγωγή ημερομηνίας.");
  }
  //if not empty inser the new data inside the form
  if(count($errors) == 0){

  	$query = "INSERT INTO homework (goals, path, homeworkToDeliver, deliveryDate) 
  			  VALUES(' $goals', '$path', '$homeworkToDeliver', '$deliveryDate')";
  	mysqli_query($db, $query);
    $_POST['goals'] = '';
    $_POST['path'] = '';
    $_POST['homeworkToDeliver'] = '';
    $_POST['deliveryDate'] = '';
     
   $announcedDate = date("Y-m-d");
   
      
    ///insert new announcement about new homework
    $query2 = "INSERT INTO announcements (date, topic, mainContent) 
  			  VALUES(' $announcedDate', 'Υποβλήθηκε η εργασία  $db->insert_id.', 'Η ημερομηνία παράδοσης της εργασίας είναι $deliveryDate')";
  	mysqli_query($db, $query2);

    

   
      header('Location: homework.php');
      
    }
    $db->close();
  }

//update homework
//check if the button from the updateHomework.php is pressed
if (isset($_POST['updateHomework'])) {
  //get the id of the document
  $id = $_SESSION['updateHomId'];
  //get the data from the form
  $goals = mysqli_real_escape_string($db, $_POST['goals']);
  $path = mysqli_real_escape_string($db, $_POST['path']);
  $homeworkToDeliver = mysqli_real_escape_string($db, $_POST['homeworkToDeliver']);
  $deliveryDate = mysqli_real_escape_string($db, $_POST['deliveryDate']);
   //check if empty
  if (empty($goals)) {
    array_push($errors, "Απαιτείται εισαγωγή στόχων.");
  
  }
  if (empty($path)) {
  	array_push($errors, "Απαιτείται εισαγωγή ονόματος/θέσης αρχείου.");
  }

  if (empty($homeworkToDeliver)) {
  	array_push($errors, "Απαιτείται εισαγωγή παραδοτέων.");
  }

  if (empty($deliveryDate)) {
  	array_push($errors, "Απαιτείται εισαγωγή ημερομηνίας.");
  }
  if(count($errors) == 0){
  //if not empty update the data from the database
  $query = "UPDATE homework SET goals='$goals', path='$path', homeworkToDeliver='$homeworkToDeliver', deliveryDate='$deliveryDate' WHERE id='$id'";
   mysqli_query($db, $query);
  
   $_POST['goals'] = '';
   $_POST['path'] = '';
   $_POST['homeworkToDeliver'] = '';
   $_POST['deliveryDate'] = '';
    unset($_SESSION['updateHomId']);
      header('Location: homework.php');
  
   }
 $db->close();
}


?>
