# Course Clone Web Service Plugin

**Moodle Local Plugin - Web Service API để clone khóa học**

Plugin `local_webservice` cung cấp web service REST API để clone khóa học trong Moodle. Plugin này cho phép external applications có thể tạo bản sao của khóa học hiện có với thông tin mới thông qua HTTP requests.

## Features

- REST API endpoint để clone khóa học
- Input/output được định dạng rõ ràng 
- Kiểm tra quyền hạn và validation đầy đủ
- Clone toàn bộ nội dung khóa học (activities, resources, settings)
- Tự động cập nhật ngày tháng cho khóa học mới
- Error handling và logging chi tiết

## API Specification

### Input Parameters

Web service function: `local_webservice_clone_course`

| Parameter         | Type   | Required | Description                              |
|-------------------|--------|----------|------------------------------------------|
| `shortname_clone` | string | Yes      | Shortname của khóa học gốc cần clone     |
| `fullname`        | string | Yes      | Tên đầy đủ cho khóa học mới              |
| `shortname`       | string | Yes      | Shortname cho khóa học mới (phải unique) |
| `startdate`       | int    | Yes      | Ngày bắt đầu (Unix timestamp)            |
| `enddate`         | int    | Yes      | Ngày kết thúc (Unix timestamp)           |

### Output Response

| Field     | Type    | Description                         |
|-----------|---------|-------------------------------------|
| `status`  | string  | "success" hoặc "error"              |
| `id`      | int     | ID của khóa học mới (0 nếu error)   |
| `message` | string  | Thông báo thành công hoặc mô tả lỗi |

#### Success Response Example:
```json
{
    "status": "success",
    "id": 42,
    "message": "Course cloned successfully"
}
```

#### Error Response Example:
```json
{
    "status": "error",
    "id": 0,
    "message": "Source course not found with shortname: MATH101"
}
```

## Installation

### 1. Install Plugin
```bash
# Copy plugin to Moodle local plugins directory
cp -r local_webservice /path/to/moodle/local/webservice

# Or clone from repository
git clone <repository-url> /path/to/moodle/local/webservice
```

### 2. Complete Installation
1. Navigate to **Site Administration** → **Notifications**
2. Click **Upgrade Moodle database now**
3. Plugin sẽ được cài đặt tự động

## Configuration

### 1. Enable Web Services
1. **Site Administration** → **Advanced features**
2. Enable **"Enable web services"** ✓
3. Enable **"Enable REST protocol"** ✓

### 2. Create Web Service User (Optional)
1. **Site Administration** → **Users** → **Accounts** → **Add a new user**
2. Create user với username: `webservice_user`
3. Assign role có quyền: 
   - `moodle/course:create`
   - `moodle/backup:backupcourse` 
   - `moodle/restore:restorecourse`

### 3. Configure External Service
1. **Site Administration** → **Server** → **Web services** → **External services**
2. Tìm **"Course Clone Service"**
3. Click **Enable** ✓
4. Click **Authorised users** → Add users cần thiết

### 4. Generate Token
1. **Site Administration** → **Server** → **Web services** → **Manage tokens**
2. Click **Create token**
3. Select user và service **"Course Clone Service"**
4. Copy token được tạo

## How It Works

### Process Flow

```
API Call → Validation → Backup → Create Course → Restore → Update Dates → Response
```

1. **Input Validation**: Kiểm tra parameters và quyền hạn
2. **Source Course Lookup**: Tìm khóa học gốc theo shortname_clone
3. **Uniqueness Check**: Kiểm tra shortname mới chưa tồn tại
4. **Course Backup**: Tạo backup của khóa học gốc
5. **New Course Creation**: Tạo khóa học mới (empty shell)
6. **Content Restoration**: Restore backup vào khóa học mới
7. **Date Updates**: Cập nhật startdate và enddate
8. **Cleanup**: Xóa temporary backup files
9. **Return Response**: Trả về kết quả

## Testing with Postman

### Prerequisites
1. Plugin đã được cài đặt và cấu hình
2. Web services đã được enable
3. Token đã được tạo
4. Có ít nhất 1 khóa học để test clone

### Step 1: Setup Postman Environment
1. Mở Postman
2. Tạo **Environment** mới với variables:
   - `moodle_url`: http://your-moodle-site.com
   - `ws_token`: your-actual-token-here

### Step 2: Create Test Request

#### Request Configuration:
- **Method**: `POST`
- **URL**: `{{moodle_url}}/webservice/rest/server.php`
- **Headers**: 
  - `Content-Type`: `application/x-www-form-urlencoded`

#### Body (form-data):
```
wstoken: {{ws_token}}
wsfunction: local_webservice_clone_course
moodlewsrestformat: json
shortname_clone: TEST_COURSE
fullname: My Cloned Course
shortname: TEST_CLONE_001
startdate: 1735689600
enddate: 1743465600
```

### Step 3: Test Scenarios

#### Test 1: Successful Clone
```
POST {{moodle_url}}/webservice/rest/server.php
Content-Type: application/x-www-form-urlencoded

wstoken={{ws_token}}
wsfunction=local_webservice_clone_course
moodlewsrestformat=json
shortname_clone=EXISTING_COURSE
fullname=Successfully Cloned Course
shortname=CLONE_SUCCESS_001
startdate=1735689600
enddate=1743465600
```

**Expected Response:**
```json
{
    "status": "success",
    "id": 15,
    "message": "Course cloned successfully"
}
```

#### Test 2: Source Course Not Found
```
shortname_clone=NON_EXISTENT_COURSE
fullname=This Should Fail
shortname=FAIL_TEST_001
startdate=1735689600
enddate=1743465600
```

**Expected Response:**
```json
{
    "status": "error",
    "id": 0,
    "message": "Source course not found with shortname: NON_EXISTENT_COURSE"
}
```

#### Test 3: Duplicate Shortname
Chạy Test 1 hai lần với cùng shortname.

**Expected Response:**
```json
{
    "status": "error",
    "id": 0,
    "message": "Course shortname already exists: CLONE_SUCCESS_001"
}
```

#### Test 4: Invalid Date Range
```
shortname_clone=EXISTING_COURSE
fullname=Invalid Date Test
shortname=DATE_TEST_001
startdate=1743465600
enddate=1735689600
```

**Expected Response:**
```json
{
    "status": "error",
    "id": 0,
    "message": "End date must be after start date"
}
```

### Step 4: Advanced Postman Setup

#### Create Postman Collection
1. Tạo Collection "Course Clone API Tests"
2. Add các test requests trên
3. Setup Pre-request Scripts để generate dynamic data:

```javascript
// Pre-request Script cho dynamic shortname
pm.environment.set("unique_shortname", "CLONE_" + Date.now());
pm.environment.set("start_timestamp", Math.floor(Date.now() / 1000) + 86400);
pm.environment.set("end_timestamp", Math.floor(Date.now() / 1000) + 2592000);
```

#### Response Tests
```javascript
// Test script để validate response
PM.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

pm.test("Response contains required fields", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('status');
    pm.expect(jsonData).to.have.property('id');
    pm.expect(jsonData).to.have.property('message');
});

pm.test("Successful clone returns valid course ID", function () {
    const jsonData = pm.response.json();
    if (jsonData.status === "success") {
        pm.expect(jsonData.id).to.be.above(0);
    }
});
```

### cURL Examples

Nếu không có Postman, có thể test bằng cURL:

#### Success Test:
```bash
curl -X POST "http://your-moodle.com/webservice/rest/server.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=local_webservice_clone_course" \
  -d "moodlewsrestformat=json" \
  -d "shortname_clone=TEST_COURSE" \
  -d "fullname=My Cloned Course" \
  -d "shortname=CURL_TEST_001" \
  -d "startdate=1735689600" \
  -d "enddate=1743465600"
```

#### Error Test:
```bash
curl -X POST "http://your-moodle.com/webservice/rest/server.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=local_webservice_clone_course" \
  -d "moodlewsrestformat=json" \
  -d "shortname_clone=NON_EXISTENT" \
  -d "fullname=This Will Fail" \
  -d "shortname=ERROR_TEST" \
  -d "startdate=1735689600" \
  -d "enddate=1743465600"
```

## Troubleshooting

### Common Issues

#### 1. "Invalid token" Error
- **Cause**: Token không đúng hoặc đã expire
- **Solution**: 
  - Kiểm tra token trong Manage tokens
  - Tạo token mới nếu cần
  - Verify user có access đến service

#### 2. "Function not found" Error  
- **Cause**: Plugin chưa được cài đặt hoặc service chưa enable
- **Solution**:
  - Kiểm tra plugin trong Site Administration → Plugins → Plugins overview
  - Enable "Course Clone Service" trong External services

#### 3. "Capability not allowed" Error
- **Cause**: User không có quyền cần thiết
- **Solution**: Assign roles với capabilities:
  - `moodle/course:create`
  - `moodle/backup:backupcourse` 
  - `moodle/restore:restorecourse`

#### 4. "Source course not found" Error
- **Cause**: Shortname không tồn tại
- **Solution**: 
  - Kiểm tra shortname trong database hoặc course management
  - Tạo test course nếu cần

### Debug Mode

Enable debug để xem chi tiết errors:
```php
// In config.php
$CFG->debug = DEBUG_DEVELOPER;
$CFG->debugdisplay = 1;
```

### Logging

Check web service logs:
1. **Site Administration** → **Server** → **Web services** → **Overview**
2. **Site Administration** → **Reports** → **Logs**
3. Filter by Web services activity

## Error Codes Reference

| Error Message | Cause | Solution |
|---------------|-------|----------|
| Source course not found | shortname_clone không tồn tại | Kiểm tra shortname trong course management |
| Course shortname already exists | shortname đã được sử dụng | Đổi shortname khác |  
| End date must be after start date | enddate <= startdate | Sửa date range |
| Invalid parameter | Parameter thiếu hoặc sai format | Kiểm tra tất cả required fields |
| Failed to create course backup | Lỗi backup process | Kiểm tra disk space và backup settings |
| Failed to restore course backup | Lỗi restore process | Kiểm tra quyền và course category |

## Technical Details

### File Structure
```
local/webservice/
├── version.php              # Plugin metadata
├── db/
│   ├── services.php        # Web service definitions  
│   └── access.php          # Capability definitions
├── lang/en/
│   └── local_webservice.php # Language strings
├── externallib.php         # Main web service implementation
└── README.md               # This documentation
```

### Dependencies
- Moodle 3.10+ (2020110900)
- Backup and Restore APIs
- External Functions API
- Course management APIs

### Performance Considerations
- Clone operation có thể mất thời gian tùy course size
- Backup files temporary lưu trữ trên server
- Memory usage tăng với large courses
- Recommend clone trong off-peak hours

### Security Notes
- Tất cả operations require proper capabilities
- Token-based authentication
- Input validation và sanitization
- Audit trail trong Moodle logs

## Support

### Requirements Check
Trước khi sử dụng, verify:
- [ ] Moodle version 3.10+
- [ ] Web services enabled
- [ ] REST protocol enabled  
- [ ] Plugin installed successfully
- [ ] User có required capabilities
- [ ] Token created và active
- [ ] Test course available

### Getting Help
1. Check Moodle logs for detailed errors
2. Verify configuration theo hướng dẫn
3. Test với simple course trước
4. Use debug mode để troubleshoot

---

**Version**: 1.0.0  
**Compatibility**: Moodle 3.10+  
**License**: GPL v3+

## Quick Start - Gọi API Clone Môn Học

### Bước 1: Chuẩn bị
1. **Plugin đã cài đặt**: Đảm bảo plugin `local_webservice` đã được install
2. **Web Services enabled**: Enable web services và REST protocol
3. **Token**: Có token để authenticate
4. **Source Course**: Có khóa học gốc để clone

### Bước 2: Gọi API

#### **API Endpoint:**
```
POST http://your-moodle.com/webservice/rest/server.php
```

#### **Postman Request:**
1. **Method**: POST
2. **URL**: `http://your-moodle.com/webservice/rest/server.php`
3. **Body** (form-urlencoded):
```
wstoken: abc123def456ghi789
wsfunction: local_webservice_clone_course
moodlewsrestformat: json
shortname_clone: MATH101
fullname: Toán Cao Cấp - Lớp Mới
shortname: MATH101_CLONE_2025
startdate: 1727136000
enddate: 1735689600
```

#### **cURL Command:**
```bash
curl -X POST "http://your-moodle.com/webservice/rest/server.php" \
  -d "wstoken=abc123def456ghi789" \
  -d "wsfunction=local_webservice_clone_course" \
  -d "moodlewsrestformat=json" \
  -d "shortname_clone=MATH101" \
  -d "fullname=Toán Cao Cấp - Lớp Mới" \
  -d "shortname=MATH101_CLONE_2025" \
  -d "startdate=1727136000" \
  -d "enddate=1735689600"
```

#### **Response Success:**
```json
{
    "status": "success",
    "id": 42,
    "message": "Course cloned successfully"
}
```

#### **Response Error:**
```json
{
    "status": "error",
    "id": 0,
    "message": "Source course not found with shortname: MATH101"
}
```

### Bước 3: Import Postman Collection

1. Download file `Course_Clone_API.postman_collection.json`
2. Mở Postman → Import → Chọn file
3. Update environment variables:
   - `moodle_url`: http://your-moodle-site.com
   - `ws_token`: your-actual-token-here
4. Chạy test requests

## Code Examples

### PHP Example:
```php
<?php
function cloneCourse($token, $moodleUrl, $sourceShortname, $newFullname, $newShortname) {
    $url = $moodleUrl . '/webservice/rest/server.php';
    
    $params = [
        'wstoken' => $token,
        'wsfunction' => 'local_webservice_clone_course',
        'moodlewsrestformat' => 'json',
        'shortname_clone' => $sourceShortname,
        'fullname' => $newFullname,
        'shortname' => $newShortname,
        'startdate' => strtotime('+1 week'),
        'enddate' => strtotime('+3 months')
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Sử dụng:
$result = cloneCourse(
    'abc123def456ghi789',
    'http://your-moodle.com',
    'MATH101',
    'Toán Cao Cấp - Khóa Mới',
    'MATH101_NEW_2025'
);

if ($result['status'] === 'success') {
    echo "Clone thành công! Course ID: " . $result['id'];
} else {
    echo "Lỗi: " . $result['message'];
}
?>
```

### JavaScript/AJAX Example:
```javascript
async function cloneCourse(token, moodleUrl, sourceShortname, newFullname, newShortname) {
    const url = `${moodleUrl}/webservice/rest/server.php`;
    
    const formData = new FormData();
    formData.append('wstoken', token);
    formData.append('wsfunction', 'local_webservice_clone_course');
    formData.append('moodlewsrestformat', 'json');
    formData.append('shortname_clone', sourceShortname);
    formData.append('fullname', newFullname);
    formData.append('shortname', newShortname);
    formData.append('startdate', Math.floor(Date.now() / 1000) + 86400); // Tomorrow
    formData.append('enddate', Math.floor(Date.now() / 1000) + 7776000); // 3 months

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        return await response.json();
    } catch (error) {
        return {
            status: 'error',
            id: 0,
            message: error.message
        };
    }
}

// Sử dụng:
cloneCourse(
    'abc123def456ghi789',
    'http://your-moodle.com',
    'MATH101',
    'Toán Cao Cấp - Khóa Mới',
    'MATH101_NEW_2025'
).then(result => {
    if (result.status === 'success') {
        console.log('Clone thành công! Course ID:', result.id);
    } else {
        console.error('Lỗi:', result.message);
    }
});
```

### Python Example:
```python
import requests
import time

def clone_course(token, moodle_url, source_shortname, new_fullname, new_shortname):
    url = f"{moodle_url}/webservice/rest/server.php"
    
    data = {
        'wstoken': token,
        'wsfunction': 'local_webservice_clone_course',
        'moodlewsrestformat': 'json',
        'shortname_clone': source_shortname,
        'fullname': new_fullname,
        'shortname': new_shortname,
        'startdate': int(time.time()) + 86400,  # Tomorrow
        'enddate': int(time.time()) + 7776000   # 3 months
    }
    
    response = requests.post(url, data=data)
    return response.json()

# Sử dụng:
result = clone_course(
    'abc123def456ghi789',
    'http://your-moodle.com',
    'MATH101',
    'Toán Cao Cấp - Khóa Mới',
    'MATH101_NEW_2025'
)

if result['status'] == 'success':
    print(f"Clone thành công! Course ID: {result['id']}")
else:
    print(f"Lỗi: {result['message']}")
```

## Common Error Handling

### Xử lý lỗi trong code:

```php
function handleCloneResult($result) {
    switch ($result['status']) {
        case 'success':
            echo "✅ Clone thành công! Course ID: " . $result['id'];
            break;
        case 'error':
            if (strpos($result['message'], 'not found') !== false) {
                echo "❌ Khóa học gốc không tồn tại";
            } elseif (strpos($result['message'], 'already exists') !== false) {
                echo "❌ Shortname đã được sử dụng";
            } elseif (strpos($result['message'], 'date') !== false) {
                echo "❌ Ngày tháng không hợp lệ";
            } else {
                echo "❌ Lỗi: " . $result['message'];
            }
            break;
    }
}
```

## Postman Collection

Import file `Course_Clone_API.postman_collection.json` để có sẵn các test cases:

1. **Success Test** - Clone thành công
2. **Source Not Found** - Khóa học gốc không tồn tại  
3. **Invalid Date Range** - Ngày tháng không hợp lệ
4. **Duplicate Shortname** - Shortname trùng lặp

Collection bao gồm:
- Pre-request scripts tự động generate unique shortname
- Test scripts validate response
- Environment variables cho URL và token