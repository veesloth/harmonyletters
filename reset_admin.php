
<?php
require_once __DIR__ . '/db_connect.php';

header('Content-Type: text/plain; charset=utf-8');

$username = 'admin';
$newPassword = 'HarmonyAdmin2026!';

echo "Database connected: " .
     $pdo->query("SELECT DATABASE()")->fetchColumn() . "\n";

$stmt = $pdo->prepare(
    "SELECT user_id, username, role
     FROM users
     WHERE username = ?
     LIMIT 1"
);
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    exit("ERROR: The admin account was not found in this database.\n");
}

echo "Account found: {$user['username']}\n";
echo "Current role: {$user['role']}\n";

$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

$update = $pdo->prepare(
    "UPDATE users SET password_hash = ? WHERE user_id = ?"
);
$update->execute([$newHash, $user['user_id']]);

$check = $pdo->prepare(
    "SELECT password_hash FROM users WHERE user_id = ?"
);
$check->execute([$user['user_id']]);
$savedHash = $check->fetchColumn();

if (password_verify($newPassword, $savedHash)) {
    echo "Password reset and verification: SUCCESS\n";
    echo "Username: admin\n";
    echo "Temporary password: HarmonyAdmin2026!\n";
} else {
    echo "ERROR: Password verification failed after updating.\n";
}
?>
