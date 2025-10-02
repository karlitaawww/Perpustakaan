<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_once __DIR__ . '/../config/database.php';

// Get member data
$id_anggota = $_SESSION['user_id'];
$query = "SELECT * FROM users
          WHERE id_user = $id_anggota";
$result = mysqli_query($conn, $query);
$anggota = mysqli_fetch_assoc($result);


$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
   
    // Validasi
    if (empty($username)) $errors[] = 'Username harus diisi';
    
    if (empty($errors)) { 
        $query = "UPDATE users SET 
                  username = '$username', 
                  email = '$email'
                  WHERE id_user = $id_anggota";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Profil berhasil diperbarui';
            // Update session username
            $_SESSION['username'] = $username;
            // Update user data
            $anggota = array_merge($anggota, [
                'username' => $username,
                'email' => $email,
            ]);
        } else {
            $errors[] = 'Gagal memperbarui profil: ' . mysqli_error($conn);
        }
    }
}

$title = 'Profil Saya';
include '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Profil Anggota</h4>
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
                               value="<?php echo htmlspecialchars($anggota['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <textarea class="form-control" id="email" name="email"><?php echo htmlspecialchars($anggota['email']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>