<?php
// 1. CẤU HÌNH CƠ BẢN
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('JOBS_PER_PAGE', 4);

// Đường dẫn tới thư mục Views (chung cho cả app và admin)
define('VIEW_PATH_APP', APP_PATH . '/views');
define('VIEW_PATH_ADMIN', ADMIN_PATH . '/views');

$baseUrl = '/helios/public/';

// AUTOLOADER NÂNG CẤP
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/config/' . $class . '.php',      // Thư mục config
        APP_PATH . '/controllers/' . $class . '.php', // Controller của App
        APP_PATH . '/models/' . $class . '.php',      // Model của App
        ADMIN_PATH . '/controllers/' . $class . '.php', // Controller của Admin
        ADMIN_PATH . '/models/' . $class . '.php',      // Model của Admin
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
// 3. DANH SÁCH ĐƯỜNG DẪN (ROUTING TỔNG HỢP BẰNG MẢNG)
// ==========================================================
// Cấu trúc: 'đường/dẫn' => ['TênController', 'tên_hàm']
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
    'network/search'           => ['NetworkController', 'search'],
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
    'noti'                     => ['NotiController', 'index'],
    'noti/mark-read'           => ['NotiController', 'markRead'],
    'noti/mark-all-read'       => ['NotiController', 'markAllRead'],
    'noti/delete'              => ['NotiController', 'deleteNoti'],
    
    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */
    'admin' => ['AdminDashboardController', 'index'],
    'admin/dashboard' => ['AdminDashboardController', 'index'],

    'admin/jobs' => ['AdminJobController', 'index'],
    'admin/jobs/create' => ['AdminJobController', 'create'],
    'admin/jobs/update' => ['AdminJobController', 'update'],
    'admin/jobs/delete' => ['AdminJobController', 'delete'],
    'admin/jobs/get-skills' => ['AdminJobController', 'getSkills'],
    'admin/companies' => ['AdminCompanyController', 'index'],
    'admin/companies/create' => ['AdminCompanyController', 'create'],
    'admin/companies/update' => ['AdminCompanyController', 'update'],
    'admin/companies/delete' => ['AdminCompanyController', 'delete'],

    'admin/posts'                  => ['AdminPostController', 'index'],
    'admin/posts/detail'           => ['AdminPostController', 'detail'],
    'admin/posts/get-posts'        => ['AdminPostController', 'getPosts'],
    'admin/posts/get-detail'       => ['AdminPostController', 'getDetail'],
    'admin/posts/create'           => ['AdminPostController', 'create'],
    'admin/posts/delete'           => ['AdminPostController', 'delete'],
    'admin/posts/update'           => ['AdminPostController', 'update'],
];
// ==========================================================
// 4. BỘ ĐIỀU HƯỚNG (DISPATCHER)
// ==========================================================
// Kiểm tra xem URL người dùng gõ có nằm trong danh sách Mảng ở trên không?
if (array_key_exists($request_uri, $routes)) {
    // Lấy tên Controller và tên Hàm từ mảng ra
    $controllerName = $routes[$request_uri][0];
    $methodName     = $routes[$request_uri][1];
    // Khởi tạo Controller và Gọi hàm tương ứng
    $controller = new $controllerName();
    $controller->$methodName();
} else {
    // NẾU TÌM KHÔNG THẤY TRONG MẢNG -> BÁO LỖI 404
    http_response_code(404);
    $pageTitle = "404 Not Found";

    // Kiểm tra xem URL có bắt đầu bằng 'admin/' không
    if (strpos($request_uri, 'admin/') === 0) {
        // Nếu là trang admin, nạp file 404.php và layout của admin
        $contentView = VIEW_PATH_ADMIN . '/404.php';
        include VIEW_PATH_ADMIN . '/layouts/main.php';
    } else {
        // Nếu là trang người dùng, nạp file 404.php và layout của app
        $contentView = VIEW_PATH_APP . '/404.php';
        include VIEW_PATH_APP . '/layouts/main.php';
    }
}