<?php
/**
 * Simple Web Service Test Interface
 * URL: http://your-moodle.com/local/webservice/test.php
 */

require_once('../../config.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Course Clone API Test</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .container { max-width: 800px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        .info { background: #d1ecf1; border-color: #b8daff; }
        input, textarea { width: 100%; margin: 5px 0; padding: 8px; }
        button { padding: 10px 20px; background: #007cba; color: white; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Course Clone API Test</h1>

        <!-- Configuration Status -->
        <div class="section info">
            <h3>📊 Configuration Status</h3>
            <p><strong>Moodle URL:</strong> <?php echo $CFG->wwwroot; ?></p>
            <p><strong>API Endpoint:</strong> <?php echo $CFG->wwwroot; ?>/webservice/rest/server.php</p>
            <p><strong>Token Available:</strong> ✅ <code>7dbe17f17c65d685d18731cefd9a2e46</code></p>
            <p><strong>Web Services:</strong> 
                <?php echo !empty($CFG->enablewebservices) ? '✅ Enabled' : '❌ Disabled'; ?>
            </p>
            <?php if (empty($CFG->enablewebservices)): ?>
            <p style="color: red;">⚠️ Enable Web Services: Site Administration → Advanced features → Enable web services</p>
            <?php endif; ?>
        </div>

        <!-- API Test Form -->
        <div class="section">
            <h3>🧪 Test API Call</h3>
            <form id="apiForm">
                <label>Web Service Token:</label>
                <input type="text" id="token" value="7dbe17f17c65d685d18731cefd9a2e46" placeholder="Your web service token" required>
                
                <label>Source Course Shortname (to clone from):</label>
                <input type="text" id="shortname_clone" placeholder="EXISTING_COURSE" required>
                
                <label>New Course Full Name:</label>
                <input type="text" id="fullname" placeholder="My Cloned Course" required>
                
                <label>New Course Shortname:</label>
                <input type="text" id="shortname" placeholder="CLONE_001" required>
                
                <label>Start Date (YYYY-MM-DD):</label>
                <input type="date" id="startdate" required>
                
                <label>End Date (YYYY-MM-DD):</label>
                <input type="date" id="enddate" required>
                
                <br><br>
                <button type="submit">🚀 Test Clone API</button>
            </form>
        </div>

        <!-- Results -->
        <div id="results" class="section" style="display:none;">
            <h3>📋 API Response</h3>
            <pre id="response"></pre>
        </div>

        <!-- cURL Example -->
        <div class="section">
            <h3>💻 cURL Command Example</h3>
            <textarea readonly rows="8">curl -X POST "<?php echo $CFG->wwwroot; ?>/webservice/rest/server.php" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "wstoken=7dbe17f17c65d685d18731cefd9a2e46" \
  -d "wsfunction=local_webservice_clone_course" \
  -d "moodlewsrestformat=json" \
  -d "shortname_clone=EXISTING_COURSE" \
  -d "fullname=My Cloned Course" \
  -d "shortname=CLONE_001" \
  -d "startdate=1735689600" \
  -d "enddate=1743465600"</textarea>
        </div>

        <!-- Postman Collection -->
        <div class="section success">
            <h3>📱 Postman Collection</h3>
            <p>Import file: <code>Course_Clone_API.postman_collection.json</code></p>
            <p>Set Environment Variables:</p>
            <ul>
                <li><code>moodle_url</code> = <?php echo $CFG->wwwroot; ?></li>
                <li><code>ws_token</code> = Your actual token</li>
            </ul>
        </div>
    </div>

    <script>
        document.getElementById('apiForm').onsubmit = function(e) {
            e.preventDefault();
            testAPI();
        };

        function testAPI() {
            const token = document.getElementById('token').value;
            const shortname_clone = document.getElementById('shortname_clone').value;
            const fullname = document.getElementById('fullname').value;
            const shortname = document.getElementById('shortname').value;
            const startdate = Math.floor(new Date(document.getElementById('startdate').value).getTime() / 1000);
            const enddate = Math.floor(new Date(document.getElementById('enddate').value).getTime() / 1000);

            const formData = new FormData();
            formData.append('wstoken', token);
            formData.append('wsfunction', 'local_webservice_clone_course');
            formData.append('moodlewsrestformat', 'json');
            formData.append('shortname_clone', shortname_clone);
            formData.append('fullname', fullname);
            formData.append('shortname', shortname);
            formData.append('startdate', startdate);
            formData.append('enddate', enddate);

            fetch('<?php echo $CFG->wwwroot; ?>/webservice/rest/server.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('results').style.display = 'block';
                document.getElementById('response').textContent = JSON.stringify(data, null, 2);
                
                // Style based on result
                const resultsDiv = document.getElementById('results');
                if (data.status === 'success') {
                    resultsDiv.className = 'section success';
                } else {
                    resultsDiv.className = 'section error';
                }
            })
            .catch(error => {
                document.getElementById('results').style.display = 'block';
                document.getElementById('results').className = 'section error';
                document.getElementById('response').textContent = 'Error: ' + error.message;
            });
        }
    </script>
</body>
</html>