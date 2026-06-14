<?php
include "db.php";


session_start(); //Optional, for future use
$error_msg = [];
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // First name
    if (empty($first_name)) {
        $error_msg['first_name'] = "First name is required.";
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/u", $first_name)) {
        $error_msg['first_name'] = "Only letter, space and hyphens allowed.";
    }

    // Last name
    if (empty($last_name)) {
        $error_msg['last_name'] = "Last name is required.";
    } elseif (!preg_match("/^[a-zA-Z\s'-]+$/u", $last_name)) {
        $error_msg['last_name'] = "Only letter, space and hyphens allowed.";
    }

    // Email
    if (empty($email)) {
        $error_msg['email'] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg['email'] = "Enter a valid email (e.g., example@gmail.com)";
    } else {
        // Check email already exists
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error_msg['email'] = "This email already registered.";
        }
        $stmt->close();
    }

    // password

    if (empty($password)) {
        $error_msg['password'] = "Password is required";
    } elseif (strlen($password < 6)) {
        $error_msg['password'] = "Password must be at least 6 characters.";
    }

    // Confirm password
    if ($password !== $confirm) {
        $error_msg['confirm_password'] = "Password don not match.";
    }

    if (!isset($_POST['terms'])) {
        $error_msg['terms'] = "You must agree to the Terms & Conditions.";
    }

    // if no errors, insert into database

    if (empty($error_msg)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("INSERT INTO users (first_name, last_name, email, password_hash) VALUES(?, ?, ?, ?)");
        $stmt->bind_param('ssss', $first_name, $last_name, $email, $hashed_password);

        if ($stmt->execute()) {
            $success_msg = "✅Registration Successful! You can now log in.";

            // Clear form fields on success(optional)

            $_POST = [];
            $first_name = $last_name = $email = '';
        } else {
            $error_msg['general'] = "Something went wrong. Please try again.";
        }

        $stmt->close();
    }
}


// mysqli->close()
// Sticky values (keep old input after errors)

$first_name_vlu = $_POST['first_name'] ?? '';
$last_name_vlu  = $_POST['last_name'] ?? '';
$email_vlu      = $_POST['email'] ?? '';

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp Form</title>
    <link rel="stylesheet" href="signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="container">
        <div class="signup_imt">
            <h1>SignUp</h1>
            <p>Register form</p>

            <?php if ($success_msg): ?>
                <div class="alert alert_success">
                    <?php echo htmlspecialchars($success_msg); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg['general'])): ?>
                <div class="alert alert_error">
                    <?php echo htmlspecialchars($error_msg['general']); ?>
                </div>
            <?php endif; ?>


            <!-- Form section -->

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="signup_form" novalidate>
                <div class="row-2">
                    <!-- First name  -->
                    <div class="signup_group">
                        <label><i class="fa-solid fa-user"></i>First Name</label>
                        <input type="text" name="first_name" class="input_text" placeholder="e.g.john" value="<?php echo htmlspecialchars($first_name_vlu); ?>">
                        <?php if (isset($error_msg['first_name'])): ?>
                            <span class="error_text"><?php echo $error_msg['first_name']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Last name -->
                    <div class="signup_group">
                        <label><i class="fa-solid fa-user"></i>Last Name</label>
                        <input type="text" name="last_name" class="input_text" placeholder="e.g. smith" value="<?php echo htmlspecialchars($last_name_vlu); ?>">
                        <?php if (isset($error_msg['last_name'])): ?>
                            <span class="error_text"><?php echo $error_msg['last_name'] ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="signup_group">
                        <label><i class="fa-solid fa-envelope"></i>Email</label>
                        <input type="email" name="email" class="input_text" placeholder="example@gmail.com" value="<?php echo htmlspecialchars($email_vlu); ?>">
                        <?php if (isset($error_msg['email'])): ?>
                            <span class="error_text"><?php echo $error_msg['email']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div class="signup_group">
                        <label><i class="fa-solid fa-lock"></i>Password</label>
                        <input type="password" name="password" class="input_text" placeholder="min.6" autocomplete="new-password">
                        <?php if (isset($error_msg['password'])): ?>
                            <span class="error_text"><?php echo $error_msg['password']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Confirm password -->
                    <div class="signup_group">
                        <label><i class="fa-solid fa-unlock-keyhole"></i>Confirm Password</label>
                        <input type="password" name="confirm_password" class="input_text" placeholder="re-enter your password">
                        <?php if (isset($error_msg['confirm_password'])): ?>
                            <span class="error_text"><?php echo $error_msg['confirm_password']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Terms ^ Conditions -->
                    <div class="terms">
                        <input type="checkbox" name="terms" class="terms">
                        <label for="terms">I agree to the Terms & Conditions.</label>
                        <?php if (isset($error_msg['terms'])): ?>
                            <span class="error_text"><?php echo $error_msg['terms']?></span>
                        <?php endif; ?>
                    </div>

                    <!-- SignUp Button -->
                    <button type="submit" class="reg_btn">Sign Up</button>
                    <hr>

                    <!-- Footer Note -->
                    <div class="footnote">
                        Your information is protected
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>