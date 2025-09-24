<?php
defined('MOODLE_INTERNAL') || die();

class block_helloworld extends block_base {

    public function init() {
        // Tiêu đề mặc định (có thể override ở specialization)
        $this->title = get_string('pluginname', 'block_helloworld');
    }

    // Chỉ định nơi block có thể hiển thị
    public function applicable_formats(): array {
        return [
            'site' => true,
            'course' => true,
            'mod' => false,
            'my' => true
        ];
    }

    // Cho phép nhiều instance cùng 1 trang
    public function instance_allow_multiple(): bool {
        return true;
    }

    // Cho phép cấu hình instance (edit form)
    public function instance_allow_config(): bool {
        return true;
    }

    // Cho phép đổi title bằng config instance
    public function specialization() {
        if (!empty($this->config->title)) {
            $this->title = $this->config->title;
        }
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;

        // Lấy message: ưu tiên config instance, nếu không thì lấy global setting (settings.php)
        $message = get_string('plugincontent', 'block_helloworld'); // fallback
        if (!empty($this->config->message)) {
            $message = $this->config->message;         // từ instance (edit_form)
        } else {
            // lấy từ setting global nếu có
            $default = get_config('block_helloworld', 'defaultmessage');
            if (!empty($default)) {
                $message = $default;
            }
        }

        // Hiển thị an toàn, dùng format_text để xử lý các định dạng, xss-safe theo PARAM_* khi lưu.
        $this->content->text = format_text($message, FORMAT_HTML);
        $this->content->footer = '';

        return $this->content;
    }
}
