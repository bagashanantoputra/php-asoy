<?php
// Connect to Database
$server = "localhost";
$user = "root";
$password = "";
$database = "db_crudphp";

$conn = mysqli_connect($server, $user, $password, $database) or die(mysqli_error($conn));


// Fetch names from database
$query = "SELECT nama FROM tb_barang";
$result = mysqli_query($conn, $query);

// Create an array to store the names
$names = array();
while ($row = mysqli_fetch_assoc($result)) {
    $names[] = $row['nama'];
}

// Generate option elements
while ($row = mysqli_fetch_assoc($result)) {
    $nama = $row['nama'];
    echo "<option value='$nama'>$nama</option>";
}

// Click Button
if (isset($_POST["btn_save"])) {
    // Check if it's an Edit or New Data
    if (isset($_GET['hal']) && $_GET['hal'] == 'edit' && isset($_GET['id'])) {
        // Edit Data
        $editId = $_GET['id'];
        $editQuery = "UPDATE tb_barang SET
                        kode = ?,
                        nama = ?,
                        asal = ?,
                        jumlah = ?,
                        satuan = ?,
                        tanggal_diterima = ?
                    WHERE id_barang = ?";
        $stmt = mysqli_prepare($conn, $editQuery);
        mysqli_stmt_bind_param($stmt, "ssssssi", $_POST['tb_kode'], $_POST['tb_nama'], $_POST['tb_asal'], $_POST['tb_jumlah'], $_POST['tb_satuan'], $_POST['tb_tanggal_terima'], $editId);
        $edit = mysqli_stmt_execute($stmt);

        // Check for errors
        if ($edit) {
            echo "<script type='text/javascript'>
                alert('Edit Success!');
                window.location.href = 'index.php';
            </script>";
        } else {
            echo "<script type='text/javascript'>
                alert('Edit Failed!');
                window.location.href = 'index.php';
            </script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        // New Data
        $insertQuery = "INSERT INTO tb_barang (kode, nama, asal, jumlah, satuan, tanggal_diterima, tanggal_simpan)
                        VALUES (?, ?, ?, ?, ?, ?, current_timestamp())";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "ssssss", $_POST['tb_kode'], $_POST['tb_nama'], $_POST['tb_asal'], $_POST['tb_jumlah'], $_POST['tb_satuan'], $_POST['tb_tanggal_terima']);
        $save = mysqli_stmt_execute($stmt);

        // Check for errors
        if ($save) {
            echo "<script type='text/javascript'>
                alert('Save Success!');
                window.location.href = 'index.php';
            </script>";
        } else {
            echo "<script type='text/javascript'>
                alert('Save Failed!');
                window.location.href = 'index.php';
            </script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Variables for editing
$vkode = "";
$vnama = "";
$vasal = "";
$vjumlah = "";
$vsatuan = "";
$vtanggal_diterima = "";

// Check Button
if (isset($_GET['hal']) && $_GET['hal'] == "edit" && isset($_GET['id'])) {
    $editId = $_GET['id'];
    $showQuery = "SELECT * FROM tb_barang WHERE id_barang = ?";
    $stmt = mysqli_prepare($conn, $showQuery);
    mysqli_stmt_bind_param($stmt, "i", $editId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_array($result);

    if ($data) {
        $vkode = $data['kode'];
        $vnama = $data['nama'];
        $vasal = $data['asal'];
        $vjumlah = $data['jumlah'];
        $vsatuan = $data['satuan'];
        $vtanggal_diterima = $data['tanggal_diterima'];
    }
    mysqli_stmt_close($stmt);
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP & MySQL Bootstrap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <link href="vendor/select2/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
</head>

<body>
    <div class="container">
        <h3 class="text-center mt-5 fw-bold">PHP & MySQL</h3>
        <h4 class="text-center fw-semibold">Create, Read, Update & Delete</h4>
        <!---Card--->
        <div class="card mt-4 w-50 mx-auto">
            <div class="card-header bg-primary text-light fw-bolder">
                Form Input
            </div>
            <div class="card-body">
                <form method="POST" action="index.php" class="row">
                    <div class="mb-3">
                        <label class="form-label">Kode Barang</label>
                        <input type="text" class="form-control" value="<?= $vkode ?>" name="tb_kode" placeholder="Masukan kode barang">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" value="<?= $vnama ?>" name="tb_nama" placeholder="Masukan nama barang">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Asal Barang</label>
                        <select class="form-select" name="tb_asal">
                            <option value="<?= $vasal ?>" selected><?= $vasal ?></option>
                            <option value="Pembelian">Pembelian</option>
                            <option value="Hibah">Hibah</option>
                            <option value="Bantuan">Bantuan</option>
                            <option value="Sumbangan">Sumbangan</option>
                        </select>
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Jumlah Barang</label>
                        <input type="number" class="form-control" value="<?= $vjumlah ?>" name="tb_jumlah" placeholder="Masukan jumlah barang">
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Satuan</label>
                        <select class="form-select" name="tb_satuan" id="tb_satuan">
                            <option value="<?= $vsatuan ?>" selected><?= $vsatuan ?></option>
                            <option value="Unit">Unit</option>
                            <option value="Kotak">Kotak</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Pax">Pax</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Tanggal Terima</label>
                        <input type="date" class="form-control" value="<?= $vtanggal_diterima ?>" name="tb_tanggal_terima">
                    </div>
                    <div class="d-flex justify-content-center m-1">
                        <button class="btn btn-primary me-1" name="btn_save" type="submit" onclick="return confirm('Are you sure you want to save?')">Save</button>
                        <button class="btn btn-secondary ms-1" name="btn_remove" type="reset">Remove</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4 mb-5">
            <div class="card-header bg-primary text-light fw-bolder">
                Data Input
            </div>
            <div class="card-body">
                <div class="col-md-6 mx-auto">
                    <form method="POST" action="index.php">

                        <div class="row mb-4">
                            <label class="col-sm-3 form-label">Select with search field</label>
                            <div class="col-sm-9">
                                <select class="choices-1" id="mySelect">
                                    <option value='' selected>Choice...</option>
                                    <?php
                                    // Generate option elements
                                    foreach ($names as $name) {
                                        echo "<option value='$name'>$name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>


                        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js">
                        </script>
                        <script>
                            const selectElement = document.querySelector('.choices-1');
                            const choices = new Choices(selectElement);
                        </script>

                        <div class="d-flex gap-2 input-grup mb-3">
                            <input type="text" name="tb_find" id="tb_find" value="<?= $_POST['tb_find'] ?? '' ?>" class="form-control" placeholder="Masukan kata kunci!">
                            <button class="btn btn-primary" name="btn_find" type="submit">Find</button>
                            <button class="btn btn-danger" name="btn_reset" type="submit">Reset</button>
                        </div>
                    </form>
                </div>
                <table id="data-table" class="table table-striped table-hover table-bordered">
                    <tr>
                        <th>No.</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Asal Barang</th>
                        <th>Jumlah</th>
                        <th>Tanggal diterima</th>
                        <th>Aksi</th>
                    </tr>

                    <?php
                    //Show Table Datas
                    $no = 1;

                    if (isset($_POST['btn_find'])) {
                        $keyword = $_POST['tb_find'];
                        $q = "SELECT * FROM tb_barang WHERE kode LIKE '%$keyword%' OR nama LIKE '%$keyword%' ORDER BY id_barang DESC";
                    } else {
                        $q = "SELECT * FROM tb_barang ORDER BY id_barang DESC";
                    }

                    $show = mysqli_query($conn, $q);
                    while ($data = mysqli_fetch_array($show)) {
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $data["kode"] ?></td>
                            <td><?= $data["nama"] ?></td>
                            <td><?= $data["asal"] ?></td>
                            <td><?= $data["jumlah"] ?> <?= $data["satuan"] ?></td>
                            <td><?= $data["tanggal_diterima"] ?></td>
                            <td>
                                <a href="index.php?hal=edit&id=<?= $data['id_barang'] ?>" class="btn btn-warning">Edit</a>
                                <a href="#" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="card-footer text-muted">
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Function to filter the options based on user input
        function filterOptions() {
            var input, filter, select, options, i;
            input = document.getElementById("search-select");
            filter = input.value.toUpperCase();
            select = document.getElementById("search-select");
            options = select.getElementsByTagName("option");

            // Loop through all the options and hide those that don't match the filter
            for (i = 0; i < options.length; i++) {
                if (options[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
                    options[i].style.display = "";
                } else {
                    options[i].style.display = "none";
                }
            }
        }

        // Attach event listener to the input field
        document.getElementById("search-select").addEventListener("keyup", filterOptions);
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>

    <script type="text/javascript">
        const selectElements = document.getElementById('mySelect');
        const table = document.getElementById('data-table');

        selectElements.addEventListener('change', function() {
            const selectedValue = selectElements.value;
            updateTable(selectedValue);
        });

        function updateTable(selectedValue) {
            const rows = table.getElementsByTagName('tr');
            for (let i = 1; i < rows.length; i++) {
                const rowData = rows[i].getElementsByTagName('td');
                const namaBarang = rowData[2].innerText;
                if (selectedValue === '' || namaBarang.includes(selectedValue)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    </script>
</body>


</html>