<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=sms_db', 'root', '');
    echo "Connected!\n";
    $stmt = $pdo->query('SELECT * FROM site_settings LIMIT 1');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Fetched:\n";
    print_r($row);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
