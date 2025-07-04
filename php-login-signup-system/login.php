<?php
# Initialize session
session_start();

# Check if user is already logged in, If yes then redirect him to index page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == TRUE) {
  echo "<script>window.location.href='./'</script>";
  exit;
}

# Include connection
require_once "./config.php";

# Define variables and initialize with empty values
$user_login_err = $user_password_err = $login_err = "";
$user_login = $user_password = "";

# Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty(trim($_POST["user_login"]))) {
    $user_login_err = "Please enter your username or an email id.";
  } else {
    $user_login = trim($_POST["user_login"]);
  }

  if (empty(trim($_POST["user_password"]))) {
    $user_password_err = "Please enter your password.";
  } else {
    $user_password = trim($_POST["user_password"]);
  }

  # Validate credentials 
  if (empty($user_login_err) && empty($user_password_err)) {
    $sql = "SELECT id, username, password FROM users WHERE username = ? OR email = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "ss", $param_user_login, $param_user_login);
      $param_user_login = $user_login;
      if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
          mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
          if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($user_password, $hashed_password)) {
              $_SESSION["id"] = $id;
              $_SESSION["username"] = $username;
              $_SESSION["loggedin"] = TRUE;
              echo "<script>window.location.href='./'</script>";
              exit;
            } else {
              $login_err = "The email or password you entered is incorrect.";
            }
          }
        } else {
          $login_err = "Invalid username or password.";
        }
      } else {
        echo "<script>alert('Oops! Something went wrong. Please try again later.');</script>";
        echo "<script>window.location.href='./login.php'</script>";
        exit;
      }
      mysqli_stmt_close($stmt);
    }
  }
  mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #6a11cb, #2575fc);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card-custom {
      background: #ffffff;
      border-radius: 20px;
      padding: 3rem;
      max-width: 460px;
      width: 100%;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }
    .form-control {
      border-radius: 12px;
    }
    .btn-custom {
      background-color: #2575fc;
      border: none;
      border-radius: 12px;
      padding: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn-custom:hover {
      background-color: #1a5ed9;
    }
    .form-title {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    .subtitle {
      font-size: 0.95rem;
      color: #555;
      margin-bottom: 2rem;
    }
    .avatar-circle {
      background-color: #2575fc;
      color: white;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 1.5rem;
      margin: 0 auto 1rem;
    }
  </style>
</head>
<body>
  <div class="card-custom">
    <div class="avatar-circle">U</div>
    <h2 class="form-title text-center">Log In</h2>
    <p class="subtitle text-center">Please login to continue</p>
    <?php if (!empty($login_err)) echo "<div class='alert alert-danger'>" . $login_err . "</div>"; ?>
    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
      <div class="mb-3">
        <label for="user_login" class="form-label">Email or username</label>
        <input type="text" class="form-control" name="user_login" id="user_login" value="<?= $user_login; ?>">
        <small class="text-danger"><?= $user_login_err; ?></small>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" name="user_password" id="password">
        <small class="text-danger"><?= $user_password_err; ?></small>
      </div>
      <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="togglePassword">
        <label class="form-check-label" for="togglePassword">Show Password</label>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-custom">Log In</button>
      </div>
    </form>
    <p class="mt-3 text-center text-muted">Don't have an account? <a href="register.php">Sign Up</a></p>
  </div>
  <script>
    document.getElementById("togglePassword").addEventListener("change", function () {
      const passwordField = document.getElementById("password");
      passwordField.type = this.checked ? "text" : "password";
    });
  </script>
</body>
</html>
