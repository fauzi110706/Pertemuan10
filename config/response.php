<?php
// config/response.php
// Helper untuk response JSON terstandar (Sesuai Modul 11B)

/**
 * Mengirim response berformat JSON dengan HTTP Status Code yang sesuai.
 *
 * @param int $statusCode HTTP status code (200, 201, 400, 404, 405, 500)
 * @param bool $success Status boolean penanda operasi
 * @param string $message Pesan informatif untuk client
 * @param mixed $data Data payload opsional
 * @return void
 */
function sendJson(
    int $statusCode,
    bool $success,
    string $message,
    $data = null
): void {
    http_response_code($statusCode);
    header("Content-Type: application/json; charset=utf-8");

    // Header CORS agar API dapat diuji atau diakses antar domain / port
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

    $response = [
        "success" => $success,
        "message" => $message
    ];

    if ($data !== null) {
        $response["data"] = $data;
    }

    echo json_encode(
        $response,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
    exit;
}

/**
 * Mengambil payload request baik dari multipart/form-data, x-www-form-urlencoded,
 * maupun raw JSON body (application/json) dari fetch() modern.
 *
 * @return array
 */
function getRequestData(): array {
    $contentType = $_SERVER["CONTENT_TYPE"] ?? "";
    if (stripos($contentType, "application/json") !== false) {
        $raw = file_get_contents("php://input");
        $json = json_decode($raw, true);
        return is_array($json) ? $json : [];
    }
    return $_POST;
}
