# Local Hello Plugin

## Mô tả
Đây là một plugin local đơn giản cho Moodle, hiển thị trang "Hello World" với một số tính năng cơ bản.

## Tính năng
- Hiển thị thông điệp chào mừng
- Hiển thị thông tin người dùng hiện tại
- Form đơn giản để nhập tên
- Tích hợp với hệ thống menu của Moodle

## Cài đặt
1. Sao chép thư mục `hello` vào thư mục `local/` của Moodle
2. Truy cập Site Administration để cài đặt plugin
3. Plugin sẽ xuất hiện trong menu chính

## Cách sử dụng
- Truy cập: `/local/hello/index.php`
- Hoặc tìm trong menu "Local plugins"

## Cấu trúc file
```
local/hello/
├── version.php          # Thông tin phiên bản plugin
├── index.php           # Trang chính
├── lib.php             # Thư viện functions
├── db/
│   └── access.php      # Định nghĩa capabilities
└── lang/
    └── en/
        └── local_hello.php  # Chuỗi ngôn ngữ tiếng Anh
```

## Permissions
Plugin này sử dụng capability `local/hello:view` để kiểm soát quyền truy cập.

## Tương thích
- Moodle 4.0+
- PHP 7.4+