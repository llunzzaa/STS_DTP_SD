<?php
$conn = new mysqli("localhost", "root", "", "db_laporan_fasilitas");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST["nama_pelapor"];
    $fasilitas = $_POST["nama_fasilitas"];
    $lokasi = $_POST["lokasi"];
    $kategori = $_POST["id_kategori"];
    $deskripsi = $_POST["deskripsi"];
    $prioritas = $_POST["prioritas"];

    $stmt = $conn->prepare(
        "INSERT INTO laporan_kerusakan
        (nama_pelapor, nama_fasilitas, lokasi, id_kategori, deskripsi, prioritas)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssiss", $nama, $fasilitas, $lokasi,
        $kategori, $deskripsi, $prioritas
    );

    $stmt->execute();

    header("Location: index.php");
    exit;
}

$data = $conn->query(
    "SELECT l.*, k.nama_kategori
    FROM laporan_kerusakan l
    JOIN kategori_fasilitas k
    ON l.id_kategori = k.id_kategori"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pelaporan Kerusakan Fasilitas Sekolah</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    margin: 20px;
    color: #1e293b;
}
header, section {
    background: white;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
}
header {
    background: #2563eb;
    color: white;
}
input, select, textarea {
    width: 100%;
    padding: 8px;
    margin: 5px 0 12px;
    box-sizing: border-box;
}
button {
    background: #2563eb;
    color: white;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}
th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
th {
    background: #dbeafe;
}
</style>
</head>
<body>

<header>
    <h1>Sistem Pelaporan Kerusakan Fasilitas Sekolah</h1>
    <p>Catat dan pantau laporan kerusakan fasilitas sekolah.</p>
    <a href="#form-laporan">Buat Laporan</a> |
    <a href="#daftar-laporan">Daftar Laporan</a>
</header>

<main>
<section id="form-laporan">
    <h2>Form Pelaporan Kerusakan</h2>

    <form method="post">
        <label>Nama pelapor</label>
        <input type="text" name="nama_pelapor" required>

        <label>Nama fasilitas</label>
        <input type="text" name="nama_fasilitas" required>

        <label>Lokasi kerusakan</label>
        <input type="text" name="lokasi" required>

        <label>Kategori fasilitas</label>
        <select name="id_kategori" required>
            <option value="">Pilih kategori</option>
            <?php
            $kategori = $conn->query(
                "SELECT * FROM kategori_fasilitas"
            );
            while ($k = $kategori->fetch_assoc()) {
                echo "<option value='" . $k["id_kategori"] . "'>"
                    . htmlspecialchars($k["nama_kategori"]) .
                    "</option>";
            }
            ?>
        </select>

        <label>Deskripsi kerusakan</label>
        <textarea name="deskripsi" required></textarea>

        <label>Prioritas</label>
        <select name="prioritas" required>
            <option value="">Pilih prioritas</option>
            <option value="Rendah">Rendah</option>
            <option value="Sedang">Sedang</option>
            <option value="Tinggi">Tinggi</option>
        </select>

        <button type="submit">Kirim Laporan</button>
    </form>
</section>

<section id="daftar-laporan">
    <h2>Daftar Laporan Kerusakan</h2>

    <table>
        <tr>
            <th>No.</th>
            <th>Fasilitas</th>
            <th>Lokasi</th>
            <th>Pelapor</th>
            <th>Prioritas</th>
            <th>Status</th>
            <th>Tanggal</th>
        </tr>

        <?php
        $no = 1;
        while ($row = $data->fetch_assoc()) {
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row["nama_fasilitas"]) ?></td>
            <td><?= htmlspecialchars($row["lokasi"]) ?></td>
            <td><?= htmlspecialchars($row["nama_pelapor"]) ?></td>
            <td><?= htmlspecialchars($row["prioritas"]) ?></td>
            <td><?= htmlspecialchars($row["status"]) ?></td>
            <td><?= htmlspecialchars($row["tanggal_laporan"]) ?></td>
        </tr>
        <?php } ?>
    </table>
</section>
</main>

<footer>
    <p>&copy; 2026 Sistem Pelaporan Kerusakan Fasilitas Sekolah.</p>
</footer>

</body>
</html>
<?php $conn->close(); ?>