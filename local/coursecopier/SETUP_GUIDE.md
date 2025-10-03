# Hướng dẫn cài đặt Course Copier Plugin

## Bước 1: Cài đặt Plugin

1. **Upload plugin:**
   - Copy thư mục `coursecopier` vào `moodle/local/`
   - Đảm bảo đường dẫn là: `moodle/local/coursecopier/`

2. **Cài đặt qua Moodle:**
   - Login với tài khoản Admin
   - Truy cập: **Site administration > Notifications**
   - Click **Upgrade Moodle database now**
   - Plugin sẽ được cài đặt tự động

## Bước 2: Cấu hình Web Services

### 2.1 Enable Web Services
1. **Site administration > Plugins > Web services > Overview**
2. Click **Enable web services** (nếu chưa enable)
3. **Enable protocols:** Chọn **REST protocol**

### 2.2 Tạo External Service
1. **Site administration > Plugins > Web services > External services**
2. Click **Add** để tạo service mới:
   - **Name:** Course Copier Service
   - **Short name:** coursecopier  
   - **Enabled:** Yes
   - **Authorised users only:** No (hoặc Yes nếu muốn giới hạn user)

### 2.3 Add Functions to Service
1. Click **Functions** bên cạnh service vừa tạo
2. Add các functions:
   - `local_coursecopier_copy_course`
   - `local_coursecopier_get_available_courses`

### 2.4 Tạo Web Service Token
1. **Site administration > Plugins > Web services > Manage tokens**
2. Click **Create token:**
   - **User:** Chọn user có quyền admin hoặc course creator
   - **Service:** Chọn "Course Copier Service"
   - **Valid until:** Để trống (không hết hạn) hoặc chọn ngày
3. **Copy token** để sử dụng trong API calls

## Bước 3: Cấp quyền User

User cần có các capabilities sau:
- `moodle/course:create` - Tạo course mới
- `moodle/course:view` - Xem courses
- `moodle/backup:backupcourse` - Backup course
- `moodle/restore:restorecourse` - Restore course

### Cách cấp quyền:
1. **Site administration > Users > Permissions > Define roles**
2. Edit role của user (ví dụ: Course creator, Manager)
3. Tìm và allow các capabilities trên

## Bước 4: Test API

### 4.1 Test bằng Debug Tool
1. Truy cập: `your-moodle-url/local/coursecopier/debug_api.php`
2. Kiểm tra status của web services và functions
3. Lấy sample data để test

### 4.2 Test bằng cURL

**Get Available Courses:**
```bash
curl -X POST "https://your-moodle.com/webservice/rest/server.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=local_coursecopier_get_available_courses" \
  -d "moodlewsrestformat=json" \
  -d "categoryid=0"
```

**Copy Course:**
```bash
curl -X POST "https://your-moodle.com/webservice/rest/server.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=local_coursecopier_copy_course" \
  -d "moodlewsrestformat=json" \
  -d "shortname_clone=ORIGINAL123" \
  -d "fullname=New Course Name" \
  -d "shortname=NEWCOURSE2025" \
  -d "startdate=1704067200" \
  -d "enddate=1719792000"
```

### 4.3 Test bằng Postman

1. **Import Collection:**
   - Mở Postman
   - Import file `Course_Copier_API.postman_collection.json`

2. **Setup Environment:**
   - Tạo environment với variables:
     - `moodle_url`: https://your-moodle.com
     - `ws_token`: your_webservice_token

3. **Run Tests:**
   - Chạy "Get Available Courses" trước để lấy danh sách courses
   - Chạy "Copy Course" để test copy function
   - Chạy "Test Invalid Dates" để test validation

## Bước 5: Troubleshooting

### Lỗi thường gặp:

**1. "Web service not available"**
- Kiểm tra web services đã enable chưa
- Kiểm tra REST protocol đã enable chưa

**2. "Invalid token"**
- Kiểm tra token có đúng không
- Kiểm tra token có expired không
- Kiểm tra user của token có đủ quyền không

**3. "Function not found"**
- Plugin chưa được cài đặt đúng
- Chạy lại database upgrade: Site administration > Notifications

**4. "Access control exception"**
- User không có đủ capabilities required
- Cấp thêm quyền cho user/role

**5. "Course not found"**
- Shortname của source course không tồn tại
- Kiểm tra user có quyền access course đó không

### Debug Tips:

1. **Enable Debugging:**
   - Site administration > Development > Debugging
   - Set Debug messages: DEVELOPER
   - Set Display debug messages: Yes

2. **Check Logs:**
   - Site administration > Reports > Logs
   - Filter by Web services để xem API calls

3. **Test Permissions:**
   - Login với user có token
   - Thử access course manually trước khi gọi API

## Bước 6: Tích hợp vào hệ thống

### API Response Format:

**Success Response:**
```json
{
  "status": "success",
  "id": 15,
  "message": "Copy môn học thành công! Đã sao chép toàn bộ nội dung từ môn học gốc."
}
```

**Error Response:**
```json
{
  "status": "error", 
  "id": 0,
  "message": "Không tìm thấy môn học với shortname: ABC123"
}
```

### Timestamp Conversion:

Sử dụng Unix timestamps:
- JavaScript: `Math.floor(Date.now() / 1000)`
- PHP: `time()` 
- Python: `int(time.time())`
- Online converter: https://www.unixtimestamp.com/

### Rate Limiting:

- Plugin không có built-in rate limiting
- Nên implement ở application layer nếu cần
- Moodle có thể có rate limiting tổng thể cho web services

## Liên hệ hỗ trợ

Nếu gặp vấn đề, kiểm tra:
1. Debug tool: `/local/coursecopier/debug_api.php`
2. Moodle logs: Site administration > Reports > Logs
3. Server error logs
4. Network connectivity và firewall settings