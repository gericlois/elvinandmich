<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

$file = $dataDir . '/rsvp.json';

$entries = [];
if (file_exists($file)) {
    $contents = file_get_contents($file);
    $decoded = json_decode($contents, true);
    if (is_array($decoded)) {
        $entries = $decoded;
    }
}

function clean($v) {
    return trim(strip_tags($v ?? ''));
}

$name       = clean($_POST['name']       ?? '');
$email      = clean($_POST['email']      ?? '');
$phone      = clean($_POST['phone']      ?? '');
$attendance = clean($_POST['attendance'] ?? '');
$guests     = clean($_POST['guests']     ?? '');
$diet       = clean($_POST['diet']       ?? '');
$role       = clean($_POST['role']       ?? '');
$message    = clean($_POST['message']    ?? '');

if ($name === '' || $email === '' || $attendance === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit;
}

$entry = [
    'id'           => uniqid(),
    'name'         => $name,
    'email'        => $email,
    'phone'        => $phone,
    'attendance'   => $attendance,
    'guests'       => $guests,
    'diet'         => $diet,
    'role'         => $role,
    'message'      => $message,
    'submitted_at' => date('Y-m-d H:i:s'),
    'ip'           => $_SERVER['REMOTE_ADDR'] ?? ''
];

$entries[] = $entry;

$fp = fopen($file, 'c+');
if ($fp && flock($fp, LOCK_EX)) {
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not save entry']);
}
