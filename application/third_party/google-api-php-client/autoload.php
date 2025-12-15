<?php
/**
 * Google API PHP Client v2.15.1 - Fixed Log Level
 * Path: httpdocs/application/third_party/google-api-php-client/autoload.php
 * 
 * โครงสร้างไฟล์จริงตาม Project Knowledge:
 * src/
 * ├── Client.php
 * ├── Service.php  
 * ├── Model.php
 * ├── Collection.php
 * ├── Exception.php
 * ├── aliases.php
 * ├── AccessToken/
 * ├── AuthHandler/
 * ├── Http/
 * ├── Service/
 * ├── Task/
 * └── Utils/
 */

// ป้องกันการโหลดซ้ำ
if (defined('GOOGLE_CLIENT_AUTOLOAD_LOADED')) {
    return true;
}
define('GOOGLE_CLIENT_AUTOLOAD_LOADED', true);

$basePath = __DIR__ . '/src/';

/**
 * Safe Logging Function - แก้ไข Log Level Issue
 */
function safe_autoload_log($level, $message) {
    try {
        // ใช้ CodeIgniter log_message แทน error_log
        if (function_exists('log_message')) {
            // Log เฉพาะใน development
            if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                log_message($level, $message);
            }
        } else {
            // Fallback สำหรับกรณีที่ไม่มี CodeIgniter
            if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
                error_log("[{$level}] {$message}");
            }
        }
    } catch (Exception $e) {
        // Silent fail
    }
}

// ตรวจสอบโครงสร้างไฟล์
if (!is_dir($basePath)) {
    safe_autoload_log('error', 'Google API Client src directory not found: ' . $basePath);
    return false;
}

// โหลดไฟล์หลักตามลำดับ (ตามที่มีอยู่จริงใน src/)
$coreFiles = [
    'Exception.php',           // โหลดก่อนเพื่อใช้ใน class อื่น
    'Model.php',              // Base model class
    'Collection.php',         // Extends Model
    'Client.php',             // หลัก Google Client
    'Service.php',            // Base service class
];

foreach ($coreFiles as $file) {
    $fullPath = $basePath . $file;
    if (file_exists($fullPath)) {
        require_once $fullPath;
    } else {
        safe_autoload_log('error', "Missing core file: $fullPath");
    }
}

// โหลดไฟล์ Support Classes ตามลำดับ dependency
$supportDirs = [
    'Utils',                  // Utilities ก่อน
    'AuthHandler',           // Authentication handlers
    'AccessToken',           // Access token classes
    'Http',                  // HTTP utilities
    'Task',                  // Task runner
    'Service',               // Service classes
];

foreach ($supportDirs as $dir) {
    $dirPath = $basePath . $dir;
    if (is_dir($dirPath)) {
        // โหลดไฟล์ตามลำดับ dependency
        $files = [];
        
        if ($dir === 'Service') {
            // สำหรับ Service directory ให้โหลด Resource.php ก่อน
            $resourceFile = $dirPath . '/Resource.php';
            if (file_exists($resourceFile)) {
                require_once $resourceFile;
            }
            // แล้วโหลดไฟล์อื่นๆ
            $files = array_filter(glob($dirPath . '/*.php'), function($file) {
                return basename($file) !== 'Resource.php' && basename($file) !== 'README.md';
            });
        } else {
            $files = glob($dirPath . '/*.php');
        }
        
        foreach ($files as $file) {
            if (is_file($file)) {
                require_once $file;
            }
        }
    }
}

// ลงทะเบียน PSR-4 Autoloader สำหรับ Google\ namespace
spl_autoload_register(function ($className) use ($basePath) {
    
    // Handle Google\ namespace ตามมาตรฐาน PSR-4
    if (strpos($className, 'Google\\') === 0) {
        // ลบ 'Google\' prefix และแปลง namespace เป็น path
        $relativePath = substr($className, 7); // ลบ 'Google\'
        $filePath = $basePath . str_replace('\\', '/', $relativePath) . '.php';
        
        if (file_exists($filePath)) {
            require_once $filePath;
            return true;
        }
    }
    
    return false;
});

// โหลด aliases.php สำหรับ backward compatibility
$aliasesFile = $basePath . 'aliases.php';
if (file_exists($aliasesFile)) {
    require_once $aliasesFile;
}

// สร้าง Drive Service class ถ้าไม่มี (ใช้วิธี manual)
if (!class_exists('Google_Service_Drive') && class_exists('Google\Service')) {
    
    /**
     * Google Drive Service v3
     * Based on Google API PHP Client structure
     */
    class Google_Service_Drive extends Google\Service {
        
        public $about;
        public $changes;
        public $channels;
        public $comments;
        public $drives;
        public $files;
        public $permissions;
        public $replies;
        public $revisions;
        public $teamdrives;
        
        public function __construct($client) {
            parent::__construct($client);
            
            $this->rootUrl = 'https://www.googleapis.com/';
            $this->servicePath = 'drive/v3/';
            $this->batchPath = 'batch/drive/v3';
            $this->version = 'v3';
            $this->serviceName = 'drive';
            
            // Create resource instances
            $this->about = new Google_Service_Drive_About_Resource($this, $this->serviceName, 'about', []);
            $this->files = new Google_Service_Drive_Files_Resource($this, $this->serviceName, 'files', []);
            $this->permissions = new Google_Service_Drive_Permissions_Resource($this, $this->serviceName, 'permissions', []);
        }
    }
    
    // About Resource
    class Google_Service_Drive_About_Resource extends Google\Service\Resource {
        
        public function get($optParams = []) {
            $params = [];
            $params = array_merge($params, $optParams);
            return $this->call('get', [$params], 'Google_Service_Drive_About');
        }
    }
    
    // Files Resource
    class Google_Service_Drive_Files_Resource extends Google\Service\Resource {
        
        public function create($postBody, $optParams = []) {
            $params = ['postBody' => $postBody];
            $params = array_merge($params, $optParams);
            return $this->call('create', [$params], 'Google_Service_Drive_DriveFile');
        }
        
        public function delete($fileId, $optParams = []) {
            $params = ['fileId' => $fileId];
            $params = array_merge($params, $optParams);
            return $this->call('delete', [$params]);
        }
        
        public function get($fileId, $optParams = []) {
            $params = ['fileId' => $fileId];
            $params = array_merge($params, $optParams);
            return $this->call('get', [$params], 'Google_Service_Drive_DriveFile');
        }
        
        public function listFiles($optParams = []) {
            $params = [];
            $params = array_merge($params, $optParams);
            return $this->call('list', [$params], 'Google_Service_Drive_FileList');
        }
        
        public function update($fileId, $postBody, $optParams = []) {
            $params = ['fileId' => $fileId, 'postBody' => $postBody];
            $params = array_merge($params, $optParams);
            return $this->call('update', [$params], 'Google_Service_Drive_DriveFile');
        }
        
        public function copy($fileId, $postBody, $optParams = []) {
            $params = ['fileId' => $fileId, 'postBody' => $postBody];
            $params = array_merge($params, $optParams);
            return $this->call('copy', [$params], 'Google_Service_Drive_DriveFile');
        }
    }
    
    // Permissions Resource
    class Google_Service_Drive_Permissions_Resource extends Google\Service\Resource {
        
        public function create($fileId, $postBody, $optParams = []) {
            $params = ['fileId' => $fileId, 'postBody' => $postBody];
            $params = array_merge($params, $optParams);
            return $this->call('create', [$params], 'Google_Service_Drive_Permission');
        }
        
        public function delete($fileId, $permissionId, $optParams = []) {
            $params = ['fileId' => $fileId, 'permissionId' => $permissionId];
            $params = array_merge($params, $optParams);
            return $this->call('delete', [$params]);
        }
        
        public function get($fileId, $permissionId, $optParams = []) {
            $params = ['fileId' => $fileId, 'permissionId' => $permissionId];
            $params = array_merge($params, $optParams);
            return $this->call('get', [$params], 'Google_Service_Drive_Permission');
        }
        
        public function listPermissions($fileId, $optParams = []) {
            $params = ['fileId' => $fileId];
            $params = array_merge($params, $optParams);
            return $this->call('list', [$params], 'Google_Service_Drive_PermissionList');
        }
        
        public function update($fileId, $permissionId, $postBody, $optParams = []) {
            $params = ['fileId' => $fileId, 'permissionId' => $permissionId, 'postBody' => $postBody];
            $params = array_merge($params, $optParams);
            return $this->call('update', [$params], 'Google_Service_Drive_Permission');
        }
    }
    
    // Model Classes
    class Google_Service_Drive_About extends Google\Model {
        
        protected $userType = 'Google_Service_Drive_User';
        protected $userDataType = '';
        protected $storageQuotaType = 'Google_Service_Drive_StorageQuota';
        protected $storageQuotaDataType = '';
        
        public $kind;
        public $user;
        public $storageQuota;
        public $importFormats;
        public $exportFormats;
        public $maxImportSizes;
        public $maxUploadSize;
        public $appInstalled;
        public $folderColorPalette;
        public $teamDriveThemes;
        public $canCreateTeamDrives;
        public $canCreateDrives;
        
        public function setUser($user) {
            $this->user = $user;
        }
        
        public function getUser() {
            return $this->user;
        }
        
        public function setStorageQuota($storageQuota) {
            $this->storageQuota = $storageQuota;
        }
        
        public function getStorageQuota() {
            return $this->storageQuota;
        }
    }
    
    class Google_Service_Drive_User extends Google\Model {
        
        public $kind;
        public $displayName;
        public $photoLink;
        public $me;
        public $permissionId;
        public $emailAddress;
        
        public function getDisplayName() {
            return $this->displayName;
        }
        
        public function getEmailAddress() {
            return $this->emailAddress;
        }
        
        public function getPhotoLink() {
            return $this->photoLink;
        }
        
        public function getMe() {
            return $this->me;
        }
        
        public function getPermissionId() {
            return $this->permissionId;
        }
    }
    
    class Google_Service_Drive_StorageQuota extends Google\Model {
        
        public $limit;
        public $usage;
        public $usageInDrive;
        public $usageInDriveTrash;
        
        public function getLimit() {
            return $this->limit;
        }
        
        public function getUsage() {
            return $this->usage;
        }
        
        public function getUsageInDrive() {
            return $this->usageInDrive;
        }
        
        public function getUsageInDriveTrash() {
            return $this->usageInDriveTrash;
        }
    }
    
    class Google_Service_Drive_DriveFile extends Google\Model {
        
        protected $capabilitiesType = 'Google_Service_Drive_Capabilities';
        protected $capabilitiesDataType = '';
        protected $contentHintsType = 'Google_Service_Drive_ContentHints';
        protected $contentHintsDataType = '';
        protected $imageMediaMetadataType = 'Google_Service_Drive_ImageMediaMetadata';
        protected $imageMediaMetadataDataType = '';
        protected $lastModifyingUserType = 'Google_Service_Drive_User';
        protected $lastModifyingUserDataType = '';
        protected $ownersType = 'Google_Service_Drive_User';
        protected $ownersDataType = 'array';
        protected $parentsType = '';
        protected $parentsDataType = 'array';
        protected $permissionsType = 'Google_Service_Drive_Permission';
        protected $permissionsDataType = 'array';
        protected $propertiesType = '';
        protected $propertiesDataType = 'map';
        protected $sharingUserType = 'Google_Service_Drive_User';
        protected $sharingUserDataType = '';
        protected $videoMediaMetadataType = 'Google_Service_Drive_VideoMediaMetadata';
        protected $videoMediaMetadataDataType = '';
        
        public $appProperties;
        public $capabilities;
        public $contentHints;
        public $createdTime;
        public $description;
        public $explicitlyTrashed;
        public $fileExtension;
        public $folderColorRgb;
        public $fullFileExtension;
        public $hasAugmentedPermissions;
        public $hasThumbnail;
        public $headRevisionId;
        public $iconLink;
        public $id;
        public $imageMediaMetadata;
        public $isAppAuthorized;
        public $kind;
        public $lastModifyingUser;
        public $md5Checksum;
        public $mimeType;
        public $modifiedByMe;
        public $modifiedByMeTime;
        public $modifiedTime;
        public $name;
        public $originalFilename;
        public $ownedByMe;
        public $owners;
        public $parents;
        public $permissions;
        public $properties;
        public $quotaBytesUsed;
        public $shared;
        public $sharedWithMeTime;
        public $sharingUser;
        public $size;
        public $spaces;
        public $starred;
        public $teamDriveId;
        public $thumbnailLink;
        public $thumbnailVersion;
        public $trashed;
        public $trashedTime;
        public $version;
        public $videoMediaMetadata;
        public $viewedByMe;
        public $viewedByMeTime;
        public $viewersCanCopyContent;
        public $webContentLink;
        public $webViewLink;
        public $writersCanShare;
        
        public function setName($name) {
            $this->name = $name;
        }
        
        public function getName() {
            return $this->name;
        }
        
        public function setMimeType($mimeType) {
            $this->mimeType = $mimeType;
        }
        
        public function getMimeType() {
            return $this->mimeType;
        }
        
        public function setParents($parents) {
            $this->parents = $parents;
        }
        
        public function getParents() {
            return $this->parents;
        }
        
        public function getId() {
            return $this->id;
        }
        
        public function getWebViewLink() {
            return $this->webViewLink;
        }
        
        public function getWebContentLink() {
            return $this->webContentLink;
        }
        
        public function getSize() {
            return $this->size;
        }
        
        public function getCreatedTime() {
            return $this->createdTime;
        }
        
        public function getModifiedTime() {
            return $this->modifiedTime;
        }
    }
    
    class Google_Service_Drive_FileList extends Google\Collection {
        
        protected $collection_key = 'files';
        protected $filesType = 'Google_Service_Drive_DriveFile';
        protected $filesDataType = 'array';
        
        public $files;
        public $incompleteSearch;
        public $kind;
        public $nextPageToken;
        
        public function setFiles($files) {
            $this->files = $files;
        }
        
        public function getFiles() {
            return $this->files;
        }
        
        public function setNextPageToken($nextPageToken) {
            $this->nextPageToken = $nextPageToken;
        }
        
        public function getNextPageToken() {
            return $this->nextPageToken;
        }
        
        public function setIncompleteSearch($incompleteSearch) {
            $this->incompleteSearch = $incompleteSearch;
        }
        
        public function getIncompleteSearch() {
            return $this->incompleteSearch;
        }
    }
    
    class Google_Service_Drive_Permission extends Google\Model {
        
        public $allowFileDiscovery;
        public $deleted;
        public $displayName;
        public $domain;
        public $emailAddress;
        public $expirationTime;
        public $id;
        public $kind;
        public $photoLink;
        public $role;
        public $teamDrivePermissionDetails;
        public $type;
        public $view;
        
        public function setAllowFileDiscovery($allowFileDiscovery) {
            $this->allowFileDiscovery = $allowFileDiscovery;
        }
        
        public function getAllowFileDiscovery() {
            return $this->allowFileDiscovery;
        }
        
        public function setEmailAddress($emailAddress) {
            $this->emailAddress = $emailAddress;
        }
        
        public function getEmailAddress() {
            return $this->emailAddress;
        }
        
        public function setRole($role) {
            $this->role = $role;
        }
        
        public function getRole() {
            return $this->role;
        }
        
        public function setType($type) {
            $this->type = $type;
        }
        
        public function getType() {
            return $this->type;
        }
        
        public function getId() {
            return $this->id;
        }
        
        public function getDisplayName() {
            return $this->displayName;
        }
    }
    
    class Google_Service_Drive_PermissionList extends Google\Collection {
        
        protected $collection_key = 'permissions';
        protected $permissionsType = 'Google_Service_Drive_Permission';
        protected $permissionsDataType = 'array';
        
        public $kind;
        public $nextPageToken;
        public $permissions;
        
        public function setPermissions($permissions) {
            $this->permissions = $permissions;
        }
        
        public function getPermissions() {
            return $this->permissions;
        }
        
        public function setNextPageToken($nextPageToken) {
            $this->nextPageToken = $nextPageToken;
        }
        
        public function getNextPageToken() {
            return $this->nextPageToken;
        }
    }
}

// สร้าง Google\Auth\OAuth2 class ถ้าไม่มี
if (!class_exists('Google\\Auth\\OAuth2')) {
    /**
     * Mock Google\Auth\OAuth2 สำหรับ Google Client Library เวอร์ชันเก่า
     */
    class Google_Auth_OAuth2 {
        
        private $clientId;
        private $clientSecret;
        private $redirectUri;
        private $scopes = [];
        
        public function __construct($config = []) {
            if (isset($config['client_id'])) {
                $this->clientId = $config['client_id'];
            }
            if (isset($config['client_secret'])) {
                $this->clientSecret = $config['client_secret'];
            }
            if (isset($config['redirect_uri'])) {
                $this->redirectUri = $config['redirect_uri'];
            }
        }
        
        public function setClientId($clientId) {
            $this->clientId = $clientId;
        }
        
        public function setClientSecret($clientSecret) {
            $this->clientSecret = $clientSecret;
        }
        
        public function setRedirectUri($redirectUri) {
            $this->redirectUri = $redirectUri;
        }
        
        public function setScopes($scopes) {
            $this->scopes = is_array($scopes) ? $scopes : [$scopes];
        }
        
        public function addScope($scope) {
            if (!in_array($scope, $this->scopes)) {
                $this->scopes[] = $scope;
            }
        }
        
        public function buildFullAuthorizationUri($options = []) {
            $params = [
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUri,
                'scope' => implode(' ', $this->scopes),
                'response_type' => 'code',
                'access_type' => 'offline'
            ];
            
            if (isset($options['prompt'])) {
                $params['prompt'] = $options['prompt'];
            }
            
            if (isset($options['state'])) {
                $params['state'] = $options['state'];
            }
            
            return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        }
        
        public function fetchAuthToken($authCode) {
            $data = [
                'code' => $authCode,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
                'grant_type' => 'authorization_code'
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
            
            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                throw new Exception('OAuth2 fetch token error: ' . $error);
            }
            
            return json_decode($response, true);
        }
    }
    
    // สร้าง namespace alias
    if (!class_exists('Google\\Auth\\OAuth2')) {
        class_alias('Google_Auth_OAuth2', 'Google\\Auth\\OAuth2');
    }
}

// สร้าง aliases หลักถ้าไม่มี
if (!class_exists('Google_Client') && class_exists('Google\Client')) {
    class_alias('Google\Client', 'Google_Client');
}

if (!class_exists('Google\Service\Drive') && class_exists('Google_Service_Drive')) {
    class_alias('Google_Service_Drive', 'Google\Service\Drive');
}

// ตรวจสอบผลการโหลดและ log - แก้ไข Log Level
$loadedClasses = [];
$allLoaded = true;

$requiredClasses = [
    'Google\Client',
    'Google_Client', 
    'Google_Service_Drive',
    'Google\Service\Resource'
];

foreach ($requiredClasses as $class) {
    if (class_exists($class)) {
        $loadedClasses[] = $class;
    } else {
        $allLoaded = false;
        safe_autoload_log('error', "Missing class: $class");
    }
}

// Log ผลลัพธ์ - ใช้ safe_autoload_log แทน error_log
if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
    if ($allLoaded) {
        safe_autoload_log('info', "Google API Client autoload successful: " . implode(', ', $loadedClasses));
    } else {
        safe_autoload_log('info', "Google API Client partial load: " . implode(', ', $loadedClasses));
    }
}

// ส่งคืนสถานะการโหลด
return count($loadedClasses) >= 2; // อย่างน้อยต้องมี Google\Client และ Google_Client