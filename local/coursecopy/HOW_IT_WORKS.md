# Cách Plugin Hoạt Động

## Kiến trúc Plugin

Plugin `local_coursecopy` được thiết kế để hoạt động với **webservice_restful** protocol của Moodle.

### 1. Plugin webservice_restful

Đây là plugin protocol chuẩn RESTful của Moodle, được cài đặt tại `local/webservice/restful/`.

**Endpoint chính**: `/webservice/restful/server.php`

**Cách hoạt động**:
- Nhận function name từ URL path (PATH_INFO)
- Lấy token từ `Authorization` header
- Lấy request format từ `Content-Type` header  
- Lấy response format từ `Accept` header
- Parse JSON body để lấy parameters
- Gọi external function tương ứng
- Trả về response theo format yêu cầu (JSON/XML)

### 2. Plugin local_coursecopy

Plugin này định nghĩa external function để copy môn học.

**Các file chính**:

#### `externallib.php`
- Class `local_coursecopy_external` extends `external_api`
- Function `copy_course()`: Logic copy môn học
- Function `copy_course_parameters()`: Định nghĩa input parameters
- Function `copy_course_returns()`: Định nghĩa output structure

#### `db/services.php`
- Đăng ký function `local_coursecopy_copy_course` với web service system
- Đăng ký service `coursecopy_service`

#### `db/access.php`
- Định nghĩa capabilities cho plugin

#### `lang/en/local_coursecopy.php`
- Language strings

## Flow hoạt động

```
1. Client gửi request:
   POST /webservice/restful/server.php/local_coursecopy_copy_course
   Headers:
     Authorization: TOKEN
     Content-Type: application/json
     Accept: application/json
   Body:
     {
       "shortname_clone": "COURSE2024",
       "fullname": "Course Copy 2025",
       "shortname": "COURSE2025",
       "startdate": 1704067200,
       "enddate": 1735689600
     }

2. webservice_restful/server.php nhận request:
   - Parse URL → Function name = "local_coursecopy_copy_course"
   - Get token từ Authorization header
   - Authenticate user với token
   - Parse JSON body → Parameters

3. webservice_restful gọi external function:
   - Load function info từ db/services.php
   - Validate capabilities
   - Call local_coursecopy_external::copy_course()

4. local_coursecopy_external::copy_course() xử lý:
   - Validate parameters (dates, required fields)
   - Check source course exists
   - Check new shortname not exists
   - Create new course
   - Copy course settings and format options
   - Return result

5. webservice_restful format response:
   - Convert return value to JSON
   - Send HTTP response với appropriate headers

6. Client nhận response:
   {
     "status": "success",
     "id": 123,
     "message": "Copy môn học thành công! ID môn học mới: 123"
   }
```

## So sánh với cách tự implement

### ❌ Cách SAI (tự implement endpoint):
```
/local/coursecopy/restful_api.php
- Phải tự handle authentication
- Phải tự parse request
- Phải tự format response
- Không follow Moodle standard
- Khó maintain
```

### ✅ Cách ĐÚNG (sử dụng webservice_restful):
```
/webservice/restful/server.php/local_coursecopy_copy_course
- Authentication tự động
- Request parsing tự động
- Response formatting tự động
- Follow Moodle standard
- Dễ maintain và extend
```

## Cấu trúc Request/Response chuẩn

### Request Format

```http
POST /webservice/restful/server.php/{function_name} HTTP/1.1
Host: your-moodle-site.com
Authorization: {token}
Content-Type: application/json
Accept: application/json

{parameter_json}
```

### Response Format (Success)

```json
{
  // Theo định nghĩa của function_returns()
  "status": "success",
  "id": 123,
  "message": "Success message"
}
```

### Response Format (Error)

```json
{
  "exception": "moodle_exception",
  "errorcode": "error_code",
  "message": "Error description"
}
```

## Token Management

Token được quản lý bởi Moodle core:

1. **Tạo token**: Site admin → Web services → Manage tokens
2. **Token được lưu trong table**: `mdl_external_tokens`
3. **Validation tự động**: webservice_restful kiểm tra:
   - Token exists
   - Token not expired (validuntil)
   - User active
   - Service enabled
   - Function allowed for service

## Capabilities

Plugin yêu cầu capabilities:
- `moodle/course:create` - Tạo môn học mới
- `moodle/course:view` - Xem thông tin môn học

Được kiểm tra tự động trong `copy_course()`:
```php
$context = context_system::instance();
self::validate_context($context);
require_capability('moodle/course:create', $context);
```

## Extend Plugin

### Thêm function mới

1. **Thêm function vào externallib.php**:
```php
public static function new_function_parameters() {
    return new external_function_parameters([...]);
}

public static function new_function($param1, $param2) {
    // Implementation
}

public static function new_function_returns() {
    return new external_single_structure([...]);
}
```

2. **Đăng ký trong db/services.php**:
```php
$functions = [
    'local_coursecopy_new_function' => [
        'classname'   => 'local_coursecopy_external',
        'methodname'  => 'new_function',
        'classpath'   => 'local/coursecopy/externallib.php',
        'description' => 'Description',
        'type'        => 'write',
        'capabilities' => 'required,capabilities',
    ],
];
```

3. **Upgrade database**: Notifications page

4. **Call API**:
```
POST /webservice/restful/server.php/local_coursecopy_new_function
```

## Debugging

### Enable debug mode
```
Site administration → Development → Debugging
- Debug messages: DEVELOPER
- Display debug messages: Yes
```

### Check logs
```
Site administration → Reports → Logs
Filter by: Web services
```

### Test token
```sql
SELECT * FROM mdl_external_tokens WHERE token = 'YOUR_TOKEN';
```

### Test function registration
```sql
SELECT * FROM mdl_external_functions WHERE name = 'local_coursecopy_copy_course';
```

## Best Practices

1. ✅ **Luôn validate parameters** trong function
2. ✅ **Sử dụng try-catch** để handle exceptions
3. ✅ **Return consistent structure** theo định nghĩa
4. ✅ **Check capabilities** trước khi thực hiện action
5. ✅ **Log important actions** để audit
6. ✅ **Use transactions** cho database operations
7. ✅ **Validate dates và required fields** đầy đủ
8. ✅ **Return meaningful error messages** bằng tiếng Việt

## Tài liệu tham khảo

- Moodle Web Services: https://docs.moodle.org/dev/Web_services
- External API: https://docs.moodle.org/dev/External_functions_API
- webservice_restful plugin: `local/webservice/restful/README.md`

## Kết luận

Plugin `local_coursecopy` là một implementation đúng chuẩn của Moodle external web service, sử dụng `webservice_restful` protocol để cung cấp RESTful API copy môn học. Plugin này:

- ✅ Follow Moodle coding standards
- ✅ Use Moodle authentication system  
- ✅ Leverage webservice_restful protocol
- ✅ Easy to maintain and extend
- ✅ Secure with capability checking
- ✅ Well documented

Đây là cách **ĐÚNG** để tạo RESTful API trong Moodle! 🎉
