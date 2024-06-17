<?php
include("../../../../config/conn.php");

$poliId = isset($_GET['poli_id']) ? $_GET['poli_id'] : null;

$dataJadwal = $conn->prepare('SELECT a.nama as nama_dokter, 
                                    b.hari as hari, 
                                    b.id as id_jp,
                                    b.jadwal_mulai as jam_mulai,
                                    b.jadwal_selesai as jam_selesai
                                    
                                    FROM dokter as a
                                    
                                    INNER JOIN jadwal_periksa as b
                                    ON a.id = b.id_dokter
                                    WHERE a.id_poli = :poli_id');
$dataJadwal->bindParam(':poli_id', $poliId);
$dataJadwal->execute();

if ($dataJadwal->rowCount() == 0) {
    echo '<option>Tidak ada jadwal</option>';
} else {
    while ($jd = $dataJadwal->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $jd['id_jp'] . '"> Dokter ' . $jd['nama_dokter'] . ' | ' . $jd['hari'] . ' | ' . $jd['jam_mulai'] . ' - ' . $jd['jam_selesai'] . '</option>';
    }
}
?>
