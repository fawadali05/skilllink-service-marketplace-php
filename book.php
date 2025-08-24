
<?php require __DIR__ . '/includes/config.php';
if(!is_consumer()){ flash('error','Please login as consumer to book.'); redirect('/login.php'); }
$consumer_id = user()['id'];
$provider_id = (int)($_GET['provider_id'] ?? $_POST['provider_id'] ?? 0);

// fetch provider
$ps = $pdo->prepare("SELECT id,name FROM users WHERE id=? AND role='provider' AND status='approved'");
$ps->execute([$provider_id]);
$prov = $ps->fetch(PDO::FETCH_ASSOC);
if(!$prov){ flash('error','Provider not found.'); redirect('/services.php'); }

if($_SERVER['REQUEST_METHOD']==='POST'){
  $date = $_POST['date'] ?? '';
  $time = $_POST['time'] ?? '';
  $hours = (int)($_POST['hours'] ?? 1);
  $address = trim($_POST['address'] ?? '');
  $notes = trim($_POST['notes'] ?? '');

  $stmt = $pdo->prepare("INSERT INTO bookings(consumer_id,provider_id,date,time,hours,address,notes,status,created_at) VALUES(?,?,?,?,?,?,?,'pending',NOW())");
  $stmt->execute([$consumer_id,$provider_id,$date,$time,$hours,$address,$notes]);
  flash('success','Booking request sent to provider.');
  redirect('/consumer_dashboard.php');
}

include __DIR__ . '/includes/header.php'; ?>
<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card">
      <div class="card-body">
        <h5 class="mb-2">Book: <?= e($prov['name']) ?></h5>
        <form method="post">
          <input type="hidden" name="provider_id" value="<?= e($provider_id) ?>">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Date</label>
              <input type="date" name="date" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Time</label>
              <input type="time" name="time" class="form-control" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Estimated Hours</label>
              <input type="number" name="hours" class="form-control" value="2" min="1" max="12">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Service Address</label>
              <input name="address" class="form-control" placeholder="House #, Street, City" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Job Details</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Describe the issue..."></textarea>
          </div>
          <button class="btn btn-primary">Send Request</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
