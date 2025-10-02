<?php
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';

if (!isPetugas()) {
    header("Location: ../../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
   
    // Validasi
    if (empty($username)) $errors[] = 'Username harus diisi';
    if (empty($email)) $errors[] = 'email harus diisi';
    if (empty($password)) $errors[] = 'password harus diisi';
    
    // Cek email 
    $query_check = "SELECT id_user FROM users WHERE email = '$email'";
    $result_check = mysqli_query($conn, $query_check);
    if (mysqli_num_rows($result_check) > 0) {
        $errors[] = 'email sudah terdaftar';
    }
    
    if (empty($errors)) {
        $query = "INSERT INTO users (username, email, password) 
                  VALUES ('$username', '$email', '$password')";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Anggota berhasil ditambahkan';
            $_POST = []; // Clear form
        } else {
            $errors[] = 'Gagal menambahkan anggota: ' . mysqli_error($conn);
        }
    }
}

$title = 'Tambah Anggota';
include '../../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Tambah Anggota Baru</h4>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo $_POST['username'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" 
                               value="<?php echo $_POST['email'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="text" class="form-control" id="password" name="password" 
                               value="<?php echo $_POST['password'] ?? ''; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>