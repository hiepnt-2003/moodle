<?php
// This file is part of Moodle - http://moodle.org/
//
// Simple test interface for JSON API endpoint
//
// @package    local_coursecopier
// @copyright  2025 Your Name
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

require_once('../../config.php');

// Require login
require_login();

// Check capabilities
$context = context_system::instance();
require_capability('moodle/course:create', $context);

$PAGE->set_url('/local/coursecopier/test_json_api.php');
$PAGE->set_context($context);
$PAGE->set_title('Test JSON API');
$PAGE->set_heading('Course Copier - JSON API Test');

echo $OUTPUT->header();

?>

<style>
.test-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
}

.test-section {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin: 20px 0;
    padding: 20px;
}

.json-input {
    width: 100%;
    height: 200px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 3px;
}

.test-button {
    background: #0073aa;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    margin: 10px 5px;
}

.test-button:hover {
    background: #005177;
}

.result-area {
    background: #fff;
    border: 1px solid #ccc;
    padding: 10px;
    margin-top: 10px;
    border-radius: 3px;
    min-height: 100px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    white-space: pre-wrap;
}

.token-info {
    background: #e7f3ff;
    border: 1px solid #b3d9ff;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}
</style>

<div class="test-container">
    <h2>JSON API Endpoint Test</h2>
    
    <div class="token-info">
        <h3>🔑 Token Information</h3>
        <p><strong>Endpoint:</strong> <code><?php echo $CFG->wwwroot; ?>/local/coursecopier/api.php</code></p>
        <p><strong>Method:</strong> POST</p>
        <p><strong>Content-Type:</strong> application/json</p>
        <p><strong>Authorization:</strong> Bearer YOUR_TOKEN (hoặc wstoken trong JSON body)</p>
        <p><strong>Your Web Service Token:</strong> <span id="userToken">Loading...</span></p>
        <button onclick="generateToken()" class="test-button">Generate New Token</button>
    </div>

    <div class="test-section">
        <h3>📋 Test Get Available Courses</h3>
        <textarea id="getCourses" class="json-input" placeholder="JSON request body...">
{
  "wstoken": "YOUR_TOKEN_HERE",
  "wsfunction": "local_coursecopier_get_available_courses",
  "moodlewsrestformat": "json",
  "categoryid": 0
}
        </textarea>
        <br>
        <button onclick="testGetCourses()" class="test-button">🚀 Test Get Courses</button>
        <button onclick="copyJsonGetCourses()" class="test-button">📋 Copy JSON</button>
        <div id="result-getCourses" class="result-area">Kết quả sẽ hiển thị ở đây...</div>
    </div>

    <div class="test-section">
        <h3>📚 Test Copy Course</h3>
        <textarea id="copyCourse" class="json-input" placeholder="JSON request body...">
{
  "wstoken": "YOUR_TOKEN_HERE",
  "wsfunction": "local_coursecopier_copy_course",
  "moodlewsrestformat": "json",
  "shortname_clone": "ORIGINAL_COURSE",
  "fullname": "New Course Name 2025",
  "shortname": "NEWCOURSE2025",
  "startdate": 1704067200,
  "enddate": 1719792000
}
        </textarea>
        <br>
        <button onclick="testCopyCourse()" class="test-button">🚀 Test Copy Course</button>
        <button onclick="copyJsonCopyCourse()" class="test-button">📋 Copy JSON</button>
        <div id="result-copyCourse" class="result-area">Kết quả sẽ hiển thị ở đây...</div>
    </div>

    <div class="test-section">
        <h3>📊 cURL Examples</h3>
        <h4>Get Available Courses:</h4>
        <code id="curlGetCourses" style="display: block; background: #f0f0f0; padding: 10px; margin: 10px 0; word-wrap: break-word;">
curl -X POST "<?php echo $CFG->wwwroot; ?>/local/coursecopier/api.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"wsfunction": "local_coursecopier_get_available_courses", "categoryid": 0}'
        </code>
        
        <h4>Copy Course:</h4>
        <code id="curlCopyCourse" style="display: block; background: #f0f0f0; padding: 10px; margin: 10px 0; word-wrap: break-word;">
curl -X POST "<?php echo $CFG->wwwroot; ?>/local/coursecopier/api.php" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"wsfunction": "local_coursecopier_copy_course", "shortname_clone": "ORIGINAL", "fullname": "New Course", "shortname": "NEW2025", "startdate": 1704067200, "enddate": 1719792000}'
        </code>
    </div>
</div>

<script>
// Load user token on page load
document.addEventListener('DOMContentLoaded', function() {
    loadUserToken();
});

function loadUserToken() {
    fetch('<?php echo $CFG->wwwroot; ?>/webservice/rest/server.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'wsfunction=core_webservice_get_site_info&moodlewsrestformat=json&wstoken=dummy'
    })
    .then(() => {
        // If we get here, we need to show manual token instruction
        document.getElementById('userToken').innerHTML = 
            '<strong style="color: #d32f2f;">Please create a web service token manually</strong><br>' +
            '1. Go to Site Administration → Server → Web Services → Manage tokens<br>' +
            '2. Create token for your user<br>' +
            '3. Copy token and paste into JSON examples above';
    })
    .catch(() => {
        document.getElementById('userToken').innerHTML = 
            '<strong style="color: #d32f2f;">Please create a web service token manually</strong>';
    });
}

function generateToken() {
    alert('Please go to:\nSite Administration → Server → Web Services → Manage tokens\nto create a new token manually.');
}

async function testGetCourses() {
    const jsonText = document.getElementById('getCourses').value;
    const resultDiv = document.getElementById('result-getCourses');
    
    try {
        const jsonData = JSON.parse(jsonText);
        
        resultDiv.textContent = 'Sending request...';
        
        const response = await fetch('<?php echo $CFG->wwwroot; ?>/local/coursecopier/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + jsonData.wstoken
            },
            body: jsonText
        });
        
        const result = await response.json();
        
        resultDiv.textContent = 'Response Status: ' + response.status + '\n\n' + 
                               JSON.stringify(result, null, 2);
                               
        if (result.status === 'success') {
            resultDiv.style.borderLeft = '5px solid #4caf50';
        } else {
            resultDiv.style.borderLeft = '5px solid #f44336';
        }
        
    } catch (error) {
        resultDiv.textContent = 'Error: ' + error.message;
        resultDiv.style.borderLeft = '5px solid #f44336';
    }
}

async function testCopyCourse() {
    const jsonText = document.getElementById('copyCourse').value;
    const resultDiv = document.getElementById('result-copyCourse');
    
    try {
        const jsonData = JSON.parse(jsonText);
        
        resultDiv.textContent = 'Sending request...';
        
        const response = await fetch('<?php echo $CFG->wwwroot; ?>/local/coursecopier/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + jsonData.wstoken
            },
            body: jsonText
        });
        
        const result = await response.json();
        
        resultDiv.textContent = 'Response Status: ' + response.status + '\n\n' + 
                               JSON.stringify(result, null, 2);
                               
        if (result.status === 'success') {
            resultDiv.style.borderLeft = '5px solid #4caf50';
        } else {
            resultDiv.style.borderLeft = '5px solid #f44336';
        }
        
    } catch (error) {
        resultDiv.textContent = 'Error: ' + error.message;
        resultDiv.style.borderLeft = '5px solid #f44336';
    }
}

function copyJsonGetCourses() {
    const text = document.getElementById('getCourses').value;
    navigator.clipboard.writeText(text).then(() => {
        alert('JSON đã được copy vào clipboard!');
    });
}

function copyJsonCopyCourse() {
    const text = document.getElementById('copyCourse').value;
    navigator.clipboard.writeText(text).then(() => {
        alert('JSON đã được copy vào clipboard!');
    });
}
</script>

<?php
echo $OUTPUT->footer();
?>