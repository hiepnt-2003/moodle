# TH Boost Theme

Theme con kế thừa từ Boost với các tính năng bổ sung:

## ✨ Tính năng

### 1. 🎨 Kế thừa từ Boost
- Theme con (child theme) kế thừa tất cả tính năng từ Boost
- Dễ dàng nâng cấp và bảo trì
- Tương thích với các phiên bản Moodle 4.0+

### 2. 🔤 FontAwesome Icons
- Tích hợp FontAwesome 6.4.0
- Hỗ trợ đầy đủ các icon: solid, regular, brands
- Sử dụng CDN để load nhanh
- Override icon system của Moodle

### 3. 👁️ Hiển thị mật khẩu trong Form đăng nhập
- Icon "mắt" để toggle hiển thị/ẩn mật khẩu
- Tự động apply cho tất cả password fields (login, change password, etc.)
- Sử dụng trang login mặc định của Moodle
- Animation mượt mà
- Responsive design
- Accessible với screen readers

## 📦 Cài đặt

### Bước 1: Copy theme vào thư mục themes
```bash
# Đã có trong: e:\GitHub\moodle\theme\th_boost
```

### Bước 2: Cài đặt plugin
1. Đăng nhập với tài khoản **Admin**
2. Truy cập: **Site administration → Notifications**
3. Click **"Upgrade Moodle database now"**
4. Chờ quá trình cài đặt hoàn tất

### Bước 3: Kích hoạt theme
1. Truy cập: **Site administration → Appearance → Themes → Theme selector**
2. Chọn **"TH Boost"** cho:
   - Default theme
   - Theme for mobile devices (nếu cần)
   - Theme for tablet devices (nếu cần)
3. Click **"Save changes"**

### Bước 4: Clear cache
1. Truy cập: **Site administration → Development → Purge all caches**
2. Click **"Purge all caches"**

## 🎯 Sử dụng

### FontAwesome Icons
Theme tự động load FontAwesome CDN. Bạn có thể sử dụng icons trong HTML:

```html
<!-- Solid icons -->
<i class="fas fa-user"></i>
<i class="fas fa-heart"></i>
<i class="fas fa-home"></i>

<!-- Regular icons -->
<i class="far fa-star"></i>
<i class="far fa-smile"></i>

<!-- Brands -->
<i class="fab fa-facebook"></i>
<i class="fab fa-twitter"></i>
```

### Password Toggle
Tính năng tự động hoạt động cho:
- Form đăng nhập (login page)
- Form đổi mật khẩu
- Bất kỳ password field nào trong Moodle

**Cách hoạt động:**
1. Icon "mắt" xuất hiện bên phải ô password
2. Click icon để hiển thị mật khẩu (icon đổi thành "mắt gạch")
3. Click lại để ẩn mật khẩu

## 🔧 Tùy chỉnh

### Thay đổi màu Brand
1. **Site administration → Appearance → Themes → TH Boost**
2. Vào tab **"General"**
3. Tìm **"Brand colour"**
4. Chọn màu mong muốn
5. **Save changes** và **Purge caches**

### Thêm Custom SCSS
1. **Site administration → Appearance → Themes → TH Boost**
2. Vào tab **"Advanced settings"**
3. Thêm SCSS vào:
   - **Raw initial SCSS**: Variables
   - **Raw SCSS**: Custom styles
4. **Save changes** và **Purge caches**

### Tùy chỉnh Password Toggle
Chỉnh sửa file: `theme/th_boost/scss/th_boost.scss`

```scss
.password-toggle-btn {
    // Đổi màu icon
    color: #007bff;
    
    // Đổi vị trí
    right: 15px;
    
    // Đổi kích thước icon
    i {
        font-size: 20px;
    }
}
```

## 📱 Responsive Design
Theme được tối ưu cho:
- 💻 Desktop (> 768px)
- 📱 Tablet (768px - 576px)
- 📱 Mobile (< 576px)

## 🔒 Accessibility
- Password toggle button có `aria-label`
- Icons có `aria-hidden="true"`
- Keyboard navigation support
- Screen reader friendly

## 🛠️ Cấu trúc thư mục

```
theme/th_boost/
├── classes/
│   └── output/
│       ├── core_renderer.php          # Override renderer
│       └── icon_system_fontawesome.php # FontAwesome icon system
├── lang/
│   └── en/
│       └── theme_th_boost.php         # Language strings
├── scss/
│   └── th_boost.scss                  # Custom SCSS
├── config.php                          # Theme configuration
├── lib.php                             # Theme functions
├── settings.php                        # Admin settings
├── version.php                         # Version info
└── README.md                           # This file
```

## 🐛 Troubleshooting

### Icons không hiển thị?
1. Check internet connection (FontAwesome load từ CDN)
2. Purge all caches
3. Hard refresh browser (Ctrl + F5)

### Password toggle không hoạt động?
1. Purge all caches
2. Check browser console for errors
3. Ensure JavaScript is enabled
4. Try different browser

### Theme không xuất hiện trong danh sách?
1. Check folder name là `th_boost`
2. Check version.php có đúng format không
3. Run Site administration → Notifications

## 📄 License
GNU GPL v3 or later

## 👨‍💻 Support
Created for Moodle custom theme development.

---

**Chúc bạn sử dụng theme hiệu quả! 🎉**
