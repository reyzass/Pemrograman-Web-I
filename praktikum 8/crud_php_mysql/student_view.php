<?php
session_start();
if (!isset($_SESSION["username"])) {
   header("Location: login.php");
   exit();
}

// Memeriksa apakah $_SESSION['start_time'] sudah diatur
if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = time(); // Jika belum diatur, atur waktu sekarang
} else {
    // Jika sudah diatur, periksa apakah waktu telah berlalu 5 detik
    if (time() - $_SESSION['start_time'] > 5) {
        session_unset(); // Hapus semua data session
        session_destroy(); // Hapus session
        header("Location: login.php"); // Redirect ke halaman login
        exit();
    } else {
        $_SESSION['start_time'] = time(); // Perbarui waktu mulai
    }
}

include("connection.php");

if (isset($_GET["message"])) {
    $message = $_GET["message"];
}

if (isset($_GET["search"])) {
    $search = mysqli_real_escape_string($connection, $_GET["search"]);
    $query = "SELECT * FROM student WHERE nim LIKE '%$search%' OR name LIKE '%$search%' ORDER BY nim ASC";
} else {
    $query = "SELECT * FROM student ORDER BY nim ASC";
}

$result = mysqli_query($connection, $query); // Eksekusi query ke database
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Mahasiswa</title>
  <link href="assets/style.css" rel="stylesheet" >
</head>
<body>
  <div class="container">
    <div id="header">
      <h1 id="logo">Data Mahasiswa</h1>
    </div>
    <hr>
    <nav>
      <ul>
        <li><a href="student_view.php">Tampil</a></li>
        <li><a href="student_add.php">Tambah</a>
        <li><a href="logout.php">Logout</a>
      </ul>
    </nav>
    <?php
      if (isset($message)) {
          echo "<div class='pesan'>$message</div>";
      }
    ?>
    <table border="1">
    <tr>
      <th>NIM</th>
      <th>Nama</th>
      <th>Tempat Lahir</th>
      <th>Tanggal Lahir</th>
      <th>Fakultas</th>
      <th>Jurusan</th>
      <th>IPK</th>
    </tr>
    <?php
      $result = mysqli_query($connection, $query);
      if(!$result) {
          die ("Query Error: ".mysqli_errno($connection)." - ".mysqli_error($connection));
      }
    
      while($data = mysqli_fetch_assoc($result)){ 
        $birth_date = strtotime($data["birth_date"]);
        $formatted_date = date("d-m-Y", $birth_date);
        
        echo "<tr>";
        echo "<td>$data[nim]</td>";
        echo "<td>$data[name]</td>";
        echo "<td>$data[birth_city]</td>";
        echo "<td>$formatted_date</td>";
        echo "<td>$data[faculty]</td>";
        echo "<td>$data[department]</td>";
        echo "<td>$data[gpa]</td>";
        echo "</tr>";
      }
      
      mysqli_free_result($result);
      mysqli_close($connection);
    ?>
    </table>
  </div>
</body>
</html>