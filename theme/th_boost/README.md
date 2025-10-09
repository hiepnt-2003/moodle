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

## 🛠️ Cấu trúc thư mục chi tiết

```
theme/th_boost/
├── classes/                            # Thư mục chứa các class PHP
│   └── output/                         # Thư mục output renderers
│       ├── core_renderer.php          # Override core renderer của Moodle
│       └── icon_system_fontawesome.php # FontAwesome icon system override
├── lang/                               # Thư mục ngôn ngữ
│   └── en/                             # Thư mục tiếng Anh
│       └── theme_th_boost.php         # Language strings (chuỗi dịch)
├── scss/                               # Thư mục SCSS
│   └── th_boost.scss                  # Custom SCSS cho password toggle
├── style/                              # Thư mục CSS đã compile
│   └── moodle.css                     # CSS đã compile sẵn
├── CHANGELOG.md                        # Lịch sử thay đổi
├── config.php                          # Theme configuration (cấu hình theme)
├── INSTALL.md                          # Hướng dẫn cài đặt
├── lib.php                             # Theme functions (các hàm xử lý)
├── settings.php                        # Admin settings (cài đặt quản trị)
├── version.php                         # Version info (thông tin phiên bản)
└── README.md                           # File này
```

---

## 📋 Chi tiết từng file và tác dụng

### 1. `version.php` - Thông tin phiên bản
**Tác dụng:** Khai báo thông tin plugin cho Moodle
```php
$plugin->component = 'theme_th_boost';      // Tên component
$plugin->version   = 2025100901;            // Phiên bản (YYYYMMDDXX)
$plugin->requires  = 2019111800;            // Yêu cầu Moodle 3.8+
$plugin->maturity  = MATURITY_STABLE;       // Độ ổn định
$plugin->release   = '1.0';                 // Release version
```
**Vai trò:** File đầu tiên Moodle đọc khi cài đặt/nâng cấp theme

---

### 2. `config.php` - Cấu hình theme
**Tác dụng:** Định nghĩa cấu hình cốt lõi của theme

```php
$THEME->name = 'th_boost';                           // Tên theme
$THEME->parents = ['boost'];                         // Theme cha (kế thừa từ Boost)
$THEME->rendererfactory = 'theme_overridden_renderer_factory';  // Cho phép override renderer
$THEME->prescsscallback = 'theme_th_boost_get_pre_scss';       // Callback SCSS trước khi compile
$THEME->scss = function($theme) { ... };             // Callback SCSS chính
$THEME->extrascsscallback = 'theme_th_boost_get_extra_scss';   // Callback SCSS bổ sung
$THEME->precompiledcsscallback = 'theme_th_boost_get_precompiled_css'; // CSS đã compile
```

**Các thành phần quan trọng:**
- `parents`: Kế thừa từ theme Boost (child theme)
- `rendererfactory`: Cho phép override các renderer của Moodle
- `scss callbacks`: Xử lý SCSS/CSS động
- `usefallback`: Sử dụng fallback khi không tìm thấy template
- `haseditswitch`: Hiển thị nút chuyển chế độ edit

---

### 3. `lib.php` - Các hàm xử lý chính
**Tác dụng:** Chứa các hàm callback và xử lý logic

#### Hàm `theme_th_boost_get_main_scss_content($theme)`
- **Mục đích:** Lấy nội dung SCSS chính từ preset
- **Luồng:**
  1. Kiểm tra preset đã chọn (default.scss/plain.scss/custom)
  2. Load file SCSS từ theme Boost
  3. Trả về nội dung SCSS

#### Hàm `theme_th_boost_get_pre_scss($theme)`
- **Mục đích:** Thêm biến SCSS trước khi compile
- **Luồng:**
  1. Đọc cài đặt từ admin (brandcolor, etc.)
  2. Chuyển đổi thành biến SCSS ($brandcolor: #xxx)
  3. Thêm custom pre-SCSS từ admin settings

#### Hàm `theme_th_boost_get_extra_scss($theme)`
- **Mục đích:** Thêm SCSS bổ sung (password toggle styles)
- **Luồng:**
  1. Load file `scss/th_boost.scss` (password toggle styles)
  2. Thêm custom SCSS từ admin settings
  3. Trả về chuỗi SCSS hoàn chỉnh

#### Hàm `theme_th_boost_get_precompiled_css()`
- **Mục đích:** Trả về CSS đã compile sẵn
- **Luồng:** Load file `style/moodle.css`

#### Hàm `theme_th_boost_pluginfile(...)`
- **Mục đích:** Xử lý file uploads (logo, background images)
- **Luồng:** Kiểm tra quyền và serve file từ file storage

---

### 4. `settings.php` - Cài đặt quản trị
**Tác dụng:** Tạo trang cài đặt trong Admin

**Các setting có sẵn:**
1. **Preset** - Chọn theme preset (default.scss/plain.scss)
2. **Preset files** - Upload custom preset files
3. **Background image** - Ảnh nền toàn site
4. **Login background image** - Ảnh nền trang login
5. **Brand color** - Màu chủ đạo của theme
6. **Raw SCSS Pre** - Custom SCSS variables
7. **Raw SCSS** - Custom SCSS styles

**Callback quan trọng:**
```php
$setting->set_updatedcallback('theme_reset_all_caches');
```
- Tự động xóa cache khi thay đổi setting

---

### 5. `classes/output/core_renderer.php` - Override Core Renderer
**Tác dụng:** Override các phương thức render của Moodle

#### Phương thức `standard_end_of_body_html()`
**Mục đích:** Thêm code vào cuối body HTML

**Luồng hoạt động:**
1. Gọi parent method (kế thừa từ Boost)
2. **Thêm FontAwesome CDN:**
   ```javascript
   <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js">
   ```
3. **Thêm JavaScript password toggle:**
   - Tìm tất cả input type="password"
   - Tạo wrapper container cho mỗi field
   - Thêm button toggle với icon eye
   - Add event listener để toggle show/hide password

#### Phương thức `standard_head_html()`
**Mục đích:** Thêm code vào head HTML

**Luồng hoạt động:**
1. Gọi parent method
2. **Thêm FontAwesome CSS:**
   ```html
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   ```

---

### 6. `classes/output/icon_system_fontawesome.php` - Icon System
**Tác dụng:** Map icons Moodle sang FontAwesome icons

**Ví dụ mapping:**
```php
'core:i/user' => 'fa-user',
'core:i/edit' => 'fa-pencil-alt',
'core:i/delete' => 'fa-trash',
'core:i/search' => 'fa-search',
```

**Lưu ý:** File này đã được tạo nhưng không được sử dụng trong `config.php` để tương thích với Moodle 3.8/3.9. FontAwesome vẫn hoạt động qua CDN.

---

### 7. `scss/th_boost.scss` - Custom SCSS
**Tác dụng:** Định nghĩa styles cho password toggle

**Các class chính:**
- `.password-toggle-container` - Wrapper cho password field
- `.password-toggle-btn` - Button toggle password
- Responsive styles cho mobile/tablet
- Hover/focus/active effects
- FontAwesome icon enhancements

---

### 8. `lang/en/theme_th_boost.php` - Language Strings
**Tác dụng:** Chứa các chuỗi ngôn ngữ

**Các string quan trọng:**
```php
$string['pluginname'] = 'TH Boost';
$string['configtitle'] = 'TH Boost';
$string['preset'] = 'Theme preset';
$string['brandcolor'] = 'Brand colour';
$string['showpassword'] = 'Show password';
$string['hidepassword'] = 'Hide password';
```

---

## 🔄 Luồng hoạt động của theme

### **A. Khi Moodle khởi động:**

```
1. Moodle load version.php
   ↓
2. Kiểm tra theme đã cài đặt chưa
   ↓
3. Load config.php để đọc cấu hình theme
   ↓
4. Kiểm tra parent theme (boost)
   ↓
5. Load lib.php để có các hàm callback
   ↓
6. Theme sẵn sàng sử dụng
```

---

### **B. Khi render một trang (Page Rendering Flow):**

```
1. Moodle bắt đầu render page
   ↓
2. Gọi core_renderer->standard_head_html()
   │  └─> Load FontAwesome CSS từ CDN
   ↓
3. Load SCSS/CSS:
   │  a) Gọi theme_th_boost_get_pre_scss()
   │     └─> Thêm variables ($brandcolor, custom pre-SCSS)
   │  
   │  b) Gọi theme_th_boost_get_main_scss_content()
   │     └─> Load preset SCSS từ Boost
   │  
   │  c) Gọi theme_th_boost_get_extra_scss()
   │     └─> Load scss/th_boost.scss (password toggle styles)
   │     └─> Thêm custom SCSS từ admin
   │  
   │  d) Compile tất cả SCSS thành CSS
   │  
   │  e) Cache CSS (Moodle sẽ cache để tăng tốc)
   ↓
4. Render page content (kế thừa từ Boost)
   ↓
5. Gọi core_renderer->standard_end_of_body_html()
   │  a) Load FontAwesome JavaScript từ CDN
   │  b) Inject password toggle JavaScript
   │     └─> Tìm tất cả input[type="password"]
   │     └─> Tạo wrapper và button toggle
   │     └─> Add event listeners
   ↓
6. Page hoàn tất render
```

---

### **C. Password Toggle Flow (Chi tiết):**

```
1. User mở trang có password field (vd: login page)
   ↓
2. Browser load page HTML
   ↓
3. core_renderer inject JavaScript vào end of body
   ↓
4. DOMContentLoaded event triggers
   ↓
5. JavaScript tìm tất cả input[type="password"]
   ↓
6. Cho mỗi password field:
   │  a) Check nếu đã có wrapper -> skip
   │  b) Tạo div.password-toggle-container
   │  c) Wrap password field
   │  d) Tạo button.password-toggle-btn
   │  e) Thêm icon fa-eye
   │  f) Append button vào wrapper
   │  g) Add click event listener
   ↓
7. User click button toggle:
   │  a) Đổi type: password ↔ text
   │  b) Đổi icon: fa-eye ↔ fa-eye-slash
   │  c) Đổi title: "Show password" ↔ "Hide password"
   ↓
8. Password hiển thị/ẩn tương ứng
```

---

### **D. Admin Settings Flow:**

```
1. Admin vào Site Administration → Appearance → Themes → TH Boost
   ↓
2. Moodle load settings.php
   ↓
3. Tạo admin_settingpage với các settings
   ↓
4. Admin thay đổi setting (vd: brandcolor)
   ↓
5. Click "Save changes"
   ↓
6. Setting được lưu vào database (config_plugins table)
   ↓
7. Callback theme_reset_all_caches() được gọi
   │  └─> Xóa tất cả theme caches
   ↓
8. Lần render tiếp theo:
   │  a) theme_th_boost_get_pre_scss() đọc setting mới
   │  b) Compile SCSS lại với giá trị mới
   │  c) Cache CSS mới
   ↓
9. Theme cập nhật với setting mới
```

---

### **E. SCSS Compilation Flow:**

```
1. Moodle cần compile SCSS (first load hoặc sau khi clear cache)
   ↓
2. Gọi pre-SCSS callback:
   │  └─> theme_th_boost_get_pre_scss($theme)
   │      └─> Tạo variables: $brandcolor, etc.
   │      └─> Thêm custom pre-SCSS từ admin
   ↓
3. Gọi main SCSS callback:
   │  └─> theme_th_boost_get_main_scss_content($theme)
   │      └─> Load default.scss hoặc plain.scss từ Boost
   ↓
4. Gọi extra SCSS callback:
   │  └─> theme_th_boost_get_extra_scss($theme)
   │      └─> Load scss/th_boost.scss
   │      └─> Thêm custom SCSS từ admin
   ↓
5. Kết hợp tất cả SCSS:
   │  [Pre-SCSS] + [Main SCSS] + [Extra SCSS]
   ↓
6. Compile SCSS → CSS bằng SCSS compiler
   ↓
7. Minify CSS (nếu production mode)
   ↓
8. Cache CSS vào Moodle data directory
   ↓
9. Serve CSS cho browser
```

---

## 🎯 Điểm mạnh của kiến trúc

### 1. **Child Theme Pattern**
- Kế thừa từ Boost → không phá vỡ core
- Dễ nâng cấp Moodle mà không ảnh hưởng theme
- Override chỉ những gì cần thiết

### 2. **Renderer Override**
- Override `core_renderer` để inject custom code
- Không sửa core files
- Tương thích với các plugin khác

### 3. **SCSS Callbacks**
- Dynamic SCSS compilation
- Admin có thể tùy chỉnh colors, styles
- Custom SCSS được merge tự động

### 4. **Progressive Enhancement**
- FontAwesome load từ CDN (fallback nếu offline)
- Password toggle enhance UX nhưng không break functionality
- JavaScript chỉ chạy khi DOM ready

### 5. **Caching Strategy**
- CSS được cache để tăng performance
- Chỉ recompile khi có thay đổi
- Cache bị clear khi admin thay đổi settings

---

## 🔍 Debug và Development

### Kiểm tra SCSS compilation:
1. Enable theme designer mode:
   ```
   Site admin → Appearance → Themes → Theme designer mode: ON
   ```
2. Mỗi request sẽ recompile SCSS → dễ debug

### Kiểm tra JavaScript:
1. Mở Browser Console (F12)
2. Check errors trong Console tab
3. Debug password toggle code

### Clear cache:
```
Site admin → Development → Purge all caches
```

### Xem compiled CSS:
```
moodledata/localcache/theme/th_boost/css/
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
