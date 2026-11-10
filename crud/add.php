<!DOCTYPE html>
<html>
<head>
    <title>Tambah Data Alat</title>
</head>
<body>
    <h2>Tambah Data Alat Medis</h2>
    <a href="index.php">Kembali ke Beranda</a><br/><br/>

    <form action="add.php" method="post" name="form1">
        <table width="40%" border="0">
            <tr> 
                <td>Nama Alat</td>
                <td><input type="text" name="nama_alat" required></td>
            </tr>
            <tr> 
                <td>Tahun</td>
                <td><input type="number" name="tahun" min="1900" max="2099" required></td>
            </tr>
            <tr> 
                <td>Merek</td>
                <td><input type="text" name="merek" required></td>
            </tr>
            <tr> 
                <td>Lokasi</td>
                <td><input type="text" name="lokasi" required></td>
            </tr>
            <tr> 
                <td></td>
                <td><input type="submit" name="Submit" value="Simpan"></td>
            </tr>
        </table>
    </form>

    <?php
    if(isset($_POST['Submit'])) {
        $nama_alat = $_POST['nama_alat'];
        $tahun = $_POST['tahun'];
        $merek = $_POST['merek'];
        $lokasi = $_POST['lokasi'];

        include_once("config.php");

        $result = mysqli_query($mysqli, "INSERT INTO alat(nama_alat,tahun,merek,lokasi) VALUES('$nama_alat','$tahun','$merek','$lokasi')");

        echo "<br/>Data berhasil ditambahkan. <a href='index.php'>Lihat Data</a>";
    }
    ?>
</body>
</html>
