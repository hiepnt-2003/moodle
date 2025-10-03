# DEBUG - Course Clone API

## Bước 1: Test debug endpoint

Thay đổi URL trong Postman thành:
```
http://localhost/local/courseclone/debug_api.php
```

Test với cùng request để xem lỗi chi tiết.

## Bước 2: Kiểm tra Moodle config

1. Đảm bảo Moodle đã cài đặt đúng
2. Plugin đã được install qua Site Administration > Notifications
3. Web services đã được enable

## Bước 3: Nếu debug_api.php hoạt động

Quay lại `simple_restful.php` và kiểm tra:

### Test với curl đơn giản:
```bash
curl -X POST http://localhost/local/courseclone/debug_api.php \
  -H "Content-Type: application/json" \
  -d '{"test": "hello"}'
```

## Các lỗi thường gặp:

### 500 Error - Moodle config
- Kiểm tra đường dẫn `../../config.php` có đúng không
- Đảm bảo Moodle database connection OK

### 500 Error - External lib
- Plugin chưa được install
- File `externallib.php` có syntax error

### 500 Error - PHP
- PHP version không tương thích
- Missing extensions

## Next Steps:
1. Test debug_api.php trước
2. Nếu OK, sẽ fix simple_restful.php
3. Nếu không OK, cần fix Moodle setup