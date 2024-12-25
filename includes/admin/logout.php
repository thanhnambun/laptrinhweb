<?php
include '../../config/db_connect.php';

// Xử lý khi form đăng ký được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $userName = $_POST['userName'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $fullName = $_POST['fullName'];
    $phoneNumber = $_POST['phoneNumber'];
    $address = $_POST['address'];

    // Kiểm tra tên người dùng hoặc email đã tồn tại
    $stmt = $connect->prepare("SELECT * FROM users WHERE userName = ? OR email = ?");
    $stmt->bind_param("ss", $userName, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Tên đăng nhập hoặc email đã được sử dụng!";
    } else {
        // Mã hóa mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Chèn dữ liệu vào cơ sở dữ liệu
        $stmt = $connect->prepare("INSERT INTO users (userName, password, email, fullName, phoneNumber, address, role, status) VALUES (?, ?, ?, ?, ?, ?, 'user', 1)");
        $stmt->bind_param("ssssss", $userName, $hashedPassword, $email, $fullName, $phoneNumber, $address);

        if ($stmt->execute()) {
            $success = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
            header("Location: http://localhost:3000/laptrinhweb/includes/login.php?message=success");
            exit();
        } else {
            $error = "Có lỗi xảy ra, vui lòng thử lại!";
        }
    }
}
?>