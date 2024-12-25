<?php
include '../../config/db_connect.php';

// Kiểm tra nếu `product_id` tồn tại và hợp lệ
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    header("Location: ../manager_product.php?message=invalid_id");
    exit();
}

$id = intval($_GET['product_id']);

// Truy vấn để lấy thông tin sản phẩm
$result = $connect->prepare("SELECT * FROM products WHERE product_id = ?");
$result->bind_param("i", $id);
$result->execute();
$product = $result->get_result()->fetch_assoc();

// Nếu sản phẩm không tồn tại
if (!$product) {
    header("Location: ../manager_product.php?message=not_found");
    exit();
}

// Xử lý cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Kiểm tra tính hợp lệ của dữ liệu đầu vào
    if (empty($product_name) || !is_numeric($price) || $price <= 0) {
        echo "Dữ liệu không hợp lệ. Vui lòng kiểm tra lại.";
    } else {
        // Cập nhật sản phẩm trong cơ sở dữ liệu
        $stmt = $connect->prepare("UPDATE products SET product_name = ?, price = ?, description = ? WHERE product_id = ?");
        $stmt->bind_param("sdsi", $product_name, $price, $description, $id);
        $stmt->execute();

        // Chuyển hướng sau khi cập nhật
        header("Location: ../manager_product.php?message=updated");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Sửa sản phẩm</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Tên sản phẩm</label>
            <input type="text" name="name" class="form-control" value="<?= $product['product_name'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" class="form-control"><?= $product['description'] ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="includes/admin/manage_product.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
</body>
</html>
