
<?php require __DIR__ . '/includes/config.php';
if(!is_provider()) redirect('/login.php');

$uid = user()['id'];

// Handle profile update
if(isset($_POST['update_profile'])){
  $bio = trim($_POST['bio'] ?? '');
  $city = trim($_POST['city'] ?? '');
  $stmt = $pdo->prepare("UPDATE users SET bio=?, city=? WHERE id=?");
  $stmt->execute([$bio,$city,$uid]);
  flash('success','Profile updated.');
  header("Location: /provider_dashboard.php"); exit;
}

// Accept / decline / complete booking
if(isset($_POST['act'], $_POST['id'])){
  $id = (int)$_POST['id'];
  if($_POST['act']==='accept'){
    $stmt = $pdo->prepare("UPDATE bookings SET status='accepted' WHERE id=? AND provider_id=? AND status='pending'");
    $stmt->execute([$id,$uid]);
  } elseif($_POST['act']==='decline'){
    $stmt = $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND provider_id=? AND status IN ('pending','accepted')");
    $stmt->execute([$id,$uid]);
  } elseif($_POST['act']==='complete'){
    $stmt = $pdo->prepare("UPDATE bookings SET status='completed' WHERE id=? AND provider_id=? AND status='accepted'");
    $stmt->execute([$id,$uid]);
  }
  flash('success','Action applied.');
  header("Location: /provider_dashboard.php"); exit;
}

// Fetch data
$me = $pdo->prepare("SELECT * FROM users WHERE id=?");
$me->execute([$uid]);
$me = $me->fetch(PDO::FETCH_ASSOC);

$bookings = $pdo->prepare("SELECT b.*, u.name as consumer FROM bookings b JOIN users u ON u.id=b.consumer_id WHERE b.provider_id=? ORDER BY FIELD(status,'pending','accepted','completed','cancelled'), created_at DESC");
$bookings->execute([$uid]);
$rows = $bookings->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/includes/header.php'; ?>

<div class="row">
  <div class="col-lg-4">
    <div class="card mb-3">
      <div class="card-body">
        <h5>My Profile</h5>
        <form method="post">
          <input type="hidden" name="update_profile" value="1">
          <div class="mb-2">
            <label class="form-label">City</label>
            <input name="city" class="form-control" value="<?= e($me['city']) ?>">
          </div>
          <div class="mb-2">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-control" rows="4" placeholder="Tell customers about your experience..."><?= e($me['bio']) ?></textarea>
          </div>
          <button class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">Incoming Bookings</h5>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>#</th><th>Consumer</th><th>Date</th><th>Time</th><th>Hours</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
              <?php foreach($rows as $r): ?>
                <tr>
                  <td><?= e($r['id']) ?></td>
                  <td><?= e($r['consumer']) ?></td>
                  <td><?= e($r['date']) ?></td>
                  <td><?= e($r['time']) ?></td>
                  <td><?= e($r['hours']) ?></td>
                  <td><span class="badge bg-<?= $r['status']==='accepted'?'success':($r['status']==='completed'?'secondary':($r['status']==='cancelled'?'danger':'warning')) ?>"><?= e(ucfirst($r['status'])) ?></span></td>
                  <td>
                    <form method="post" class="d-flex gap-1">
                      <input type="hidden" name="id" value="<?= e($r['id']) ?>">
                      <?php if($r['status']==='pending'): ?>
                        <button name="act" value="accept" class="btn btn-sm btn-success">Accept</button>
                        <button name="act" value="decline" class="btn btn-sm btn-outline-danger">Decline</button>
                      <?php elseif($r['status']==='accepted'): ?>
                        <button name="act" value="complete" class="btn btn-sm btn-primary">Mark Completed</button>
                        <button name="act" value="decline" class="btn btn-sm btn-outline-danger">Cancel</button>
                      <?php else: ?>
                        <span class="text-muted">No actions</span>
                      <?php endif; ?>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if(!$rows): ?><tr><td colspan="7" class="text-center text-muted">No bookings yet.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
