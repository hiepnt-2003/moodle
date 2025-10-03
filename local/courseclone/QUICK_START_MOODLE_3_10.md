# 🚀 Quick Start Guide - Moodle 3.10 + RESTful Plugin

## 🎯 **Setup dành riêng cho môi trường của bạn**

Hướng dẫn nhanh để setup Course Clone API trên **Moodle 3.10** với **RESTful Protocol Plugin đã cài đặt**.

---

## 📋 **Bước 1: Verify Current Setup**

✅ **Moodle 3.10** - Confirmed  
✅ **RESTful Protocol Plugin** - Đã cài đặt (version 2024050604)  
✅ **Web Services** - Cần enable  

---

## ⚡ **Bước 2: Enable Web Services** 

1. **Site Administration** → **Advanced features**
2. ✅ Tick **Enable web services**
3. Click **Save changes**

---

## 🔌 **Bước 3: Enable RESTful Protocol**

1. **Site Administration** → **Server** → **Web services** → **Manage protocols**
2. ✅ Enable **RESTful protocol** (từ plugin bạn đã cài)
3. ✅ Enable **REST protocol** (backup option)

---

## 🎛️ **Bước 4: Create Web Service**

1. **Site Administration** → **Server** → **Web services** → **External services**
2. Click **Add** để tạo service mới:
   ```
   Name: Course Clone RESTful API
   Short name: courseclone_restful
   Enabled: ✅ Yes
   Authorised users only: ✅ Yes (recommended)
   ```
3. Click **Add service**

---

## 🔧 **Bước 5: Add Functions to Service**

1. Trong **External services**, tìm service vừa tạo
2. Click **Functions** 
3. Click **Add functions** và thêm:
   - `local_courseclone_get_course_list`
   - `local_courseclone_get_clone_status`  
   - `local_courseclone_clone_course`

---

## 👤 **Bước 6: Create API User & Token**

### **6.1 Authorize User**
1. Trong service, click **Authorised users**
2. Add user muốn cấp quyền API (có thể dùng admin account)

### **6.2 Create Token**  
1. **Site Administration** → **Server** → **Web services** → **Manage tokens**
2. Click **Create token**:
   ```
   User: [User đã authorize]
   Service: Course Clone RESTful API
   Valid until: [Để trống = không expire]
   ```
3. **📝 LưU TOKEN** này lại - bạn sẽ cần để test API!

---

## 🧪 **Bước 7: Test API với Postman**

### **7.1 Import Collection**
1. Mở Postman
2. Import file `Course_Clone_API.postman_collection.json` từ plugin
3. Set variables:
   - `moodle_url`: URL Moodle của bạn (vd: `http://localhost/moodle`)  
   - `webservice_token`: Token vừa tạo ở bước 6

### **7.2 Test Requests**
Thử chạy các requests theo thứ tự:

1. **Get Course List** - Lấy danh sách courses
2. **Get Clone Status** - Check thông tin 1 course cụ thể  
3. **Clone Course** - Thực hiện clone course

---

## 📡 **Example API Call**

```bash
curl -X POST "http://your-moodle-site.com/webservice/rest/server.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "wsfunction": "local_courseclone_get_course_list",
    "moodlewsrestformat": "json",
    "categoryid": 0,
    "visible": true
  }'
```

---

## 🔍 **Troubleshooting**

### **❌ "Invalid token" Error**
- Check token tạo đúng service chưa
- Verify user có quyền access service
- Ensure token chưa expire

### **❌ "Function not found" Error**  
- Check plugin Course Clone đã install chưa
- Verify functions đã add vào service chưa
- Check user có capabilities cần thiết chưa

### **❌ CORS Error (cho web apps)**
Thêm vào `config.php`:
```php
$CFG->webservice_cors_enabled = true;
$CFG->webservice_cors_allowedorigins = array('*'); // Chỉ dùng cho dev
```

---

## 🎉 **Next Steps**

Sau khi setup xong:

1. **📚 Đọc documentation** trong `RESTFUL_BEARER_SETUP.md`
2. **🧪 Test với Postman** collection đã import
3. **🔗 Integrate** vào application của bạn
4. **📊 Monitor** API usage qua Moodle logs

---

**🚀 Bây giờ bạn đã có full RESTful API cho Course Clone với Bearer Token authentication!**