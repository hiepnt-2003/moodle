# Hướng dẫn cài đặt và sử dụng Plugin Course Copy

## Giới thiệu

Plugin Course Copy sử dụng **webservice_restful** protocol để cung cấp RESTful API copy môn học trong Moodle 3.9.

**Endpoint**: `/webservice/restful/server.php/local_coursecopy_copy_course`

## Bước 1: Kiểm tra Plugin Restful đã được cài đặt

Plugin restful phải được cài đặt tại: `local/webservice/restful/`

Kiểm tra bằng cách truy cập:
```
Site administration → Plugins → Web services → Manage protocols
```

Đảm bảo **RESTful protocol** đã được kích hoạt (có biểu tượng mắt mở).

## Bước 2: Cài đặt Plugin Course Copy

Plugin đã được tạo tại: `local/coursecopy/`

### 2.1. Cập nhật database

1. Đăng nhập Moodle với tài khoản admin
2. Truy cập: **Site administration → Notifications**
3. Click **Upgrade Moodle database now**
4. Đợi quá trình cài đặt hoàn tất

## Bước 3: Kích hoạt Web Services

### 3.1. Enable Web Services

1. Truy cập: **Site administration → Advanced features**
2. Tích chọn **Enable web services**
3. Click **Save changes**

### 3.2. Enable RESTful Protocol (nếu chưa kích hoạt)

1. Truy cập: **Site administration → Plugins → Web services → Manage protocols**
2. Click biểu tượng mắt bên cạnh **RESTful protocol** để kích hoạt

## Bước 4: Tạo User cho Web Service

### 4.1. Tạo user mới (khuyến nghị) hoặc sử dụng user hiện có

1. Truy cập: **Site administration → Users → Accounts → Add a new user**
2. Điền thông tin:
   - Username: `wsuser` (hoặc tên bạn muốn)
   - Password: Tạo mật khẩu mạnh
   - First name: Web Service
   - Surname: User
   - Email: Địa chỉ email hợp lệ
3. Click **Create user**

### 4.2. Cấp quyền cho user

1. Truy cập: **Site administration → Users → Permissions → Define roles**
2. Click **Add a new role**
3. Chọn **Archetype: Manager**
4. Hoặc cấp các quyền sau cho role hiện có:
   - `moodle/course:create`
   - `moodle/course:view`
   - `moodle/webservice:createtoken`

5. Assign role cho user:
   - Truy cập: **Site administration → Users → Permissions → Assign system roles**
   - Chọn role vừa tạo hoặc **Manager**
   - Add user `wsuser` vào role

## Bước 5: Tạo Token

### 5.1. Tạo External Service (nếu chưa có)

1. Truy cập: **Site administration → Plugins → Web services → External services**
2. Tìm service: **Course Copy Service** (shortname: `coursecopy_service`)
3. Nếu chưa có, plugin sẽ tự động tạo khi cài đặt

### 5.2. Tạo Token

1. Truy cập: **Site administration → Plugins → Web services → Manage tokens**
2. Click **Add**
3. Điền thông tin:
   - **User**: Chọn user `wsuser` vừa tạo
   - **Service**: Chọn **Course Copy Service** (hoặc chọn **All services** để test nhiều functions)
   - **Valid until**: Để trống (không giới hạn) hoặc chọn ngày hết hạn
4. Click **Save changes**
5. **LƯU LẠI TOKEN** - Token sẽ được hiển thị trong danh sách, có dạng chuỗi dài như:
   ```
   a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
   ```

## Bước 6: Test API bằng Postman

### 6.1. Import Postman Collection

1. Mở Postman
2. Click **Import** → **File** → Chọn file `Course_Copy_API.postman_collection.json`
3. Collection sẽ được import với tên **Course Copy API - Moodle 3.9**

### 6.2. Cấu hình Environment Variables

1. Click vào tab **Variables** trong Collection
2. Cập nhật giá trị:
   - `moodle_url`: URL Moodle của bạn (ví dụ: `http://localhost/moodle` hoặc `https://yourmoodle.com`)
   - `moodle_token`: Token vừa tạo ở bước 5.2

### 6.3. Tạo môn học test

Trước khi test API, cần có môn học nguồn để copy:

1. Truy cập Moodle
2. Tạo một môn học mới:
   - Shortname: `COURSE2024`
   - Fullname: `Test Course 2024`
   - Thêm một vài nội dung vào môn học

### 6.4. Test API Request

1. Trong Postman, chọn request **Copy Course**
2. Kiểm tra URL đúng: `{moodle_url}/webservice/restful/server.php/local_coursecopy_copy_course`
3. Kiểm tra headers:
   - `Authorization`: `{token}` (không cần Bearer prefix)
   - `Content-Type`: `application/json`
   - `Accept`: `application/json`
4. Kiểm tra request body:
   ```json
   {
       "shortname_clone": "COURSE2024",
       "fullname": "Course Copy 2025",
       "shortname": "COURSE2025",
       "startdate": 1704067200,
       "enddate": 1735689600
   }
   ```
5. Click **Send**

### 6.5. Kiểm tra kết quả

**Success Response:**
```json
{
    "status": "success",
    "id": 123,
    "message": "Copy môn học thành công! ID môn học mới: 123"
}
```

**Error Response:**
```json
{
    "status": "error",
    "id": 0,
    "message": "Mô tả lỗi"
}
```

## Bước 7: Tính toán Timestamp cho ngày tháng

### 7.1. Sử dụng Online Tool

- Website: https://www.unixtimestamp.com/
- Chọn ngày và giờ, website sẽ tự động tạo timestamp

### 7.2. Sử dụng PHP

```php
<?php
echo strtotime('2024-01-01'); // 1704067200
echo strtotime('2025-01-01'); // 1735689600
?>
```

### 7.3. Sử dụng JavaScript

```javascript
console.log(Math.floor(new Date('2024-01-01').getTime() / 1000)); // 1704067200
console.log(Math.floor(new Date('2025-01-01').getTime() / 1000)); // 1735689600
```

### 7.4. Sử dụng Python

```python
from datetime import datetime
print(int(datetime(2024, 1, 1).timestamp()))  # 1704067200
print(int(datetime(2025, 1, 1).timestamp()))  # 1735689600
```

## Bước 8: Test các trường hợp khác

### Test 1: Tên tiếng Việt

Chọn request **Copy Course with Vietnamese Characters** và gửi

### Test 2: Validation lỗi

Chọn request **Test Date Validation Error** để test lỗi enddate < startdate

## Troubleshooting - Xử lý lỗi

### Lỗi: "Invalid token"

**Nguyên nhân:** Token không đúng hoặc không tồn tại

**Giải pháp:**
1. Kiểm tra lại token trong Moodle admin
2. Copy lại token chính xác (không có khoảng trắng thừa)
3. Paste token vào Postman environment variable

### Lỗi: "Authorization token required"

**Nguyên nhân:** Token không được gửi trong Authorization header

**Giải pháp:**
1. Kiểm tra Authorization header có token
2. Đảm bảo header là `Authorization: TOKEN` (không cần Bearer prefix)

### Lỗi: "Không tìm thấy môn học với shortname: ..."

**Nguyên nhân:** Môn học nguồn không tồn tại

**Giải pháp:**
1. Kiểm tra shortname môn học nguồn
2. Truy cập: Site administration → Courses → Manage courses and categories
3. Tìm môn học và kiểm tra shortname chính xác

### Lỗi: "Shortname đã tồn tại: ..."

**Nguyên nhân:** Môn học với shortname mới đã tồn tại

**Giải pháp:**
1. Chọn shortname khác cho môn học mới
2. Hoặc xóa/đổi tên môn học cũ

### Lỗi: "Ngày kết thúc phải sau ngày bắt đầu"

**Nguyên nhân:** enddate <= startdate

**Giải pháp:**
1. Đảm bảo enddate > startdate
2. Kiểm tra lại timestamp

### Debug Mode

Để xem chi tiết lỗi:

1. Truy cập: **Site administration → Development → Debugging**
2. Set **Debug messages** to **DEVELOPER: Extra Moodle debug messages for developers**
3. Tích chọn **Display debug messages**
4. Gửi lại API request và xem response chi tiết

## Bước 9: Sử dụng trong ứng dụng thực tế

### Ví dụ PHP

```php
<?php
$url = "http://localhost/moodle/webservice/restful/server.php/local_coursecopy_copy_course";
$token = "a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6";

$data = [
    'shortname_clone' => 'COURSE2024',
    'fullname' => 'Course Copy 2025',
    'shortname' => 'COURSE2025',
    'startdate' => strtotime('2024-01-01'),
    'enddate' => strtotime('2025-01-01')
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n" .
                   "Accept: application/json\r\n" .
                   "Authorization: $token\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$response = json_decode($result, true);

if ($response['status'] === 'success') {
    echo "Success! New course ID: " . $response['id'] . "\n";
} else {
    echo "Error: " . $response['message'] . "\n";
}
?>
```

### Ví dụ cURL

```bash
curl -X POST http://localhost/moodle/webservice/restful/server.php/local_coursecopy_copy_course \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6" \
  -d '{
    "shortname_clone": "COURSE2024",
    "fullname": "Course Copy 2025",
    "shortname": "COURSE2025",
    "startdate": 1704067200,
    "enddate": 1735689600
  }'
```

### Lưu ý quan trọng

1. **Function name trong URL**: Tên function được chỉ định trong PATH của URL, không phải trong body
2. **Authorization header**: Token được đặt trực tiếp trong Authorization header, không cần prefix "Bearer"
3. **Headers bắt buộc**: Phải có đầy đủ 3 headers: Authorization, Content-Type, Accept
4. **Response format**: Được xác định bởi Accept header (application/json)

## Lưu ý bảo mật

1. **Bảo mật token**: Không chia sẻ token, không commit vào git
2. **Sử dụng HTTPS**: Trong môi trường production, luôn dùng HTTPS
3. **Giới hạn quyền**: Chỉ cấp quyền cần thiết cho user web service
4. **Token expiry**: Đặt thời gian hết hạn cho token trong môi trường production
5. **Rate limiting**: Cân nhắc thêm rate limiting để tránh abuse

## Kiểm tra Plugin đã cài đặt thành công

1. Truy cập: **Site administration → Plugins → Local plugins**
2. Tìm **Course Copy** trong danh sách
3. Kiểm tra version: 1.0.0

Hoặc

1. Truy cập: **Site administration → Plugins → Web services → External services**
2. Tìm **Course Copy Service** (shortname: coursecopy_service)
3. Click **Functions** để xem list functions

Chúc bạn thành công! 🎉
