
<?php require __DIR__ . '/includes/config.php';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$city = trim($_GET['city'] ?? '');
$q = trim($_GET['q'] ?? '');

$sql = "SELECT u.id,u.name,u.city,u.bio, u.created_at, GROUP_CONCAT(c.name SEPARATOR ', ') as skills
        FROM users u
        LEFT JOIN provider_skills ps ON ps.user_id=u.id
        LEFT JOIN categories c ON c.id=ps.category_id
        WHERE u.role='provider' AND u.status='approved'";
$params = [];
if($category){ $sql .= " AND ps.category_id=?"; $params[] = $category; }
if($city!==''){ $sql .= " AND u.city LIKE ?"; $params[] = "%$city%"; }
if($q!==''){ $sql .= " AND (u.name LIKE ? OR u.bio LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
$sql .= " GROUP BY u.id ORDER BY u.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pros = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cats = $pdo->query("SELECT id,name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/includes/header.php'; ?>

<div class="card mb-3">
  <div class="card-body">
    <form class="row g-2">
      <div class="col-md-4">
        <input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search providers or skills...">
      </div>
      <div class="col-md-3">
        <select class="form-select" name="category">
          <option value="0">All categories</option>
          <?php foreach($cats as $c): ?>
            <option value="<?= e($c['id']) ?>" <?= $category==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <input class="form-control" name="city" value="<?= e($city) ?>" placeholder="City">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100">Filter</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-3">
<?php foreach($pros as $p): ?>
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2">
          <img class="avatar me-2" src="https://api.dicebear.com/9.x/initials/svg?seed=<?= urlencode($p['name']) ?>" alt="">
          <div>
            <div class="fw-bold"><?= e($p['name']) ?></div>
            <small class="text-muted"><?= e($p['city']) ?></small>
          </div>
        </div>
        <div><span class="badge bg-light text-dark"><?= e($p['skills'] ?: 'No skills set') ?></span></div>
        <p class="mt-2 text-muted"><?= e($p['bio'] ?: 'No bio yet.') ?></p>
        <a class="btn btn-sm btn-primary" href="/provider_profile.php?id=<?= e($p['id']) ?>">View Profile</a>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<?php if(!$pros): ?>
  <div class="col-12"><div class="alert alert-info">No providers found. Try removing some filters.</div></div>
<?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
