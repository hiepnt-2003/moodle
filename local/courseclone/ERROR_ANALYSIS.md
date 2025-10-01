# Giải thích lỗi và cách khắc phục

## Lỗi gặp phải: "Course restore failed: error/cannot_precheck_wrong_status"

### Nguyên nhân:
1. **Backup/Restore process phức tạp** - Moodle backup/restore có nhiều bước validation
2. **Status conflict** - Restore controller không thể thực hiện precheck do trạng thái không đúng
3. **Permission issues** - Có thể thiếu quyền hoặc context không đúng

### Giải pháp đã áp dụng:

#### 1. Đơn giản hóa code 
- Loại bỏ backup/restore phức tạp
- Sử dụng `duplicate_course()` function của Moodle (đơn giản hơn)
- Giảm thiểu các step validation

#### 2. Cập nhật logic clone:
```php
// Thay vì backup/restore:
$new_course = duplicate_course(
    $source_course->id, 
    $fullname, 
    $shortname, 
    $category, 
    $visible, 
    $options
);
```

#### 3. Sửa error messages
- Thay `get_string()` bằng plain text để tránh dependency issues

### Test lại với Postman:

#### Request mới:
```
POST {{moodle_url}}/webservice/rest/server.php

Body parameters:
- wstoken: [YOUR_TOKEN]
- wsfunction: local_courseclone_clone_course  
- moodlewsrestformat: json
- shortname_clone: [EXISTING_COURSE_SHORTNAME]
- fullname: Test Clone Course 2025
- shortname: test_clone_2025
- startdate: 1704067200
- enddate: 1735689600
```

#### Kiểm tra trước khi test:
1. **Có course để clone không?**
2. **Token có đúng service không?**
3. **User có đủ quyền không?**

### Nếu vẫn lỗi:

#### Lỗi "Function not found":
- Plugin chưa install đúng
- Function chưa được add vào service

#### Lỗi "Access denied":  
- User không có quyền `moodle/course:create`
- Token không được assign đúng service

#### Lỗi "Course not found":
- Shortname source course không tồn tại
- Kiểm tra bằng: Site Administration → Courses → Manage courses

### Debug steps:
1. Check Moodle logs: Site Administration → Reports → Logs
2. Test với admin user
3. Verify service configuration
4. Test với course có sẵn trong hệ thống

File đã được sửa để đơn giản hóa và tránh lỗi backup/restore phức tạp!