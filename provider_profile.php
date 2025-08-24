
<?php require __DIR__ . '/includes/config.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT u.*, GROUP_CONCAT(c.name SEPARATOR ', ') as skills
  FROM users u
  LEFT JOIN provider_skills ps ON ps.user_id=u.id
  LEFT JOIN categories c ON c.id=ps.category_id
  WHERE u.id=? AND u.role='provider' AND u.status='approved' GROUP BY u.id");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$p){ flash('error','Provider not found.'); redirect('/services.php'); }
include __DIR__ . '/includes/header.php'; ?>

<div class="row g-3">
  <div class="col-md-4">
    <div class="card">
      <img src="https://api.dicebear.com/9.x/notionists/svg?seed=<?= urlencode($p['name']) ?>" class="card-img-top" alt="">
      <div class="card-body">
        <h5><?= e($p['name']) ?></h5>
        <div class="text-muted"><?= e($p['city']) ?></div>
        <div class="mt-2"><span class="badge bg-light text-dark"><?= e($p['skills'] ?: 'No skills set') ?></span></div>
        <p class="mt-2"><?= nl2br(e($p['bio'])) ?></p>
        <?php if(is_consumer()): ?>
          <a class="btn btn-primary" href="/book.php?provider_id=<?= e($p['id']) ?>">Book this provider</a>
        <?php else: ?>
          <div class="alert alert-small alert-info mt-2">Login as consumer to book.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <h6>Recent Reviews</h6>
        <?php
          $rs = $pdo->prepare("SELECT r.*, u.name as consumer FROM reviews r JOIN users u ON u.id=r.consumer_id WHERE r.provider_id=? ORDER BY r.created_at DESC LIMIT 6");
          $rs->execute([$p['id']]);
          $reviews = $rs->fetchAll(PDO::FETCH_ASSOC);
          if(!$reviews) echo '<div class="text-muted">No reviews yet.</div>';
          foreach($reviews as $r){
            echo '<div class="border-bottom py-2"><strong>'.e($r['consumer']).'</strong> — ⭐'.e($r['rating']).'<br><small>'.e($r['comment']).'</small></div>';
          }
        ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
