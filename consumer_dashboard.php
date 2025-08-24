
<?php require __DIR__ . '/includes/config.php';
if(!is_consumer()) redirect('/login.php');

$uid = user()['id'];

// Mark booking completed (after provider completes)
if(isset($_POST['review_booking_id'])){
  $bid = (int)$_POST['review_booking_id'];
  $rating = (int)$_POST['rating'];
  $comment = trim($_POST['comment']);
  $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id=? AND consumer_id=? AND status='completed'");
  $stmt->execute([$bid,$uid]);
  if($stmt->fetch()){
    $ins = $pdo->prepare("INSERT INTO reviews(booking_id,consumer_id,provider_id,rating,comment,created_at) SELECT b.id,b.consumer_id,b.provider_id,?,?,NOW() FROM bookings b WHERE b.id=?");
    $ins->execute([$rating,$comment,$bid]);
    flash('success','Thanks for your review!');
  }
  header("Location: /consumer_dashboard.php"); exit;
}

$bookings = $pdo->prepare("SELECT b.*, u.name as provider FROM bookings b JOIN users u ON u.id=b.provider_id WHERE b.consumer_id=? ORDER BY b.created_at DESC");
$bookings->execute([$uid]);
$rows = $bookings->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/includes/header.php'; ?>

<h4 class="mb-3">My Bookings</h4>
<div class="table-responsive">
<table class="table align-middle">
  <thead><tr><th>#</th><th>Provider</th><th>Date</th><th>Time</th><th>Hours</th><th>Status</th><th>Action</th></tr></thead>
  <tbody>
    <?php foreach($rows as $r): ?>
      <tr>
        <td><?= e($r['id']) ?></td>
        <td><?= e($r['provider']) ?></td>
        <td><?= e($r['date']) ?></td>
        <td><?= e($r['time']) ?></td>
        <td><?= e($r['hours']) ?></td>
        <td><span class="badge bg-<?= $r['status']==='accepted'?'success':($r['status']==='completed'?'secondary':($r['status']==='cancelled'?'danger':'warning')) ?>"><?= e(ucfirst($r['status'])) ?></span></td>
        <td>
          <?php if($r['status']==='pending'): ?>
            <form method="post" action="/helpers/cancel_booking.php" class="d-inline">
              <input type="hidden" name="id" value="<?= e($r['id']) ?>">
              <button class="btn btn-sm btn-outline-danger">Cancel</button>
            </form>
          <?php elseif($r['status']==='completed'): ?>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#rev<?= e($r['id']) ?>">Review</button>
            <div class="modal fade" id="rev<?= e($r['id']) ?>"><div class="modal-dialog"><div class="modal-content">
              <form method="post">
                <div class="modal-header"><h5 class="modal-title">Review Provider</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                  <input type="hidden" name="review_booking_id" value="<?= e($r['id']) ?>">
                  <label class="form-label">Rating (1-5)</label>
                  <input name="rating" type="number" min="1" max="5" class="form-control" required>
                  <label class="form-label mt-2">Comment</label>
                  <textarea name="comment" class="form-control" rows="3"></textarea>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Submit Review</button></div>
              </form>
            </div></div></div>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if(!$rows): ?><tr><td colspan="7" class="text-center text-muted">No bookings yet.</td></tr><?php endif; ?>
  </tbody>
</table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
