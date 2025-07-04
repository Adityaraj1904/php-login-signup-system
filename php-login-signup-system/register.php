<?php
# Include connection
require_once "./config.php";

# Define variables and initialize with empty values
$username_err = $email_err = $password_err = "";
$username = $email = $password = "";

# Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter a username.";
  } else {
    $username = trim($_POST["username"]);
    if (!ctype_alnum(str_replace(array("@", "-", "_"), "", $username))) {
      $username_err = "Username can only contain letters, numbers and symbols like '@', '_', or '-'.";
    } else {
      $sql = "SELECT id FROM users WHERE username = ?";
      if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $username;
        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);
          if (mysqli_stmt_num_rows($stmt) == 1) {
            $username_err = "This username is already registered.";
          }
        } else {
          echo "<script>alert('Oops! Something went wrong. Please try again later.')</script>";
        }
        mysqli_stmt_close($stmt);
      }
    }
  }

  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter an email address";
  } else {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $email_err = "Please enter a valid email address.";
    } else {
      $sql = "SELECT id FROM users WHERE email = ?";
      if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;
        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);
          if (mysqli_stmt_num_rows($stmt) == 1) {
            $email_err = "This email is already registered.";
          }
        } else {
          echo "<script>alert('Oops! Something went wrong. Please try again later.');</script>";
        }
        mysqli_stmt_close($stmt);
      }
    }
  }

  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } else {
    $password = trim($_POST["password"]);
    if (strlen($password) < 8) {
      $password_err = "Password must contain at least 8 or more characters.";
    }
  }

  if (empty($username_err) && empty($email_err) && empty($password_err)) {
    $sql = "INSERT INTO users(username, email, password) VALUES (?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);
      $param_username = $username;
      $param_email = $email;
      $param_password = password_hash($password, PASSWORD_DEFAULT);
      if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Registration completed successfully. Login to continue.');</script>";
        echo "<script>window.location.href='./login.php';</script>";
        exit;
      } else {
        echo "<script>alert('Oops! Something went wrong. Please try again later.');</script>";
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
  <title>Sign Up</title>
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
      max-width: 500px;
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
    <div class="avatar-circle">S</div>
    <h2 class="form-title text-center">Sign Up</h2>
    <p class="subtitle text-center">Create your account below</p>
    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="<?= $username; ?>" required>
        <small class="text-danger"><?= $username_err; ?></small>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= $email; ?>" required>
        <small class="text-danger"><?= $email_err; ?></small>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" id="password" value="<?= $password; ?>" required>
        <small class="text-danger"><?= $password_err; ?></small>
      </div>
      <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="togglePassword">
        <label class="form-check-label" for="togglePassword">Show Password</label>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-custom">Sign Up</button>
      </div>
    </form>
    <p class="mt-3 text-center text-muted">Already have an account? <a href="login.php">Login</a></p>
  </div>
  <script>
    document.getElementById("togglePassword").addEventListener("change", function () {
      const pwd = document.getElementById("password");
      pwd.type = this.checked ? "text" : "password";
    });
  </script>
</body>
</html>
