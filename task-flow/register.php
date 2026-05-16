<?php
$page = 'page-auth';
require_once 'includes/header.php';
require_once 'config/database.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email is already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed]);
            $success = "Account created! <a href='login.php'>Login here</a>.";
        }
    }
}
?>

<div class="reg-wrap">
  <div class="bg-orb orb1"></div>
  <div class="bg-orb orb2"></div>
  <div class="bg-orb orb3"></div>

  <div class="reg-card">
    <div class="reg-logo">
      <div class="logo-icon">✅</div>
      <span class="logo-text">TaskFlow</span>
    </div>

    <h2 class="reg-heading">Create account</h2>
    <p class="reg-sub">Start managing your tasks today</p>

    <?php if ($error): ?>
      <div class="error-msg" style="display:block;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success-msg" style="display:block;"><?= $success ?></div>
    <?php endif; ?>

    <div class="error-msg" id="errorMsg"></div>

    <form method="POST" id="regForm">

      <div class="field" id="fUser">
        <label>Username</label>
        <div class="field-inner">
          <input type="text" name="username" id="username"
                 placeholder="yourname"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
          <i class="ti ti-user field-icon"></i>
        </div>
        <span class="field-hint">Username is required.</span>
      </div>

      <div class="field" id="fEmail">
        <label>Email</label>
        <div class="field-inner">
          <input type="email" name="email" id="email"
                 placeholder="you@example.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          <i class="ti ti-mail field-icon"></i>
        </div>
        <span class="field-hint">Enter a valid email.</span>
      </div>

      <div class="field" id="fPw">
        <label>Password</label>
        <div class="field-inner">
          <input type="password" name="password" id="password" placeholder="Min. 6 characters">
          <i class="ti ti-lock field-icon"></i>
          <button class="toggle-pw" type="button" id="togglePw" aria-label="Show password">
            <i class="ti ti-eye" id="eyeIcon"></i>
          </button>
        </div>
        <div class="pw-strength" id="pwStrength">
          <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
          <span class="strength-label" id="strengthLabel"></span>
        </div>
        <span class="field-hint">Password must be at least 6 characters.</span>
      </div>

      <div class="field" id="fConfirm">
        <label>Confirm password</label>
        <div class="field-inner">
          <input type="password" name="confirm_password" id="confirm" placeholder="Re-enter password">
          <i class="ti ti-lock field-icon"></i>
          <i class="ti match-icon" id="matchIcon"></i>
        </div>
        <span class="field-hint">Passwords do not match.</span>
      </div>

      <button class="btn-register" type="submit" id="regBtn">
        <span class="btn-text">Create Account</span>
        <div class="spinner"></div>
      </button>

    </form>

    <div class="divider"><span>or</span></div>
    <p class="login-link">Already have an account? <a href="login.php">Sign in</a></p>
  </div>
</div>

<script>
const pwIn       = document.getElementById('password');
const confirmIn  = document.getElementById('confirm');
const togglePw   = document.getElementById('togglePw');
const eyeIcon    = document.getElementById('eyeIcon');
const strengthEl = document.getElementById('pwStrength');
const fillEl     = document.getElementById('strengthFill');
const labelEl    = document.getElementById('strengthLabel');
const matchIcon  = document.getElementById('matchIcon');
const regBtn     = document.getElementById('regBtn');
const errorMsg   = document.getElementById('errorMsg');

togglePw.addEventListener('click', () => {
  const show = pwIn.type === 'password';
  pwIn.type = show ? 'text' : 'password';
  eyeIcon.className = show ? 'ti ti-eye-off' : 'ti ti-eye';
});

function getStrength(pw) {
  let score = 0;
  if (pw.length >= 6)  score++;
  if (pw.length >= 10) score++;
  if (/[A-Z]/.test(pw)) score++;
  if (/[0-9]/.test(pw)) score++;
  if (/[^A-Za-z0-9]/.test(pw)) score++;
  return score;
}

pwIn.addEventListener('input', () => {
  const val = pwIn.value;
  if (!val) { strengthEl.classList.remove('show'); return; }
  strengthEl.classList.add('show');
  const score = getStrength(val);
  const configs = [
    { w: '20%',  bg: '#ef4444', label: 'Very weak' },
    { w: '40%',  bg: '#f97316', label: 'Weak' },
    { w: '60%',  bg: '#eab308', label: 'Fair' },
    { w: '80%',  bg: '#22c55e', label: 'Strong' },
    { w: '100%', bg: '#16a34a', label: 'Very strong' },
  ];
  const c = configs[Math.min(score - 1, 4)] || configs[0];
  fillEl.style.width = c.w;
  fillEl.style.background = c.bg;
  labelEl.textContent = c.label;
  labelEl.style.color = c.bg;
  checkMatch();
});

confirmIn.addEventListener('input', checkMatch);

function checkMatch() {
  const co = confirmIn.value;
  if (!co) { matchIcon.style.display = 'none'; return; }
  matchIcon.style.display = 'block';
  if (pwIn.value === co) {
    matchIcon.className = 'ti ti-check match-icon';
    matchIcon.style.color = '#22c55e';
    document.getElementById('fConfirm').classList.remove('error');
  } else {
    matchIcon.className = 'ti ti-x match-icon';
    matchIcon.style.color = '#ef4444';
  }
}

document.getElementById('regForm').addEventListener('submit', function () {
  errorMsg.style.display = 'none';
  let valid = true;
  ['fUser','fEmail','fPw','fConfirm'].forEach(id =>
    document.getElementById(id).classList.remove('error')
  );

  if (!document.getElementById('username').value.trim()) {
    document.getElementById('fUser').classList.add('error'); valid = false;
  }
  const e = document.getElementById('email').value;
  if (!e || !e.includes('@')) {
    document.getElementById('fEmail').classList.add('error'); valid = false;
  }
  if (pwIn.value.length < 6) {
    document.getElementById('fPw').classList.add('error'); valid = false;
  }
  if (pwIn.value !== confirmIn.value) {
    document.getElementById('fConfirm').classList.add('error'); valid = false;
  }

  if (!valid) {
    errorMsg.textContent = 'Please fix the errors above.';
    errorMsg.style.display = 'block';
    return false;
  }

  regBtn.classList.add('loading');
});
</script>

<?php require_once 'includes/footer.php'; ?>
