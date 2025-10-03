# 🔐 RESTful Bearer Token Setup Guide

## 📋 **Tổng quan**

Hướng dẫn cài đặt **Bearer Token Authentication** cho Course Clone API trên **Moodle 3.10** với **RESTful Protocol Plugin**. 

✅ **Compatible với RESTful plugin đã cài đặt của bạn!**

## 🚀 **Prerequisites**

- ✅ **Moodle 3.10** (confirmed)
- ✅ **RESTful Protocol Plugin** (đã cài đặt - detected từ screenshot)
- ✅ **Web Services enabled**
- ✅ **Course Clone Plugin** (plugin này)

## ⚙️ **Setup Instructions**

### **Bước 1: Cài đặt Middleware**

1. **Copy file restful_api.php** vào thư mục plugin:
   ```
   local/courseclone/restful_api.php ✅ (đã có)
   ```

2. **Tích hợp vào Moodle Web Service**
   
   **Option A: Sửa file server.php (Recommended)**
   
   Edit file: `webservice/rest/server.php` (trong thư mục gốc Moodle)
   
   Thêm code này **SAU dòng** `require_once($CFG->dirroot . "/webservice/rest/locallib.php");`:
   
   ```php
   // RESTful Bearer Token Support
   if (file_exists($CFG->dirroot . '/local/courseclone/restful_api.php')) {
       require_once($CFG->dirroot . '/local/courseclone/restful_api.php');
       local_courseclone_handle_restful_request();
   }
   ```
   
   **Option B: Custom Endpoint (Alternative)**
   
   Tạo file mới: `webservice/rest/restful_server.php`
   ```php
   <?php
   require('../../config.php');
   require_once($CFG->dirroot . "/webservice/rest/locallib.php");
   require_once($CFG->dirroot . '/local/courseclone/restful_api.php');
   
   // Handle RESTful request with Bearer token
   local_courseclone_handle_restful_request();
   
   // Continue with normal web service processing
   require('server.php');
   ?>
   ```

### **Bước 2: Cấu hình RESTful Plugin (Moodle 3.10)**

Với RESTful plugin đã cài đặt, thêm vào `config.php`:

```php
// RESTful Protocol Configuration for Moodle 3.10
$CFG->webservice_restful_enabled = true;

// Enable CORS for RESTful API (nếu cần cho web apps)
$CFG->webservice_cors_enabled = true;
$CFG->webservice_cors_allowedorigins = array(
    'http://localhost:3000',
    'https://yourdomain.com',
    '*'  // Chỉ dùng cho development
);

// RESTful specific settings
$CFG->webservice_restful_charset = 'UTF-8';
$CFG->webservice_restful_response_type = 'json';
```

### **Bước 3: Web Service Configuration**

Trong Moodle Admin:

1. **Site Administration** > **Server** > **Web services** > **Overview**
   - ✅ Enable web services
   - ✅ Enable protocols > REST protocol

2. **External services** > Add service:
   - Name: `Course Clone RESTful Service`
   - Short name: `courseclone_restful`
   - Functions: All course clone functions
   - ✅ Enabled

3. **Manage tokens** > Create token:
   - Service: `Course Clone RESTful Service`
   - User: Target user
   - **Lưu token để sử dụng với Bearer authentication**

## 🧪 **Testing**

### **1. Test với HTML Client**

Sử dụng `test_api.html`:
1. Chọn **"🔐 RESTful Bearer Token"**
2. Nhập Moodle URL và token
3. Test các API functions

### **2. Test với cURL**

```bash
# Get Course List
curl -X POST "http://localhost/moodle/webservice/rest/server.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "wsfunction": "local_courseclone_get_course_list",
    "moodlewsrestformat": "json",
    "categoryid": 0,
    "visible": true
  }'

# Clone Course  
curl -X POST "http://localhost/moodle/webservice/rest/server.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "wsfunction": "local_courseclone_clone_course",
    "moodlewsrestformat": "json",
    "shortname_clone": "MATH101",
    "fullname": "Mathematics 101 - New",
    "shortname": "MATH101_NEW",
    "startdate": 1704067200,
    "enddate": 1719792000
  }'
```

### **3. Test với Postman**

Import Collection và sử dụng requests có suffix **"(RESTful Bearer)"**:
- Clone Course (RESTful Bearer Token)
- Get Course List (RESTful Bearer)  
- Get Clone Status (RESTful Bearer)

## 📊 **Request/Response Format**

### **Request Headers:**
```
POST /webservice/rest/server.php
Content-Type: application/json
Accept: application/json
Authorization: Bearer {your_token}
```

### **Request Body:**
```json
{
  "wsfunction": "local_courseclone_get_course_list",
  "moodlewsrestformat": "json",
  "categoryid": 0,
  "visible": true
}
```

### **Success Response:**
```json
[
  {
    "id": 5,
    "fullname": "Mathematics 101",
    "shortname": "MATH101",
    "categoryid": 2,
    "visible": 1
  }
]
```

### **Error Response:**
```json
{
  "exception": "invalid_token_exception",
  "message": "Invalid token - token not found",
  "debuginfo": "Token validation failed"
}
```

## 🔒 **Security Features**

### **Token Validation:**
- ✅ Validates token against `external_tokens` table
- ✅ Checks token expiration
- ✅ Verifies user permissions
- ✅ Supports service-specific tokens

### **CORS Support:**
- ✅ Handles preflight OPTIONS requests
- ✅ Configurable allowed origins
- ✅ Proper CORS headers

### **Error Handling:**
- ✅ Secure error messages
- ✅ No token exposure in responses
- ✅ Proper HTTP status codes

## 🚨 **Troubleshooting**

### **1. "Invalid token" Error**
```bash
# Check token exists
SELECT * FROM mdl_external_tokens WHERE token = 'your_token';

# Check token expiry  
SELECT *, FROM_UNIXTIME(validuntil) as expiry 
FROM mdl_external_tokens WHERE token = 'your_token';
```

### **2. "Function not found" Error**
- Verify plugin is installed: `local/courseclone/version.php`
- Check functions in service: Site Admin > External services
- Ensure user has capabilities

### **3. CORS Issues**
```php
// In config.php - enable CORS
$CFG->webservice_cors_enabled = true;
```

### **4. Bearer Token Not Recognized**
- Check if middleware file exists: `local/courseclone/restful_api.php`
- Verify integration in `webservice/rest/server.php`
- Check server supports getallheaders() function

## 📱 **JavaScript Examples**

### **Fetch API:**
```javascript
async function callAPI(functionName, params) {
  const response = await fetch('/webservice/rest/server.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify({
      wsfunction: functionName,
      moodlewsrestformat: 'json',
      ...params
    })
  });
  
  return await response.json();
}

// Usage
const courses = await callAPI('local_courseclone_get_course_list', {
  categoryid: 0,
  visible: true
});
```

### **Axios:**
```javascript
const api = axios.create({
  baseURL: '/webservice/rest',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Bearer ' + token
  }
});

const courses = await api.post('/server.php', {
  wsfunction: 'local_courseclone_get_course_list',
  moodlewsrestformat: 'json',
  categoryid: 0,
  visible: true
});
```

## ✅ **Benefits của RESTful Bearer Token**

1. **🔐 Security**: Token trong header, không expose trong URL/body
2. **📱 Standard**: Tuân thủ chuẩn REST API authentication
3. **🚀 Performance**: Cleaner request structure
4. **🔄 CORS Friendly**: Dễ dàng handle cross-origin requests
5. **📊 Monitoring**: Token không appear trong access logs
6. **🛠️ Integration**: Dễ tích hợp với modern frontend frameworks

## 📚 **Next Steps**

1. **Setup middleware** theo Option A hoặc B
2. **Test với HTML client** để verify functionality  
3. **Configure CORS** nếu cần thiết cho web applications
4. **Update frontend** applications để sử dụng Bearer token
5. **Monitor performance** và security logs