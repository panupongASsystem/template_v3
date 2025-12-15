<?php

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
    function flattenArray($array, $prefix = '') {
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

    // Function to display data in HTML table format
    function displayDataInTable($data) {
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

    // Flatten the array and display data in HTML table format
    $flattenedResults = array_map('flattenArray', $results);

    // Remove the index '0' from 'contract' headers
    foreach ($flattenedResults as &$row) {
        $row = array_combine(
            array_map(function ($key) {
                return str_replace('_0_', '_', $key);
            }, array_keys($row)),
            $row
        );
    }

    displayDataInTable($flattenedResults);
} else {
    echo "No data found.";
}

?>
