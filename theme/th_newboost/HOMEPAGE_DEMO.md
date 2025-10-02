# Modern Homepage Demo - TH NewBoost Theme

Sao chép HTML code dưới đây và paste vào HTML Block trên homepage của bạn:

## 📋 Full Homepage HTML Code

```html
<!-- Modern Hero Section -->
<div class="modern-hero">
    <div class="hero-content">
        <h1><i class="fas fa-rocket"></i> Chào mừng đến với Learning Platform</h1>
        <p>Nền tảng học tập trực tuyến hiện đại với hàng ngàn khóa học chất lượng cao</p>
        <div class="hero-buttons">
            <a href="#" class="btn btn-light btn-lg">
                <i class="fas fa-play-circle"></i> Bắt đầu học
            </a>
            <a href="#" class="btn btn-outline-light btn-lg">
                <i class="fas fa-search"></i> Khám phá khóa học
            </a>
        </div>
    </div>
</div>

<!-- Modern Features Section -->
<div class="modern-features">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-star"></i> Tính năng nổi bật</h2>
            <p>Những gì làm nên sự khác biệt của chúng tôi</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h3>Video HD</h3>
                <p>Học với video chất lượng cao, âm thanh chuẩn, phụ đề đầy đủ</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3>Chứng chỉ</h3>
                <p>Nhận chứng chỉ được công nhận sau khi hoàn thành khóa học</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Cộng đồng</h3>
                <p>Tham gia cộng đồng học viên năng động và nhiệt huyết</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Hỗ trợ 24/7</h3>
                <p>Đội ngũ hỗ trợ luôn sẵn sàng giúp đỡ bạn mọi lúc</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Học mọi nơi</h3>
                <p>Truy cập từ mọi thiết bị: máy tính, tablet, điện thoại</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-infinity"></i>
                </div>
                <h3>Học vô hạn</h3>
                <p>Truy cập không giới hạn vào tất cả khóa học đã đăng ký</p>
            </div>
        </div>
    </div>
</div>

<!-- Modern Stats Section -->
<div class="modern-stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="stat-number">10,000+</div>
                <div class="stat-label">Học viên</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-number">500+</div>
                <div class="stat-label">Khóa học</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-number">200+</div>
                <div class="stat-label">Giảng viên</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-star"></i></div>
                <div class="stat-number">4.8/5</div>
                <div class="stat-label">Đánh giá</div>
            </div>
        </div>
    </div>
</div>

<!-- Modern CTA Section -->
<div class="modern-cta">
    <div class="cta-content">
        <h2><i class="fas fa-graduation-cap"></i> Sẵn sàng bắt đầu?</h2>
        <p>Tham gia cộng đồng học viên của chúng tôi và bắt đầu hành trình học tập ngay hôm nay!</p>
        <a href="#" class="btn btn-lg">
            Đăng ký ngay <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
```

## 🎯 Hướng dẫn sử dụng

### 1. Kích hoạt Theme
- Site administration → Appearance → Themes → Theme selector
- Chọn "TH NewBoost"
- Clear cache

### 2. Thêm HTML Block
- Vào Dashboard/Homepage
- Turn editing on
- Add a block → HTML
- Paste toàn bộ HTML code ở trên
- Save changes

### 3. Tùy chỉnh

#### Thay đổi màu gradient:
Vào `scss/pre.scss` và sửa:
```scss
$gradient-start: #667eea;  // Màu của bạn
$gradient-end: #764ba2;    // Màu của bạn
```

#### Thay đổi nội dung:
- Sửa text trong HTML
- Thay đổi icons (tìm mã icon tại: https://fontawesome.com/icons)
- Thay đổi số liệu trong Stats Section

## ✨ Các hiệu ứng có sẵn

### Hero Section:
- ✅ Animated background grid
- ✅ Pulse animation cho icons
- ✅ Hover effects cho buttons

### Features Cards:
- ✅ 3D hover effect (lift up)
- ✅ Border color animation
- ✅ Icon scale và rotate animation
- ✅ Box shadow transition

### Stats Section:
- ✅ Bounce animation cho icons
- ✅ Gradient background
- ✅ Responsive grid

### CTA Section:
- ✅ Decorative circles
- ✅ Button hover với icon transition
- ✅ Text shadow effects

## 🎨 Class names có sẵn

Bạn có thể sử dụng các class sau trong code của mình:

- `.modern-hero` - Hero section với gradient
- `.modern-features` - Features grid section
- `.feature-card` - Individual feature card
- `.modern-stats` - Statistics section
- `.modern-cta` - Call-to-action section
- `.modern-course-card` - Course card design
- `.gradient-text` - Text với gradient color
- `.modern-shadow` - Box shadow effect
- `.modern-radius` - Border radius lớn

## 📱 Responsive

Theme tự động responsive cho:
- Desktop (>768px): Full layout
- Tablet (768px-576px): 2 columns
- Mobile (<576px): 1 column, adjusted font sizes

## 🚀 Performance Tips

1. **Clear cache** sau mỗi lần thay đổi SCSS
2. **Optimize images** trước khi upload
3. **Use lazy loading** cho images nếu có nhiều
4. **Minimize custom CSS** trong admin panel

---

**Enjoy your beautiful modern homepage! 🎉**
