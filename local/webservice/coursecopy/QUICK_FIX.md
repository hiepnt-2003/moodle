# Quick Fix - Token Configuration

## 🎯 Vấn đề: "Invalid token - token not found"

### ✅ GIẢI PHÁP NHANH:

#### 1. Tạo lại Token đúng cách
```
Site Administration → Server → Web services → Manage tokens
→ Delete token cũ
→ Create token
→ Service: CHỌN "Course Copy RESTful Service" (QUAN TRỌNG!)
→ User: Admin User
→ Save và copy token
```

#### 2. Test URL trực tiếp
```
POST http://your-moodle-site/webservice/restful/server.php/local_coursecopy_copy_course

Headers:
Content-Type: application/json
Accept: application/json  
Authorization: ef6ecf5ad8726d4a56f41690d467181e

Body:
{
  "shortname_clone": "MATH101",
  "fullname": "Demo Course Copy - Spring 2025",
  "shortname": "DEMO2025", 
  "startdate": 1704067200,
  "enddate": 1719792000
}
```

#### 3. Postman Variables
```
base_url: http://localhost (hoặc domain của bạn)
token: ef6ecf5ad8726d4a56f41690d467181e (token từ bước 1)
```

### ❌ KHÔNG DÙNG:
- ~~Bearer {{token}}~~
- ~~Authorization: Bearer ef6ecf...~~

### ✅ DÙNG:
- `Authorization: {{token}}`
- `Authorization: ef6ecf5ad8726d4a56f41690d467181e`

**Token phải được tạo cho SERVICE CỤ THỂ, không phải "All services"!**