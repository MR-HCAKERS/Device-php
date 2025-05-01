<?php
// مسیر ذخیره فایل
$dataFile = 'data.json';

// دریافت اطلاعات از POST
$data = file_get_contents('php://input');
$dataArray = json_decode($data, true);

// چک می‌کنیم که فایل داده‌ها وجود دارد یا خیر
if (file_exists($dataFile)) {
    $existingData = json_decode(file_get_contents($dataFile), true);
} else {
    $existingData = [];
}

// اضافه کردن داده‌های جدید به داده‌های قبلی
$existingData[] = $dataArray;

// ذخیره اطلاعات به فایل data.json
file_put_contents($dataFile, json_encode($existingData, JSON_PRETTY_PRINT));

// پاسخ به درخواست
echo "اطلاعات با موفقیت ذخیره شد.";
?>
