
<?php require __DIR__ . '/includes/config.php';
session_destroy();
session_start();
flash('success','You are logged out.');
header("Location: /index.php");
exit;
