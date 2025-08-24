
<?php
// Start session & define DB config (adjust if needed)
if (session_status() === PHP_SESSION_NONE) session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'servicemarket');
define('DB_USER', 'root');
define('DB_PASS', '');
define('APP_NAME', 'SkillLink — Hire Local Experts');

date_default_timezone_set('Asia/Karachi');

// connect PDO
try {
  $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
  $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
  die("DB connection failed. Create DB '" . DB_NAME . "' then run setup.php. Error: " . htmlspecialchars($e->getMessage()));
}

// helpers
function e($str){ return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
function is_logged(){ return isset($_SESSION['user']); }
function user(){ return $_SESSION['user'] ?? null; }
function is_admin(){ return isset($_SESSION['user']) && $_SESSION['user']['role']==='admin'; }
function is_provider(){ return isset($_SESSION['user']) && $_SESSION['user']['role']==='provider'; }
function is_consumer(){ return isset($_SESSION['user']) && $_SESSION['user']['role']==='consumer'; }

function flash($key, $val=null){
  if($val===null){ $msg = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $msg; }
  $_SESSION['flash'][$key] = $val;
}

function redirect($path){ header("Location: " . $path); exit; }
?>
