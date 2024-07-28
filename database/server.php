<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// initializing variables
$username = "";
$email    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'Hackathon');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
    array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "Email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
    $password = password_hash($password_1, PASSWORD_DEFAULT); // encrypt the password before saving in the database

    $query = "INSERT INTO users (username, email, password) 
              VALUES('$username', '$email', '$password')";
    if (mysqli_query($db, $query)) {
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = mysqli_insert_id($db);
        $_SESSION['success'] = "You are now logged in";
        header('location: index.php');
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($db);
    }
  }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
    array_push($errors, "Username is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
    $query = "SELECT * FROM users WHERE username='$username'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1) {
      $user = mysqli_fetch_assoc($results);
      if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['success'] = "You are now logged in";
        header('location: index.php');
      } else {
        array_push($errors, "Wrong username/password combination");
      }
    } else {
      array_push($errors, "Wrong username/password combination");
    }
  }
}

// ADD TOPIC
if (isset($_POST['add_topic'])) {
  $topic = mysqli_real_escape_string($db, $_POST['topic']);
  $summary = mysqli_real_escape_string($db, $_POST['summary']);
  $transcription = mysqli_real_escape_string($db, $_POST['transcription']);
  $user_id = $_SESSION['user_id'];

  if (empty($topic)) { array_push($errors, "Topic is required"); }
  if (empty($summary)) { array_push($errors, "Summary is required"); }
  if (empty($transcription)) { array_push($errors, "Transcription is required"); }

  if (count($errors) == 0) {
    $query = "INSERT INTO chats (user_id, topic, summary, transcription) VALUES('$user_id', '$topic', '$summary', '$transcription')";
    if (mysqli_query($db, $query)) {
      $_SESSION['success'] = "Topic added successfully";
      header('location: index.php');
    } else {
      echo "Error: " . $query . "<br>" . mysqli_error($db);
    }
  }
}
?>
