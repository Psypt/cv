<?php
header('Content-Type: application/json; charset=utf-8');
$file = __DIR__ . '/medication-state.json';

if($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(!file_exists($file)){
        echo json_encode((object)[]);
        exit;
    }
    $data = file_get_contents($file);
    if($data === false) http_response_code(500);
    echo $data;
    exit;
}

// POST: receive JSON and write it to disk
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $raw = file_get_contents('php://input');
    if($raw === false){ http_response_code(400); echo json_encode(['error'=>'no input']); exit; }
    // basic validation
    $decoded = json_decode($raw);
    if($decoded === null && json_last_error() !== JSON_ERROR_NONE){ http_response_code(400); echo json_encode(['error'=>'invalid json']); exit; }

    // write atomically
    $tmp = $file . '.tmp';
    if(file_put_contents($tmp, $raw) === false){ http_response_code(500); echo json_encode(['error'=>'write failed']); exit; }
    if(!rename($tmp, $file)){ http_response_code(500); echo json_encode(['error'=>'rename failed']); exit; }
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error'=>'method not allowed']);

?>
