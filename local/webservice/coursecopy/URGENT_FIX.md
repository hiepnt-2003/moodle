# URGENT FIX - Function Name Updated

## 🚨 **Lỗi đã được sửa:** Cannot find file externallib.php

### ✅ **Đã thay đổi:**

#### **1. Component Name:**
- **Trước:** `local_coursecopy`  
- **Sau:** `local_webservice_coursecopy`

#### **2. Function Name:**
- **Trước:** `local_coursecopy_copy_course`
- **Sau:** `local_webservice_coursecopy_copy_course`

#### **3. New API Endpoint:**
```
POST /webservice/restful/server.php/local_webservice_coursecopy_copy_course
```

### 🔧 **Cần làm ngay:**

#### **1. Reinstall Plugin:**
```
Site Administration → Notifications
→ Click "Upgrade Moodle database now"
```

#### **2. Recreate Service:**
```
Site Administration → Server → Web services → External services
→ Delete service cũ "Course Copy RESTful Service"
→ Create service mới với function: local_webservice_coursecopy_copy_course
```

#### **3. Recreate Token:**
```
Site Administration → Server → Web services → Manage tokens  
→ Delete token cũ
→ Create token mới cho service vừa tạo
```

#### **4. Update Postman:**
```
URL: {{base_url}}/webservice/restful/server.php/local_webservice_coursecopy_copy_course
Token: [token mới từ bước 3]
```

### 📝 **Test Request:**
```bash
curl -X POST "http://your-site/webservice/restful/server.php/local_webservice_coursecopy_copy_course" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: your_new_token" \
  -d '{
    "shortname_clone": "DEMO2024",
    "fullname": "Test Course Copy",
    "shortname": "TEST2025",
    "startdate": 1704067200,
    "enddate": 1719792000
  }'
```

**Thực hiện theo thứ tự trên để sửa lỗi!** 🎯