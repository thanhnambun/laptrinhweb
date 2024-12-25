<?php
// Kết nối cơ sở dữ liệu
include '../../config/db_connect.php';

// Kiểm tra nếu form được gửi đi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $category_name = $_POST['category'];
    $description = $_POST['description'];

    // Kiểm tra và xử lý ảnh tải lên
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $uploadFolder = '../assets/image/';

        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($uploadFolder)) {
            mkdir($uploadFolder, 0777, true);
        }

        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = time() . '.' . $fileExtension; // Tạo tên file mới
        $destination = $uploadFolder . $newFileName;

        // Di chuyển file vào thư mục đích
        if (move_uploaded_file($fileTmpPath, $destination)) {
            $image_url = 'http://localhost:3000/laptrinhweb/admin/assets/image/' . $newFileName;

            // Kiểm tra danh mục (category) trong cơ sở dữ liệu
            $stmt = $connect->prepare("SELECT category_id FROM categories WHERE category_name = ?");
            $stmt->bind_param("s", $category_name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $category_id = $result->fetch_assoc()['category_id'];
            } else {
                // Thêm danh mục mới nếu chưa tồn tại
                $stmt = $connect->prepare("INSERT INTO categories (category_name) VALUES (?)");
                $stmt->bind_param("s", $category_name);
                $stmt->execute();
                $category_id = $stmt->insert_id;
            }
            $stmt = $connect->prepare("INSERT INTO products (product_name, price, category_id, image, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdsss", $product_name, $price, $category_id, $image_url, $description);
            $stmt->execute();

            header("Location: ../manager_product.php?message=added");
            exit();
        } else {
            echo "Không thể tải file lên, vui lòng thử lại.";
        }
    } else {
        echo "File ảnh không hợp lệ hoặc chưa được chọn.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>
    body {
        display: flex;
        height: 100vh;
        margin: 0;
        overflow: hidden;
    }

    .sidebar {
        width: 250px;
        background-color: #343a40;
        color: #fff;
        padding: 20px 0;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        height: 100%;
        /* Chiều cao đầy màn hình */
        position: sticky;
        /* Sidebar cố định */
        top: 0;
    }

    .sidebar a {
        color: #fff;
        text-decoration: none;
        padding: 10px 20px;
        display: block;
    }

    .sidebar a:hover {
        background-color: #495057;
    }

    .content {
        flex-grow: 1;
        overflow-y: auto;
        /* Bật cuộn cho nội dung */
        padding: 20px;
        background-color: #f8f9fa;
    }
</style>

<body>

    <div class="sidebar">
        <h3 class="text-center">Quản lý</h3>
        <a href="../admin/manage_product.php">Quản lý sản phẩm</a>
        <a href="../admin/manage_user.php">Quản lý người dùng</a>
        <a href="orders.php">Quản lý đơn hàng</a>
    </div>

    <div class="container mt-5">
        <h2 class="mb-4">Thêm sản phẩm</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="product_name" class="form-label">Tên sản phẩm</label>
                <input type="text" name="product_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Giá</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Phân loại</label>
                <select name="category" class="form-control" required>
                    <option value="Cà phê">Cà phê</option>
                    <option value="Nước ép">Nước ép</option>
                    <option value="Sữa chua">Sữa chua</option>
                    <option value="Trà">Trà</option>
                    <option value="Sinh tố">Sinh tố</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Ảnh sản phẩm</label>
                <input type="file" name="image" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="../admin/manage_product.php" class="btn btn-secondary">Quay lại</a>
        </form>

    </div>
</body>

</html>