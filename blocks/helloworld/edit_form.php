<?php
defined('MOODLE_INTERNAL') || die();

class block_helloworld_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        // Lưu ý: đặt tên field có tiền tố 'config_' (Moodle sẽ lưu vào $this->config-><name-without-config_>)
        $mform->addElement('text', 'config_title', get_string('pluginname', 'block_helloworld'));
        $mform->setType('config_title', PARAM_TEXT);

        $mform->addElement('textarea', 'config_message',
            get_string('configmessage', 'block_helloworld'),
            'wrap="virtual" rows="5" cols="50"');
        // Dùng PARAM_RAW nếu muốn giữ HTML, hoặc PARAM_TEXT nếu chỉ text thuần.
        $mform->setType('config_message', PARAM_RAW);
        $mform->addHelpButton('config_message', 'configmessage', 'block_helloworld');
    }
}
