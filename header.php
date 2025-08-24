
<?php require_once __DIR__ . '/config.php'; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
      <div class="container">
        <a class="navbar-brand" href="/index.php"><?= e(APP_NAME) ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="/services.php">Browse</a></li>
            <li class="nav-item"><a class="nav-link" href="/providers.php">Providers</a></li>
            <?php if(is_admin()): ?>
              <li class="nav-item"><a class="nav-link" href="/admin/dashboard.php">Admin</a></li>
            <?php endif; ?>
          </ul>
          <ul class="navbar-nav ms-auto">
            <?php if(is_logged()): $u=user(); ?>
              <li class="nav-item"><span class="nav-link">Hi, <?= e($u['name']) ?> (<?= e($u['role']) ?>)</span></li>
              <?php if(is_consumer()): ?>
                <li class="nav-item"><a class="btn btn-sm btn-primary" href="/consumer_dashboard.php">My Dashboard</a></li>
              <?php elseif(is_provider()): ?>
                <li class="nav-item"><a class="btn btn-sm btn-primary" href="/provider_dashboard.php">My Dashboard</a></li>
              <?php endif; ?>
              <li class="nav-item ms-2"><a class="btn btn-sm btn-outline-secondary" href="/logout.php">Logout</a></li>
            <?php else: ?>
              <li class="nav-item"><a class="nav-link" href="/login.php">Login</a></li>
              <li class="nav-item"><a class="btn btn-sm btn-primary" href="/register.php">Sign up</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>

    <main class="container my-4">
      <?php if($m = flash('success')): ?>
        <div class="alert alert-success"><?= e($m) ?></div>
      <?php endif; ?>
      <?php if($m = flash('error')): ?>
        <div class="alert alert-danger"><?= e($m) ?></div>
      <?php endif; ?>
