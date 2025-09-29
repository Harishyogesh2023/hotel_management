<?php
$page_title = "Login";
include 'config/database.php';
include 'includes/session.php';
include 'classes/User.php';


if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    
    if ($user->login()) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['email'] = $user->email;
        $_SESSION['first_name'] = $user->first_name;
        $_SESSION['last_name'] = $user->last_name;
        $_SESSION['phone'] = $user->phone;
        $_SESSION['is_admin'] = (int)$user->is_admin;
       
        echo '<div style="color:blue; font-weight:bold;">DEBUG: Login successful.<br>is_admin: ' . htmlspecialchars($_SESSION['is_admin']) . '<br>user_id: ' . htmlspecialchars($user->id) . '<br>username: ' . htmlspecialchars($user->username) . '<br></div>';
        
        echo '<div style="color:green; font-weight:bold;">DEBUG: SESSION is_admin: ' . (isset($_SESSION['is_admin']) ? htmlspecialchars($_SESSION['is_admin']) : 'not set') . '</div>';
      
        if ($_SESSION['is_admin'] === 1) {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit();
    } else {
        $error_message = 'Invalid username or password. Please try again.';

        echo '<div style="color:red; font-weight:bold;">DEBUG: Login failed for username/email: ' . htmlspecialchars($_POST['username']) . '</div>';
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-hotel text-primary fa-3x mb-3"></i>
                        <h2 class="font-serif">Welcome Back</h2>
                        <p class="text-muted">Sign in to your Grand Hotel account</p>
                    </div>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-2"></i>Username or Email
                            </label>
                            <input type="text" class="form-control" id="username" name="username" required 
                                   placeholder="Enter your username or email">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="password" name="password" required 
                                       placeholder="Enter your password">
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y" 
                                        onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password-toggle"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <a href="forgot-password.php" class="text-decoration-none">Forgot your password?</a>
                    </div>
                    
                    <div class="text-center mt-3">
                        <p class="text-muted mb-0">
                            Don't have an account? 
                            <a href="register.php" class="text-decoration-none fw-bold">Sign up here</a>
                        </p>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggle = document.getElementById(fieldId + '-toggle');
    
    if (field.type === 'password') {
        field.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}
</script>

<?php include 'includes/footer.php'; ?>