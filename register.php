
<?php require __DIR__ . '/includes/config.php';
$cats = $pdo->query("SELECT id,name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']==='POST'){
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $role = $_POST['role'] ?? 'consumer';
  $city = trim($_POST['city'] ?? '');
  $pass = $_POST['password'] ?? '';
  $skills = $_POST['skills'] ?? []; // array of category ids

  if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    flash('error','Invalid email.');
  } else {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $status = ($role==='provider') ? 'pending' : 'approved';
    $stmt = $pdo->prepare("INSERT INTO users(name,email,phone,password_hash,role,status,city) VALUES(?,?,?,?,?,?,?)");
    try{
      $stmt->execute([$name,$email,$phone,$hash,$role,$status,$city]);
      $uid = $pdo->lastInsertId();
      if($role==='provider' && $skills){
        $ps = $pdo->prepare("INSERT INTO provider_skills(user_id,category_id) VALUES(?,?)");
        foreach($skills as $cid){ $ps->execute([$uid,$cid]); }
      }
      flash('success', $role==='provider' ? 'Account created! Please wait for admin approval.' : 'Account created! You can log in now.');
      redirect('/login.php');
    } catch(Exception $e){
      flash('error','Email already used or invalid data.');
    }
  }
}

include __DIR__ . '/includes/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">Create an account</h5>
        <form method="post">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Full Name</label>
              <input name="name" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Phone</label>
              <input name="phone" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">City</label>
              <input name="city" class="form-control" placeholder="Karachi, Lahore, ...">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Password</label>
              <input name="password" type="password" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Account Type</label>
              <select name="role" class="form-select" id="roleSelect">
                <option value="consumer">I need a service</option>
                <option value="provider">I provide a service</option>
              </select>
            </div>
          </div>

          <div id="providerFields" class="mb-3" style="display:none">
            <label class="form-label">Select your skills (you can pick multiple)</label>
            <div class="row">
              <?php foreach($cats as $c): ?>
                <div class="col-6 col-md-4">
                  <label class="form-check">
                    <input type="checkbox" class="form-check-input" name="skills[]" value="<?= e($c['id']) ?>"> <?= e($c['name']) ?>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <button class="btn btn-primary w-100">Create Account</button>
        </form>
        <p class="form-help mt-2">Already have an account? <a href="/login.php">Login</a></p>
      </div>
    </div>
  </div>
</div>

<script>
const roleSel = document.getElementById('roleSelect');
const pf = document.getElementById('providerFields');
function togglePF(){ pf.style.display = roleSel.value==='provider' ? '' : 'none'; }
roleSel.addEventListener('change', togglePF); togglePF();
</script>

<?php include __DIR__ . "/includes/footer.php"; ?>
