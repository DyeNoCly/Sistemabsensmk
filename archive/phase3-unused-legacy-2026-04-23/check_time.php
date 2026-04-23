<?php
// Direct connection string for testing
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

// Check current time and day
$result = $conn->query("SELECT DATE(NOW()) as curr_date, TIME(NOW()) as curr_time, DAYOFWEEK(NOW()) as day_num, DAYNAME(NOW()) as day_name");
$row = $result->fetch(PDO::FETCH_ASSOC);
echo "Current Date: " . $row['curr_date'] . "\n";
echo "Current Time: " . $row['curr_time'] . "\n";
echo "Day Number: " . $row['day_num'] . " (1=Sun, 2=Mon, ..., 7=Sat)\n";
echo "Day Name: " . $row['day_name'] . "\n";

// Check schedules for class 8
echo "\n--- JADWAL untuk Kelas VIII (idk=8) ---\n";
$result = $conn->query("SELECT idj, idm, idg, idk, idh, jam_mulai, jam_selesai, aktif FROM jadwal WHERE idk=8 ORDER BY idh, jam_mulai");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $days = [1=>'Min', 2=>'Sen', 3=>'Sel', 4=>'Rab', 5=>'Kam', 6=>'Jum', 7=>'Sab'];
    echo sprintf("ID:%2d | Hari:%s(%s) | Jam:%s-%s | Mata:%d | Guru:%d | Aktif:%s\n", 
        $row['idj'], $days[$row['idh']] ?? '?', $row['idh'], 
        substr($row['jam_mulai'],0,5), substr($row['jam_selesai'],0,5), 
        $row['idm'], $row['idg'], $row['aktif']);
}
?>
