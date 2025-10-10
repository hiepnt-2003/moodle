# TH Boost Theme

## 📝 Mô tả

**TH Boost** là một theme Moodle tùy chỉnh kế thừa từ theme Boost mặc định, được thiết kế để nâng cao trải nghiệm người dùng với các tính năng bổ sung như:

- ✅ **Toggle password visibility**: Nút ẩn/hiện mật khẩu trên form đăng nhập
- 🎨 **Custom styling**: Giao diện được tùy chỉnh với CSS/SCSS
- 🔧 **Dễ dàng mở rộng**: Kế thừa toàn bộ tính năng từ Boost

## 🚀 Tính năng chính

### 1. Password Toggle Button
- Hiển thị nút toggle (ẩn/hiện) trên tất cả các trường password
- Icon sử dụng FontAwesome 4.7.0 (fa-eye, fa-eye-slash)
- Tự động nhận diện và áp dụng cho mọi input[type="password"]
- Responsive và tương thích với mobile

### 2. Kế thừa từ Boost
- Tất cả tính năng của theme Boost được giữ nguyên
- Hỗ trợ Bootstrap 4
- FontAwesome 4.7.0 được tích hợp sẵn
- Responsive design

## 📋 Yêu cầu hệ thống

- Moodle 3.9+ hoặc cao hơn
- PHP 7.3+ (tùy theo phiên bản Moodle)
- Theme Boost (đã có sẵn trong Moodle)

## 📦 Cài đặt

### Bước 1: Copy theme vào Moodle

```bash
# Copy thư mục th_boost vào thư mục theme của Moodle
cp -r th_boost /path/to/moodle/theme/
```

### Bước 2: Cài đặt theme

1. Đăng nhập Moodle với quyền **Administrator**
2. Vào **Site administration** → **Notifications**
3. Moodle sẽ tự động phát hiện theme mới và cài đặt
4. Click **Upgrade Moodle database now**

### Bước 3: Kích hoạt theme

1. Vào **Site administration** → **Appearance** → **Themes** → **Theme selector**
2. Chọn **TH Boost** làm theme mặc định
3. Click **Save**

### Bước 4: Xóa cache

**Quan trọng**: Sau khi cài đặt, bắt buộc phải xóa cache:

1. Vào **Site administration** → **Development** → **Purge all caches**
2. Hoặc truy cập: `http://your-moodle-site/admin/purgecaches.php`

## 🗂️ Cấu trúc thư mục

```
th_boost/
├── classes/
│   └── output/
│       └── core_renderer.php      # Override renderer, thêm password toggle
├── db/
│   └── (chưa có - có thể thêm sau)
├── lang/
│   └── en/
│       └── (chưa có - có thể thêm sau)
├── scss/
│   └── th_boost.scss              # Custom SCSS cho password toggle styling
├── style/
│   └── moodle.css                 # Precompiled CSS fallback
├── config.php                     # Theme configuration
├── lib.php                        # Theme functions (SCSS callbacks)
├── version.php                    # Theme version info
└── README.md                      # Documentation (file này)
```

## 🔧 Cấu hình

### File quan trọng

#### 1. `config.php`
Cấu hình chính của theme:
```php
$THEME->name = 'th_boost';
$THEME->parents = ['boost'];  // Kế thừa từ Boost
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;  // Bắt buộc!
```

#### 2. `lib.php`
Chứa các callback functions:
- `theme_th_boost_get_main_scss_content()`: Load preset SCSS
- `theme_th_boost_get_pre_scss()`: Thêm biến SCSS (colors, etc.)
- `theme_th_boost_get_extra_scss()`: Load custom SCSS (th_boost.scss)
- `theme_th_boost_get_precompiled_css()`: CSS fallback

#### 3. `classes/output/core_renderer.php`
Override `standard_end_of_body_html()` để inject JavaScript cho password toggle.

## 🎨 Tùy chỉnh

### Thay đổi màu sắc (Brand Color)

1. Vào **Site administration** → **Appearance** → **TH Boost**
2. Tìm mục **Brand colour**
3. Nhập mã màu (ví dụ: `#007bff`)
4. **Save changes**
5. **Purge all caches**

### Thêm Custom SCSS

1. Vào **Site administration** → **Appearance** → **TH Boost**
2. Tìm mục **Raw initial SCSS** (SCSS ban đầu) - để thêm biến
3. Hoặc **Raw SCSS** (SCSS cuối cùng) - để ghi đè CSS
4. Nhập code SCSS
5. **Save changes** và **Purge all caches**

### Sửa đổi password toggle style

Chỉnh sửa file `scss/th_boost.scss`:

```scss
.password-toggle-btn {
    // Màu nút
    color: #6c757d;
    
    &:hover {
        color: #007bff;  // Màu khi hover
        background-color: rgba(0, 123, 255, 0.05);
    }
}
```

## 🐛 Troubleshooting

### Icon không hiển thị

**Nguyên nhân**: Cache chưa được xóa hoặc thiếu `iconsystem` config.

**Giải pháp**:
1. Kiểm tra `config.php` có dòng:
   ```php
   $THEME->iconsystem = \core\output\icon_system::FONTAWESOME;
   ```
2. Purge all caches
3. Hard refresh trình duyệt (Ctrl+Shift+R hoặc Cmd+Shift+R)

### Password toggle không hoạt động

**Nguyên nhân**: JavaScript chưa được load hoặc conflict.

**Giải pháp**:
1. Mở Console (F12) → kiểm tra lỗi JavaScript
2. Purge all caches
3. Kiểm tra file `core_renderer.php` đã override đúng

### CSS không áp dụng

**Nguyên nhân**: SCSS chưa được compile lại.

**Giải pháp**:
1. Purge all caches
2. Kiểm tra file `scss/th_boost.scss` có đúng cú pháp không
3. Kiểm tra `lib.php` → `theme_th_boost_get_extra_scss()` có load file đúng không

## 📚 Tài liệu tham khảo

- [Moodle Theme Development](https://docs.moodle.org/dev/Themes)
- [Boost Theme Documentation](https://docs.moodle.org/en/Boost_theme)
- [FontAwesome 4.7.0 Icons](https://fontawesome.com/v4/icons/)
- [Bootstrap 4 Documentation](https://getbootstrap.com/docs/4.6/)

## 🤝 Đóng góp

Nếu bạn muốn đóng góp cho theme này:

1. Fork repository
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📝 License

Theme này kế thừa license của Moodle:
- **GNU General Public License v3.0** hoặc cao hơn
- Xem chi tiết tại: http://www.gnu.org/copyleft/gpl.html

## 👨‍💻 Tác giả

- **TH Boost Theme**
- Copyright: 2025
- Dựa trên: Moodle Boost Theme

## 📞 Hỗ trợ

Nếu gặp vấn đề, hãy:
1. Kiểm tra phần **Troubleshooting** ở trên
2. Xem Moodle logs: **Site administration** → **Reports** → **Logs**
3. Tạo issue trên GitHub (nếu có)

## 🔄 Changelog

### Version 1.0.0 (2025-10-10)
- ✨ Initial release
- ✅ Password toggle functionality
- 🎨 Custom SCSS styling
- 📱 Responsive design
- 🔧 Inherit from Boost theme

## ⚙️ Nâng cao

### Debug Mode

Để bật debug mode và xem lỗi SCSS:

1. Vào **Site administration** → **Development** → **Debugging**
2. Set **Debug messages** = **DEVELOPER**
3. Check **Display debug messages**
4. Save changes

### Kiểm tra compiled CSS

File CSS được compile sẽ nằm trong:
```
moodledata/localcache/theme/<theme_revision>/th_boost/
```

### Override thêm methods

Trong `core_renderer.php`, bạn có thể override thêm:

```php
// Override header
public function standard_head_html() {
    $output = parent::standard_head_html();
    // Add custom code here
    return $output;
}

// Override footer
public function standard_footer_html() {
    $output = parent::standard_footer_html();
    // Add custom code here
    return $output;
}
```

---

**Happy Moodling! 🎓✨**
