<?php
include_once("config.php");

$result = mysqli_query($mysqli, "SELECT * FROM alat ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Alat Medis - SIMRS</title>
</head>
<body>
    <h2>Data Peralatan Medis</h2>
    <a href="add.php">+ Tambah Data Alat</a><br/><br/>

    <table width='80%' border=1 cellpadding=5 cellspacing=0>
        <tr bgcolor='#CCCCCC'>
            <th>No</th>
            <th>Nama Alat</th>
            <th>Tahun</th>
            <th>Merek</th>
            <th>Lokasi</th>
            <th>Aksi</th>
        </tr>
        <?php  
        $no = 1;
        while($user_data = mysqli_fetch_array($result)) {         
            echo "<tr>";
            echo "<td>".$no++."</td>";
            echo "<td>".$user_data['nama_alat']."</td>";
            echo "<td>".$user_data['tahun']."</td>";
            echo "<td>".$user_data['merek']."</td>";    
            echo "<td>".$user_data['lokasi']."</td>";    
            echo "<td><a href='edit.php?id=$user_data[id]'>Edit</a> | <a href='delete.php?id=$user_data[id]' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Delete</a></td></tr>";        
        }
        ?>
    </table>
</body>
</html>
