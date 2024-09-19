<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Calculator</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input, button {
            margin: 10px 0;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            width: 100%;
            font-size: 16px;
        }
        input:focus, button:focus {
            border-color: #007BFF;
            outline: none;
        }
        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 18px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .result {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f8f9fa;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        p {
            font-size: 16px;
            color: #555;
        }

        /* Notification styles */
        .notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease, top 0.3s ease;
        }

        .notification.error {
            background-color: #dc3545;
        }

        .notification.show {
            display: block;
            opacity: 1;
            top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Data Calculator</h1>
        <label for="keterangan">Program hitung statiska untuk kelas MI 1B</label>
        <form method="post">
            <label for="data">Enter data (comma-separated):</label>
            <input type="text" id="data" name="data" placeholder="pisahkan dengan (, setiap angka), 50, 65, 60, ..." required>
            <button type="submit">Calculate</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Ambil data dari form input
            $dataInput = $_POST['data'];

            // Konversi input string menjadi array angka
            $dataArray = array_map('floatval', array_filter(array_map('trim', explode(',', $dataInput)), 'is_numeric'));

            if (empty($dataArray)) {
                echo '<div class="notification error show">No valid data entered.</div>';
            } else {
                // Hitung xmin, xmax
                $xmin = min($dataArray);
                $xmax = max($dataArray);

                // Hitung jumlah kelas (k)
                $numClasses = ceil(1 + 3.3 * log10(count($dataArray)));

                // Hitung range step
                $rangeStep = ceil(($xmax - $xmin) / $numClasses);
                $ranges = [];
                for ($i = $xmin; $i <= $xmax; $i += $rangeStep) {
                    $ranges[] = ['start' => $i, 'end' => $i + $rangeStep - 1, 'count' => 0];
                }

                // Hitung frekuensi tiap range
                foreach ($dataArray as $value) {
                    foreach ($ranges as &$range) {
                        if ($value >= $range['start'] && $value <= $range['end']) {
                            $range['count']++;
                            break;
                        }
                    }
                }

                // Tampilkan hasil
                echo '
                    <div class="result">
                        <p><strong>Total Number of Data Points:</strong> ' . count($dataArray) . '</p>
                        <p><strong>Number of Classes (k):</strong> ' . $numClasses . '</p>
                        <p><strong>Minimum (xmin):</strong> ' . number_format($xmin, 2) . '</p>
                        <p><strong>Maximum (xmax):</strong> ' . number_format($xmax, 2) . '</p>
                        <p><strong>Range Step:</strong> ' . $rangeStep . '</p>
                        <p><strong>Sorted Data:</strong></p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Index</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>';
                foreach ($dataArray as $index => $value) {
                    echo '
                        <tr>
                            <td>' . ($index + 1) . '</td>
                            <td>' . number_format($value, 2) . '</td>
                        </tr>';
                }
                echo '
                            </tbody>
                        </table>
                    </div>';
                echo '<div class="notification show">Calculation successful!</div>';
            }
        }
        ?>
    </div>
</body>
</html>
