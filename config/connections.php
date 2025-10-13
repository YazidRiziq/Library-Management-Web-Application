<?php 
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "latihanphp";

    $conn = mysqli_connect($host, $user, $pass, $db);

    //untuk cek koneksi berhasil atau tidak
    if (!$conn) {
        die("Koneksi Gagal: " . mysqli_connect_error());
    }

?>