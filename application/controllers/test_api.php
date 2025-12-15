<?php
// ไฟล์: test_api.php (วางใน root directory ของเว็บ Local)
// สำหรับทดสอบว่า API ทำงานหรือไม่

$action = $_GET['action'] ?? 'info';

switch ($action) {
    case 'info':
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Local API Test</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
                button { padding: 10px 15px; margin: 5px; }
                pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
            </style>
        </head>
        <body>
            <h1>Local API Server Test</h1>
            
            <div class="test-section">
                <h2>Server Information</h2>
                <p><strong>Server Name:</strong> <?php echo $_SERVER['SERVER_NAME'] ?? 'Unknown'; ?></p>
                <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></p>
                <p><strong>Current Directory:</strong> <?php echo getcwd(); ?></p>
                <p><strong>Test URL:</strong> http://<?php echo $_SERVER['SERVER_NAME']; ?>/test_api.php</p>
            </div>
            
            <div class="test-section">
                <h2>API Tests</h2>
                <button onclick="testConnection()">Test Connection</button>
                <button onclick="testCodeIgniter()">Test CodeIgniter</button>
                <button onclick="testDirectAPI()">Test Direct API</button>
                <pre id="test-result"></pre>
            </div>
            
            <div class="test-section">
                <h2>Directory Check</h2>
                <button onclick="checkDirectories()">Check Upload Directories</button>
                <pre id="dir-result"></pre>
            </div>

            <script>
                function testConnection() {
                    fetch('?action=test_connection')
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('test-result').textContent = JSON.stringify(data, null, 2);
                        })
                        .catch(error => {
                            document.getElementById('test-result').textContent = 'Error: ' + error;
                        });
                }
                
                function testCodeIgniter() {
                    fetch('/index.php/api/test_connection')
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('test-result').textContent = data;
                        })
                        .catch(error => {
                            document.getElementById('test-result').textContent = 'CodeIgniter API Error: ' + error;
                        });
                }
                
                function testDirectAPI() {
                    fetch('/api/test_connection')
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('test-result').textContent = data;
                        })
                        .catch(error => {
                            document.getElementById('test-result').textContent = 'Direct API Error: ' + error;
                        });
                }
                
                function checkDirectories() {
                    fetch('?action=check_dirs')
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('dir-result').textContent = JSON.stringify(data, null, 2);
                        })
                        .catch(error => {
                            document.getElementById('dir-result').textContent = 'Error: ' + error;
                        });
                }
            </script>
        </body>
        </html>
        <?php
        break;
        
    case 'test_connection':
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Local test API is working',
            'timestamp' => date('Y-m-d H:i:s'),
            'server' => $_SERVER['SERVER_NAME'] ?? 'localhost'
        ]);
        break;
        
    case 'check_dirs':
        header('Content-Type: application/json');
        
        $dirs_to_check = [
            './docs/',
            './docs/back_office/',
            './docs/back_office/img/',
            './docs/back_office/file/',
            './application/',
            './application/controllers/'
        ];
        
        $results = [];
        foreach ($dirs_to_check as $dir) {
            $results[$dir] = [
                'exists' => is_dir($dir) ? 'Yes' : 'No',
                'writable' => is_writable($dir) ? 'Yes' : 'No'
            ];
        }
        
        $results['files_check'] = [
            'Api.php' => file_exists('./application/controllers/Api.php') ? 'Exists' : 'Missing',
            'index.php' => file_exists('./index.php') ? 'Exists' : 'Missing'
        ];
        
        echo json_encode($results, JSON_PRETTY_PRINT);
        break;
        
    default:
        echo "Invalid action";
        break;
}
?>