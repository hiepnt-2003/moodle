# 🏠 SETUP MODERN HOMEPAGE - Hướng dẫn Chi Tiết

## 📋 Mục Lục
1. [Chuẩn bị](#chuẩn-bị)
2. [Hướng dẫn từng bước](#hướng-dẫn-từng-bước)
3. [Vị trí Block](#vị-trí-block)
4. [HTML Code đầy đủ](#html-code-đầy-đủ)
5. [Tùy chỉnh](#tùy-chỉnh)
6. [Troubleshooting](#troubleshooting)

---

## ✅ Chuẩn Bị

### Yêu cầu:
- [x] Theme TH NewBoost đã được cài đặt
- [x] Theme TH NewBoost đã được kích hoạt
- [x] Cache đã được clear
- [x] Đăng nhập với tài khoản Admin

### Kiểm tra:
```
1. Vào: Site administration → Appearance → Themes → Theme selector
2. Verify: Default theme = "TH NewBoost"
3. Click: "Clear theme caches"
```

---

## 🎯 Hướng Dẫn Từng Bước

### **BƯỚC 1: Vào Dashboard**

```
1. Click vào "Dashboard" trong sidebar
2. URL sẽ là: http://your-site/my/
```

---

### **BƯỚC 2: Bật Edit Mode**

```
Nếu thấy nút "Customise this page" hoặc "Turn editing on":
→ Click vào nút đó

Nếu không thấy:
→ Bạn đã ở edit mode rồi
```

---

### **BƯỚC 3: Thêm HTML Block**

#### 3.1. Tìm nút "Add a block"
```
Location: Sidebar bên trái, cuối cùng
Icon: 📦 hoặc [+]
Text: "Add a block"
```

#### 3.2. Click "Add a block"
```
→ Dropdown menu sẽ xuất hiện
```

#### 3.3. Chọn "HTML"
```
Từ danh sách → Click "HTML"
→ Block mới xuất hiện bên phải (main content area)
```

---

### **BƯỚC 4: Configure HTML Block**

#### 4.1. Tìm block vừa tạo
```
Block title: "(new HTML block)"
Location: Bên phải, trong main content area
```

#### 4.2. Click biểu tượng ⚙️ (Configure)
```
Hoặc click text: "Configure (new HTML block) block"
→ Form configuration sẽ mở ra
```

#### 4.3. Điền Form

**📝 Block title:**
```
Option 1: Để trống (không hiển thị title)
Option 2: "Modern Homepage"
Option 3: "Welcome"

Recommended: Để trống
```

**📝 Content:**
```
Scroll xuống → Tìm text area lớn có label "Content"
→ Paste toàn bộ HTML code từ section dưới đây
```

**📝 Where this block appears:**

Scroll xuống tìm section này:

```
☑️ Display on page types: Dashboard page
☑️ Original block location: Dashboard page
```

**📝 Default region:**
```
Select: "Content" (hoặc "Main content")
KHÔNG chọn: "Side-pre" hoặc "Side-post"
```

**📝 Default weight:**
```
Nhập: -10
(Để block hiển thị ở đầu trang)
```

**📝 On this page:**

```
☑️ Visible: Yes
   Region: Content
   Weight: -10
```

---

### **BƯỚC 5: Save Changes**

```
1. Scroll xuống cuối form
2. Click nút "Save changes"
3. Đợi page reload
```

---

### **BƯỚC 6: Thoát Edit Mode**

```
Click nút "Stop customising this page"
hoặc "Turn editing off"

Location: Góc trên bên phải
```

---

### **BƯỚC 7: Xem Kết Quả**

```
1. Refresh trang: Press F5
2. Scroll xuống xem 4 sections:
   ✅ Hero Section (gradient purple)
   ✅ Features Section (6 cards)
   ✅ Stats Section (4 numbers)
   ✅ CTA Section (call-to-action)
```

---

## 📍 Vị Trí Block

### **Hiện tại (BEFORE):**

```
┌─────────────┬────────────────────────────────┐
│  Sidebar    │   Main Content Area            │
│             │                                │
│ Dashboard   │   [My Data Block]              │
│ Site home   │                                │
│ Calendar    │   ← THÊM HTML BLOCK VÀO ĐÂY   │
│ ...         │                                │
│             │                                │
│ Add a block │                                │
└─────────────┴────────────────────────────────┘
```

### **Sau khi thêm (AFTER):**

```
┌─────────────┬────────────────────────────────┐
│  Sidebar    │   Main Content Area            │
│             │                                │
│ Dashboard   │   🚀 HERO SECTION              │
│ Site home   │   (Full width gradient)        │
│ Calendar    │                                │
│ ...         │   ⭐ FEATURES SECTION          │
│             │   (6 cards in grid)            │
│             │                                │
│             │   📊 STATS SECTION             │
│             │   (4 statistics)               │
│             │                                │
│             │   🎓 CTA SECTION               │
│             │   (Call-to-action button)      │
│             │                                │
│             │   [My Data Block]              │
│ Add a block │   (optional - có thể ẩn)       │
└─────────────┴────────────────────────────────┘
```

---

## 📄 HTML Code Đầy Đủ

Copy toàn bộ code dưới đây và paste vào field "Content":

```html
<!-- ========================================
     SECTION 1: HERO SECTION
     Banner chào mừng với gradient background
     ======================================== -->
<div class="modern-hero">
    <div class="hero-content">
        <h1><i class="fas fa-rocket"></i> Chào mừng đến với Learning Platform</h1>
        <p>Nền tảng học tập trực tuyến hiện đại với hàng ngàn khóa học chất lượng cao</p>
        <div class="hero-buttons">
            <a href="/my/" class="btn btn-light btn-lg">
                <i class="fas fa-play-circle"></i> Bắt đầu học
            </a>
            <a href="/course/" class="btn btn-outline-light btn-lg">
                <i class="fas fa-search"></i> Khám phá khóa học
            </a>
        </div>
    </div>
</div>

<!-- ========================================
     SECTION 2: FEATURES SECTION
     6 tính năng nổi bật với cards
     ======================================== -->
<div class="modern-features">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-star"></i> Tính năng nổi bật</h2>
            <p>Những gì làm nên sự khác biệt của chúng tôi</p>
        </div>
        
        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h3>Video HD</h3>
                <p>Học với video chất lượng cao, âm thanh chuẩn, phụ đề đầy đủ</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3>Chứng chỉ</h3>
                <p>Nhận chứng chỉ được công nhận sau khi hoàn thành khóa học</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Cộng đồng</h3>
                <p>Tham gia cộng đồng học viên năng động và nhiệt huyết</p>
            </div>
            
            <!-- Feature 4 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Hỗ trợ 24/7</h3>
                <p>Đội ngũ hỗ trợ luôn sẵn sàng giúp đỡ bạn mọi lúc</p>
            </div>
            
            <!-- Feature 5 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Học mọi nơi</h3>
                <p>Truy cập từ mọi thiết bị: máy tính, tablet, điện thoại</p>
            </div>
            
            <!-- Feature 6 -->
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

<!-- ========================================
     SECTION 3: STATS SECTION
     Thống kê số liệu với gradient background
     ======================================== -->
<div class="modern-stats">
    <div class="container">
        <div class="stats-grid">
            <!-- Stat 1 -->
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="stat-number">10,000+</div>
                <div class="stat-label">Học viên</div>
            </div>
            
            <!-- Stat 2 -->
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-number">500+</div>
                <div class="stat-label">Khóa học</div>
            </div>
            
            <!-- Stat 3 -->
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-number">200+</div>
                <div class="stat-label">Giảng viên</div>
            </div>
            
            <!-- Stat 4 -->
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-star"></i></div>
                <div class="stat-number">4.8/5</div>
                <div class="stat-label">Đánh giá</div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================
     SECTION 4: CTA SECTION
     Call-to-action cuối trang
     ======================================== -->
<div class="modern-cta">
    <div class="cta-content">
        <h2><i class="fas fa-graduation-cap"></i> Sẵn sàng bắt đầu?</h2>
        <p>Tham gia cộng đồng học viên của chúng tôi và bắt đầu hành trình học tập ngay hôm nay!</p>
        <a href="/login/signup.php" class="btn btn-lg">
            Đăng ký ngay <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>
```

---

## 🎨 Tùy Chỉnh

### **1. Thay đổi Text**

```html
<!-- Hero Section -->
<h1>Tiêu đề của bạn</h1>
<p>Mô tả của bạn</p>

<!-- Features -->
<h3>Tên tính năng của bạn</h3>
<p>Mô tả tính năng</p>

<!-- Stats -->
<div class="stat-number">Số của bạn</div>
<div class="stat-label">Label của bạn</div>

<!-- CTA -->
<h2>Call to action của bạn</h2>
<p>Description</p>
```

### **2. Thay đổi Links**

```html
<!-- Hero buttons -->
<a href="/your-link" class="btn btn-light btn-lg">
    Button Text
</a>

<!-- CTA button -->
<a href="/register" class="btn btn-lg">
    Sign Up
</a>
```

### **3. Thay đổi Icons**

Tìm icons tại: https://fontawesome.com/icons

```html
<!-- Thay class icon -->
<i class="fas fa-rocket"></i>        <!-- Rocket -->
<i class="fas fa-graduation-cap"></i>  <!-- Education -->
<i class="fas fa-laptop-code"></i>   <!-- Programming -->
<i class="fas fa-paint-brush"></i>   <!-- Design -->
<i class="fas fa-camera"></i>        <!-- Photography -->
<i class="fas fa-music"></i>         <!-- Music -->
```

### **4. Thay đổi Số lượng Cards**

```html
<!-- Chỉ 3 cards -->
<div class="features-grid">
    <div class="feature-card">...</div>
    <div class="feature-card">...</div>
    <div class="feature-card">...</div>
</div>

<!-- 4 cards -->
<!-- Grid sẽ tự động responsive -->

<!-- 6 cards (mặc định) -->
```

### **5. Ẩn "My Data Block"**

```
1. Click ⚙️ trên "My Data Block"
2. Chọn "Hide My Data Block block"
```

### **6. Thay đổi Màu Gradient**

Edit file: `theme/th_newboost/scss/pre.scss`

```scss
$gradient-start: #your-color;  // Thay #667eea
$gradient-end: #your-color;    // Thay #764ba2
```

Sau đó:
```
Site administration → Development → Purge all caches
```

---

## 🐛 Troubleshooting

### ❌ **Problem 1: Block không hiển thị**

**Nguyên nhân:**
- Region không đúng (chọn Side thay vì Content)
- Display page types không đúng
- Block bị ẩn

**Giải pháp:**
```
1. Re-configure block:
   - Region: Content
   - Display on: Dashboard page
   - Visible: Yes
   
2. Clear cache:
   Site admin → Development → Purge all caches
   
3. Refresh browser: Ctrl+Shift+R
```

---

### ❌ **Problem 2: CSS không được apply**

**Nguyên nhân:**
- Theme không phải TH NewBoost
- Cache chưa clear
- Browser cache

**Giải pháp:**
```
1. Verify theme:
   Site admin → Appearance → Themes → Theme selector
   → Chọn TH NewBoost
   
2. Clear theme caches:
   Click "Clear theme caches"
   
3. Clear browser cache:
   Ctrl+Shift+Delete → Clear browsing data
   
4. Hard refresh:
   Ctrl+Shift+R (Windows)
   Cmd+Shift+R (Mac)
```

---

### ❌ **Problem 3: Icons không hiển thị**

**Nguyên nhân:**
- FontAwesome chưa được load
- Icon class sai

**Giải pháp:**
```
1. Verify FontAwesome trong config.php:
   $THEME->iconsystem = \core\output\icon_system_fontawesome::class;
   
2. Clear all caches
   
3. Check icon class:
   ✅ <i class="fas fa-star"></i>
   ❌ <i class="fa fa-star"></i> (FA4 - có thể không work)
   
4. Check browser console (F12):
   Xem có error về fonts không
```

---

### ❌ **Problem 4: Block bị sidebar che**

**Nguyên nhân:**
- Width của content area bị giới hạn

**Giải pháp:**
```html
<!-- Thêm negative margin -->
<div class="modern-hero" style="margin: 0 -15px;">
    ...
</div>

<!-- Hoặc full width -->
<div class="modern-hero" style="margin-left: -50vw; margin-right: -50vw; left: 50%; position: relative; right: 50%; width: 100vw;">
    ...
</div>
```

---

### ❌ **Problem 5: Layout bị vỡ trên mobile**

**Nguyên nhân:**
- Grid không responsive
- Font size quá lớn

**Giải pháp:**
```
Theme đã có responsive CSS!
Nếu vẫn bị vỡ, thêm:

<style>
@media (max-width: 768px) {
    .modern-hero h1 {
        font-size: 2rem !important;
    }
    .features-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>
```

---

### ❌ **Problem 6: Hover effects không hoạt động**

**Nguyên nhân:**
- CSS chưa được compile
- Browser không support transforms

**Giải pháp:**
```
1. Clear all caches
2. Test trên browser khác (Chrome, Firefox)
3. Check browser console for CSS errors
4. Verify post.scss đã compile
```

---

## ✅ Checklist Hoàn Thành

### **Pre-Setup:**
- [ ] Theme TH NewBoost installed
- [ ] Theme TH NewBoost activated
- [ ] All caches cleared
- [ ] Logged in as Admin

### **During Setup:**
- [ ] Dashboard accessed
- [ ] Edit mode enabled
- [ ] HTML block added
- [ ] HTML code pasted (all 4 sections)
- [ ] Block title configured
- [ ] Region = Content
- [ ] Weight = -10
- [ ] Display on = Dashboard page
- [ ] Changes saved

### **Post-Setup:**
- [ ] Edit mode exited
- [ ] Page refreshed
- [ ] 4 sections visible
- [ ] Hover effects working
- [ ] Icons displaying
- [ ] Buttons clickable
- [ ] Responsive on mobile

---

## 📊 Kết Quả Mong Đợi

### **Visual Structure:**

```
🎨 Navbar (Gradient Purple)
├─────────────────────────────────┐
│ 🚀 HERO SECTION                 │
│ • Gradient background           │
│ • Animated grid pattern         │
│ • Pulse icon animation          │
│ • 2 CTA buttons                 │
├─────────────────────────────────┤
│ ⭐ FEATURES SECTION             │
│ • 6 cards in grid               │
│ • 3D hover effects              │
│ • Icon scale + rotate           │
│ • Border animation              │
├─────────────────────────────────┤
│ 📊 STATS SECTION                │
│ • Gradient background           │
│ • 4 statistics                  │
│ • Bounce animation icons        │
│ • Big numbers                   │
├─────────────────────────────────┤
│ 🎓 CTA SECTION                  │
│ • Full width gradient           │
│ • Decorative circles            │
│ • Animated button               │
│ • Arrow slide effect            │
└─────────────────────────────────┘
```

### **Animations:**
- ✅ Hero: Pulse icon + moving background grid
- ✅ Features: Card lift + icon rotate on hover
- ✅ Stats: Icon bounce continuously
- ✅ CTA: Arrow slides right on hover

### **Responsive:**
- ✅ Desktop: 3 columns grid
- ✅ Tablet: 2 columns grid
- ✅ Mobile: 1 column stack

---

## 🎯 Next Steps

1. **Test thoroughly:**
   - Desktop view
   - Tablet view (resize browser)
   - Mobile view
   - Different browsers

2. **Customize content:**
   - Replace text with your content
   - Update links to real pages
   - Change numbers in stats
   - Modify icons if needed

3. **Optional enhancements:**
   - Add more sections
   - Change colors
   - Add custom images
   - Integrate with courses

---

## 📞 Support

Nếu gặp vấn đề:

1. **Read this guide again** - Kiểm tra từng bước
2. **Check Troubleshooting** - Tìm solution cho error
3. **Clear all caches** - 90% problems solved by this
4. **Contact admin** - Nếu vẫn không work

---

## 📝 Change Log

**v1.0 (2025-10-02)**
- Initial release
- 4 main sections
- Full responsive
- FontAwesome icons
- Detailed troubleshooting

---

**Setup time: ~5 minutes**
**Difficulty: ⭐⭐☆☆☆ (Easy)**

**Chúc bạn thành công! 🎉**
