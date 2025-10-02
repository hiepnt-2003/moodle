# 🚀 Quick Start Guide - TH NewBoost Theme

## ⚡ Cài đặt nhanh trong 5 phút

### Bước 1: Cài đặt Theme (2 phút)

1. **Truy cập Notifications**
   ```
   Site administration → Notifications
   ```

2. **Click "Upgrade Moodle database now"**

3. **Đợi quá trình cài đặt hoàn tất**

### Bước 2: Kích hoạt Theme (1 phút)

1. **Truy cập Theme Selector**
   ```
   Site administration → Appearance → Themes → Theme selector
   ```

2. **Chọn TH NewBoost cho các device types:**
   - Default: TH NewBoost ✅
   - Mobile: TH NewBoost ✅
   - Tablet: TH NewBoost ✅
   - Legacy: TH NewBoost ✅

3. **Click "Clear theme caches"**

### Bước 3: Tạo Modern Homepage (2 phút)

1. **Truy cập Dashboard**
   ```
   Dashboard hoặc Home page
   ```

2. **Turn editing on**

3. **Add a block → HTML**

4. **Copy HTML từ file `HOMEPAGE_DEMO.md` và paste vào**

5. **Save changes**

6. **Turn editing off** để xem kết quả

---

## 🎨 Tùy chỉnh màu sắc

### Thay đổi Brand Color

```
Site administration → Appearance → Themes → TH NewBoost
→ Brand colour: Chọn màu của bạn
→ Save changes
```

### Thay đổi Gradient Colors

Edit file `scss/pre.scss`:
```scss
$gradient-start: #YOUR_COLOR;
$gradient-end: #YOUR_COLOR;
```

Sau đó clear cache:
```
Site administration → Development → Purge all caches
```

---

## 📋 Checklist hoàn thành

- [ ] Theme đã được cài đặt
- [ ] Theme đã được kích hoạt cho tất cả devices
- [ ] Cache đã được clear
- [ ] Homepage HTML block đã được thêm
- [ ] FontAwesome icons hiển thị đúng
- [ ] Animations hoạt động mượt mà
- [ ] Responsive trên mobile/tablet

---

## 🐛 Troubleshooting

### Icons không hiển thị?
```
1. Clear cache: Site administration → Development → Purge all caches
2. Kiểm tra $THEME->iconsystem trong config.php
3. Refresh browser (Ctrl + F5)
```

### Styles không áp dụng?
```
1. Clear theme caches
2. Kiểm tra file post.scss có đúng syntax không
3. Xem console browser có errors không
```

### Homepage không hiển thị đúng?
```
1. Kiểm tra HTML block đã paste đúng code
2. Turn editing off để xem
3. Kiểm tra responsive trên các device khác nhau
```

---

## 📞 Support

Nếu gặp vấn đề, hãy:
1. Đọc file README.md
2. Kiểm tra HOMEPAGE_DEMO.md
3. Liên hệ administrator

---

**Happy Moodling! 🎓✨**
