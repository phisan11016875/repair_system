<?php
session_start();
require_once '../configdb.php';
require_once '../classes/AssetManagement.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['psd_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing psd_id parameter']);
    exit();
}

$assetManager = new AssetManagement();
$asset = $assetManager->getAssetById($_GET['psd_id']);

if ($asset) {
    echo json_encode($asset);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Asset not found']);
}
