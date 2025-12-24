# 🚀 Content Moderation System - Quick Start (5 Phút)

## ⏱️ Quick Setup

### Bước 1: Database Migration (2 phút)

**Mở PhpMyAdmin:**

```
1. Vào http://localhost/phpmyadmin
2. Chọn database của bạn
3. Click "Import" tab
4. Chọn file: migrations/content_moderation_migration.sql
5. Click "Import" button
```

Done! ✅

### Bước 2: Test Hệ Thống (1 phút)

**Mở test page:**

```
http://localhost/phpnangcao/MVC/test_content_moderation.php
```

Sẽ thấy các test cases và spam scores. ✅

### Bước 3: Login Admin (1 phút)

```
1. Truy cập: http://localhost/phpnangcao/MVC
2. Login với tài khoản admin
3. Truy cập dashboard
```

### Bước 4: Truy cập Moderation Panel (1 phút)

```
http://localhost/phpnangcao/MVC/ContentModerationController
```

**Hoặc từ admin menu:**

```
Admin → Kiểm Duyệt Nội Dung
```

---

## 📊 Sử Dụng

### Xem Review Chờ Duyệt

1. Click "Chờ Duyệt"
2. Thấy danh sách review
3. Click "Xem" để chi tiết

### Kiểm Duyệt Review

1. Đọc nội dung
2. Xem spam score + phân tích
3. Chọn hành động:
   - **Duyệt** ✅ - Review hiển thị
   - **Từ Chối** ❌ - Ẩn, lưu lý do
   - **Spam** 🚫 - Đánh dấu spam

### Approve Hàng Loạt

1. Chọn multiple checkboxes
2. Click "Duyệt Được Chọn"
3. Done!

---

## 🎨 Features

### Dashboard

- Thống kê tổng quát
- Số review chờ/duyệt/spam
- Quick links

### Pending Reviews

- Danh sách chờ duyệt
- Spam score (0-100)
- Bulk approve

### Review Detail

- Nội dung đầy đủ
- Spam analysis results
- Approve/Reject/Spam buttons
- Ghi chú

---

## 🔧 Tùy Chỉnh

### Thêm Từ Cấm

Edit `app/ContentModerationService.php`:

```php
private static $prohibitedWords = [
    'từ cấm 1', 'từ cấm 2',
    // Thêm từ khóa khác
];
```

### Thay Thresholds

Trong `ContentModerationService::analyzeContent()`:

```php
if ($result['spam_score'] > 50) {  // Thay từ 60
    $result['is_approved'] = false;
}
```

---

## 📈 Spam Score

```
< 30      → Auto APPROVED ✅
30-60     → PENDING (cần xem) ⏳
> 60      → Auto SPAM ❌
```

Hệ thống tự động kiểm tra:

- Độ dài nội dung
- Từ cấm
- URL/Email/Phone
- Rating không phù hợp
- Chữ hoa quá nhiều
- Ký tự đặc biệt
- Từ/ký tự lặp

---

## 🎯 Workflow

```
User gửi review
    ↓
Auto sanitize & analyze
    ↓
├─ Spam → Ẩn tự động
├─ Pending → Chờ admin
└─ Approved → Hiển thị
    ↓
Admin có thể override
    ↓
Review hiển thị (nếu approved)
```

---

## 📱 Mobile-Friendly

✅ Dashboard responsive
✅ Tables mobile-friendly
✅ Buttons touch-friendly

---

## 🔒 Security

✅ Admin-only access
✅ Input sanitization
✅ SQL injection prevention
✅ Audit trail

---

## ⚠️ Troubleshooting

**Không thấy admin menu?**

- Cần thêm menu item vào sidebar (xem ADMIN_MENU_ITEM.php)

**Review không hiển thị?**

- Kiểm tra moderation_status = 'approved'

**Spam score cao?**

- Tùy chỉnh từ cấm hoặc thresholds

---

## 📚 Tài Liệu Đầy Đủ

- `README_MODERATION.md` - Full guide
- `CONTENT_MODERATION_GUIDE.md` - Technical details
- `DEPLOYMENT_SUMMARY.md` - Deployment guide

---

## ✅ Hoàn Tất!

Setup xong! Bạn đã có:

✅ Tự động phân tích spam  
✅ Admin dashboard kiểm duyệt  
✅ Approve/Reject/Spam actions  
✅ Hàng loạt duyệt  
✅ Thống kê & audit trail

🎉 **Sẵn sàng bảo vệ nội dung!**

---

## 🎓 Pro Tips

1. **Ngay lập tức duyệt review tốt**

   - Dùng bulk approve cho review đã check

2. **Từ chối = Feedback cho user**

   - Luôn ghi rõ lý do từ chối

3. **Spam cần xem lại**

   - Không phải tất cả spam score cao = spam thật
   - Review lại nếu cần

4. **Custom từ cấm**

   - Thêm từ khóa phù hợp với sản phẩm của bạn

5. **Monitor thường xuyên**
   - Kiểm tra daily hoặc weekly

---

**Version**: 1.0  
**Last Updated**: December 2025  
**Status**: Production Ready ✅

💡 **Cần giúp?** Xem tài liệu hoặc check logs
