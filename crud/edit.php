<?php
include_once("config.php");

if(isset($_POST['update'])) {   
    $id = $_POST['id'];
    $nama_alat = $_POST['nama_alat'];
    $tahun = $_POST['tahun'];
    $merek = $_POST['merek'];
    $lokasi = $_POST['lokasi'];

    $result = mysqli_query($mysqli, "UPDATE alat SET nama_alat='$nama_alat',tahun='$tahun',merek='$merek',lokasi='$lokasi' WHERE id=$id");

    header("Location: index.php");
}

$id = $_GET['id'];
$result = mysqli_query($mysqli, "SELECT * FROM alat WHERE id=$id");

while($user_data = mysqli_fetch_array($result)) {
    $nama_alat = $user_data['nama_alat'];
    $tahun = $user_data['tahun'];
    $merek = $user_data['merek'];
    $lokasi = $user_data['lokasi'];
}
?>
<!DOCTYPE html>
<html>
<head>  
    <title>Edit Data Alat</title>
</head>
<body>
    <h2>Edit Data Alat Medis</h2>
    <a href="index.php">Home</a><br/><br/>

    <form name="update_user" method="post" action="edit.php">
        <table border="0">
            <tr> 
                <td>Nama Alat</td>
                <td><input type="text" name="nama_alat" value="<?php echo $nama_alat;?>"></td>
            </tr>
            <tr> 
                <td>Tahun</td>
                <td><input type="number" name="tahun" value="<?php echo $tahun;?>"></td>
            </tr>
            <tr> 
                <td>Merek</td>
                <td><input type="text" name="merek" value="<?php echo $merek;?>"></td>
            </tr>
            <tr> 
                <td>Lokasi</td>
                <td><input type="text" name="lokasi" value="<?php echo $lokasi;?>"></td>
            </tr>
            <tr>
                <td><input type="hidden" name="id" value="<?php echo $_GET['id'];?>"></td>
                <td><input type="submit" name="update" value="Update"></td>
            </tr>
        </table>
    </form>
</body>
</html>
