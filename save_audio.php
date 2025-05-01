<?php
// مسیر ذخیره فایل صوتی
$uploadDir = 'Micro/';

// بررسی اینکه پوشه Micro وجود دارد یا خیر
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);  // اگر پوشه وجود ندارد، آن را می‌سازیم
}

// دریافت فایل صوتی
if (isset($_FILES['audio'])) {
    $audioFile = $_FILES['audio'];
    $filePath = $uploadDir . time() . '.webm';  // نام فایل با زمان فعلی

    // انتقال فایل به پوشه
    if (move_uploaded_file($audioFile['tmp_name'], $filePath)) {
        echo "صدا با موفقیت ذخیره شد.";
    } else {
        echo "خطا در ذخیره فایل صوتی.";
    }
} else {
    echo "فایل صوتی ارسال نشد.";
}
?>
