<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Google Drive Hybrid System Configuration
|--------------------------------------------------------------------------
|
| This file contains the Google Drive API configuration for the Hybrid
| Google Drive System that supports both Personal and System storage modes.
|
*/

/*
|--------------------------------------------------------------------------
| Google API Credentials
|--------------------------------------------------------------------------
|
| You can get these credentials from Google Cloud Console:
| https://console.cloud.google.com/
|
| 1. Create a new project or select existing project
| 2. Enable Google Drive API
| 3. Create credentials (OAuth 2.0 client ID)
| 4. Add your domain to authorized origins
| 5. Add redirect URI: http://yourdomain.com/index.php/google_drive_hybrid/oauth_callback
|
*/

$config['google_client_id'] = 'YOUR_GOOGLE_CLIENT_ID_HERE';
$config['google_client_secret'] = 'YOUR_GOOGLE_CLIENT_SECRET_HERE';

/*
|--------------------------------------------------------------------------
| OAuth Redirect URI
|--------------------------------------------------------------------------
|
| This should match exactly with what you configured in Google Cloud Console
| Usually: http://yourdomain.com/index.php/google_drive_hybrid/oauth_callback
|
*/

$config['google_redirect_uri'] = base_url('index.php/google_drive_hybrid/oauth_callback');

/*
|--------------------------------------------------------------------------
| Google API Scopes
|--------------------------------------------------------------------------
|
| Required scopes for Hybrid Google Drive System
|
*/

$config['google_scopes'] = [
    'https://www.googleapis.com/auth/drive',
    'https://www.googleapis.com/auth/drive.file',
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile'
];

/*
|--------------------------------------------------------------------------
| Google Drive Settings
|--------------------------------------------------------------------------
|
| General settings for Google Drive functionality
|
*/

$config['google_drive_enabled'] = TRUE;
$config['auto_create_folders'] = TRUE;
$config['debug_mode'] = FALSE;

/*
|--------------------------------------------------------------------------
| Hybrid Mode Settings
|--------------------------------------------------------------------------
|
| Settings specific to Hybrid mode functionality
|
*/

// Allow users to use Personal Google Drive
$config['allow_personal_google'] = TRUE;

// Default drive mode for new users ('system', 'personal', 'auto')
$config['default_drive_mode'] = 'auto';

// Allow users to switch between modes
$config['allow_mode_switching'] = TRUE;

/*
|--------------------------------------------------------------------------
| File Upload Settings
|--------------------------------------------------------------------------
|
| Configuration for file uploads
|
*/

// Maximum file size in bytes (100MB default)
$config['max_file_size'] = 104857600;

// Allowed file types
$config['allowed_file_types'] = [
    'jpg', 'jpeg', 'png', 'gif', 'bmp',
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
    'txt', 'rtf', 'csv',
    'zip', 'rar', '7z',
    'mp4', 'avi', 'mov', 'wmv',
    'mp3', 'wav', 'aac'
];

// Convert allowed types to string for database storage
$config['allowed_file_types_string'] = implode(',', $config['allowed_file_types']);

/*
|--------------------------------------------------------------------------
| Storage Quota Settings
|--------------------------------------------------------------------------
|
| Default storage quotas for users
|
*/

// Default quota per user (1GB)
$config['default_user_quota'] = 1073741824;

// Maximum quota that can be assigned (10GB)
$config['max_user_quota'] = 10737418240;

// System storage limit (100GB)
$config['system_storage_limit'] = 107374182400;

/*
|--------------------------------------------------------------------------
| Security Settings
|--------------------------------------------------------------------------
|
| Security-related configurations
|
*/

// Allow external sharing (outside organization)
$config['allow_external_sharing'] = FALSE;

// Require verification for external sharing
$config['require_external_verification'] = TRUE;

// Maximum sharing per day per user
$config['max_daily_shares'] = 50;

// Token refresh buffer time (in seconds)
$config['token_refresh_buffer'] = 300; // 5 minutes

/*
|--------------------------------------------------------------------------
| Performance Settings
|--------------------------------------------------------------------------
|
| Settings to optimize performance
|
*/

// Enable caching
$config['enable_caching'] = TRUE;

// Cache duration for folder contents (in seconds)
$config['folder_cache_duration'] = 300; // 5 minutes

// Enable background sync
$config['enable_background_sync'] = TRUE;

// Sync interval (in minutes)
$config['sync_interval'] = 30;

/*
|--------------------------------------------------------------------------
| Logging Settings
|--------------------------------------------------------------------------
|
| Activity logging configuration
|
*/

// Enable activity logging
$config['enable_activity_logging'] = TRUE;

// Log retention period (in days)
$config['log_retention_days'] = 90;

// Activities to log
$config['logged_activities'] = [
    'upload', 'download', 'delete', 'share', 'unshare',
    'create_folder', 'delete_folder', 'rename',
    'mode_switch', 'google_connect', 'google_disconnect'
];

/*
|--------------------------------------------------------------------------
| UI/UX Settings
|--------------------------------------------------------------------------
|
| User interface configurations
|
*/

// Default view mode ('list' or 'grid')
$config['default_view_mode'] = 'list';

// Items per page
$config['items_per_page'] = 50;

// Enable drag and drop upload
$config['enable_drag_drop'] = TRUE;

// Enable keyboard shortcuts
$config['enable_keyboard_shortcuts'] = TRUE;

// Show file thumbnails
$config['show_thumbnails'] = TRUE;

/*
|--------------------------------------------------------------------------
| Email Notification Settings
|--------------------------------------------------------------------------
|
| Configuration for email notifications
|
*/

// Enable email notifications
$config['enable_email_notifications'] = TRUE;

// Admin email for system notifications
$config['admin_email'] = 'admin@yourdomain.com';

// Notification events
$config['notification_events'] = [
    'file_shared' => TRUE,
    'quota_warning' => TRUE,
    'quota_exceeded' => TRUE,
    'large_file_upload' => TRUE,
    'external_share' => TRUE
];

/*
|--------------------------------------------------------------------------
| Advanced Settings
|--------------------------------------------------------------------------
|
| Advanced configurations for power users
|
*/

// Enable debug logging
$config['debug_logging'] = FALSE;

// API timeout (in seconds)
$config['api_timeout'] = 60;

// Maximum retry attempts for API calls
$config['max_retry_attempts'] = 3;

// Enable alternative OAuth flow for compatibility
$config['alternative_oauth'] = TRUE;

// Use cURL instead of Google Client when necessary
$config['use_curl_fallback'] = TRUE;

/*
|--------------------------------------------------------------------------
| System Integration Settings
|--------------------------------------------------------------------------
|
| Settings for integrating with existing systems
|
*/

// Position-based folder access
$config['position_based_access'] = TRUE;

// Department folder auto-creation
$config['auto_create_department_folders'] = FALSE;

// Sync user changes with Google Drive
$config['sync_user_changes'] = TRUE;

// Enable SSO integration
$config['enable_sso'] = FALSE;

/*
|--------------------------------------------------------------------------
| Mobile App Settings
|--------------------------------------------------------------------------
|
| Configuration for mobile app compatibility
|
*/

// Enable mobile API endpoints
$config['enable_mobile_api'] = TRUE;

// Mobile app deep linking
$config['mobile_deep_linking'] = TRUE;

// Offline mode support
$config['offline_mode_support'] = FALSE;

/*
|--------------------------------------------------------------------------
| Backup and Recovery Settings
|--------------------------------------------------------------------------
|
| Data backup and recovery configurations
|
*/

// Enable automatic backups
$config['enable_auto_backup'] = FALSE;

// Backup frequency (daily, weekly, monthly)
$config['backup_frequency'] = 'weekly';

// Backup retention (number of backups to keep)
$config['backup_retention'] = 4;

// Include file content in backups
$config['backup_include_content'] = FALSE;

/*
|--------------------------------------------------------------------------
| Development Settings
|--------------------------------------------------------------------------
|
| Settings for development and testing
|
*/

// Enable test mode
$config['test_mode'] = FALSE;

// Mock Google API responses
$config['mock_api_responses'] = FALSE;

// Test user credentials
$config['test_user_email'] = 'test@yourdomain.com';

// Enable verbose error messages
$config['verbose_errors'] = FALSE;

/*
|--------------------------------------------------------------------------
| Multi-language Support
|--------------------------------------------------------------------------
|
| Internationalization settings
|
*/

// Default language
$config['default_language'] = 'thai';

// Available languages
$config['available_languages'] = [
    'thai' => 'ไทย',
    'english' => 'English'
];

// Enable language switching
$config['enable_language_switching'] = TRUE;

/*
|--------------------------------------------------------------------------
| Custom Settings
|--------------------------------------------------------------------------
|
| Add your custom settings here
|
*/

// Organization name
$config['organization_name'] = 'Your Organization';

// System name
$config['system_name'] = 'Hybrid Google Drive System';

// Version
$config['system_version'] = '2.0.0';

// Support email
$config['support_email'] = 'support@yourdomain.com';

/*
|--------------------------------------------------------------------------
| End of Configuration
|--------------------------------------------------------------------------
*/