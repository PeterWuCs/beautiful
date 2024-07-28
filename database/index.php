<?php 
session_start(); 

if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
}
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="header">
    <h2>Home Page</h2>
</div>
Welcome to my project page    
<div class="content">
    <!-- notification message -->
    <?php if (isset($_SESSION['success'])) : ?>
      <div class="error success" >
        <h3>
          <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
          ?>
        </h3>
      </div>
    <?php endif ?>

    <!-- logged in user information -->
    <?php  if (isset($_SESSION['username'])) : ?>
        <p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
        <p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>

        <!-- Add Topic Form -->
        <button id="showForm">Add Topic</button>
        <form id="topicForm" action="index.php" method="post" style="display:none;">
            <div class="input-group">
                <label>Topic</label>
                <input type="text" name="topic" required>
            </div>
            <div class="input-group">
                <label>Summary</label>
                <textarea name="summary" required></textarea>
            </div>
            <div class="input-group">
                <label>Transcription</label>
                <textarea name="transcription" required></textarea>
            </div>
            <div class="input-group">
                <button type="submit" class="btn" name="add_topic">Add</button>
            </div>
        </form>

        <h3>Your Topics</h3>
        <?php
        // Include server.php to handle form submission
        include('server.php');

        // Fetch and display user's topics
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * FROM chats WHERE user_id=$user_id ORDER BY created_at DESC";
        $results = mysqli_query($db, $query);

        while ($row = mysqli_fetch_assoc($results)) {
            echo "<div class='topic'>";
            echo "<h4>" . htmlspecialchars($row['topic']) . "</h4>";
            echo "<p><strong>Summary:</strong> " . htmlspecialchars($row['summary']) . "</p>";
            echo "<p><strong>Transcription:</strong> " . htmlspecialchars($row['transcription']) . "</p>";
            echo "<small>Added on: " . $row['created_at'] . "</small>";
            echo "</div>";
        }
        ?>
    <?php endif ?>
</div>

<script>
document.getElementById('showForm').addEventListener('click', function() {
    var form = document.getElementById('topicForm');
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
});
</script>
</body>
</html>
