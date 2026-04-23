<?php
$host = '127.0.0.1';
$db = 'sisabsi';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Delete any existing active schedule for class 7 today
$conn->query("UPDATE jadwal SET aktif=0 WHERE idk=7 AND idh=4");

// Create a new active schedule for class 7 on Wednesday from 20:30-21:30
// Using subject 1 (Matematika) and teacher 9
$result = $conn->query("SELECT MAX(idj) as max_id FROM jadwal");
$max_id = intval($result->fetchColumn()) + 1;

$sql = "INSERT INTO jadwal (idj, idm, idg, idk, idh, jam_mulai, jam_selesai, aktif) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([
    $max_id,           // idj - new ID
    1,                 // idm - subject ID (Matematika)
    9,                 // idg - teacher ID
    7,                 // idk - class VII (Zildjian's class)
    4,                 // idh - day 4 (Wednesday)
    '20:30:00',       // jam_mulai
    '21:30:00',       // jam_selesai
    1                  // aktif - mark as active
]);

echo "Active schedule created for class 7 (Zildjian's class)!\n";
echo "Subject: Matematika (ID:1)\n";
echo "Teacher: ID 9\n";
echo "Time: 20:30-21:30 (Wednesday)\n";
echo "Schedule ID: $max_id\n";
?>
