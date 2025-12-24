# 📚 PHP Bookstore - MVC Application

Ứng dụng quản lý cửa hàng sách trực tuyến với hệ thống kiểm duyệt nội dung, đánh giá sản phẩm, quản lý đơn hàng và tích hợp thanh toán VNPay.

---

## 🚀 Tính Năng Chính

### 👥 Hệ Thống Người Dùng

- Đăng ký, đăng nhập, quên mật khẩu
- Quản lý hồ sơ cá nhân
- Hệ thống quyền & phân quyền (Admin, Moderator, User)

### 📦 Quản Lý Sản Phẩm

- Danh mục sản phẩm theo loại
- Tìm kiếm và lọc sản phẩm
- Chi tiết sản phẩm, hình ảnh, giá cả
- Wishlist (danh sách yêu thích)

### 🛒 Giỏ Hàng & Đơn Hàng

- Thêm/xóa/cập nhật giỏ hàng
- Lưu trữ giỏ hàng (session + database)
- Quản lý đơn hàng
- Lịch sử mua hàng

### ⭐ Đánh Giá & Nhận Xét

- Hệ thống đánh giá 5 sao
- Viết nhận xét sản phẩm
- **Hệ thống kiểm duyệt tự động** (xem bên dưới)

### 🛡️ Kiểm Duyệt Nội Dung (Content Moderation)

Hệ thống AI phát hiện spam, nội dung không phù hợp:

- **Điểm spam** (0-100): phân loại mức độ
- **Trạng thái**: Đợi duyệt, Phê duyệt, Từ chối, Spam
- **Dashboard Admin**: Duyệt/từ chối nhận xét theo batch
- **Khuyến nghị tự động**: Dựa trên nội dung
- **Lý do từ chối**: Ghi chú chi tiết khi từ chối

### 💳 Thanh Toán

- Tích hợp VNPay
- Xử lý phản hồi thanh toán
- Quản lý trạng thái đơn hàng

### 📰 Tin Tức & Chương Trình Khuyến Mãi

- Danh sách tin tức
- Quản lý sự kiện khuyến mãi
- Sản phẩm quảng cáo nổi bật

### 💬 Chatbox

- Chat trực tuyến với khách hàng
- Lưu lịch sử cuộc trò chuyện
- Thông báo tin nhắn mới

---

## 📁 Cấu Trúc Dự Án

```
├── app/                              # Core Framework
│   ├── App.php                       # Bootstrap ứng dụng
│   ├── config.php                    # Cấu hình toàn cục
│   ├── DB.php                        # Kết nối database
│   ├── Controller.php                # Base controller
│   ├── ContentModerationService.php  # Dịch vụ kiểm duyệt
│   ├── EmailService.php              # Dịch vụ email
│   └── helpers.php                   # Hàm trợ giúp
│
├── controllers/                      # Điểm vào ứng dụng
│   ├── Home.php                      # Trang chủ
│   ├── AuthController.php            # Đăng nhập/đăng ký
│   ├── Product.php                   # Sản phẩm
│   ├── ReviewController.php          # Đánh giá & kiểm duyệt
│   ├── CartController.php            # Giỏ hàng
│   ├── OrderController.php           # Đơn hàng
│   ├── VnpayReturnController.php     # Xử lý thanh toán
│   ├── WishlistController.php        # Danh sách yêu thích
│   ├── ChatboxController.php         # Chat
│   ├── NewsController.php            # Tin tức
│   ├── PromotionController.php       # Khuyến mãi
│   ├── Admin.php                     # Dashboard Admin
│   ├── AdminManagementController.php # Quản lý hệ thống
│   └── ...
│
├── models/                           # Xử lý dữ liệu
│   ├── ReviewModel.php               # Quản lý đánh giá
│   ├── OrderModel.php                # Quản lý đơn hàng
│   └── ...
│
├── views/                            # Template HTML
│   ├── review/                       # Views đánh giá
│   ├── order/                        # Views đơn hàng
│   └── ...
│
├── middleware/                       # Xử lý trung gian
│   └── PermissionMiddleware.php      # Kiểm tra quyền
│
├── migrations/                       # Schema database
│   └── content_moderation_migration.sql
│
├── public/                           # Assets (CSS, JS, hình ảnh)
│   ├── css/
│   ├── js/
│   └── images/
│
├── vnpay_php/                        # Thư viện VNPay
│
├── vendor/                           # Composer dependencies
│
├── index.php                         # Front controller
├── composer.json                     # Quản lý dependencies
├── .htaccess                         # Cấu hình Apache
├── README.md                         # File này
├── CONTENT_MODERATION_GUIDE.md       # Hướng dẫn kiểm duyệt
├── README_MODERATION.md              # Quick start kiểm duyệt
├── QUICKSTART.md                     # Hướng dẫn nhanh
└── DEPLOYMENT_SUMMARY.md             # Tóm tắt triển khai
```

---

## ⚙️ Cài Đặt & Chạy

### 1️⃣ Yêu Cầu Hệ Thống

- PHP 7.4+
- MySQL 5.7+
- Apache (với mod_rewrite)
- Composer

### 2️⃣ Cài Đặt

```bash
# Clone/tải dự án
cd d:\xamcc\htdocs\phpnangcao\MVC

# Cài dependencies
composer install

# Tạo database
# Nhập SQL từ folder migrations/
```

### 3️⃣ Cấu Hình

Sửa file `app/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bookstore_db');
define('BASE_URL', 'http://localhost/phpnangcao/MVC/');
```

### 4️⃣ Chạy Ứng Dụng

```
http://localhost/phpnangcao/MVC/
```

---

## 🔐 Hệ Thống Kiểm Duyệt Nội Dung

### Cách Hoạt Động

1. Khách hàng gửi đánh giá/nhận xét
2. `ContentModerationService` phân tích tự động:
   - Phát hiện spam, từ khóa cấm
   - Tính toán điểm spam (0-100)
   - Đề xuất trạng thái ban đầu
3. Admin xem dashboard → duyệt/từ chối
4. Nhận xét được phê duyệt → hiển thị trên trang sản phẩm

### Các Trạng Thái

| Trạng Thái | Ý Nghĩa               |
| ---------- | --------------------- |
| `pending`  | Đợi duyệt             |
| `approved` | Đã phê duyệt          |
| `rejected` | Bị từ chối            |
| `spam`     | Được xác định là spam |

### Dashboard Admin

- URL: `/admin/reviews` (cần quyền Moderator)
- Lọc theo trạng thái
- Xem chi tiết + lý do
- Duyệt hoặc từ chối

Chi tiết xem: [CONTENT_MODERATION_GUIDE.md](CONTENT_MODERATION_GUIDE.md)

---

## 📋 Schema Database Chính

### Bảng Reviews (Mở Rộng)

```sql
ALTER TABLE reviews ADD COLUMN (
  moderation_status VARCHAR(20) DEFAULT 'pending',
  spam_score DECIMAL(5,2) DEFAULT 0,
  ly_do_tu_choi TEXT,
  moderated_by INT,
  moderation_date DATETIME,
  moderation_notes TEXT
);
```

### Các Bảng Khác

- `users` - Người dùng
- `products` - Sản phẩm
- `orders` - Đơn hàng
- `order_details` - Chi tiết đơn hàng
- `reviews` - Đánh giá
- `wishlist` - Danh sách yêu thích
- `chatbox` - Tin nhắn
- `news` - Tin tức
- `promotions` - Khuyến mãi
- Và nhiều bảng khác...

---

## 🎯 Hướng Dẫn Nhanh

### Cho Admin

1. Đăng nhập với tài khoản admin
2. Vào `Admin Dashboard` → `Quản Lý Đánh Giá`
3. Xem nhận xét chờ duyệt
4. Click "Phê Duyệt" hoặc "Từ Chối"
5. Nhập lý do (nếu từ chối)

### Cho Khách Hàng

1. Mua sản phẩm
2. Viết đánh giá trên trang sản phẩm
3. Nhận xét tự động được kiểm duyệt
4. Xem nhận xét sau khi được phê duyệt

---

## 🔧 API Endpoints

### Đánh Giá

- `POST /reviews/add` - Thêm đánh giá
- `GET /reviews/product/{id}` - Lấy đánh giá sản phẩm
- `POST /reviews/moderate` - Duyệt/từ chối (Admin)

### Giỏ Hàng

- `POST /cart/add` - Thêm sản phẩm
- `POST /cart/remove` - Xóa sản phẩm
- `GET /cart` - Xem giỏ hàng

### Đơn Hàng

- `POST /orders/create` - Tạo đơn hàng
- `GET /orders` - Lịch sử đơn hàng
- `GET /orders/{id}` - Chi tiết đơn hàng

### Wishlist

- `POST /wishlist/add` - Thêm yêu thích
- `DELETE /wishlist/{id}` - Xóa yêu thích
- `GET /wishlist` - Danh sách yêu thích

---

## 📞 Support & Tài Liệu

- 📖 [QUICKSTART.md](QUICKSTART.md) - Hướng dẫn nhanh 5 phút
- 📋 [CONTENT_MODERATION_GUIDE.md](CONTENT_MODERATION_GUIDE.md) - Hướng dẫn kiểm duyệt chi tiết
- 🚀 [DEPLOYMENT_SUMMARY.md](DEPLOYMENT_SUMMARY.md) - Tóm tắt triển khai

---

## 📝 Lịch Sử Phát Triển

### v1.0 (Hiện Tại)

- ✅ Hệ thống quản lý sách
- ✅ Giỏ hàng & đơn hàng
- ✅ Đánh giá & kiểm duyệt
- ✅ Thanh toán VNPay
- ✅ Chat trực tuyến
- ✅ Quản lý người dùng & quyền

---

## 📄 License

Dự án này được phát triển cho mục đích học tập và sử dụng nội bộ.

---

**Cập nhật lần cuối**: 24 Tháng 12, 2025
