<?php
require_once 'BaseModel.php';

class AdminModel extends BaseModel {
    protected $table = 'tbladmin';

    // ====================== ĐĂNG KÝ ADMIN ======================
    public function register($username, $password, $email, $fullname) {
        // Mã hóa mật khẩu trước khi lưu
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO {$this->table} (username, password, email, fullname) 
                VALUES (?, ?, ?, ?)";
        return $this->query($sql, [$username, $hashed, $email, $fullname]);
    }

    // ====================== ĐĂNG NHẬP ADMIN ======================
    public function login($username, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $result = $this->select($sql, [$username]);

        if ($result && password_verify($password, $result[0]['password'])) {
            return $result[0];
        }

        return false;
    }

    // ====================== KIỂM TRA TÊN ĐĂNG NHẬP ======================
    public function exists($username) {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE username = ?";
        $result = $this->select($sql, [$username]);
        return $result[0]['total'] ?? 0;
    }

    // ====================== LẤY DANH SÁCH ADMIN (dành cho tương lai) ======================
    public function getAllAdmins() {
        $sql = "SELECT id, username, email, fullname, created_at FROM {$this->table} ORDER BY created_at DESC";
        return $this->select($sql);
    }

    // ====================== XOÁ ADMIN ======================
    public function deleteAdmin($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->query($sql, [$id]);
    }
}
