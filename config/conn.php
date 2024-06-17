<?php
$servername = "localhost";
$username = "root";
$password = "";

try {
  $conn = new PDO("mysql:host=$servername;dbname=bk_poliklinik", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

function insertDokter($nama, $alamat, $no_hp, $poli, $conn) {
  try {
      // Prepare SELECT statement to fetch poli_id based on selected 'poli'
      $sql_poli = "SELECT id FROM poli WHERE id = :poli";
      $stmt_poli = $conn->prepare($sql_poli);
      $stmt_poli->bindParam(':poli', $poli);
      $stmt_poli->execute();

      // Fetch poli_id
      $poli_id = $stmt_poli->fetchColumn();

      // Prepare INSERT statement for dokter
      $sql_dokter = "INSERT INTO dokter (nama, alamat, no_hp, id_poli) VALUES (:nama, :alamat, :no_hp, :poli_id)";
      $stmt_dokter = $conn->prepare($sql_dokter);

      // Bind parameters
      $stmt_dokter->bindParam(':nama', $nama);
      $stmt_dokter->bindParam(':alamat', $alamat);
      $stmt_dokter->bindParam(':no_hp', $no_hp);
      $stmt_dokter->bindParam(':poli_id', $poli_id);

      // Execute statement
      $stmt_dokter->execute();
      // Return success message
      return "Record updated successfully";
  } catch (PDOException $e) {
      // Handle database error
      echo "Error: " . $e->getMessage();
  }
}

function deleteDokter($id, $conn) {
  try {
      // Prepare DELETE statement
      $sql = "DELETE FROM dokter WHERE id = :id";
      $stmt = $conn->prepare($sql);

      // Bind parameters
      $stmt->bindParam(':id', $id);

      // Execute statement
      $stmt->execute();

      header('Refresh: 0, url:dokter.php');
  } catch (PDOException $e) {
      // Handle database error
      echo "Error: " . $e->getMessage();
  }
}

// Function to update doctor data
function updateDokter2($id, $nama, $alamat, $no_hp, $id_poli, $conn) {
  try {
      $sql_dokter = "UPDATE dokter SET nama = :nama, alamat = :alamat, no_hp = :no_hp, id_poli = :id_poli WHERE id = :id";
      $stmt_dokter = $conn->prepare($sql_dokter);

      // Bind parameters
      $stmt_dokter->bindParam(':nama', $nama, PDO::PARAM_STR);
      $stmt_dokter->bindParam(':alamat', $alamat, PDO::PARAM_STR);
      $stmt_dokter->bindParam(':no_hp', $no_hp, PDO::PARAM_STR);
      $stmt_dokter->bindParam(':id_poli', $id_poli, PDO::PARAM_INT);
      $stmt_dokter->bindParam(':id', $id, PDO::PARAM_INT);

      // Execute statement
      $stmt_dokter->execute();

      // Return success message
      return "Record updated successfully";
  } catch (PDOException $e) {
      // Handle database error
      return "Error: " . $e->getMessage();
  }
}

function updateDokter($id, $nama, $alamat, $no_hp, $poli, $conn) {
  try {
      // Prepare SELECT statement to fetch poli_id based on selected 'poli'
      $sql_poli = "SELECT id FROM poli WHERE id = :poli";
      $stmt_poli = $conn->prepare($sql_poli);
      $stmt_poli->bindParam(':poli', $poli, PDO::PARAM_INT);
      $stmt_poli->execute();

      // Fetch poli_id
      $poli_id = $stmt_poli->fetchColumn();

      if ($poli_id) {
          // Prepare UPDATE statement for dokter
          $sql_dokter = "UPDATE dokter SET nama = :nama, alamat = :alamat, no_hp = :no_hp, id_poli = :poli_id WHERE id = :id";
          $stmt_dokter = $conn->prepare($sql_dokter);

          // Bind parameters
          $stmt_dokter->bindParam(':nama', $nama, PDO::PARAM_STR);
          $stmt_dokter->bindParam(':alamat', $alamat, PDO::PARAM_STR);
          $stmt_dokter->bindParam(':no_hp', $no_hp, PDO::PARAM_STR);
          $stmt_dokter->bindParam(':poli_id', $poli_id, PDO::PARAM_INT);
          $stmt_dokter->bindParam(':id', $id, PDO::PARAM_INT);

          // Execute statement
          $stmt_dokter->execute();

          // Return success message
          return "Record updated successfully";
      } else {
          // If poli_id is not found
          return "Invalid poli selected";
      }
  } catch (PDOException $e) {
      // Handle database error
      return "Error: " . $e->getMessage();
  }
}

function getDokter($conn, $id_dokter) {
  $query = 'SELECT * FROM dokter WHERE id = :id';
  $stmt = $conn->prepare($query);
  $stmt->bindParam(':id', $id_dokter, PDO::PARAM_INT);
  $stmt->execute();

  if ($stmt->rowCount() == 1) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
  } else {
      return null;
  }
}

function insertPasien($nama, $alamat, $no_hp, $no_ktp, $no_rm, $conn) {
  try {
      // Begin a transaction
      $conn->beginTransaction();

      // Query to get the maximum number from no_rm in the format YYYYMM-XXX
      $queryGetRm = "SELECT MAX(SUBSTRING(no_rm, 8)) as last_queue_number FROM pasien";
      
      // Prepare and execute the PDO statement
      $stmt = $conn->prepare($queryGetRm);
      $stmt->execute();

      // Fetch the result and get the last queue number
      $rowRm = $stmt->fetch(PDO::FETCH_ASSOC);
      $lastQueueNumber = $rowRm['last_queue_number'];

      // If the table is empty, set the last queue number to 0
      $lastQueueNumber = $lastQueueNumber ? $lastQueueNumber : 0;

      // Get the current year and month (e.g., 202405)
      $tahun_bulan = date("Ym");

      // Create the new queue number by adding 1 to the last queue number
      $newQueueNumber = $lastQueueNumber + 1;

      // Create the new no_rm in the format YYYYMM-XXX
      $no_rm = $tahun_bulan . "-" . str_pad($newQueueNumber, 3, '0', STR_PAD_LEFT);

      // Insert the new patient record into the database
      $queryInsert = "INSERT INTO pasien (nama, alamat, no_hp, no_ktp, no_rm) VALUES (:nama, :alamat, :no_hp, :no_ktp, :no_rm)";
      
      // Prepare and bind parameters
      $stmtInsert = $conn->prepare($queryInsert);
      $stmtInsert->bindParam(':nama', $nama);
      $stmtInsert->bindParam(':alamat', $alamat);
      $stmtInsert->bindParam(':no_hp', $no_hp);
      $stmtInsert->bindParam(':no_ktp', $no_ktp);
      $stmtInsert->bindParam(':no_rm', $no_rm);
      
      // Execute the insert statement
      $stmtInsert->execute();

      // Commit the transaction
      $conn->commit();
      // Return success message
      return "Record updated successfully";
      
  } catch (Exception $e) {
      // Rollback the transaction if something goes wrong
      $conn->rollBack();
      echo "Failed: " . $e->getMessage();
  } 
}

function updatePasien($id, $nama, $alamat, $no_hp, $no_ktp, $no_rm, $conn) {
  try {
    // Prepare an SQL statement for execution
    $sql = "UPDATE pasien SET nama = :nama, alamat = :alamat, no_hp = :no_hp, no_ktp = :no_ktp, no_rm = :no_rm WHERE id = :id";
    $stmt = $conn->prepare($sql);

    // Bind the parameters to the SQL query
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':nama', $nama, PDO::PARAM_STR);
    $stmt->bindParam(':alamat', $alamat, PDO::PARAM_STR);
    $stmt->bindParam(':no_hp', $no_hp, PDO::PARAM_STR);
    $stmt->bindParam(':no_ktp', $no_ktp, PDO::PARAM_STR);
    $stmt->bindParam(':no_rm', $no_rm, PDO::PARAM_STR);

    // Execute the statement
    $stmt->execute();

    // Return success message
    return "Record updated successfully";
  } catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

function deletePasien ($id, $conn)
{
  try {
    // Prepare DELETE statement
    $sql = "DELETE FROM pasien WHERE id = :id";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':id', $id);

    // Execute statement
    $stmt->execute();

    header('Refresh: 0, url:pasien.php');
  } catch (PDOException $e) {
    // Handle database error
    echo "Error: " . $e->getMessage();
  }
}

function getNextNoRm($conn) {
  // Query to get the maximum number from no_rm in the format YYYYMM-XXX
  $queryGetRm = "SELECT MAX(SUBSTRING(no_rm, 8)) as last_queue_number FROM pasien";
  
  // Prepare and execute the PDO statement
  $stmt = $conn->prepare($queryGetRm);
  $stmt->execute();

  // Fetch the result and get the last queue number
  $rowRm = $stmt->fetch(PDO::FETCH_ASSOC);
  $lastQueueNumber = $rowRm['last_queue_number'];

  // If the table is empty, set the last queue number to 0
  $lastQueueNumber = $lastQueueNumber ? $lastQueueNumber : 0;

  // Get the current year and month (e.g., 202312)
  $tahun_bulan = date("Ym");

  // Create the new queue number by adding 1 to the last queue number
  $newQueueNumber = $lastQueueNumber + 1;

  // Create the new no_rm in the format YYYYMM-XXX
  $no_rm = $tahun_bulan . "-" . str_pad($newQueueNumber, 3, '0', STR_PAD_LEFT);

  return $no_rm;
}

function insertPoli ($nama_poli, $keterangan, $conn) {
  try {
    // Prepare an insert statement
    $query = "INSERT INTO poli (nama_poli, keterangan) VALUES (:nama_poli, :keterangan)";
    $stmt = $conn->prepare($query);
    
    // Bind the parameters
    $stmt->bindParam(':nama_poli', $nama_poli);
    $stmt->bindParam(':keterangan', $keterangan);
    
    // Execute the statement
    $stmt->execute();
    
    // Check if the insert was successful
    if ($stmt->rowCount() > 0) {
        return true; // Insert successful
    } else {
        return false; // Insert failed
    }
  } catch (PDOException $e) {
    // Handle any errors
    echo "Error: " . $e->getMessage();
    return false;
  }
}

function updatePoli($id, $nama_poli, $keterangan, $conn) {
  // Update query with placeholders
  $sql = "UPDATE poli SET nama_poli = :nama_poli, keterangan = :keterangan WHERE id = :id";
  
  try {
      // Prepare the statement
      $stmt = $conn->prepare($sql);
      
      // Bind the parameters
      $stmt->bindParam(':nama_poli', $nama_poli);
      $stmt->bindParam(':keterangan', $keterangan);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      
      // Execute the statement
      $stmt->execute();
      
      // Return a success message
      return "Record updated successfully";
  } catch (PDOException $e) {
      // Return an error message
      return "Error: " . $e->getMessage();
  }
}

function deletePoli ($id, $conn)
{
  try {
    // Prepare DELETE statement
    $sql = "DELETE FROM poli WHERE id = :id";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':id', $id);

    // Execute statement
    $stmt->execute();

    header('Refresh: 0, url:poli.php');
  } catch (PDOException $e) {
    // Handle database error
    echo "Error: " . $e->getMessage();
  }
}

function insertObat ($nama_obat, $kemasan, $harga, $conn) {
  try {
    // Prepare an insert statement
    $query = "INSERT INTO obat (nama_obat, kemasan, harga) VALUES (:nama_obat, :kemasan, :harga)";
    $stmt = $conn->prepare($query);
    
    // Bind the parameters
    $stmt->bindParam(':nama_obat', $nama_obat);
    $stmt->bindParam(':kemasan', $kemasan);
    $stmt->bindParam(':harga', $harga);
    
    // Execute the statement
    $stmt->execute();
    
    // Check if the insert was successful
    if ($stmt->rowCount() > 0) {
        return true; // Insert successful
    } else {
        return false; // Insert failed
    }
  } catch (PDOException $e) {
    // Handle any errors
    echo "Error: " . $e->getMessage();
    return false;
  }
}

function updateObat($id, $nama_obat, $kemasan, $harga, $conn) {
  // Update query with placeholders
  $sql = "UPDATE obat SET nama_obat = :nama_obat, kemasan = :kemasan, harga = :harga WHERE id = :id";
  
  try {
      // Prepare the statement
      $stmt = $conn->prepare($sql);
      
      // Bind the parameters
      $stmt->bindParam(':nama_obat', $nama_obat);
      $stmt->bindParam(':kemasan', $kemasan);
      $stmt->bindParam(':harga', $harga);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      
      // Execute the statement
      $stmt->execute();
      
      // Return a success message
      return "Record updated successfully";
  } catch (PDOException $e) {
      // Return an error message
      return "Error: " . $e->getMessage();
  }
}

function deleteObat ($id, $conn) {
  try {
    // Prepare DELETE statement
    $sql = "DELETE FROM obat WHERE id = :id";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':id', $id);

    // Execute statement
    $stmt->execute();

    header('Refresh: 0, url:obat.php');
  } catch (PDOException $e) {
    // Handle database error
    echo "Error: " . $e->getMessage();
  }
}

function getEnumValues($conn, $table, $column) {
  $query = "SHOW COLUMNS FROM $table LIKE '$column'";
  $stmt = $conn->query($query);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  
  $enum = str_replace("'", "", substr($row['Type'], 5, (strlen($row['Type'])-6)));
  $enumArray = explode(',', $enum);
  
  return $enumArray;
}

function insertJadwal ($id_dokter, $hari, $jadwal_mulai, $jadwal_selesai, $conn) {
  $sql = "INSERT INTO jadwal_periksa (id_dokter, jadwal_mulai, jadwal_selesai, hari) VALUES (:id_dokter, :jadwal_mulai, :jadwal_selesai, :hari)";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':id_dokter', $id_dokter, PDO::PARAM_INT);
  $stmt->bindParam(':jadwal_mulai', $jadwal_mulai);
  $stmt->bindParam(':jadwal_selesai', $jadwal_selesai);
  $stmt->bindParam(':hari', $hari);
  if ($stmt->execute()) {
      return "Record inserted successfully";
  } else {
      return "Error inserting record: " . $stmt->errorInfo()[2];
  }
}

function updateJadwal ($id, $hari, $jadwal_mulai, $jadwal_selesai, $conn) {
   // Update query with placeholders
   $sql = "UPDATE jadwal_periksa SET hari = :hari, jadwal_mulai = :jadwal_mulai, jadwal_selesai = :jadwal_selesai WHERE id = :id";
  
   try {
       // Prepare the statement
       $stmt = $conn->prepare($sql);
       
       // Bind the parameters
       $stmt->bindParam(':hari', $hari);
       $stmt->bindParam(':jadwal_mulai', $jadwal_mulai);
       $stmt->bindParam(':jadwal_selesai', $jadwal_selesai);
       $stmt->bindParam(':id', $id, PDO::PARAM_INT);
       
       // Execute the statement
       $stmt->execute();
       
       // Return a success message
       return "Record updated successfully";
   } catch (PDOException $e) {
       // Return an error message
       return "Error: " . $e->getMessage();
   }
}

function deleteJadwal ($id, $conn) {
  try {
    // Prepare DELETE statement
    $sql = "DELETE FROM jadwal_periksa WHERE id = :id";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':id', $id);

    // Execute statement
    $stmt->execute();

    header('Refresh: 0, url:jadwal-periksa.php');
  } catch (PDOException $e) {
    // Handle database error
    echo "Error: " . $e->getMessage();
  }
}

function insertDaftarPoli($id_pasien, $id_jadwal, $keluhan, $no_antrian, $conn) {
  $id_pasien = $_SESSION['id'];

  // Query to get the maximum number from no_antrian
  $queryGetAntrian = "SELECT MAX(no_antrian) as last_queue_number FROM daftar_poli";
      
  // Prepare and execute the PDO statement
  $stmt = $conn->prepare($queryGetAntrian);
  $stmt->execute();

  // Fetch the result and get the last queue number
  $rowAntrian = $stmt->fetch(PDO::FETCH_ASSOC);
  $lastQueueNumber = $rowAntrian['last_queue_number'];

  // If the table is empty, set the last queue number to 0
  $lastQueueNumber = $lastQueueNumber? $lastQueueNumber : 0;

  // Create the new queue number by adding 1 to the last queue number
  $newQueueNumber = $lastQueueNumber + 1;

  $no_antrian = $newQueueNumber;

  // Insert query
  $queryInsert = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian, status_periksa) 
                  VALUES (:id_pasien, :id_jadwal, :keluhan, :no_antrian, '0')";

  // Prepare the PDO statement
  $stmt = $conn->prepare($queryInsert);

  // Bind the parameters
  $stmt->bindParam(':id_pasien', $id_pasien);
  $stmt->bindParam(':id_jadwal', $id_jadwal);
  $stmt->bindParam(':keluhan', $keluhan);
  $stmt->bindParam(':no_antrian', $no_antrian);

  // Execute the query
  $stmt->execute();

  // Check if the insertion was successful
  if ($stmt->rowCount() > 0) {
    return "Record updated successfully";
  } else {
    return false;
  }
}

?>