<?php
ob_start();  // Start output buffering

include 'nav.php';
require_once('lib/TCPDF-main/tcpdf.php');

// Fetch user's plans from the database
$query = "SELECT * FROM plan WHERE userEmail = '$email'";
$result = $conn->query($query);
$fullName = $fname . " " . $lname;

// Create new PDF document
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($fullName);
$pdf->SetTitle('Nutrition Plans');
$pdf->SetHeaderData('', 0, 'Nutrition Plans', "$fullName's Plans");

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins and auto page breaks
$pdf->SetMargins(15, 27, 15);
$pdf->SetAutoPageBreak(TRUE, 25);

// Add a page
$pdf->AddPage();

// Set content
$html = "<h2>{$fullName}'s Nutrition Plans</h2>";
$html .= "<p>You have {$result->num_rows} plan(s).</p>";
$html .= "<table border='1' cellpadding='4'>
            <thead>
                <tr>
                    <th><strong>#</strong></th>
                    <th><strong>Initial Weight (kg)</strong></th>
                    <th><strong>Target Weight (kg)</strong></th>
                    <th><strong>Start Date</strong></th>
                    <th><strong>Due Date</strong></th>
                    <th><strong>Description</strong></th>
                    <th><strong>Status</strong></th>
                </tr>
            </thead>
            <tbody>";

if ($result->num_rows > 0) {
    $i = 1;
    while ($row = $result->fetch_assoc()) {
        $statusText = ($row['statusID'] == 4) ? 'Opened' : 'Closed';
        $html .= "<tr>
                    <td>{$i}</td>
                    <td>{$row['InitialWeight']}</td>
                    <td>{$row['targetWeight']}</td>
                    <td>{$row['startDate']}</td>
                    <td>{$row['dueDate']}</td>
                    <td>{$row['description']}</td>
                    <td>{$statusText}</td>
                </tr>";
        $i++;
    }
} else {
    $html .= "<tr><td colspan='7' align='center'>No plans found.</td></tr>";
}

$html .= "</tbody></table>";

// Output the HTML content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Clean the output buffer before sending the PDF
ob_end_clean();

$pdf->Output('nutrition_plans.pdf', 'D');  // Use 'D' to force download instead of inline view
?>
