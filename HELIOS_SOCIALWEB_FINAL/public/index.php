<?php
// 1. CẤU HÌNH CƠ BẢN
define('ROOT_PATH', dirname(__DIR__));
define('VIEW_PATH', ROOT_PATH . '/app/views');
define('APP_PATH', ROOT_PATH . '/app');
$baseUrl = '/helios/public/';
spl_autoload_register(function ($class) {
    $controllerPath = ROOT_PATH . '/app/controllers/' . $class . '.php';
    $modelPath      = ROOT_PATH . '/app/models/' . $class . '.php';
    if (file_exists($controllerPath)) require_once $controllerPath;
    elseif (file_exists($modelPath))  require_once $modelPath;
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
    | NOTI
    |--------------------------------------------------------------------------
    */
    'noti'                     => ['NotiController', 'index'],
    'noti/mark-read'           => ['NotiController', 'markRead'],
    'noti/mark-all-read'       => ['NotiController', 'markAllRead'],
    'noti/delete'              => ['NotiController', 'deleteNoti']
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
    $pageTitle   = "404 Not Found";
    $contentView = VIEW_PATH . '/404.php';
    include VIEW_PATH . '/layouts/main.php';
}
?>