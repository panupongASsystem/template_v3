<?php

// Function to convert Thai date text to datetime format
function convertThaiDateToDatetime($textDate)
{
    // Map Thai month abbreviations to English month names
    $thaiMonths = array(
        'ม.ค' => 'Jan',
        'ก.พ' => 'Feb',
        'มี.ค' => 'Mar',
        'เม.ย' => 'Apr',
        'พ.ค' => 'May',
        'มิ.ย' => 'Jun',
        'ก.ค' => 'Jul',
        'ส.ค' => 'Aug',
        'ก.ย' => 'Sep',
        'ต.ค' => 'Oct',
        'พ.ย' => 'Nov',
        'ธ.ค' => 'Dec'
    );

    // Replace Thai month abbreviations with English month names
    foreach ($thaiMonths as $thaiMonth => $englishMonth) {
        $textDate = str_replace($thaiMonth, $englishMonth, $textDate);
    }

    // Debugging: Output the modified text date after replacing Thai month abbreviations
    // echo "Modified Text Date: $textDate<br>";

    // Extract the year from the input text date
    if (preg_match('/(\d{1,2}) (\w{3}\.?) (\d{2})(?: (\d{2}):(\d{2}))?/', $textDate, $matches)) {
        $day = isset($matches[1]) ? $matches[1] : '';
        $month = isset($matches[2]) ? rtrim($matches[2], ".") : ''; // Remove trailing period if present
        $year = isset($matches[3]) ? $matches[3] : '';
        $hour = isset($matches[4]) ? $matches[4] : '00';
        $minute = isset($matches[5]) ? $matches[5] : '00';

        // Debugging: Output extracted date components
        // echo "Day: $day, Month: $month, Year: $year, Hour: $hour, Minute: $minute<br>";
    } else {
        // Debugging: Output the input text date and the result of the regular expression match
        echo "Input Text Date: " . $textDate . "<br>";
        echo "Regular Expression Result: " . (preg_match('/(\d{1,2}) (\w{3}) (\d{2})(?: (\d{2}):(\d{2}))?/', $textDate, $matches) ? "Matched" : "Not Matched") . "<br>";

        // If the regular expression does not match, handle the error here
        // For example, you can set default values or return an error message
        echo "Failed to extract date components from the input text date.";
        exit(); // Terminate the script execution
    }

    // Convert the year to the Gregorian calendar year
    $gregorianYear = ($year + 2500) - 543;

    // Combine the day, month, and Gregorian year into the new text date
    $textDate = "$day $month $gregorianYear BE $hour:$minute";

    // Define the format of the input text date
    $inputFormat = 'd M Y BE H:i';

    // Create a DateTime object from the input text date using the specified format
    $dateTime = DateTime::createFromFormat($inputFormat, $textDate);

    // Check if the conversion was successful
    if ($dateTime !== false) {
        // Convert DateTime object to another format if needed
        $outputFormat = 'Y-m-d H:i:s'; // Output format with date and time
        $formattedDateTime = $dateTime->format($outputFormat);

        // Return the formatted datetime
        return $formattedDateTime;
    } else {
        // Return an error message if the conversion failed
        return "Failed to convert the text date to datetime.";
    }
}


// Define the JSON URL
$json_url = "https://govspending.data.go.th/api/service/cgdcontract?api-key=TH3JFBwJZlaXdDCpcVfSFGuoofCJ1heX&dept_code=6450704&year=2566&limit=500";

// Get the JSON data from the URL
$json_data = file_get_contents($json_url);

// Decode the JSON data into an associative array
$data = json_decode($json_data, true);

// Check if the 'result' key exists in the data
if (isset($data['result']) && is_array($data['result'])) {
    $results = $data['result'];

    // Function to flatten nested arrays and combine keys
    function flattenArray($array, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // Recursively flatten nested arrays
                $result += flattenArray($value, $prefix . $key . '_');
            } else {
                // Combine keys with underscores
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }

    // Flatten the array and filter out elements with missing keys
    $flattenedResults = [];
    foreach ($results as $result) {
        $flattenedResult = flattenArray($result);
        if (array_key_exists('project_id', $flattenedResult)) {
            $flattenedResults[] = $flattenedResult;
        }
    }

    // Remove the index '0' from 'contract' headers
    foreach ($flattenedResults as &$row) {
        $row = array_combine(
            array_map(function ($key) {
                return str_replace('_0_', '_', $key);
            }, array_keys($row)),
            $row
        );

        // Convert Thai date text to datetime format for specific fields
        $fieldsToConvert = ['announce_date', 'transaction_date', 'contract_contract_date', 'contract_contract_finish_date'];
        foreach ($fieldsToConvert as $field) {
            if (isset($row[$field]) && $row[$field] !== '-') {
                $row[$field] = convertThaiDateToDatetime($row[$field]);
            }
        }
    }
    // // Debugging: Output the structure of the data
    // echo "<pre>";
    // print_r($flattenedResults);
    // echo "</pre>";

    // Function to display data in HTML table format
    function displayDataInTable($data)
    {
        // Start table
        echo "<table border='1'>";

        // Display header row
        echo "<tr>";
        foreach ($data[0] as $key => $value) {
            // Remove index number from the header
            $key = preg_replace('/^\d+_/', '', $key);
            echo "<th>{$key}</th>";
        }
        echo "</tr>";

        // Display data rows
        foreach ($data as $row) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>{$value}</td>";
            }
            echo "</tr>";
        }

        // End table
        echo "</table>";
    }

    displayDataInTable($flattenedResults);
} else {
    echo "No data found.";
}
