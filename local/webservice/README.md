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

### **🔧 2. Web Service Configuration**

#### **Enable Web Services:**
1. **Site Administration** → **Advanced Features**
2. Enable **"Enable web services"**
3. Enable **"Enable REST protocol"**

#### **Create Web Service User:**
1. **Site Administration** → **Users** → **Accounts** → **Add a new user**
2. Create dedicated web service user account

#### **Configure Service:**
1. **Site Administration** → **Server** → **Web services** → **External services**
2. Find **"Course Clone Service"** 
3. **Enable** the service
4. **Add** authorized users

#### **Generate Token:**
1. **Site Administration** → **Server** → **Web services** → **Manage tokens**
2. **Create token** for web service user
3. Select **"Course Clone Service"**
4. Copy generated token

### **🔌 3. API Usage Examples**

#### **cURL Example:**
```bash
curl -X POST "https://your-moodle.com/webservice/rest/server.php" \
  -d "wstoken=YOUR_TOKEN_HERE" \
  -d "wsfunction=local_webservice_clone_course" \
  -d "moodlewsrestformat=json" \
  -d "shortname_clone=MATH101" \
  -d "fullname=Advanced Mathematics - New Class" \
  -d "shortname=MATH101_NEW" \
  -d "startdate=1735689600" \
  -d "enddate=1743465600"
```

#### **PHP Example:**
```php
<?php
$url = 'https://your-moodle.com/webservice/rest/server.php';
$token = 'YOUR_TOKEN_HERE';

$params = [
    'wstoken' => $token,
    'wsfunction' => 'local_webservice_clone_course',
    'moodlewsrestformat' => 'json',
    'shortname_clone' => 'MATH101',
    'fullname' => 'Advanced Mathematics - New Class',
    'shortname' => 'MATH101_NEW',
    'startdate' => strtotime('+1 week'),
    'enddate' => strtotime('+2 months')
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
print_r($result);
?>
```

#### **JavaScript/AJAX Example:**
```javascript
const cloneCourse = async (params) => {
    const url = 'https://your-moodle.com/webservice/rest/server.php';
    const token = 'YOUR_TOKEN_HERE';
    
    const formData = new FormData();
    formData.append('wstoken', token);
    formData.append('wsfunction', 'local_webservice_clone_course');
    formData.append('moodlewsrestformat', 'json');
    formData.append('shortname_clone', params.shortname_clone);
    formData.append('fullname', params.fullname);
    formData.append('shortname', params.shortname);
    formData.append('startdate', params.startdate);
    formData.append('enddate', params.enddate);
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Clone failed:', error);
        return {
            status: 'error',
            id: 0,
            message: error.message
        };
    }
};

// Usage
cloneCourse({
    shortname_clone: 'MATH101',
    fullname: 'Advanced Mathematics - New Class',
    shortname: 'MATH101_NEW',
    startdate: Math.floor(Date.now() / 1000) + 86400, // Tomorrow
    enddate: Math.floor(Date.now() / 1000) + 2592000  // 30 days
}).then(result => {
    console.log('Clone result:', result);
});
```

---

## 🛡️ Error Handling

### **🔍 Common Error Scenarios:**

#### **❌ Source Course Not Found**
```json
{
    "status": "error",
    "id": 0,
    "message": "Source course not found with shortname: NONEXISTENT"
}
```

#### **❌ Shortname Already Exists**
```json
{
    "status": "error",
    "id": 0,
    "message": "Course shortname already exists: MATH101_DUPLICATE"
}
```

#### **❌ Invalid Date Range**
```json
{
    "status": "error",
    "id": 0,
    "message": "End date must be after start date"
}
```

#### **❌ Insufficient Permissions**
```json
{
    "status": "error",
    "id": 0,
    "message": "You do not have permission to clone courses"
}
```

#### **❌ Backup/Restore Failure**
```json
{
    "status": "error",
    "id": 0,
    "message": "Failed to create course backup: disk space insufficient"
}
```

### **🔧 Error Recovery:**
- **Validation Errors**: Fix input parameters và retry
- **Permission Errors**: Check user capabilities và service configuration  
- **Backup Failures**: Check disk space và Moodle backup settings
- **Restore Failures**: Verify course category permissions
- **Network Errors**: Implement retry logic với appropriate delays

---

## 🏗️ Technical Architecture

### **📁 File Structure:**
```
local/webservice/
├── version.php                 # Plugin metadata
├── db/
│   ├── services.php           # Web service definitions
│   └── access.php             # Capability definitions
├── lang/en/
│   └── local_webservice.php   # Language strings
├── externallib.php            # Main web service class
├── test.php                   # Testing interface
└── README.md                  # Documentation
```

### **🔌 Web Service Configuration:**

#### **Function Definition:**
```php
$functions = [
    'local_webservice_clone_course' => [
        'classname'   => 'local_webservice_external',
        'methodname'  => 'clone_course',
        'classpath'   => 'local/webservice/externallib.php',
        'description' => 'Clone a course with new details',
        'type'        => 'write',
        'capabilities' => 'moodle/course:create,moodle/backup:backupcourse,moodle/restore:restorecourse',
    ],
];
```

#### **Service Definition:**
```php
$services = [
    'Course Clone Service' => [
        'functions' => ['local_webservice_clone_course'],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'course_clone_service',
    ],
];
```

### **🔐 Security Features:**

#### **Capability Requirements:**
- `moodle/course:create` - Create new courses
- `moodle/backup:backupcourse` - Create course backups  
- `moodle/restore:restorecourse` - Restore course content

#### **Input Validation:**
- Parameter type checking
- Required field validation
- Date range validation
- Shortname uniqueness check
- SQL injection prevention

#### **Error Handling:**
- Graceful failure modes
- Detailed error messages
- Cleanup on failures
- Transaction safety

---

## 🎯 Use Cases và Applications

### **🏫 Educational Institutions:**

#### **🔄 Semester Course Setup:**
```php
// Clone courses for new semester
foreach ($semester_courses as $course) {
    $result = clone_course(
        $course['template_shortname'],
        $course['name'] . ' - Spring 2025',
        $course['shortname'] . '_S2025',
        $spring_start_date,
        $spring_end_date
    );
}
```

#### **👥 Multi-Section Courses:**
```php
// Create multiple sections of same course
for ($section = 1; $section <= 5; $section++) {
    $result = clone_course(
        'MATH101_TEMPLATE',
        "Mathematics 101 - Section {$section}",
        "MATH101_SEC{$section}",
        $semester_start,
        $semester_end
    );
}
```

### **🏢 Corporate Training:**

#### **📚 Training Program Rollout:**
```php
// Deploy training across departments
$departments = ['HR', 'IT', 'Sales', 'Marketing'];
foreach ($departments as $dept) {
    $result = clone_course(
        'COMPLIANCE_TEMPLATE',
        "Compliance Training - {$dept}",
        "COMPLIANCE_{$dept}_2025",
        $training_start,
        $training_end
    );
}
```

### **🔗 Integration Examples:**

#### **📊 LMS Integration:**
```php
// Integrate with external student information system
$external_courses = fetch_from_sis();
foreach ($external_courses as $ext_course) {
    if ($ext_course['needs_clone']) {
        $result = clone_course(
            $ext_course['template'],
            $ext_course['full_name'],
            $ext_course['short_name'],
            $ext_course['start_date'],
            $ext_course['end_date']
        );
        
        update_sis_with_course_id($ext_course['id'], $result['id']);
    }
}
```

---

## 🎓 Testing và Debugging

### **🧪 Built-in Test Interface:**

Plugin cung cấp web interface để test web service tại:
```
https://your-moodle.com/local/webservice/test.php
```

**Features:**
- ✅ Interactive form để test parameters
- 📋 List existing courses để reference
- 📊 Real-time response display
- 📖 API documentation built-in
- 🔍 Error debugging information

### **🐛 Debug Mode:**

Enable debugging trong Moodle để see detailed error messages:
```php
// In config.php
$CFG->debug = DEBUG_DEVELOPER;
$CFG->debugdisplay = 1;
```

### **📝 Logging:**

Plugin logs major operations:
- Course clone attempts
- Success/failure status
- Error details
- Performance metrics

Check logs at: **Site Administration** → **Reports** → **Logs**

---

## 💡 Best Practices

### **⚡ Performance Optimization:**

#### **🎯 Efficient Cloning:**
- Clone during low-traffic periods
- Use background tasks for large courses
- Monitor disk space for backups
- Clean up temporary files

#### **📊 Batch Operations:**
```php
// Process multiple clones efficiently
$clone_queue = [];
foreach ($courses_to_clone as $course) {
    $clone_queue[] = [
        'shortname_clone' => $course['template'],
        'fullname' => $course['name'],
        'shortname' => $course['code'],
        'startdate' => $course['start'],
        'enddate' => $course['end']
    ];
}

// Process in batches to avoid timeouts
$batch_size = 5;
$batches = array_chunk($clone_queue, $batch_size);
foreach ($batches as $batch) {
    process_clone_batch($batch);
    sleep(2); // Prevent server overload
}
```

### **🔒 Security Best Practices:**

#### **🛡️ Token Management:**
- Use dedicated web service accounts
- Rotate tokens regularly
- Restrict token permissions
- Monitor API usage

#### **📝 Audit Trail:**
```php
// Log all clone operations
function log_clone_operation($params, $result, $user_id) {
    $log_entry = [
        'timestamp' => time(),
        'user_id' => $user_id,
        'action' => 'course_clone',
        'source_course' => $params['shortname_clone'],
        'target_course' => $params['shortname'],
        'status' => $result['status'],
        'new_course_id' => $result['id']
    ];
    
    // Store in custom log table or use Moodle events
    \core\event\course_created::create($log_entry)->trigger();
}
```

### **🎯 Error Prevention:**

#### **✅ Pre-flight Checks:**
```php
// Validate before attempting clone
function pre_clone_validation($params) {
    $checks = [
        'source_exists' => check_source_course($params['shortname_clone']),
        'target_available' => check_shortname_available($params['shortname']),
        'dates_valid' => validate_date_range($params['startdate'], $params['enddate']),
        'permissions_ok' => check_user_capabilities(),
        'disk_space' => check_available_disk_space(),
    ];
    
    return array_filter($checks) === $checks; // All true
}
```

---

## 🔮 Future Enhancements

### **🚀 Planned Features:**

#### **📅 Advanced Date Handling:**
- Automatic date shifting for activities
- Holiday calendar integration  
- Timezone support

#### **🎨 Customization Options:**
- Selective content cloning
- Template-based cloning
- Bulk operation support

#### **📊 Enhanced Reporting:**
- Clone operation analytics
- Performance monitoring
- Usage statistics

#### **🔗 Extended Integration:**
- External calendar sync
- Student enrollment automation
- Grade book template application

---

## 📞 Support và Documentation

### **📚 Additional Resources:**
- [Moodle Web Services Documentation](https://docs.moodle.org/dev/Web_services)
- [Backup and Restore API](https://docs.moodle.org/dev/Backup_API)
- [External Functions](https://docs.moodle.org/dev/External_functions_API)

### **🆘 Troubleshooting:**
- Check Moodle logs for detailed errors
- Verify web service configuration
- Test with minimal data set first
- Contact system administrator for permission issues

Plugin **local_webservice** cung cấp powerful và flexible solution cho course cloning needs, với robust error handling và comprehensive documentation để ensure successful implementation!