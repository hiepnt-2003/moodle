# User Activity Report Plugin

Plugin báo cáo nhật ký hoạt động của người dùng trong Moodle.

## 🗂️ Cấu trúc file sau khi dọn dẹp:

```
report/userreport/
│   index.php                    # Trang chính của plugin
│   locallib.php                 # Form và các hàm thư viện cục bộ  
│   settings.php                 # Đăng ký plugin vào admin menu
│   version.php                  # Thông tin phiên bản
│   styles.css                   # CSS cho giao diện
│   README.md                    # Hướng dẫn sử dụng
│
├───classes/
│   └───privacy/
│       └───provider.php         # Privacy provider (GDPR compliance)
│
├───db/
│   └───access.php               # Định nghĩa capabilities
│
└───lang/en/
    └───report_userreport.php    # String tiếng Anh
```

## Tính năng

1. **Form lọc dữ liệu**:
   - Chọn người dùng (loại trừ người dùng đã bị xóa)
   - Chọn ngày bắt đầu và ngày kết thúc  
   - Chọn khóa học cụ thể hoặc tất cả khóa học

2. **Hiển thị báo cáo dạng bảng** với các cột:
   - Thời gian
   - Tên đầy đủ người dùng
   - Người dùng bị ảnh hưởng
   - Bối cảnh sự kiện
   - Thành phần
   - Tên sự kiện
   - Mô tả
   - Nguồn gốc
   - Địa chỉ IP

3. **Tích hợp vào hệ thống Report** của Moodle

## Cài đặt

1. Copy thư mục `userreport` vào `report/` của Moodle
2. Truy cập trang quản trị để cài đặt plugin
3. Plugin sẽ xuất hiện trong menu Reports

## Quyền truy cập

Plugin yêu cầu quyền `report/userreport:view` để xem báo cáo.

Các vai trò có quyền mặc định:
- Manager
- Course creator
- Editing teacher

## Sử dụng

1. Truy cập **Site administration > Reports > User Activity Report**
2. Chọn các tiêu chí lọc:
   - Người dùng muốn xem
   - Khoảng thời gian
   - Khóa học (tùy chọn)
3. Nhấn "Tạo báo cáo" để xem kết quả

## Kỹ thuật

- Sử dụng bảng `logstore_standard_log` để lấy dữ liệu log
- Tích hợp với hệ thống form của Moodle
- Responsive design với Bootstrap CSS
- Hỗ trợ phân trang (giới hạn 1000 kết quả)

## Bảo mật

- Plugin không lưu trữ dữ liệu cá nhân
- Chỉ hiển thị dữ liệu log có sẵn trong hệ thống
- Kiểm tra quyền truy cập nghiêm ngặt