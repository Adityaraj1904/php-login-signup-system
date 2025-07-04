<?php
# Start the session
session_start();

# Redirect to login page if user is not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== TRUE) {
  echo "<script>window.location.href='./login.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styling -->
  <style>
    body {
      background-color: #f8f9fa;
    }
    .profile-container {
      margin-top: 80px;
      padding: 40px;
      background: white;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0,0,0,0.08);
    }
    .avatar {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <div class="container">

    <!-- Welcome message -->
    <div class="alert alert-success mt-5 text-center">
      ✅ Welcome! You are now signed in to your account.
    </div>

    <!-- Profile section -->
    <div class="row justify-content-center">
      <div class="col-md-6 text-center profile-container">
        <!-- Avatar generated based on name -->
        <img 
          src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username']); ?>&background=random&color=fff&size=150" 
          alt="User avatar" 
          class="avatar"
        >
        <h3>Hello, <?= htmlspecialchars($_SESSION["username"]); ?></h3>
        <a href="./logout.php" class="btn btn-primary mt-3">Log Out</a>
      </div>
    </div>
  </div>
</body>
</html>
