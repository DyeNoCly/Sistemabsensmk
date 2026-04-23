<?php
require_once "../config/conn.php";

$nis = $_POST['nis'];
$idm = $_POST['idm'];
$tanggal = $_POST['tanggal'];
$status = $_POST['status'];

$cek = $pdo->prepare("SELECT id FROM absensi WHERE nis=? AND idm=? AND tanggal=?");
$cek->execute([$nis,$idm,$tanggal]);

if($cek->rowCount()==0){

$stmt = $pdo->prepare("INSERT INTO absensi(nis,idm,tanggal,status) VALUES(?,?,?,?)");
$stmt->execute([$nis,$idm,$tanggal,$status]);

}else{

$stmt = $pdo->prepare("UPDATE absensi SET status=? WHERE nis=? AND idm=? AND tanggal=?");
$stmt->execute([$status,$nis,$idm,$tanggal]);

}

echo "success";