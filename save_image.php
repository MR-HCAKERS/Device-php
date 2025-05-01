<?php
// مسیر ذخیره عکس
$uploadDir = 'image/';

// بررسی اینکه پوشه Micro وجود دارد یا خیر
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);  // اگر پوشه وجود ندارد، آن را می‌سازیم
}

// دریافت تصویر از POST
$data = file_get_contents('php://input');
$imageData = json_decode($data, true);

// ذخیره تصویر به صورت base64
if (isset($imageData['photo'])) {
    $image = $imageData['photo'];
    $image = str_replace('data:image/png;base64,', '', $image);
    $image = str_replace(' ', '+', $image);
    $imageData = base64_decode($image);

    $filePath = $uploadDir . time() . '.png';  // نام فایل با زمان فعلی

    if (file_put_contents($filePath, $imageData)) {
        echo json_encode(['status' => 'success', 'message' => 'تصویر با موفقیت ذخیره شد.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'خطا در ذخیره تصویر.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'هیچ تصویری ارسال نشد.']);
}
?>
