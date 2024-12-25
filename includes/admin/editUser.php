<?php
include '../../config/db_connect.php';

// Kiểm tra nếu `user_id` tồn tại và hợp lệ
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    header("Location: ../manager_user.php?message=invalid_id");
    exit();
}

$id = intval($_GET['user_id']);

// Truy vấn để lấy thông tin người dùng
$result = $connect->prepare("SELECT * FROM users WHERE user_id = ?");
$result->bind_param("i", $id);
$result->execute();
$user = $result->get_result()->fetch_assoc();

// Nếu người dùng không tồn tại
if (!$user) {
    header("Location: ../manager_user.php?message=not_found");
    exit();
}

// Xử lý cập nhật thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role']; // Nếu có quyền quản lý vai trò người dùng

    // Kiểm tra tính hợp lệ của dữ liệu đầu vào
    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.";
    } else {
        // Cập nhật thông tin người dùng trong cơ sở dữ liệu
        $stmt = $connect->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $name, $email, $role, $id);
        $stmt->execute();

        // Chuyển hướng sau khi cập nhật
        header("Location: ../manager_user.php?message=updated");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin người dùng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Chỉnh sửa thông tin người dùng</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Họ và tên</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>"
                    required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>"
                    required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Vai trò</label>
                <select name="role" class="form-control">
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>Người dùng</option>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="../manager_user.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>

</html>