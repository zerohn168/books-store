# Hệ Thống Kiểm Duyệt Nội Dung (Content Moderation System)

## Tổng Quan

Hệ thống kiểm duyệt nội dung được thiết kế để:

- **Tự động phân tích** nội dung đánh giá/bình luận để phát hiện spam
- **Cảnh báo admin** về nội dung nghi ngờ
- **Cho phép admin** kiểm duyệt thủ công và quyết định duyệt/từ chối
- **Ngăn chặn** nội dung không phù hợp hiển thị cho khách hàng

## Cấu Trúc

### 1. ContentModerationService (app/ContentModerationService.php)

Lớp dịch vụ chính xử lý phân tích nội dung tự động.

#### Phương Thức Chính:

**`analyzeContent($content, $rating = 5)`**

- Phân tích nội dung tự động
- Trả về: mảng kết quả với các thông tin:
  - `is_approved`: boolean - Nội dung có được duyệt tự động không
  - `spam_score`: số từ 0-100 - Điểm spam (cao hơn = nguy hiểm hơn)
  - `issues`: mảng - Danh sách vấn đề phát hiện
  - `warnings`: mảng - Cảnh báo
  - `prohibited_words_found`: mảng - Từ cấm tìm thấy
  - `suspicious_patterns_found`: mảng - Patterns nghi ngờ

**`sanitizeContent($content)`**

- Làm sạch nội dung (loại bỏ HTML, ký tự điều khiển)
- Trả về: nội dung đã được sanitize

**`getTrustScore($spam_score)`**

- Tính mức độ tin cậy (0-100, cao hơn = tin cậy hơn)
- Công thức: `max(0, 100 - spam_score)`

**`getPredictedStatus($spam_score)`**

- Dự đoán trạng thái duyệt dựa vào spam_score
- Trả về: 'spam', 'pending', hoặc 'approved'

#### Quy Tắc Phân Tích:

1. **Độ dài nội dung**: 10-5000 ký tự
2. **Từ cấm**: Kiểm tra danh sách từ cấm (có thể custom)
3. **Spam patterns**: URL, email, phone
4. **Rating không phù hợp**: Rating cao nhưng nội dung tiêu cực
5. **Viết hoa quá nhiều**: >50% chữ hoa
6. **Ký tự đặc biệt**: >30% ký tự đặc biệt
7. **Repeat characters**: Ký tự lặp quá nhiều
8. **Từ lặp**: Từ lặp >40% tần suất

#### Spam Score Thresholds:

- **> 60**: Tự động từ chối (spam)
- **30-60**: Chờ kiểm duyệt thủ công
- **< 30**: Tự động duyệt

### 2. ReviewModel (models/ReviewModel.php)

Thêm các phương thức kiểm duyệt:

**`getPendingReviews()`** - Danh sách chờ duyệt
**`getRejectedReviews()`** - Danh sách spam/từ chối
**`getApprovedReviews()`** - Danh sách đã duyệt
**`getModerationStats()`** - Thống kê tổng quát
**`updateModerationStatus($reviewId, $status, $adminId, $reason, $notes)`** - Cập nhật trạng thái
**`recordSpamAnalysis($reviewId, $spamScore, $prohibitedWords)`** - Ghi nhận kết quả phân tích
**`getReviewDetail($reviewId)`** - Chi tiết review để kiểm duyệt
**`bulkUpdateStatus($reviewIds, $status, $adminId)`** - Duyệt hàng loạt

### 3. ReviewController (controllers/ReviewController.php)

Khi user gửi review, hệ thống sẽ:

```php
1. Sanitize nội dung
2. Phân tích tự động (analyzeContent)
3. Ghi nhận spam score
4. Cập nhật trạng thái dựa vào dự đoán
5. Thông báo cho user kết quả
```

### 4. ContentModerationController (controllers/ContentModerationController.php)

Controller admin để quản lý kiểm duyệt.

**Routes:**

- `GET /ContentModerationController` - Dashboard
- `GET /ContentModerationController/pending` - Danh sách chờ duyệt
- `GET /ContentModerationController/approved` - Danh sách đã duyệt
- `GET /ContentModerationController/rejected` - Danh sách spam/từ chối
- `GET /ContentModerationController/review/{id}` - Chi tiết review
- `POST /ContentModerationController/approve` - Duyệt review
- `POST /ContentModerationController/reject` - Từ chối review
- `POST /ContentModerationController/markSpam` - Đánh dấu spam
- `POST /ContentModerationController/bulkApprove` - Duyệt hàng loạt

### 5. Database Schema

Thêm các cột vào bảng `tblreview`:

```sql
ALTER TABLE tblreview ADD COLUMN IF NOT EXISTS ly_do_tu_choi VARCHAR(500) NULL;
ALTER TABLE tblreview ADD COLUMN IF NOT EXISTS moderation_status ENUM('pending', 'approved', 'rejected', 'spam');
ALTER TABLE tblreview ADD COLUMN IF NOT EXISTS moderated_by INT NULL;
ALTER TABLE tblreview ADD COLUMN IF NOT EXISTS moderation_date DATETIME NULL;
ALTER TABLE tblreview ADD COLUMN IF NOT EXISTS flagged_as_spam INT DEFAULT 0;
ALTER TABLE tblreview ADD COLUMN IF NOT EXISTS contains_prohibited_words INT DEFAULT 0;
ALTER TABLE tblreview ADD COLUMN IF NOT EXISTS moderation_notes TEXT NULL;
```

## Hướng Dẫn Sử Dụng

### Cho Admin

1. **Truy cập Dashboard**

   - URL: `/ContentModerationController`
   - Xem thống kê tổng quát

2. **Xem danh sách chờ duyệt**

   - URL: `/ContentModerationController/pending`
   - Click "Xem" để xem chi tiết

3. **Kiểm duyệt chi tiết**

   - Đọc nội dung review
   - Xem spam score và phân tích tự động
   - Chọn hành động:
     - **Duyệt**: Review hiển thị cho khách hàng
     - **Từ chối**: Review không hiển thị, lưu lý do từ chối
     - **Spam**: Review đánh dấu là spam, không hiển thị

4. **Duyệt hàng loạt**
   - Chọn multiple checkboxes
   - Click "Duyệt Được Chọn"

### Cho User

1. **Gửi review**

   - Nội dung được tự động kiểm tra
   - Nếu spam: hiển thị cảnh báo
   - Nếu cần kiểm duyệt: "Admin sẽ kiểm duyệt sớm"
   - Nếu ok: "Review đã được duyệt"

2. **Chỉ xem review đã duyệt**
   - Chỉ review với `moderation_status = 'approved'` mới hiển thị

## Tùy Chỉnh

### Thêm từ cấm

Chỉnh sửa file `app/ContentModerationService.php`:

```php
private static $prohibitedWords = [
    'từ 1', 'từ 2', 'từ 3',
    // Thêm thêm từ khóa cần cấm
];
```

### Thay đổi thresholds

```php
// Trong phương thức analyzeContent()
if ($result['spam_score'] > 60) { // Thay đổi 60
    $result['is_approved'] = false;
}
```

### Thêm quy tắc kiểm tra

```php
// Trong analyzeContent(), thêm logic mới
// Ví dụ: kiểm tra từ khóa đặc biệt, domain blacklist, v.v.
```

## Thống Kê

Dashboard hiển thị:

- **Tổng đánh giá**: Tất cả review
- **Chờ duyệt**: Reviews với `moderation_status = 'pending'`
- **Đã duyệt**: Reviews với `moderation_status = 'approved'`
- **Spam/Từ Chối**: Reviews với `moderation_status IN ('spam', 'rejected')`

## Workflow

```
User gửi review
    ↓
Sanitize nội dung
    ↓
Phân tích tự động (spam_score)
    ↓
    ├─→ spam_score > 60 → Tự động spam
    ├─→ 30 < spam_score ≤ 60 → Chờ duyệt (cần admin xem)
    └─→ spam_score ≤ 30 → Tự động duyệt
    ↓
Review hiển thị hoặc chờ duyệt
    ↓
Admin có thể override quyết định
    ↓
User nhìn thấy review đã duyệt
```

## Lợi Ích

✅ **Giảm spam**: Tự động phát hiện spam content
✅ **Tiết kiệm thời gian**: Admin chỉ cần xem review nghi ngờ
✅ **Bảo vệ uy tín**: Không hiển thị nội dung không phù hợp
✅ **Tracking**: Lưu lại tất cả quyết định duyệt
✅ **Linh hoạt**: Admin có thể override quyết định tự động

## Kỹ Thuật

- **Framework**: PHP MVC custom
- **Database**: MySQL
- **Algorithm**: Pattern matching + keyword filtering + heuristics
- **Performance**: O(n) analysis, indexed queries

## Troubleshooting

**Review không được duyệt:**

- Kiểm tra `moderation_status` trong database
- Kiểm tra spam_score - quá cao?
- View chỉ lấy review với `trangthai = 'đã duyệt'`

**Spam score quá cao:**

- Kiểm tra các từ cấm trong nội dung
- Kiểm tra patterns (URL, email)
- Xem chi tiết phân tích để hiểu lý do

**Admin không thấy pending reviews:**

- Kiểm tra permissions
- Refresh page
