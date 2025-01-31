<?php
header('Content-Type: application/json');
require_once '../classes/FSNManager.php';

try {
    if (!isset($_GET['group_id']) || !isset($_GET['class_id']) || !isset($_GET['type_id'])) {
        throw new Exception('Missing required parameters');
    }

    $fsnManager = new FSNManager();
    $fsnNumber = $fsnManager->generateFSNNumber(
        $_GET['group_id'],
        $_GET['class_id'],
        $_GET['type_id']
    );
    
    if (!$fsnNumber) {
        throw new Exception('Failed to generate FSN number');
    }

    echo json_encode(array(
        'success' => true,
        'fsn_number' => $fsnNumber
    ));

} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}