
<?php include __DIR__ . "/includes/header.php"; ?>

<div class="hero text-center">
  <h1 class="display-6 mb-2">Find trusted local help — fast</h1>
  <p class="lead text-muted mb-4">Plumbers, Electricians, Welders, Painters, Carpenters & more.</p>
  <a class="btn btn-primary btn-rounded px-4" href="/services.php">Browse Services</a>
  <a class="btn btn-outline-primary btn-rounded px-4 ms-2" href="/register.php">Become a Provider</a>
</div>

<div class="row g-3">
<?php
$cats = $pdo->query("SELECT id,name,icon FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
foreach($cats as $c): ?>
  <div class="col-6 col-md-4 col-lg-3">
    <a class="card h-100" href="/services.php?category=<?= e($c['id']) ?>">
      <div class="card-body text-center">
        <div style="font-size:28px"><?= e($c['icon']) ?></div>
        <div class="fw-bold mt-2"><?= e($c['name']) ?></div>
      </div>
    </a>
  </div>
<?php endforeach; ?>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
