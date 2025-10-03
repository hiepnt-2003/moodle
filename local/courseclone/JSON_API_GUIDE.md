# 🚀 Course Clone REST API - JSON Format Guide

## 📋 **Tổng quan**

Hướng dẫn sử dụng **Course Clone API** với định dạng **JSON** thay vì FormData truyền thống. JSON format được khuyến nghị cho các ứng dụng modern và RESTful API.

## 🔧 **Cấu hình cần thiết**

### 1. **Web Service Setup** (trong Moodle Admin)
```
Site Administration > Server > Web services > Overview
✅ Enable web services
✅ Enable protocols > REST protocol
```

### 2. **Service Configuration**
```
External services > Add service:
- Name: Course Clone Service  
- Short name: courseclone_service
- Functions: local_courseclone_clone_course, local_courseclone_get_course_list, local_courseclone_get_clone_status
```

### 3. **Token Generation**
```
Manage tokens > Create token:
- User: [User có quyền webservice]
- Service: Course Clone Service
```

## 📡 **API Endpoints**

### **Base URL**
```
POST {{moodle_url}}/webservice/rest/server.php
```

### **Headers** (cho JSON format)
```json
{
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

## 🔍 **1. Get Course List**

### **Request JSON Body:**
```json
{
  "wstoken": "your_token_here",
  "wsfunction": "local_courseclone_get_course_list", 
  "moodlewsrestformat": "json",
  "categoryid": 0,
  "visible": true
}
```

### **Response Example:**
```json
[
  {
    "id": 5,
    "fullname": "Toán học cơ bản",
    "shortname": "MATH101",
    "categoryid": 2,
    "visible": 1,
    "startdate": 1640995200,
    "enddate": 1656547200,
    "format": "topics",
    "lang": "",
    "theme": ""
  },
  {
    "id": 7, 
    "fullname": "Lập trình PHP",
    "shortname": "PHP_2024",
    "categoryid": 3,
    "visible": 1,
    "startdate": 1640995200,
    "enddate": 1656547200,
    "format": "weeks",
    "lang": "vi",
    "theme": ""
  }
]
```

## 📊 **2. Get Clone Status**

### **Request JSON Body:**
```json
{
  "wstoken": "your_token_here",
  "wsfunction": "local_courseclone_get_clone_status",
  "moodlewsrestformat": "json", 
  "courseid": 5
}
```

### **Response Example:**
```json
{
  "id": 5,
  "fullname": "Toán học cơ bản",
  "shortname": "MATH101",
  "categoryid": 2,
  "visible": true,
  "startdate": 1640995200,
  "enddate": 1656547200,
  "format": "topics",
  "lang": "",
  "theme": "",
  "can_clone": true,
  "clone_info": {
    "sections_count": 12,
    "activities_count": 45,
    "estimated_size": "2.5MB",
    "restrictions": []
  }
}
```

## 🔄 **3. Clone Course**

### **Request JSON Body:**
```json
{
  "wstoken": "your_token_here",
  "wsfunction": "local_courseclone_clone_course",
  "moodlewsrestformat": "json",
  "shortname_clone": "MATH101",
  "fullname": "Toán học cơ bản - Lớp 2", 
  "shortname": "MATH101_CLASS2",
  "startdate": 1704067200,
  "enddate": 1719792000
}
```

### **Success Response:**
```json
{
  "status": "success",
  "id": 123,
  "message": "Copy môn học thành công!",
  "new_course": {
    "id": 123,
    "fullname": "Toán học cơ bản - Lớp 2",
    "shortname": "MATH101_CLASS2",
    "categoryid": 2,
    "startdate": 1704067200,
    "enddate": 1719792000
  }
}
```

### **Error Response:**
```json
{
  "status": "error", 
  "id": 0,
  "message": "Không tìm thấy môn học với shortname: MATH101",
  "errorcode": "course_not_found"
}
```

## 🧪 **Testing với các tools**

### **1. Test Client HTML**
Sử dụng file `test_api.html` đã tạo:
- Tích ✅ "Sử dụng JSON format"
- Nhập token và Moodle URL
- Test từng function

### **2. Postman Collection** 
Import file `Course_Clone_API.postman_collection.json`:
- Các request có suffix "(JSON Format)" 
- Set variables: `moodle_url`, `webservice_token`
- Execute requests

### **3. cURL Examples**

#### Get Course List:
```bash
curl -X POST "http://localhost/moodle/webservice/rest/server.php" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "wstoken": "your_token_here",
    "wsfunction": "local_courseclone_get_course_list",
    "moodlewsrestformat": "json",
    "categoryid": 0,
    "visible": true
  }'
```

#### Clone Course:
```bash
curl -X POST "http://localhost/moodle/webservice/rest/server.php" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "wstoken": "your_token_here",
    "wsfunction": "local_courseclone_clone_course", 
    "moodlewsrestformat": "json",
    "shortname_clone": "MATH101",
    "fullname": "Toán học cơ bản - Lớp 2",
    "shortname": "MATH101_CLASS2",
    "startdate": 1704067200,
    "enddate": 1719792000
  }'
```

## ⚡ **JavaScript/AJAX Examples**

### **Fetch API:**
```javascript
async function cloneCourse(data) {
  try {
    const response = await fetch('http://localhost/moodle/webservice/rest/server.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        wstoken: 'your_token_here',
        wsfunction: 'local_courseclone_clone_course',
        moodlewsrestformat: 'json',
        ...data
      })
    });
    
    const result = await response.json();
    
    if (result.exception) {
      throw new Error(result.message);
    }
    
    return result;
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
}

// Usage
cloneCourse({
  shortname_clone: 'MATH101',
  fullname: 'Toán học cơ bản - Lớp 2',
  shortname: 'MATH101_CLASS2', 
  startdate: 1704067200,
  enddate: 1719792000
}).then(result => {
  console.log('Clone successful:', result);
}).catch(error => {
  console.error('Clone failed:', error);
});
```

### **jQuery AJAX:**
```javascript
$.ajax({
  url: 'http://localhost/moodle/webservice/rest/server.php',
  method: 'POST',
  contentType: 'application/json',
  dataType: 'json',
  data: JSON.stringify({
    wstoken: 'your_token_here',
    wsfunction: 'local_courseclone_get_course_list',
    moodlewsrestformat: 'json',
    categoryid: 0,
    visible: true
  }),
  success: function(data) {
    console.log('Courses:', data);
  },
  error: function(xhr, status, error) {
    console.error('Error:', error);
  }
});
```

## 🔒 **Security & Best Practices**

### **1. Token Security**
- Không hardcode token trong client-side code
- Sử dụng environment variables
- Rotate tokens định kỳ
- Restrict token permissions

### **2. Input Validation**
- Validate tất cả parameters
- Sanitize user input 
- Check date ranges (startdate < enddate)
- Verify course existence trước khi clone

### **3. Error Handling**
- Check response status codes
- Handle network timeouts
- Provide user-friendly error messages
- Log errors for debugging

## 📈 **Performance Tips**

### **1. Request Optimization**
- Minimize request payload size
- Cache course list khi có thể
- Implement request debouncing
- Use connection pooling

### **2. Response Handling** 
- Parse JSON responses efficiently
- Handle large course lists với pagination
- Implement progress indicators cho long operations
- Cache frequently accessed data

## 🚨 **Troubleshooting**

### **Common Issues:**

1. **"Invalid token" Error**
   - Check token expiry
   - Verify service permissions
   - Ensure user has webservice capabilities

2. **"Function not found" Error**
   - Verify function names spelling
   - Check if functions are added to service
   - Ensure plugin is installed and enabled

3. **"Invalid JSON" Error**
   - Validate JSON syntax
   - Check Content-Type header
   - Ensure proper encoding (UTF-8)

4. **CORS Issues** (for web clients)
   - Configure Moodle CORS settings
   - Use server-side proxy if needed
   - Check browser console for CORS errors

### **Debug Mode:**
Enable trong Moodle:
```
Site Administration > Development > Debugging
- Debug messages: DEVELOPER
- Display debug messages: Yes
```

## 📚 **Resources**

- [Moodle Web Services Documentation](https://docs.moodle.org/en/Web_services)
- [REST API Best Practices](https://restfulapi.net/)
- [JSON Schema Validation](https://json-schema.org/)
- Test Client: `test_api.html`
- Postman Collection: `Course_Clone_API.postman_collection.json`