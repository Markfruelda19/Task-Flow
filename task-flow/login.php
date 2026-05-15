<?php
require_once 'includes/header.php';
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<div class="login-wrap">
  <div class="bg-orb orb1"></div>
  <div class="bg-orb orb2"></div>
  <div class="bg-orb orb3"></div>

  <div class="login-card">
    <div class="login-logo">
      <div class="logo-icon">✅</div>
      <span class="logo-text">TaskFlow</span>
    </div>

    <h2 class="login-heading">Welcome back</h2>
    <p class="login-sub">Sign in to your account to continue</p>

    <?php if ($error): ?>
    <div class="error-msg" style="display:block;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" id="loginForm">
      <div class="field" id="fieldEmail">
        <label for="email">Email</label>
        <div class="field-inner">
          <input type="email" id="email" name="email"
                 placeholder="you@example.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 autocomplete="email">
          <i class="ti ti-mail field-icon"></i>
        </div>
        <span class="field-hint">Please enter a valid email.</span>
      </div>

      <div class="field" id="fieldPw">
        <label for="password">Password</label>
        <div class="field-inner">
          <input type="password" id="password" name="password"
                 placeholder="••••••••" autocomplete="current-password">
          <i class="ti ti-lock field-icon"></i>
          <button class="toggle-pw" type="button" id="togglePw" aria-label="Toggle password">
            <i class="ti ti-eye" id="eyeIcon"></i>
          </button>
        </div>
        <span class="field-hint">Password cannot be empty.</span>
      </div>

      <div class="row-opts">
        <label class="remember">
          <input type="checkbox" name="remember"> Remember me
        </label>
        <a href="#" class="forgot">Forgot password?</a>
      </div>

      <button class="btn-login" type="submit" id="loginBtn">
        <span class="btn-text">Sign In</span>
        <div class="spinner"></div>
      </button>
    </form>

    <div class="divider"><span>or</span></div>
    <p class="register-link">No account yet? <a href="register.php">Create one</a></p>
  </div>
</div>
<script>
const loginBtn   = document.getElementById('loginBtn');
const emailIn    = document.getElementById('email');
const pwIn       = document.getElementById('password');
const togglePw   = document.getElementById('togglePw');
const eyeIcon    = document.getElementById('eyeIcon');
const fieldEmail = document.getElementById('fieldEmail');
const fieldPw    = document.getElementById('fieldPw');

togglePw.addEventListener('click', () => {
    const show = pwIn.type === 'password';
    pwIn.type = show ? 'text' : 'password';
    eyeIcon.className = show ? 'ti ti-eye-off' : 'ti ti-eye';
});

function clearErrors() {
    fieldEmail.classList.remove('error');
    fieldPw.classList.remove('error');
}

emailIn.addEventListener('input', clearErrors);
pwIn.addEventListener('input', clearErrors);

document.getElementById('loginForm').addEventListener('submit', function() {
    loginBtn.classList.add('loading');
});
</script>
<?php require_once 'includes/footer.php'; ?>
