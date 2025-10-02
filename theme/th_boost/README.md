# TH Boost Theme

## Mô tả
TH Boost là một theme con kế thừa từ theme Boost của Moodle với hỗ trợ FontAwesome icons.

## Tính năng
- Kế thừa tất cả tính năng từ theme Boost
- Hỗ trợ FontAwesome icons
- Dễ dàng tùy chỉnh với SCSS
- Tương thích với Moodle 3.9+

## Cài đặt
1. Sao chép thư mục `th_boost` vào thư mục `theme/` của Moodle
2. Đăng nhập với tài khoản admin
3. Truy cập: Site administration → Notifications
4. Làm theo hướng dẫn để cài đặt plugin

## Kích hoạt theme
1. Đăng nhập với tài khoản admin
2. Truy cập: Site administration → Appearance → Themes → Theme selector
3. Chọn "TH Boost" làm theme mặc định

## Sử dụng FontAwesome Icons
Theme này đã được cấu hình để sử dụng FontAwesome icons. Bạn có thể sử dụng các icon trong code như sau:

### Trong PHP/Mustache templates:
```html
<i class="fa fa-user"></i>
<i class="fas fa-home"></i>
<i class="far fa-heart"></i>
```

### Trong SCSS:
```scss
.my-class::before {
    font-family: 'FontAwesome';
    content: '\f007'; // User icon
}
```

## Tùy chỉnh
### Custom SCSS
Bạn có thể thêm custom SCSS vào hai file:
- `scss/pre.scss` - Load trước main SCSS
- `scss/post.scss` - Load sau main SCSS

### Thay đổi màu sắc
1. Truy cập: Site administration → Appearance → Themes → TH Boost
2. Cấu hình Brand colour và các thiết lập khác

## Cấu trúc thư mục
```
th_boost/
├── config.php          # Cấu hình theme
├── version.php         # Thông tin version
├── lib.php             # Functions
├── README.md           # File này
├── lang/
│   └── en/
│       └── theme_th_boost.php
└── scss/
    ├── pre.scss        # Pre SCSS
    └── post.scss       # Post SCSS
```

## Hỗ trợ
Để được hỗ trợ, vui lòng liên hệ với admin hoặc developer.

## License
GNU GPL v3 or later
