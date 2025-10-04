# Course Copier Plugin - Moodle Webservice# Course Copier Plugin for Moodle



Plugin Moodle local để clone/copy khóa học với JSON API endpoint và token authentication.Plugin này cung cấp RESTful API để copy môn học trong Moodle với các thông tin tùy chỉnh.



## 🎯 Tính năng chính## Tính năng



- **Clone khóa học**: Copy một khóa học hiện có với thông tin mới- Copy môn học từ một course hiện có

- **JSON API**: Endpoint nhận JSON body và trả về JSON response  - Tùy chỉnh tên đầy đủ, tên viết tắt, ngày bắt đầu và ngày kết thúc cho course mới

- **Token Authentication**: Hỗ trợ Bearer token và token trong JSON body- RESTful API có thể test bằng Postman

- **CORS Support**: Cho phép cross-origin requests- Sao chép toàn bộ nội dung course (activities, resources, settings)

- **Validation**: Kiểm tra đầu vào và quyền truy cập đầy đủ

## Cài đặt

## 📋 Webservice Clone Khóa học

1. Copy thư mục `coursecopier` vào `moodle/local/`

### Đầu vào (Input)2. Truy cập Administration > Site administration > Notifications để cài đặt plugin

- `shortname_clone`: Shortname của khóa học nguồn cần clone3. Cấu hình Web Services:

- `fullname`: Tên đầy đủ của khóa học mới   - Administration > Site administration > Plugins > Web services > Overview

- `shortname`: Shortname của khóa học mới (phải unique)   - Enable web services

- `startdate`: Ngày bắt đầu (Unix timestamp)   - Enable protocols (REST protocol)

- `enddate`: Ngày kết thúc (Unix timestamp)   - Create a service và add functions:

     - `local_coursecopier_copy_course`

### Đầu ra (Output)     - `local_coursecopier_get_available_courses`

- `status`: "success" hoặc "error"

- `id`: ID của khóa học mới (0 nếu lỗi)## API Endpoints

- `message`: Thông báo thành công hoặc mô tả lỗi

### 1. Copy Course (JSON REST API - Recommended)

## 🚀 API Endpoints

**Endpoint:** `/local/coursecopier/api.php`

### Base URL

```**Method:** POST

POST /local/coursecopier/api.php

```**Headers:**

- `Content-Type`: application/json

### Authentication- `Authorization`: Bearer {token} (optional, token can also be in body)

Hỗ trợ 2 cách xác thực:

**JSON Body:**

**1. Authorization Header (Recommend):**```json

```http{

Authorization: Bearer YOUR_TOKEN  "wsfunction": "local_coursecopier_copy_course",

```  "wstoken": "your_token_here", 

  "shortname_clone": "ORIGINAL123",

**2. Token trong JSON body:**  "fullname": "New Course Name",

```json  "shortname": "NEWCOURSE2025",

{  "startdate": 1704067200,

  "wstoken": "YOUR_TOKEN",  "enddate": 1719792000

  ...}

}```

```

### 1.1. Copy Course (Traditional Moodle Web Service)

### Clone Course Request

```http**Endpoint:** `/webservice/rest/server.php`

POST /local/coursecopier/api.php

Content-Type: application/json**Method:** POST

Authorization: Bearer YOUR_TOKEN

**Parameters:**

{- `wstoken`: Web service token

  "wsfunction": "local_coursecopier_clone_course",- `wsfunction`: `local_coursecopier_copy_course`

  "shortname_clone": "COURSE123",- `moodlewsrestformat`: json

  "fullname": "Khóa học Clone 2025",- `shortname_clone`: Shortname của môn học nguồn cần copy

  "shortname": "CLONE2025", - `fullname`: Tên đầy đủ cho môn học mới

  "startdate": 1704067200,- `shortname`: Tên viết tắt cho môn học mới

  "enddate": 1719792000- `startdate`: Ngày bắt đầu (timestamp Unix)

}- `enddate`: Ngày kết thúc (timestamp Unix)

```

**Response:**

### Success Response```json

```json{

{  "status": "success",

  "status": "success",  "id": 123,

  "id": 25,  "message": "Copy môn học thành công! Đã sao chép toàn bộ nội dung từ môn học gốc."

  "message": "Copy môn học thành công! Đã sao chép toàn bộ nội dung từ môn học gốc."}

}```

```

**Error Response:**

### Error Response```json

```json{

{  "status": "error",

  "status": "error",  "id": 0,

  "id": 0,  "message": "Không tìm thấy môn học với shortname: ABC123"

  "message": "Không tìm thấy môn học với shortname: COURSE123"}

}```

```

### 2. Get Available Courses (JSON REST API - Recommended)

## 🔧 Cài đặt Plugin

**Endpoint:** `/local/coursecopier/api.php`

### 1. Upload Plugin

```bash**Method:** POST

# Copy plugin vào thư mục local/

cp -r coursecopier /path/to/moodle/local/**Headers:**

```- `Content-Type`: application/json

- `Authorization`: Bearer {token}

### 2. Cài đặt từ Moodle Admin

1. Đăng nhập với tài khoản Admin**JSON Body:**

2. Vào **Site Administration → Notifications**```json

3. Click **Upgrade Moodle database now**{

  "wsfunction": "local_coursecopier_get_available_courses",

### 3. Cấu hình Web Services  "wstoken": "your_token_here",

1. **Enable Web Services:**  "categoryid": 0

   - Vào **Site Administration → Advanced features**}

   - Check **Enable web services**```



2. **Enable Protocols:**### 2.1. Get Available Courses (Traditional Moodle Web Service)

   - Vào **Site Administration → Server → Web services → Manage protocols**

   - Enable **REST protocol****Endpoint:** `/webservice/rest/server.php`



3. **Create Token:****Method:** POST

   - Vào **Site Administration → Server → Web services → Manage tokens**

   - Click **Create token****Parameters:**

   - Chọn user và service (hoặc All services)- `wstoken`: Web service token

   - Copy token để sử dụng- `wsfunction`: `local_coursecopier_get_available_courses`

- `moodlewsrestformat`: json

## 📋 Test với Postman- `categoryid`: ID danh mục (0 = tất cả)



### 1. Import Collection**Response:**

Import file `Course_Copier_API.postman_collection.json` vào Postman```json

{

### 2. Setup Environment Variables  "courses": [

- `moodle_url`: URL của Moodle site (vd: https://yourmoodle.com)    {

- `ws_token`: Web service token từ Moodle admin      "id": 2,

      "fullname": "Course Example",

### 3. Test Requests      "shortname": "EXAMPLE123",

- **Clone Course**: Test chính để clone khóa học      "category": 1,

- **Get Available Courses**: Lấy danh sách khóa học có thể clone      "startdate": 1609459200,

- **Test Invalid Dates**: Test validation với ngày không hợp lệ      "enddate": 1617235200,

      "visible": true

## 🔐 Capabilities Required    }

  ],

User cần có các quyền sau:  "total": 1,

- `moodle/course:create`: Tạo khóa học mới  "status": "success",

- `moodle/course:view`: Xem danh sách khóa học  "message": "Lấy danh sách môn học thành công"

- `moodle/backup:backupcourse`: Backup khóa học nguồn}

- `moodle/restore:restorecourse`: Restore vào khóa học mới```



## 📊 HTTP Status Codes## Postman Collection



- `200`: Success### Setup Postman Environment

- `400`: Bad Request (JSON invalid, thiếu parameters)Tạo environment với variables:

- `401`: Unauthorized (token invalid/expired)- `moodle_url`: URL của Moodle site (ví dụ: https://yourmoodle.com)

- `405`: Method Not Allowed (chỉ cho phép POST)- `ws_token`: Web service token

- `500`: Internal Server Error

### Test Cases

## 🧪 Testing Examples

#### Test 1: Get Available Courses (JSON API)

### cURL Example```json

```bashPOST {{moodle_url}}/local/coursecopier/api.php

curl -X POST "https://yourmoodle.com/local/coursecopier/api.php" \Content-Type: application/json

  -H "Content-Type: application/json" \Authorization: Bearer {{ws_token}}

  -H "Authorization: Bearer YOUR_TOKEN" \

  -d '{{

    "wsfunction": "local_coursecopier_clone_course",  "wsfunction": "local_coursecopier_get_available_courses",

    "shortname_clone": "ORIGINAL123",  "categoryid": 0

    "fullname": "New Course 2025",}

    "shortname": "NEW2025",```

    "startdate": 1704067200,

    "enddate": 1719792000#### Test 2: Copy Course (JSON API)

  }'```json

```POST {{moodle_url}}/local/coursecopier/api.php

Content-Type: application/json

### JavaScript/Fetch ExampleAuthorization: Bearer {{ws_token}}

```javascript

const response = await fetch('/local/coursecopier/api.php', {{

  method: 'POST',  "wsfunction": "local_coursecopier_copy_course",

  headers: {  "shortname_clone": "ORIGINAL123",

    'Content-Type': 'application/json',  "fullname": "New Course Name",

    'Authorization': 'Bearer ' + token  "shortname": "NEWCOURSE123",

  },  "startdate": 1609459200,

  body: JSON.stringify({  "enddate": 1617235200

    wsfunction: 'local_coursecopier_clone_course',}

    shortname_clone: 'ORIGINAL123',```

    fullname: 'New Course 2025',

    shortname: 'NEW2025',#### Test 3: Traditional Web Service (Fallback)

    startdate: 1704067200,```

    enddate: 1719792000POST {{moodle_url}}/webservice/rest/server.php

  })Content-Type: application/x-www-form-urlencoded

});

wstoken={{ws_token}}

const result = await response.json();&wsfunction=local_coursecopier_copy_course

console.log(result);&moodlewsrestformat=json

```&shortname_clone=ORIGINAL123

&fullname=New Course Name

## 🐛 Common Issues&shortname=NEWCOURSE123

&startdate=1609459200

1. **"Token is required"**: Đảm bảo token được gửi trong header hoặc JSON body&enddate=1617235200

2. **"Invalid token"**: Kiểm tra token có tồn tại và chưa hết hạn```

3. **"JSON body is required"**: Đảm bảo Content-Type là application/json

4. **"Permission denied"**: User cần có capability tương ứng## Permissions



## 📁 File StructurePlugin yêu cầu các permissions sau:

- `moodle/course:create` - Tạo course mới

```- `moodle/backup:backupcourse` - Backup course

local/coursecopier/- `moodle/restore:restorecourse` - Restore course

├── api.php                          # JSON API endpoint chính- `moodle/course:view` - Xem danh sách course

├── externallib.php                  # External web service functions

├── version.php                      # Plugin version info## Lưu ý

├── Course_Copier_API.postman_collection.json # Postman test collection

├── README.md                        # Documentation này1. **Timestamps**: Sử dụng Unix timestamps cho startdate và enddate

├── db/2. **Shortname**: Phải unique trong hệ thống

│   ├── access.php                   # Capabilities definition3. **Permissions**: User gọi API phải có đủ quyền trên course nguồn và system

│   └── services.php                 # Web service functions & services4. **Backup/Restore**: Plugin sử dụng Moodle backup/restore API để copy toàn bộ nội dung

└── lang/

    └── en/## Troubleshooting

        └── local_coursecopier.php   # English language strings

```### Lỗi thường gặp:



## 🏗️ Technical Details1. **"Web service not enabled"**: Kích hoạt web services trong Administration

2. **"Invalid token"**: Kiểm tra token và user permissions

- **Moodle Version**: 3.10+3. **"Capability required"**: User cần có đủ permissions

- **PHP Version**: 7.4+4. **"Course not found"**: Kiểm tra shortname_clone có tồn tại không

- **Plugin Type**: Local plugin

- **Database**: Sử dụng Moodle backup/restore API### Debug mode:

- **Security**: Token authentication, capability checks, input validationEnable debugging trong Moodle để xem chi tiết lỗi:

`Administration > Site administration > Development > Debugging`

## 📞 Support

## Tác giả

Nếu có vấn đề với plugin:

1. Kiểm tra Moodle logs tại **Site Administration → Reports → Logs**Plugin được phát triển cho Moodle 3.10+

2. Kiểm tra Web service logs tại **Site Administration → Development → Web service test client**

3. Ensure backup/restore capabilities are properly configured## License



---GNU GPL v3 or later

**Plugin Version**: v1.0  
**Compatible**: Moodle 3.10+  
**License**: GPL v3 or later