<?php
// File: public/index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Tự động nạp các thư viện từ Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Luôn khởi động session ở đầu tiên
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. CẤU HÌNH CƠ BẢN
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('JOBS_PER_PAGE', 4);
define('VIEW_PATH_APP', APP_PATH . '/views');
define('VIEW_PATH_ADMIN', ADMIN_PATH . '/views');

$baseUrl = '/';

// Tạo URL base đầy đủ để dùng trong các link tuyệt đối (như email)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$fullBaseUrl = $protocol . '://' . $host . $baseUrl;


// AUTOLOADER NÂNG CẤP
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/config/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php',
        ADMIN_PATH . '/controllers/' . $class . '.php',
        ADMIN_PATH . '/models/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// 2. XỬ LÝ URL
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($request_uri, $baseUrl) === 0) {
    $request_uri = substr($request_uri, strlen($baseUrl));
}
$request_uri = trim($request_uri, '/');


// ==========================================================
// 3. ROUTING VÀ BẢO VỆ
// ==========================================================

// Danh sách các route công khai không yêu cầu đăng nhập
$publicRoutes = [
    'login',
    'register',
    'verify-email',
    'forgot-password', 
    'reset-password',  
];

// ----- BẢO VỆ TOÀN CỤC -----
$isLoggedIn = isset($_SESSION['user_id']);
$isPublicRoute = in_array($request_uri, $publicRoutes);

if (!$isLoggedIn && !$isPublicRoute) {
    header('Location: ' . $baseUrl . 'login');
    exit();
}

// ----- BẢO VỆ TRANG ADMIN -----
if (strpos($request_uri, 'admin') === 0) {
    if (!$isLoggedIn || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
        http_response_code(403);
        die('Access Denied: You do not have permission to access this page.');
    }
}

// DANH SÁCH ĐƯỜNG DẪN
$routes = [
    /*
    |--------------------------------------------------------------------------
    | HOME
    |--------------------------------------------------------------------------
    */
    ''                         => ['HomeController', 'index'],
    'home'                     => ['HomeController', 'index'],
    'home/create-post'         => ['HomeController', 'createPost'],
    'home/delete-post'         => ['HomeController', 'deletePost'],
    'home/update-post'         => ['HomeController', 'updatePost'],
    'home/get-post-edit'       => ['HomeController', 'getPostForEdit'],
    'home/react'               => ['HomeController', 'react'],
    'home/get-reaction-counts' => ['HomeController', 'getReactionCounts'],
    'home/get-comments'        => ['HomeController', 'getComments'],
    'home/add-comment'         => ['HomeController', 'addComment'],
    'home/delete-comment'      => ['HomeController', 'deleteComment'],
    'home/update-comment'      => ['HomeController', 'updateComment'],
    'home/hide-comment'        => ['HomeController', 'hideComment'],
    'home/add-post-images'     => ['HomeController', 'addPostImages'],
    'home/delete-post-image'   => ['HomeController', 'deletePostImage'],
    /*
    |--------------------------------------------------------------------------
    | ABOUT ME
    |--------------------------------------------------------------------------
    */
    'about-me'                 => ['AboutMeController', 'index'],
    'about-me/update'          => ['AboutMeController', 'update'],
    'about-me/update-bio'      => ['AboutMeController', 'updateBio'],
    'about-me/add-experience'  => ['AboutMeController', 'addExperience'],
    'about-me/edit-experience' => ['AboutMeController', 'editExperience'],
    'about-me/add-education'   => ['AboutMeController', 'addEducation'],
    'about-me/edit-education'  => ['AboutMeController', 'editEducation'],
    'about-me/add-skill'       => ['AboutMeController', 'addSkill'],
    'about-me/delete-skill'    => ['AboutMeController', 'deleteSkill'],
    'about-me/update-image'    => ['AboutMeController', 'updateImage'],
    /*
    |--------------------------------------------------------------------------
    | NETWORK
    |--------------------------------------------------------------------------
    */
    'network'                  => ['NetworkController', 'index'],
    'network/suggestions'      => ['NetworkController', 'suggestions'],
    'network/send-request'     => ['NetworkController', 'sendRequest'],
    'network/accept-request'   => ['NetworkController', 'acceptRequest'],
    'network/ignore-request'   => ['NetworkController', 'ignoreRequest'],
    'network/remove-connection' => ['NetworkController', 'removeConnection'],
    'network/search'           => ['NetworkController', 'search'],
    'network/get-profile'    => ['NetworkController', 'getQuickProfile'],
    /*
    |--------------------------------------------------------------------------
    | JOB
    |--------------------------------------------------------------------------
    */
    'job'                      => ['JobController', 'index'],
    'job/detail'               => ['JobController', 'detail'],
    /*
    |--------------------------------------------------------------------------
    | MESSAGE
    |--------------------------------------------------------------------------
    */    
    // Message routes
    'message'                    => ['MessageController', 'index'],
    'message/send'               => ['MessageController', 'send'],
    'message/upload'             => ['MessageController', 'upload'],
    'message/poll'               => ['MessageController', 'poll'],
    'message/delete'             => ['MessageController', 'delete'],
    'message/delete-conversation'=> ['MessageController', 'deleteConversation'],
    'message/pin'                => ['MessageController', 'pin'],
    'message/search'             => ['MessageController', 'search'],
    'message/search-messages'    => ['MessageController', 'searchMessages'],
    'message/pinned-list'        => ['MessageController', 'getPinnedList'],
    /*
    |--------------------------------------------------------------------------
    | NOTI
    |--------------------------------------------------------------------------
    */
    // Thông báo
    'noti'                     => ['NotiController', 'index'],
    'noti/mark-read'           => ['NotiController', 'markRead'],
    'noti/mark-all-read'       => ['NotiController', 'markAllRead'],
    'noti/delete'              => ['NotiController', 'deleteNoti'],
    'noti/filter'              => ['NotiController', 'filter'],
    'noti/unread-count'        => ['NotiController', 'getUnreadCount'],
    'noti/toggle-notifications'=> ['NotiController', 'toggleNotifications'],
        
    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */
    'admin' => ['AdminDashboardController', 'index'],
    'admin/dashboard' => ['AdminDashboardController', 'index'],
    // USERS
    'admin/users'               => ['AdminUserController','index'],
    'admin/users/get-users'     => ['AdminUserController','getUsers'],
    'admin/users/get-detail'    => ['AdminUserController','getDetail'],
    'admin/users/create'        => ['AdminUserController','create'],
    'admin/users/toggle-status' => ['AdminUserController','toggleStatus'], 

     // JOBS & COMPANIES
    'admin/jobs' => ['AdminJobController', 'index'],
    'admin/jobs/create' => ['AdminJobController', 'create'],
    'admin/jobs/update' => ['AdminJobController', 'update'],
    'admin/jobs/delete' => ['AdminJobController', 'delete'],
    'admin/jobs/get-skills' => ['AdminJobController', 'getSkills'],
    'admin/companies' => ['AdminCompanyController', 'index'],
    'admin/companies/create' => ['AdminCompanyController', 'create'],
    'admin/companies/update' => ['AdminCompanyController', 'update'],
    'admin/companies/delete' => ['AdminCompanyController', 'delete'],
    
    // POSTS    
    'admin/posts'                  => ['AdminPostController', 'index'],
    'admin/posts/detail'           => ['AdminPostController', 'detail'],
    'admin/posts/get-posts'        => ['AdminPostController', 'getPosts'],
    'admin/posts/get-detail'       => ['AdminPostController', 'getDetail'],
    'admin/posts/create'           => ['AdminPostController', 'create'],
    'admin/posts/delete'           => ['AdminPostController', 'delete'],
    'admin/posts/update'           => ['AdminPostController', 'update'],
    'admin/posts/upload-images' => ['AdminPostController', 'uploadImages'],
    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION
    |--------------------------------------------------------------------------
    */
    'register' => ['AuthController', 'handleRegister'],
    'verify-email' => ['AuthController', 'verifyEmail'],
    'login' => ['AuthController', 'handleLogin'],
    'logout' => ['AuthController', 'logout'],
    'forgot-password' => ['AuthController', 'handleForgotPassword'],
    'reset-password' => ['AuthController', 'handleResetPassword'],
];
// ==========================================================
// 4. BỘ ĐIỀU HƯỚNG (DISPATCHER)
// ==========================================================
if (array_key_exists($request_uri, $routes)) {
    $controllerName = $routes[$request_uri][0];
    $methodName = $routes[$request_uri][1];
    $controller = new $controllerName();
    $controller->$methodName();
} else {
    http_response_code(404);
    $pageTitle = "404 Not Found";
    if (strpos($request_uri, 'admin/') === 0) {
        $contentView = VIEW_PATH_ADMIN . '/404.php';
        include VIEW_PATH_ADMIN . '/layouts/main.php';
    } else {
        $contentView = VIEW_PATH_APP . '/404.php';
        include VIEW_PATH_APP . '/layouts/main.php';
    }
}
