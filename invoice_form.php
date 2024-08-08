
<?php
// Подключаем автозагрузчик Composer
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $surname = $_POST["surname"];
    $city = $_POST["city"];
    $deliveryDate = $_POST["deliveryDate"];
    $address = $_POST["address"];
    $furnitureColor = isset($_POST["furnitureColor"]) ? $_POST["furnitureColor"] : "";

    // Создание словаря для хранения предметов мебели и их количества
    $furnitureItems = [];
    $furnitureItems = fillFurnitureItems($furnitureItems);

    $targetDir = "uploads/"; // Папка для сохранения загруженных файлов
    $targetFile = $targetDir . basename($_FILES["priceFile"]["name"]);
    $uploadOk = true;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Проверка формата файла
    if ($fileType != "txt") {
        echo "Ошибка: Разрешены только файлы с расширением .txt.";
        $uploadOk = false;
    }

    // Перемещение файла в указанную папку
    if ($uploadOk == true && move_uploaded_file($_FILES["priceFile"]["tmp_name"], $targetFile)) {
        //echo "Файл успешно загружен.";

        $furniturePrices = processPriceFile($targetFile);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $invoiceNumber = mt_rand(1000, 9999);
        $colorMultiplier = getColorMultiplier($furnitureColor);

        $totalPrice = generateInvoice($sheet, $invoiceNumber, $furnitureItems, $furnitureColor, $furniturePrices, $city, $address, $colorMultiplier);

        prepareResultsFolder();

        $excelFilePath = "result/Документ_на_выдачу_{$invoiceNumber}.xlsx";
        saveFile($spreadsheet, $excelFilePath);

        $totalPrice = urlencode($totalPrice);
        $excelFilePath = urlencode($excelFilePath);

        $data = array(
            'totalPrice' => $totalPrice,
            'excelFilePath' => $excelFilePath
        );

        // Устанавливаем заголовок для ответа как JSON
        header('Content-Type: application/json');

        // Преобразуем массив в формат JSON и отправляем его
        echo json_encode($data);
    } else {
        echo "Ошибка при обработке файла с ценами.";
    }
} else {
        echo "Ошибка при загрузке файла.";
}


function fillFurnitureItems($furnitureItems) {
    for ($i = 1; $i <= 6; $i++) {
            if (isset($_POST["itemName$i"])) {
            $itemName = $_POST["itemName$i"];
            $quantity = $_POST["quantity$i"];
            if (!empty($itemName) && $quantity > 0) {
                // Добавление предмета мебели и его количества в словарь
                $furnitureItems[$itemName] = $quantity;
            }
        }
    }
    return $furnitureItems;
}

function processPriceFile($targetFile) {
    $pricesFile = fopen($targetFile, "r");
        if ($pricesFile) {
            // Пропускаем первую строку с датой
            fgets($pricesFile);

            // Создаем словарь для хранения цен на предметы мебели
            $furniturePrices = [];
            // Считываем цены из файла и добавляем их в словарь
            while (($line = fgets($pricesFile)) !== false) {
                $parts = explode(" ", $line);
                $itemName = $parts[0];
                $price = $parts[1];
                $furniturePrices[$itemName] = $price;
            }
            fclose($pricesFile);
        }
    return $furniturePrices;
}

function printBarcode($sheet, $colorImagePath, $row) {
    if (file_exists($colorImagePath)) {
        // Вставляем изображение
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Image');
        $drawing->setDescription('Image');
        $drawing->setPath($colorImagePath);
        $drawing->setCoordinates("C{$row}");
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(4);
        $drawing->setWorksheet($sheet);
    } else {
        echo "Изображение {$colorImagePath} не найдено";
    }
}

function printInvoiceHeader($sheet, $row, $invoiceNumber) {
    $invoiceText = "Накладная №" . $invoiceNumber;
    $sheet->mergeCells('A' . $row . ':F' . $row);
    $sheet->setCellValue("A{$row}", $invoiceText);
    $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
}

function printAddres($sheet, $row, $city, $address) {
    $addressText = "Адрес получения заказа: г. {$city}, {$address}";
    $sheet->mergeCells('A' . $row . ':F' . $row);
    $sheet->setCellValue("A{$row}", $addressText);
    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
}

function printDate($sheet, $row) {
    $currentDate = date("d.m.Y");
    $dateText = "Дата получения заказа: {$currentDate}";
    $sheet->mergeCells('A' . $row . ':F' . $row);
    $sheet->setCellValue('A' . $row, $dateText);
    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
}

function printTableHeaders($sheet, $row) {
    $sheet->setCellValue("A{$row}", '№');
    $sheet->setCellValue("B{$row}", 'Наименование товара');
    $sheet->setCellValue("C{$row}", 'Цвет');
    $sheet->setCellValue("D{$row}", 'Цена');
    $sheet->setCellValue("E{$row}", 'Количество');
    $sheet->setCellValue("F{$row}", 'Сумма');
    $sheet->getStyle("A{$row}:F{$row}")->getFont()->setBold(true);
}

function printGuranteeText($sheet, $row, $filePath) {
    $styleText = [
        'alignment' => [
            'wrapText' => true
        ],
    ];

    $guranteeText = file_get_contents($filePath);

    $lines = explode("\n", $guranteeText);
    $numLines = count($lines);

    $sheet->mergeCells('A' . $row . ':F' . $row + $numLines + 3);
    $sheet->setCellValue('A' . $row, $guranteeText);

    $sheet->getStyle('A' . $row)->applyFromArray($styleText);

}


function getColorMultiplier($color) {
    switch ($color) {
        case "Орех":
            return 1.1;
        case "Дуб мореный":
            return 1.2;
        case "Палисандр":
            return 1.3;
        case "Эбеновое дерево":
            return 1.4;
        case "Клен":
            return 1.5;
        case "Лиственница":
            return 1.6;
        default:
            return 1;
    }
}

function getFileName($furnitureColor) {
    switch ($furnitureColor) {
        case "Орех":
            return "walnut";
        case "Дуб мореный":
            return "oak";
        case "Палисандр":
            return "rosewood";
        case "Эбеновое дерево":
            return "ebony";
        case "Клен":
            return "maple";
        case "Лиственница":
            return "larch";
        default:
            return "";
    }
}

function printColorImage($sheet, $row, $colorImagePath) {
    // Проверяем существует ли изображение
    if (file_exists($colorImagePath)) {
        // Вставляем изображение
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Image');
        $drawing->setDescription('Image');
        $drawing->setPath($colorImagePath);
        $drawing->setCoordinates("C{$row}");
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(3);
        $drawing->setWidth(35); // Устанавливаем ширину изображения
        $drawing->setHeight(35); // Устанавливаем высоту изображения
        $drawing->setWorksheet($sheet);
    } else {
        echo "Не найдено изображение {$colorImagePath}";
    }
}

function setTableStyles($sheet, $firstTableRow, $lastTableRow) {
    $styleTable = [
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];

    $sheet->getStyle("A{$firstTableRow}:F{$lastTableRow}")->applyFromArray($styleTable);
    $sheet->getStyle('F' . ($lastTableRow + 1))->applyFromArray($styleTable);
}

function printTableSummary($sheet, $row, $totalPrice) {
    $sheet->mergeCells('B' . $row . ':E' . $row);
    $sheet->setCellValue("B{$row}", 'Итого:');
    $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B{$row}")->getFont()->setBold(true);
    $sheet->setCellValue("F{$row}", $totalPrice);
    $sheet->getStyle("F{$row}")->getFont()->setBold(true);
}

function printColorRow($sheet, $row, $furnitureColor, $price, $colorMultiplier) {
    $sheet->mergeCells('A' . $row . ':A' . $row + 1);
    $sheet->mergeCells('B' . $row . ':B' . $row + 1);
    $sheet->mergeCells('C' . $row . ':C' . $row + 1);
    $sheet->mergeCells('D' . $row . ':E' . $row + 1);
    $sheet->mergeCells('F' . $row . ':F' . $row + 1);
    $sheet->setCellValue('B' . $row, 'Цвет: ' . $furnitureColor);

    $sheet->setCellValue("D{$row}", $colorMultiplier);
    $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $colorFileName = getFileName($furnitureColor);
    $colorImagePath = "img/{$colorFileName}.png";
    printColorImage($sheet, $row, $colorImagePath);

    $sheet->setCellValue("F{$row}", $price);
}

function printSummary($sheet, $row, $itemsCount, $totalPrice) {
    $summary = "Всего наименований: $itemsCount, на общую сумму: $totalPrice";
    $sheet->setCellValue('A' . $row, $summary);
}

function setAutosize($sheet) {
    foreach (range('B', 'F') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
}

function generateInvoice($sheet, $invoiceNumber, $furnitureItems, $furnitureColor, $furniturePrices, $city, $address, $colorMultiplier) {
    $row = 1;

    $sheet->mergeCells("C{$row}:F" . $row + 3);

    $colorImagePath = "img/barcode.png";
    printBarcode($sheet, $colorImagePath, $row);

    $row++;
    $row++;
    $row++;
    $row++;

    printInvoiceHeader($sheet, $row, $invoiceNumber);

    $row++;

    printAddres($sheet, $row, $city, $address);

    $row++;

    printDate($sheet, $row);

    $row++;
    $row++;

    $firstTableRow = $row;

    printTableHeaders($sheet, $firstTableRow);
    $row++;

    $itemNumber = 1;
    $price = 0;
    foreach ($furnitureItems as $itemName => $quantity) {
        $itemPrice = $furniturePrices[$itemName];
        $totalItemPrice = $itemPrice * $quantity;

        // Заполняем строки таблицы
        $sheet->setCellValue('A' . $row, $itemNumber);
        $sheet->setCellValue('B' . $row, $itemName);
        $sheet->setCellValue('D' . $row, $itemPrice);
        $sheet->setCellValue('E' . $row, $quantity);
        $sheet->setCellValue('F' . $row, $totalItemPrice);
        $row++;
        $itemNumber++;
        $price += $totalItemPrice;
    }

    printColorRow($sheet, $row, $furnitureColor, $price, $colorMultiplier);

    $row++;

    $lastTableRow = $row;

    $row++;

    $totalPrice = $price * $colorMultiplier;
    printTableSummary($sheet, $row, $totalPrice);

    setTableStyles($sheet, $firstTableRow, $lastTableRow);

    $row++;
    $row++;

    $itemsCount = count($furnitureItems);
    printSummary($sheet, $row, $itemsCount, $totalPrice);

    $row++;
    $row++;

    $guranteeFilePath = 'txt/gurantee.txt';
    printGuranteeText($sheet, $row, $guranteeFilePath);

    $lastRow = $row;
    $sheet->getStyle("A1:F{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);;

    setAutosize($sheet);
    return $totalPrice;
}

function prepareResultsFolder() {
    if (!file_exists('result')) {
        mkdir('result', 0777, true); // Создаем папку result с правами доступа 0777
    }

    $files = glob('result/*'); // Получаем все файлы в папке
    foreach($files as $file){ // Перебираем файлы
        if(is_file($file)) unlink($file); // Удаляем файл
    }
}

function saveFile($spreadsheet, $filePath) {
    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);
}
?>
