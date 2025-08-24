
<?php require __DIR__ . '/includes/config.php';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
  $stmt->execute([$email]);
  $u = $stmt->fetch(PDO::FETCH_ASSOC);
  if($u && password_verify($pass, $u['password_hash'])){
    if($u['status']!=='approved' && $u['role']!=='admin'){
      flash('error','Your account is not approved yet.');
      redirect('/login.php');
    }
    $_SESSION['user'] = [
      'id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],
      'role'=>$u['role'],'status'=>$u['status']
    ];
    flash('success','Welcome back!');
    if($u['role']==='provider') redirect('/provider_dashboard.php');
    elseif($u['role']==='consumer') redirect('/consumer_dashboard.php');
    else redirect('/admin/dashboard.php');
  } else {
    flash('error','Invalid email or password');
  }
}
include __DIR__ . '/includes/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">Login</h5>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" required>
          </div>
          <button class="btn btn-primary w-100">Login</button>
        </form>
        <p class="form-help mt-3">No account? <a href="/register.php">Create one</a></p>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
