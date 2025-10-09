# Hướng dẫn cài đặt Theme TH Boost

## 📋 Yêu cầu hệ thống
- Moodle 3.8 trở lên (tương thích với 3.9, 4.0+)
- PHP 7.2 trở lên
- Kết nối Internet (để load FontAwesome CDN)

## 🚀 Cài đặt chi tiết

### Phương pháp 1: Cài đặt thủ công (Đề xuất)

#### Bước 1: Upload theme
1. Copy toàn bộ thư mục `th_boost` vào `[moodledir]/theme/`
2. Đảm bảo cấu trúc thư mục:
   ```
   [moodledir]/theme/th_boost/
   ├── classes/
   ├── lang/
   ├── layout/
   ├── scss/
   ├── style/
   ├── config.php
   ├── lib.php
   ├── settings.php
   ├── version.php
   └── README.md
   ```

#### Bước 2: Set quyền (Linux/Unix)
```bash
cd /path/to/moodle/theme/
chmod -R 755 th_boost
chown -R www-data:www-data th_boost  # Hoặc user web server của bạn
```

#### Bước 3: Cài đặt từ Moodle Admin
1. Đăng nhập với tài khoản **Administrator**
2. Truy cập: **Site administration** (hoặc **Quản trị trang**)
3. Click vào **Notifications** (hoặc **Thông báo**)
4. Moodle sẽ phát hiện plugin mới
5. Click **"Upgrade Moodle database now"** (hoặc **"Nâng cấp cơ sở dữ liệu Moodle"**)
6. Đợi quá trình cài đặt hoàn tất
7. Click **Continue** (hoặc **Tiếp tục**)

#### Bước 4: Kích hoạt theme
1. **Site administration → Appearance → Themes → Theme selector**
   (hoặc **Quản trị trang → Giao diện → Chủ đề → Chọn chủ đề**)
2. Chọn **"TH Boost"** cho các thiết bị:
   - **Default theme** (Chủ đề mặc định)
   - **Mobile theme** (Chủ đề di động) - nếu muốn
   - **Tablet theme** (Chủ đề máy tính bảng) - nếu muốn
3. Click **"Save changes"** (hoặc **"Lưu thay đổi"**)

#### Bước 5: Clear cache (Quan trọng!)
1. **Site administration → Development → Purge all caches**
   (hoặc **Quản trị trang → Phát triển → Xóa tất cả cache**)
2. Click **"Purge all caches"**
3. Hoặc chạy CLI:
   ```bash
   php admin/cli/purge_caches.php
   ```

#### Bước 6: Kiểm tra cài đặt
1. Logout và reload trang
2. Vào trang Login
3. Kiểm tra:
   - ✅ Icons FontAwesome hiển thị
   - ✅ Nút "mắt" xuất hiện bên ô password
   - ✅ Click nút có thể hiển thị/ẩn mật khẩu

### Phương pháp 2: Cài đặt qua Git (Cho developers)

```bash
cd /path/to/moodle/theme/
git clone [repository-url] th_boost
cd th_boost
# Set quyền
chmod -R 755 .
```

Sau đó làm theo Bước 3-6 ở trên.

## ⚙️ Cấu hình Theme (Tùy chọn)

### Thay đổi màu chủ đề
1. **Site administration → Appearance → Themes → TH Boost**
2. Tab **"General"**
3. **Brand colour**: Chọn màu chính cho theme
4. **Save changes** và **Purge caches**

### Upload Background Image
1. **Site administration → Appearance → Themes → TH Boost**
2. Tab **"General"**
3. **Background image**: Upload hình nền
4. **Login page background image**: Upload hình nền trang login
5. **Save changes** và **Purge caches**

### Custom SCSS
1. **Site administration → Appearance → Themes → TH Boost**
2. Tab **"Advanced settings"**
3. **Raw initial SCSS**: Thêm variables SCSS
   ```scss
   // Example
   $primary: #0066cc;
   $font-size-base: 1rem;
   ```
4. **Raw SCSS**: Thêm custom styles
   ```scss
   // Example
   .navbar {
       background: linear-gradient(45deg, #667eea, #764ba2);
   }
   ```
5. **Save changes** và **Purge caches**

## 🔧 Troubleshooting (Xử lý sự cố)

### Lỗi: Theme không xuất hiện trong danh sách
**Nguyên nhân:**
- Tên thư mục không đúng
- Thiếu file version.php hoặc config.php
- Lỗi syntax trong PHP

**Giải pháp:**
1. Kiểm tra tên thư mục phải là `th_boost`
2. Kiểm tra file `version.php` có đúng format
3. Check PHP error logs:
   ```bash
   tail -f /var/log/apache2/error.log  # Apache
   tail -f /var/log/nginx/error.log    # Nginx
   ```

### Lỗi: Icons FontAwesome không hiển thị
**Nguyên nhân:**
- Không có kết nối Internet
- CDN bị chặn bởi firewall/CSP
- Cache chưa được xóa

**Giải pháp:**
1. Kiểm tra kết nối Internet
2. Check browser Console (F12) để xem lỗi
3. Purge all caches
4. Hard refresh: Ctrl + F5 (Windows) hoặc Cmd + Shift + R (Mac)
5. Check CSP headers nếu server có cấu hình security headers

### Lỗi: Password toggle không hoạt động
**Nguyên nhân:**
- JavaScript bị tắt
- Lỗi JavaScript conflicts
- Cache chưa được xóa

**Giải pháp:**
1. Enable JavaScript trong browser
2. Check browser Console (F12) để xem lỗi JavaScript
3. Purge all caches
4. Thử browser khác để test
5. Disable các plugins/extensions browser tạm thời

### Lỗi: Theme bị lỗi giao diện
**Nguyên nhân:**
- SCSS không compile được
- Thiếu parent theme (Boost)
- Cache cũ

**Giải pháp:**
1. Đảm bảo theme Boost có sẵn và active
2. Purge all caches
3. Check debug mode:
   ```php
   // config.php
   $CFG->debug = E_ALL;
   $CFG->debugdisplay = 1;
   ```
4. Rebuild theme cache:
   ```bash
   php admin/cli/purge_caches.php
   ```

### Lỗi: Permission denied
**Nguyên nhân:**
- Quyền file/folder không đúng

**Giải pháp (Linux/Unix):**
```bash
cd /path/to/moodle/theme/
chmod -R 755 th_boost
chown -R www-data:www-data th_boost  # Change user to your web server user
```

## 📊 Kiểm tra phiên bản

Xem phiên bản theme đã cài:
1. **Site administration → Plugins → Plugin overview**
2. Tìm **"TH Boost"** trong danh sách themes
3. Check version number và status

Hoặc CLI:
```bash
php admin/cli/plugin_info.php theme_th_boost
```

## 🔄 Nâng cấp Theme

### Từ phiên bản cũ lên mới
1. Backup theme cũ:
   ```bash
   cp -r th_boost th_boost.backup
   ```
2. Upload phiên bản mới (overwrite)
3. **Site administration → Notifications**
4. Click **"Upgrade Moodle database now"**
5. **Purge all caches**

## 🗑️ Gỡ cài đặt

1. Chuyển sang theme khác trước (Boost, Classic...)
2. **Site administration → Plugins → Plugin overview**
3. Tìm **"TH Boost"** và click **"Uninstall"**
4. Xác nhận uninstall
5. Xóa thư mục:
   ```bash
   rm -rf /path/to/moodle/theme/th_boost
   ```
6. **Purge all caches**

## 📞 Hỗ trợ

Nếu gặp vấn đề:
1. Check README.md để xem tài liệu
2. Check Moodle logs: **Site administration → Reports → Logs**
3. Enable debug mode để xem chi tiết lỗi
4. Check browser Console (F12)

## ✅ Checklist sau khi cài đặt

- [ ] Theme xuất hiện trong Theme selector
- [ ] Theme được chọn làm default
- [ ] Cache đã được purge
- [ ] FontAwesome icons hiển thị đúng
- [ ] Password toggle hoạt động ở trang login
- [ ] Theme hiển thị tốt trên mobile
- [ ] Không có lỗi JavaScript trong Console
- [ ] Không có PHP errors trong logs

---

**Chúc bạn cài đặt thành công! 🎉**

Nếu có vấn đề, hãy kiểm tra lại từng bước và đảm bảo đã **Purge all caches** sau mỗi thay đổi.
