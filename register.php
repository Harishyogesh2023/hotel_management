<?php
$page_title = "Register";
include 'config/database.php';
include 'includes/session.php';
include 'classes/User.php';

// Redirect if already logged in
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
    
    // Validate input
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $error_message = 'Passwords do not match.';
    } else if (strlen($_POST['password']) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } else {
        $user->username = $_POST['username'];
        $user->password = $_POST['password'];
        $user->email = $_POST['email'];
        $user->first_name = $_POST['first_name'];
        $user->last_name = $_POST['last_name'];
        $user->phone = $_POST['phone'];
        
        if ($user->register()) {
            $success_message = 'Registration successful! You can now login with your credentials.';
        } else {
            $error_message = 'Registration failed. Username or email may already exist.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus text-primary fa-3x mb-3"></i>
                        <h2 class="font-serif">Join Grand Hotel</h2>
                        <p class="text-muted">Create your account to start booking luxury accommodations</p>
                    </div>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">
                                    <i class="fas fa-user me-2"></i>First Name
                                </label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required 
                                       value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>"
                                       placeholder="Enter your first name">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Last Name
                                </label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required 
                                       value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>"
                                       placeholder="Enter your last name">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-at me-2"></i>Username
                            </label>
                            <input type="text" class="form-control" id="username" name="username" required 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                   placeholder="Choose a unique username">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   placeholder="Enter your email address">
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-2"></i>Phone Number (Optional)
                            </label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                   placeholder="Enter your phone number">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="position-relative">
                                    <input type="password" class="form-control" id="password" name="password" required 
                                           placeholder="Create a secure password">
                                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y" 
                                            onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-toggle"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Must be at least 6 characters long</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Confirm Password
                                </label>
                                <div class="position-relative">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required 
                                           placeholder="Confirm your password">
                                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y" 
                                            onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye" id="confirm_password-toggle"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and 
                                <a href="#" class="text-decoration-none">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="text-muted mb-0">
                            Already have an account? 
                            <a href="login.php" class="text-decoration-none fw-bold">Sign in here</a>
                        </p>
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


document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    let strength = 0;
    
    if (password.length >= 6) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    const strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const strengthColors = ['danger', 'warning', 'info', 'success', 'success'];
    
    let indicator = document.getElementById('password-strength');
    if (!indicator) {
        indicator = document.createElement('small');
        indicator.id = 'password-strength';
        indicator.className = 'form-text';
        this.parentNode.appendChild(indicator);
    }
    
    if (password.length > 0) {
        indicator.className = `form-text text-${strengthColors[strength - 1] || 'danger'}`;
        indicator.textContent = `Password strength: ${strengthText[strength - 1] || 'Very Weak'}`;
    } else {
        indicator.textContent = '';
    }
});
</script>

<?php include 'includes/footer.php'; ?>