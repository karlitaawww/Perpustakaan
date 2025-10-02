<?php
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';

if (!isPetugas()) {
    header("Location: ../../../auth/login.php");
    exit();
}

require_once __DIR__ . '/../../../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET['id'];

// Get member data
$query = "SELECT * FROM users WHERE id_user = $id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (!$anggota) {
    header("Location: index.php");
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Validasi
    if (empty($username)) $errors[] = 'Username harus diisi';
    if (empty($email)) $errors[] = 'Email harus diisi';
    if (empty($password)) $errors[] = 'Password harus diisi';
    
    // Cek NIM unik (kecuali untuk dirinya sendiri)
    $query_check = "SELECT id_user FROM users WHERE email = '$email' AND id_users != $id";
    $result_check = mysqli_query($conn, $query_check);
    if (mysqli_num_rows($result_check) > 0) {
        $errors[] = 'email sudah digunakan oleh anggota lain';
    }
    
    if (empty($errors)) {
        $query = "UPDATE anggota SET 
                  username = '$username', 
                  email = '$email', 
                  password = '$password' 
                  WHERE id_user = $id";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Data anggota berhasil diperbarui';
            // Update member data
            $user = array_merge($user, [
                'username' => $username,
                'email' => $email,
                'password' => $password
            ]);
        } else {
            $errors[] = 'Gagal memperbarui anggota: ' . mysqli_error($conn);
        }
    }
}

$title = 'Edit Anggota';
include '../../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Edit Anggota</h4>
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
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="text" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($user['password']); ?>">
                            </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../../includes/footer.php'; ?>