<?php
session_start();
require_once '../DB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обработка загрузки изображения
    $uploadDir = '../../uploads/promotions/';
    $imagePath = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tempName = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . $_FILES['image']['name'];
        $targetPath = $uploadDir . $fileName;
        
        // Проверяем и создаем директорию если её нет
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Перемещаем загруженный файл
        if (move_uploaded_file($tempName, $targetPath)) {
            $imagePath = $fileName;
        }
    }
    
    // Остальной код добавления акции в базу данных
    // ...
} 