# Changelog - TH Boost Theme

All notable changes to this project will be documented in this file.

## [1.0.0] - 2025-10-09

### ✨ Added
- **Child theme** kế thừa từ Boost theme
- **FontAwesome 6.4.0** integration
  - Support solid, regular, và brand icons
  - CDN loading for better performance
  - Custom icon system mapping
- **Password toggle feature**
  - Eye icon để show/hide password
  - Tự động apply cho tất cả password fields
  - Smooth animations và transitions
  - Accessible với screen readers
- **Custom SCSS support**
  - Pre-SCSS cho variables
  - Extra SCSS cho custom styles
- **Admin settings page**
  - Brand colour picker
  - Background image uploader
  - Login page background
  - Preset selection
- **Responsive design**
  - Mobile-friendly (< 576px)
  - Tablet support (576px - 768px)
  - Desktop optimized (> 768px)

### 🔧 Technical Features
- Override core_renderer for FontAwesome integration
- Custom icon_system_fontawesome class
- JavaScript auto-injection for password toggle
- SCSS compilation callbacks
- File serving for uploaded images

### 📚 Documentation
- Comprehensive README.md
- Detailed INSTALL.md with troubleshooting
- Inline code documentation
- PHPDoc comments

### 🎯 Compatibility
- Moodle 3.8+
- Moodle 3.9
- Moodle 4.0+
- PHP 7.2+

### 🔒 Security
- No user data stored (privacy compliance)
- Secure file handling
- XSS protection in templates
- CSRF protection for forms

---

## Future Plans (Roadmap)

### Version 1.1.0 (Planned)
- [ ] Dark mode toggle
- [ ] More color scheme presets
- [ ] Custom login page templates
- [ ] Additional FontAwesome icon mappings
- [ ] Performance optimizations

### Version 1.2.0 (Planned)
- [ ] Homepage customization options
- [ ] Custom footer content
- [ ] Social media links
- [ ] Enhanced typography options
- [ ] More animation effects

### Version 2.0.0 (Future)
- [ ] Complete UI redesign options
- [ ] Drag-drop homepage builder
- [ ] Advanced customization panel
- [ ] Multi-language enhanced support
- [ ] Integration with popular plugins

---

## Notes

### Breaking Changes
- None in this version (first release)

### Deprecated Features
- None

### Known Issues
- FontAwesome requires internet connection (CDN)
- SCSS compilation may be slow on first load

### Migration Notes
- Migrating from Boost: No special steps needed
- Migrating from other themes: Review custom settings

---

**For detailed installation instructions, see [INSTALL.md](INSTALL.md)**
