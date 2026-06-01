<?php
// File: app/controllers/AuthController.php

class AuthController {

    private function jsonResponse($success, $message, $data = []) {
        header('Content-Type: application/json');
        echo json_encode(
        ['success' => $success, 'message' => $message] + $data,
        JSON_UNESCAPED_UNICODE
    );
        exit();
    }

    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'User'; 

            if (empty($name) || empty($email) || empty($password)) {
                $this->jsonResponse(false, 'Vui lòng điền đầy đủ thông tin bắt buộc.');
            }
            if ($password !== $confirmPassword) {
                $this->jsonResponse(false, 'Lỗi: Mật khẩu xác nhận không khớp.');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->jsonResponse(false, 'Lỗi: Định dạng email không hợp lệ.');
            }
            if (strlen($password) < 6) {
                $this->jsonResponse(false, 'Lỗi: Mật khẩu phải có ít nhất 6 ký tự.');
            }

            $userModel = new UserModel();
            if ($userModel->findUserByEmail($email)) {
                $this->jsonResponse(false, 'Lỗi: Địa chỉ email này đã được sử dụng.');
            }

            $allowedRoles = ['User', 'Admin'];
            if (!in_array($role, $allowedRoles)) {
                $this->jsonResponse(false, 'Lỗi: Vai trò không hợp lệ.');
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));
            $tokenExpires = (new DateTime())->modify('+1 hour')->format('Y-m-d H:i:s');
            
            $userId = $userModel->createUser($name, $email, $hashedPassword, $token, $tokenExpires, $role);

            if ($userId) {
                global $fullBaseUrl; // Lấy biến từ index.php
                $verificationLink = $fullBaseUrl . "verify-email?token=" . $token;
                
                $subject = "Xác thực tài khoản Helios của bạn";
                $body = "
                    <h2>Chào mừng bạn đến với Mạng xã hội Helios!</h2>
                    <p>Cảm ơn bạn đã đăng ký. Vui lòng nhấp vào liên kết bên dưới để kích hoạt tài khoản của bạn:</p>
                    <p><a href='{$verificationLink}' style='padding: 10px 15px; background-color: #0d6efd; color: white; text-decoration: none; border-radius: 5px;'>Kích hoạt tài khoản</a></p>
                    <p>Nếu bạn không thể nhấp vào nút, vui lòng sao chép và dán URL sau vào trình duyệt của bạn:</p>
                    <p>{$verificationLink}</p>
                    <p>Liên kết này sẽ hết hạn sau 1 giờ.</p>
                ";

                $emailSent = Mail::send($email, $subject, $body);

                if ($emailSent) {
                   $this->jsonResponse(true, 'Đăng ký thành công! Vui lòng kiểm tra email.');
                } else {
                   $this->jsonResponse(false, 'Đăng ký thành công, nhưng đã có lỗi xảy ra khi gửi email xác thực. Vui lòng liên hệ quản trị viên.');
                }
            } else {
                $this->jsonResponse(false, "Đã có lỗi không xác định xảy ra trong quá trình đăng ký.");
            }
        } else {
            $pageTitle = "Đăng ký tài khoản";
            $jsFiles = ['auth.js'];
            $contentView = VIEW_PATH_APP . '/auth/register.php';
            include VIEW_PATH_APP . '/layouts/auth_layout.php';
        }
    }

    public function handleLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Nếu người dùng đã đăng nhập, chuyển hướng dựa trên vai trò đã có
        if (isset($_SESSION['user_id'])) {
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') {
                 header('Location: ' . $GLOBALS['baseUrl'] . 'admin/dashboard');
            } else {
                 header('Location: ' . $GLOBALS['baseUrl'] . 'home');
            }
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $this->jsonResponse(false, 'Vui lòng nhập đầy đủ email và mật khẩu.'); 
            }

            $userModel = new UserModel();
            $account = $userModel->findUserByEmail($email);

            if (!$account) {
                $this->jsonResponse(false, 'Email hoặc mật khẩu không chính xác.'); 
            }
            
            if ($account['TrangThai'] !== 'active') {
                $this->jsonResponse(false, 'Tài khoản của bạn chưa được kích hoạt hoặc đã bị khóa.'); 
            }

            if (!password_verify($password, $account['MatKhau'])) { 
                $this->jsonResponse(false, 'Email hoặc mật khẩu không chính xác.');
            }

            // Đăng nhập thành công
            session_regenerate_id(true);
            $_SESSION['user_id'] = $account['MaNguoiDung'];
            $_SESSION['user_email'] = $account['Email'];
            
            $userDetails = $userModel->getUser($account['MaNguoiDung']);
            if ($userDetails) {
                $_SESSION['user_name'] = $userDetails['HoTen'];
                $_SESSION['user_avatar'] = $userDetails['AnhDaiDien'];
                $_SESSION['user_role'] = $userDetails['VaiTro'];
            }

            // Xác định URL chuyển hướng
            $redirectUrl = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin')
                ? $GLOBALS['baseUrl'] . 'admin/dashboard'
                : $GLOBALS['baseUrl'] . 'home';
            
            $this->jsonResponse(true, 'Đăng nhập thành công!', ['redirectUrl' => $redirectUrl]);
            
        } else {
            $pageTitle = "Đăng nhập";
            $jsFiles = ['auth.js'];
            $contentView = VIEW_PATH_APP . '/auth/login.php';
            include VIEW_PATH_APP . '/layouts/auth_layout.php';
        }
    }

    public function verifyEmail() {
        $token = $_GET['token'] ?? '';
        $message = '';
        $isSuccess = false;

        if (empty($token)) {
            $message = 'Token không hợp lệ.';
        } else {
            $userModel = new UserModel();
            $user = $userModel->findUserByToken($token);

            if (!$user || new DateTime() > new DateTime($user['TokenExpiresAt'])) {
                $message = 'Token không hợp lệ hoặc đã hết hạn.';
            } else if ($userModel->activateUser($user['MaTaiKhoan'])) {
                $isSuccess = true;
                $message = 'Tài khoản của bạn đã được xác thực thành công! Đang chuyển hướng đến trang đăng nhập...';
            } else {
                $message = 'Lỗi khi xác thực tài khoản.';
            }
        }

        // Trả về một trang HTML giao diện đẹp thay vì trả về JSON
        echo "<!DOCTYPE html>
        <html lang='vi'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Xác thực Email | Helios</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
            " . ($isSuccess ? "<meta http-equiv='refresh' content='3;url=" . $GLOBALS['baseUrl'] . "login'>" : "") . "
            <style>body { background-color: #f3f8ff; }</style>
        </head>
        <body class='d-flex align-items-center justify-content-center' style='min-height: 100vh;'>
            <div class='card p-4 shadow-sm text-center' style='max-width: 400px; border-radius: 12px; border: none;'>
                <h3 class='mb-3'>" . ($isSuccess ? "🎉 Thành công!" : "❌ Lỗi xác thực") . "</h3>
                <p class='text-muted'>$message</p>
                " . (!$isSuccess ? "<a href='" . $GLOBALS['baseUrl'] . "login' class='btn btn-primary mt-3'>Về trang đăng nhập</a>" : "") . "
            </div>
        </body>
        </html>";
        exit();
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // 1. Hủy tất cả các biến session
        $_SESSION = array();
        
        // 2. Xóa cookie session phía client
        // Điều này sẽ xóa session nếu bạn không đóng trình duyệt
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // 3. Cuối cùng, hủy session trên server
        session_destroy();
        
        // 4. Thêm các header để ngăn trình duyệt cache
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
        // 5. Chuyển hướng về trang đăng nhập
        header('Location: ' . $GLOBALS['baseUrl'] . 'login?status=loggedout');
        exit;
    }

    public function handleForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->jsonResponse(false, "Email không hợp lệ.");
            }

            $userModel = new UserModel();
            $user = $userModel->findUserByEmail($email);

            // Luôn trả về thành công để hacker không dò được email nào có trong hệ thống
            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expiresAt = (new DateTime())->modify('+30 minutes')->format('Y-m-d H:i:s');
                $userModel->updatePasswordResetToken($email, $token, $expiresAt);
                
                global $fullBaseUrl;
                $resetLink = $fullBaseUrl . "reset-password?token=" . $token;
                
                $subject = "Yêu cầu đặt lại mật khẩu cho tài khoản Helios";
                $body = "<p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Vui lòng nhấp vào liên kết bên dưới để tạo mật khẩu mới:</p>";
                $body .= "<p><a href='{$resetLink}' style='padding: 10px 15px; background-color: #0d6efd; color: white; text-decoration: none; border-radius: 5px;'>Đặt lại mật khẩu</a></p>";
                $body .= "<p>Nếu nút trên không hoạt động, copy link này: {$resetLink}</p>";
                $body .= "<p>Liên kết này sẽ hết hạn sau 30 phút.</p>";

                Mail::send($email, $subject, $body);
            }
            
            // TRẢ VỀ JSON THAY VÌ ECHO HTML
            $this->jsonResponse(true, "Nếu địa chỉ email của bạn tồn tại trong hệ thống, chúng tôi đã gửi một liên kết đặt lại mật khẩu đến đó.");

        } else {
            $pageTitle = "Quên mật khẩu";
            $jsFiles = ['auth.js'];
            $contentView = VIEW_PATH_APP . '/auth/forgot-password.php';
            include VIEW_PATH_APP . '/layouts/auth_layout.php';
        }
    }

    public function handleResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($token) || empty($password) || $password !== $confirmPassword) {
                $this->jsonResponse(false, "Dữ liệu không hợp lệ hoặc mật khẩu không khớp.");
            }
            if (strlen($password) < 6) {
                $this->jsonResponse(false, "Mật khẩu phải có ít nhất 6 ký tự.");
            }

            $userModel = new UserModel();
            $user = $userModel->findUserByResetToken($token);

            if (!$user || new DateTime() > new DateTime($user['ResetTokenExpiresAt'])) {
                $this->jsonResponse(false, "Token không hợp lệ hoặc đã hết hạn. Vui lòng thử lại yêu cầu quên mật khẩu.");
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 
            
            if ($userModel->updatePassword($user['MaTaiKhoan'], $hashedPassword)) {
                // Trả về JSON thành công
                $this->jsonResponse(true, "Mật khẩu của bạn đã được cập nhật thành công.");
            } else {
                $this->jsonResponse(false, "Đã có lỗi xảy ra khi cập nhật mật khẩu.");
            }

        } else {
            // Hiển thị form reset
            $token = $_GET['token'] ?? '';
            if (empty($token)) {
                $this->jsonResponse(false, "Thiếu token.");
            }
            
            // Có thể kiểm tra token ở đây trước khi hiển thị form
            $userModel = new UserModel();
            $user = $userModel->findUserByResetToken($token);
            if (!$user || new DateTime() > new DateTime($user['ResetTokenExpiresAt'])) {
                $this->jsonResponse(false, "Token không hợp lệ hoặc đã hết hạn.");
            }
            
            $jsFiles = ['auth.js'];
            $pageTitle = "Đặt lại mật khẩu";
            $contentView = VIEW_PATH_APP . '/auth/reset-password.php';
            include VIEW_PATH_APP . '/layouts/auth_layout.php';
        }
    }
}
