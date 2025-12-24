    
<?php
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

class AuthController extends Controller {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    // Hiển thị form đăng ký
    //http://localhost/MVC3/AuthController/Show
    public function Show() {
        $this->view("homePage",["page"=>"RegisterView"]);
    }

    // Xử lý đăng ký, gửi OTP
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            if ($fullname === '' || $email === '' || $password === '') {
                echo '<div class="container mt-5"><div class="alert alert-danger">Vui lòng nhập đầy đủ thông tin!</div></div>';
                $this->view("homePage",["page"=>"RegisterView"]);
                return;
            }

            // Tạo mã OTP
            $otp = rand(100000, 999999);
            $_SESSION['register'] = [
                'fullname' => $fullname,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'otp' => $otp
            ];
            // Gửi OTP qua email
            $this->sendOtpEmail($email, $otp);

            // Hiển thị form nhập OTP
            $this->view("homePage",["page"=>"OtpView"]);
        }
    }

    // Gửi OTP qua Gmail
    private function sendOtpEmail($email, $otp) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'zerohn889@gmail.com'; // Thay bằng Gmail của bạn
            $mail->Password = 'ijgl wiav jtpq nuto'; // Thay bằng App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('zerohn889@gmail.com', 'Your App');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Mã OTP xác thực đăng ký";
            $mail->Body = "Mã OTP của bạn là: <b>$otp</b>";

            $mail->send();
        } catch (Exception $e) {
            echo "Gửi email thất bại: {$mail->ErrorInfo}";
        }
    }

    // Xác thực OTP
    public function verifyOtp() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputOtp = $_POST['otp'];
            if (isset($_SESSION['register']) && $_SESSION['register']['otp'] == $inputOtp) {
                // Lưu user vào DB
                $user = $this->model('UserModel');
                $email = $_SESSION['register']['email'];
                if ($user->emailExists($email)) {
                    echo '<div class="container mt-5"><div class="alert alert-danger">Email đã được đăng ký. Vui lòng sử dụng email khác!</div></div>';
                    unset($_SESSION['register']);
                    $this->view("homePage",["page"=>"RegisterView"]);
                    return;
                }
                $user->email = $email;
                $user->password = $_SESSION['register']['password'];
                $user->fullname = $_SESSION['register']['fullname'];
                $user->token = bin2hex(random_bytes(16));
                $result = $user->create();
                
                if ($result) {
                    // Lấy thông tin user vừa tạo
                    $stmt = $user->findByEmail($email);
                    $newUser = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Tự động đăng nhập sau khi đăng ký thành công
                    $_SESSION['user'] = [
                        'id' => $newUser['id'],
                        'email' => $newUser['email'],
                        'fullname' => $newUser['fullname']
                    ];
                    
                    unset($_SESSION['register']);
                    
                    // Nếu có URL return, chuyển hướng về đó
                    if (isset($_SESSION['return_url'])) {
                        $redirect_url = $_SESSION['return_url'];
                        unset($_SESSION['return_url']);
                        header('Location: ' . $redirect_url);
                    } else {
                        header('Location: ' . APP_URL . '/Home/show');
                    }
                    exit();
                }
            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Mã OTP không đúng!</div></div>';
                $this->view("homePage",["page"=>"OtpView"]);
            }
        }
    }
    // Hiển thị form đăng nhập
    public function showLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Nếu người dùng đã đăng nhập, chuyển hướng về trang chủ
        if (isset($_SESSION['user'])) {
            header('Location: ' . APP_URL . '/Home/show');
            exit();
        }
        
        $this->view("homePage", ["page"=>"LoginView"]);
    }

        // Xử lý đăng nhập
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = trim($_POST['email']); // có thể là email hoặc username
            $password = trim($_POST['password']);
            $role = $_POST['role'] ?? 'user';
            
            // Lưu URL trước khi đăng nhập
            $return_url = isset($_SESSION['return_url']) ? $_SESSION['return_url'] : APP_URL . '/Home/checkoutInfo';

        if ($role === 'admin') {
            // Đăng nhập admin
            $adminModel = $this->model('AdminModel');
            $admin = $adminModel->login($identifier, $password);
            if ($admin) {
                session_start();
                $_SESSION['admin'] = $admin;
                header('Location: ' . APP_URL . '/Admin/listOrders');
                exit();
            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Tên đăng nhập hoặc mật khẩu admin không đúng!</div></div>';
                $this->view("homePage", ["page" => "LoginView"]);
                return;
            }
        } else {
            // Đăng nhập người dùng
            $userModel = $this->model('UserModel');
            $stmt = $userModel->findByEmail($identifier);
            $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

            if ($user && password_verify($password, $user['password'])) {
                // ✅ Kiểm tra tài khoản bị khóa
                if (isset($user['is_locked']) && $user['is_locked'] == 1) {
                    echo '<div class="container mt-5"><div class="alert alert-danger">Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên!</div></div>';
                    $this->view("homePage", ["page" => "LoginView"]);
                    return;
                }
                
                // ✅ Lưu thông tin user vào session
                $_SESSION['user'] = [
                    'id' => (int)$user['user_id'], // Sửa thành user_id theo cấu trúc DB
                    'email' => $user['email'],
                    'fullname' => $user['fullname']
                ];
                
                error_log('Login successful. User session data: ' . print_r($_SESSION['user'], true));
                
                // ✅ TỰ ĐỘNG LOAD GIỎ HÀNG TỪ DATABASE
                $cartModel = $this->model('ShoppingCartModel');
                $userId = (int)$_SESSION['user']['id'];
                error_log('Loading cart for user_id: ' . $userId);
                
                $savedCart = $cartModel->loadCart($userId);
                error_log('Cart items loaded: ' . count($savedCart ?? []));
                
                if (!empty($savedCart)) {
                    $_SESSION['cart'] = $savedCart;
                    error_log('Loaded cart from database: ' . count($savedCart) . ' items');
                } else {
                    $_SESSION['cart'] = [];
                    error_log('No cart found in database for user_id: ' . $userId);
                }
                
                // ✅ TỰ ĐỘNG LOAD DANH SÁCH YÊU THÍCH TỪ DATABASE
                try {
                    $wishlistModel = $this->model('WishlistModel');
                    $wishlistItems = $wishlistModel->getByEmail($user['email']);
                    if (!empty($wishlistItems)) {
                        $_SESSION['wishlist'] = $wishlistItems;
                        error_log('Loaded wishlist from database: ' . count($wishlistItems) . ' items');
                    } else {
                        $_SESSION['wishlist'] = [];
                    }
                } catch (Exception $e) {
                    error_log('Error loading wishlist: ' . $e->getMessage());
                    $_SESSION['wishlist'] = [];
                }
                
                // Chuyển hướng về trang trước đó nếu có
                if (isset($_SESSION['return_url'])) {
                    $redirect_url = $_SESSION['return_url'];
                    unset($_SESSION['return_url']);
                    error_log('Redirecting to: ' . $redirect_url);
                    header('Location: ' . $redirect_url);
                } else {
                    header('Location: ' . APP_URL . '/Home/show');
                }
                exit();
            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Email hoặc mật khẩu người dùng không đúng!</div></div>';
                $this->view("homePage", ["page" => "LoginView"]);
                return;
            }
        }
    }
}

    // Đăng xuất
    public function logout() {
        session_start();
        
        // ✅ TRƯỚC KHI XÓA SESSION: LƯU GIỎ HÀNG VÀO DATABASE
        if (isset($_SESSION['user']) && isset($_SESSION['cart'])) {
            $userId = (int)$_SESSION['user']['id'];
            error_log('Attempting to save cart before logout for user_id: ' . $userId);
            error_log('Cart items to save: ' . count($_SESSION['cart'] ?? []));
            
            try {
                $cartModel = $this->model('ShoppingCartModel');
                $result = $cartModel->saveCart($userId, $_SESSION['cart']);
                error_log('Cart save result: ' . ($result ? 'SUCCESS' : 'FAILED'));
                error_log('Cart saved before logout for user: ' . $userId);
            } catch (Exception $e) {
                error_log('Error saving cart before logout: ' . $e->getMessage());
            }
        } else {
            error_log('Logout: User not set or cart empty');
        }
        
        // ✅ DANH SÁCH YÊU THÍCH: Đã lưu trực tiếp vào DB khi add/remove, không cần lưu thêm
        // Wishlist sẽ được load lại từ DB khi user login
        
        // Xóa session
        session_destroy();
        header('Location: ' . APP_URL . '/Home');
        exit();
    }

    // Hiển thị form quên mật khẩu
    public function forgotPassword() {
        //$this->view("Font_end/ForgotPasswordView");
        $this->view("homePage",["page"=>"ForgotPasswordView"]);
    }

    // Xử lý gửi lại mật khẩu mới qua email
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $userModel = $this->model('UserModel');
            $stmt = $userModel->findByEmail($email);
            $user = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
            if ($user) {
                $newPass = substr(bin2hex(random_bytes(4)), 0, 8);
                $userModel->updatePassword($email, password_hash($newPass, PASSWORD_DEFAULT));
                $this->sendNewPasswordEmail($email, $newPass);
                echo '<div class="container mt-5"><div class="alert alert-success">Mật khẩu mới đã được gửi về email của bạn!</div></div>';
            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Email không tồn tại!</div></div>';
            }
            //$this->view("Font_end/ForgotPasswordView");
             $this->view("homePage",["page"=>"ForgotPasswordView"]);
            
        }
    }

    // Gửi mật khẩu mới qua email
    private function sendNewPasswordEmail($email, $newPass) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'zerohn889@gmail.com';
            $mail->Password = 'ijgl wiav jtpq nuto';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('vanntphpmailer@gmail.com', 'Your App');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Mật khẩu mới cho tài khoản của bạn";
            $mail->Body = "Mật khẩu mới của bạn là: <b>$newPass</b>";
            $mail->send();
        } catch (Exception $e) {
            // Không echo lỗi ra ngoài
        }
    }
 // ====================== PHẦN ĐĂNG NHẬP ADMIN ======================
    public function ShowAdminLogin() {
        $this->view("Font_end/LoginAdminView");
    }

    public function AdminLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            $adminModel = $this->model("AdminModel");
            $admin = $adminModel->login($username, $password);

            if ($admin) {
                session_start();
                $_SESSION['admin'] = $admin;
                header("Location: " . APP_URL . "/Admin/listOrders");
                exit();
            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Tên đăng nhập hoặc mật khẩu không đúng!</div></div>';
                $this->view("Font_end/LoginAdminView");
            }
        }
    }

    public function AdminLogout() {
        session_start();
        unset($_SESSION['admin']);
        header("Location: " . APP_URL . "/HOME/");
        exit();
    }

    public function ShowAdminRegister() {
       $this->view("homePage", ["page" => "RegisterAdminView"]);
    }

    public function AdminRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $email = trim($_POST['email']);
            $fullname = trim($_POST['fullname']);

            if ($username === '' || $password === '' || $email === '' || $fullname === '') {
                echo '<div class="container mt-5"><div class="alert alert-danger">Vui lòng nhập đầy đủ thông tin!</div></div>';
                $this->view("homePage", ["page" => "RegisterAdminView"]);
                return;
            }

            $adminModel = $this->model("AdminModel");
            if ($adminModel->exists($username) > 0) {
                echo '<div class="container mt-5"><div class="alert alert-danger">Tên đăng nhập đã tồn tại!</div></div>';
                $this->view("homePage", ["page" => "RegisterAdminView"]);
                return;
            }

            // Tạo mã OTP
            $otp = rand(100000, 999999);
            $_SESSION['admin_register'] = [
                'username' => $username,
                'password' => $password,
                'email' => $email,
                'fullname' => $fullname,
                'otp' => $otp
            ];

            // Gửi OTP qua email
            $this->sendOtpEmail($email, $otp);

            // Chuyển đến trang nhập OTP cho admin
            $this->view("homePage", ["page" => "OtpView", "isAdmin" => true]);
        }
    }

    // Xác thực OTP cho Admin
    public function verifyAdminOtp() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputOtp = $_POST['otp'];
            if (isset($_SESSION['admin_register']) && $_SESSION['admin_register']['otp'] == $inputOtp) {
                // Lấy thông tin đăng ký từ session
                $adminData = $_SESSION['admin_register'];
                
                // Tạo tài khoản admin
                $adminModel = $this->model("AdminModel");
                $result = $adminModel->register(
                    $adminData['username'],
                    $adminData['password'],
                    $adminData['email'],
                    $adminData['fullname']
                );

                if ($result) {
                    unset($_SESSION['admin_register']);
                    echo '<div class="container mt-5"><div class="alert alert-success">Đăng ký admin thành công! Mời bạn đăng nhập.</div></div>';
                    $this->view("Font_end/LoginAdminView");
                } else {
                    echo '<div class="container mt-5"><div class="alert alert-danger">Có lỗi xảy ra khi đăng ký!</div></div>';
                    $this->view("homePage", ["page" => "RegisterAdminView"]);
                }
            } else {
                echo '<div class="container mt-5"><div class="alert alert-danger">Mã OTP không đúng!</div></div>';
                $this->view("homePage", ["page" => "OtpView", "isAdmin" => true]);
            }
        }
    }
}
