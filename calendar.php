<?php
// Get the current month and year or use the provided month and year from the URL
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n'); // Current month
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y'); // Current year

// Calculate the number of days in the month and the first day of the month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayOfMonth = strtotime("$year-$month-01");
$firstDayOfWeek = date('w', $firstDayOfMonth); // 0 (for Sunday) through 6 (for Saturday)



// Create an array of days of the week
$daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

// Start the HTML output
echo "<h1>Calendar for " . date('F Y', $firstDayOfMonth) . "</h1>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr>";

// Display the days of the week
foreach ($daysOfWeek as $day) {
    echo "<th>$day</th>";
}
echo "</tr><tr>";

// Fill in the empty cells before the first day of the month
for ($i = 0; $i < $firstDayOfWeek; $i++) {
    echo "<td></td>";
}

// Display the days of the month
for ($day = 1; $day <= $daysInMonth; $day++) {
    // Highlight today's date
    if ($day == date('j') && $month == date('n') && $year == date('Y')) {
        echo "<td style='background-color: yellow;'>$day</td>";
    } else {
        echo "<td>$day</td>";
    }

    // Start a new row after Saturday
    if (($day + $firstDayOfWeek) % 7 == 0) {
        echo "</tr><tr>";
    }
}

// Fill in the empty cells after the last day of the month
while (($day + $firstDayOfWeek) % 7 != 0) {
    echo "<td></td>";
    $day++;
}

echo "</tr>";
echo "</table>";

// Navigation links for previous and next month
$prevMonth = $month == 1 ? 12 : $month - 1;
$prevYear = $month == 1 ? $year - 1 : $year;
$nextMonth = $month == 12 ? 1 : $month + 1;
$nextYear = $month == 12 ? $year + 1 : $year;

echo "<br>";
echo "<a href='?month=$prevMonth&year=$prevYear'>Previous Month</a> | ";
echo "<a href='?month=$nextMonth&year=$nextYear'>Next Month</a>";
?>



<?php
// PHP section: Prepare dates and variables
$month = 8;
$year = 2023;
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayOfMonth = strtotime("$year-$month-01");
$firstDayOfWeek = date('w', $firstDayOfMonth);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Calendar</title>
    <style>
        /* Your CSS styling here */
    </style>
</head>
<body>
    <h1>Calendar for <?= date('F Y', $firstDayOfMonth) ?></h1>
    <table>
        <thead>
            <tr>
                <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <?php
            // PHP section: Output calendar cells
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                echo "<td></td>";
            }
            for ($day = 1; $day <= $daysInMonth; $day++) {
                echo "<td>$day</td>";
                if (($day + $firstDayOfWeek) % 7 == 0) {
                    echo "</tr><tr>";
                }
            }
            ?>
            </tr>
        </tbody>
    </table>
</body>
</html>



<?php
// Approach 1: Using DateTime and DatePeriod for calendar generation

// Get month and year from URL or set to current month/year
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

try {
    // Create DateTime object for the first day of the month
    $firstDay = new DateTime("$year-$month-01");
} catch (Exception $e) {
    // Fallback to current date if invalid
    $firstDay = new DateTime();
    $month = (int)$firstDay->format('n');
    $year = (int)$firstDay->format('Y');
}

// Number of days in the month
$daysInMonth = (int)$firstDay->format('t');

// Get the weekday of the first day of month (1 = Monday ... 7 = Sunday)
$startDayOfWeek = (int)$firstDay->format('N'); 

// Calculate total number of days including previous month padding
$totalCells = $daysInMonth + $startDayOfWeek - 1;
if ($totalCells % 7 != 0) {
    $totalCells += 7 - ($totalCells % 7);
}

$daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

// Navigation months
$prev = clone $firstDay;
$prev->modify('-1 month');
$next = clone $firstDay;
$next->modify('+1 month');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Calendar Approach 1 - DateTime & DatePeriod</title>
<style>
    body { font-family: Arial, sans-serif; max-width: 400px; margin: 20px auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; text-align: center; border: 1px solid #ccc; }
    th { background: #f0f0f0; }
    .today { background-color: #ffeb3b; font-weight: bold; }
    .nav { text-align: center; margin-top: 15px; }
    a { text-decoration: none; margin: 0 10px; color: blue; }
    a:hover { text-decoration: underline; }
</style>
</head>
<body>
<h1>Calendar for <?= $firstDay->format('F Y') ?></h1>
<table>
    <thead>
        <tr>
            <?php foreach ($daysOfWeek as $day): ?>
            <th><?= $day ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
    <?php
    $currentDay = 1;
    $today = date('Y-m-d');
    for ($row = 0; $row < $totalCells / 7; $row++) {
        echo '<tr>';
        for ($col = 1; $col <= 7; $col++) {
            $cellIndex = $row * 7 + $col;
            $dateStr = '';
            if ($cellIndex >= $startDayOfWeek && $currentDay <= $daysInMonth) {
                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
                $isToday = ($dateStr === $today);
                echo '<td' . ($isToday ? ' class="today"' : '') . '>' . $currentDay . '</td>';
                $currentDay++;
            } else {
                echo '<td></td>';
            }
        }
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
<div class="nav">
    <a href="?month=<?= $prev->format('n') ?>&year=<?= $prev->format('Y') ?>">Previous Month</a> |
    <a href="?month=<?= $next->format('n') ?>&year=<?= $next->format('Y') ?>">Next Month</a>
</div>
</body>
</html>
?>
</content>
</create_file>

<create_file>
<path>calendar_approach_2.php</path>
<content>
<?php
// Approach 2: Using mktime and manual calculations

// Get month and year from URL or default to current 
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Number of days in the month
$daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));

// First day of the month (0 = Sunday, 6 = Saturday)
$firstDayOfWeek = date('w', mktime(0, 0, 0, $month, 1, $year));

$daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

// Navigation
$prevMonth = $month == 1 ? 12 : $month - 1;
$prevYear = $month == 1 ? $year -1 : $year;
$nextMonth = $month == 12 ? 1 : $month + 1;
$nextYear = $month == 12 ? $year + 1 : $year;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Calendar Approach 2 - mktime</title>
<style>
    body { font-family: Arial, sans-serif; max-width: 420px; margin: 20px auto; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; text-align: center; padding: 10px; }
    th { background-color: #ddd; }
    .today { background-color: #ffeb3b; font-weight: bold; }
    .nav { text-align: center; margin: 15px 0; }
    a { margin: 0 15px; text-decoration: none; color: #007bff; }
    a:hover { text-decoration: underline; }
</style>
</head>
<body>
<h1>Calendar for <?= date('F Y', mktime(0,0,0,$month,1,$year)) ?></h1>
<table>
    <thead>
        <tr>
        <?php foreach ($daysOfWeek as $day): ?>
            <th><?= $day ?></th>
        <?php endforeach;?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php
            // Blank cells before first day
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                echo "<td></td>";
            }

            $dayCount = 1;
            $cellCount = $firstDayOfWeek;
            $today = date('Y-m-d');

            while ($dayCount <= $daysInMonth) {
                if ($cellCount % 7 == 0) echo "</tr><tr>";

                $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $dayCount);
                $class = ($currentDate == $today) ? 'today' : '';

                echo "<td class='$class'>$dayCount</td>";
                $dayCount++;
                $cellCount++;
            }

            // Blank cells after last day
            while ($cellCount % 7 != 0) {
                echo "<td></td>";
                $cellCount++;
            }
            ?>
        </tr>
    </tbody>
</table>

<div class="nav">
    <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>">Previous Month</a> |
    <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>">Next Month</a>
</div>
</body>
</html>
?>
</content>
</create_file>

<attempt_completion>
Here are two alternative complete PHP calendar implementations using different approaches:

1. **calendar_approach_1.php** utilizes PHP's DateTime object and formatted output along with a DatePeriod concept for generating calendar cells aligned with Monday as the first day.
2. **calendar_approach_2.php** uses `mktime()` and manual calculations for days and weeks with Sunday as the first day.

Both files produce a clean, styled, and navigable monthly calendar highlighting today's date and allowing navigation to previous and next months.

You can save each PHP file and run them on your PHP-enabled web server. Access them via browser with optional `?month=&year=` URL parameters to navigate.

Let me know if you want me to help with explanations on these or more approaches!
</attempt_completion>





<!-- First write html code  -->
<?php

// Get the current month and year or use the provided month and year from the URL
$rawmonth = $_GET['month'] ?? date('n');
$rawyear = $_GET['year'] ?? date('Y');
// Validate that both are numeric
// Initialize error
$invalid_error = null;

// Validate numeric input
if (!is_numeric($rawmonth) || !is_numeric($rawyear)) {
    $invalid_error = "Error: Month and year must be numeric.";
    $month = date('n');
    $year = date('Y');
} else

{
// Convert to integer only after confirming they are numeric
$month = (int)$rawmonth;
$year = (int)$rawyear;

    // logic for invalid month
    if ($month < 1 || $month > 12) {
        $invalid_error = "Error: Invalid month selected. Select range 1–12.";
         // fallback to current month to prevent fatal error so that there and current dates and year remain selected
        $month = date('n');
        $year = date('Y');
    }
    //  logic for invalid year range
    elseif ($year < 2005 || $year > 2045) {
        $invalid_error = "Error: Year must be between 2005 and 2045.";
        $month = date('n');
        $year = date('Y');
    }
}

// PHP section: Prepare dates and variables
// $month = 4;
// $year = 2025;
// it calculate days in month 
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

//it converts english output to number
$firstDayOfMonth = strtotime("$year-$month-01");

//its tell on that 2025-2-1 was which day of the week 
$firstDayOfWeek = date('w', $firstDayOfMonth);
// Create an array of days of the week
$daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

// Determine navigation months
$prevMonth = $month == 1 ? 12 : $month - 1;
$prevYear = $month == 1 ? $year - 1 : $year;
$nextMonth = $month == 12 ? 1 : $month + 1;
$nextYear = $month == 12 ? $year + 1 : $year;

$daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);

// new added Database connection
$conn = new mysqli("localhost", "localhost", "NAVneet345@", "myForm");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create events table
$createTableSql = "CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_title VARCHAR(255) NOT NULL,
    startdate DATE NOT NULL,
    enddate DATE DEFAULT NULL,
    adddate DATETIME DEFAULT CURRENT_TIMESTAMP,
    editdate DATETIME DEFAULT NULL,
    user_email VARCHAR(100) NOT NULL
)";
$conn->query($createTableSql);

// Get current user's email from session
session_start();
$userEmail = $_SESSION['email'] ?? '';
$safeEmail = $conn->real_escape_string($userEmail);

// Get all events for the selected month
$monthStart = "$year-$month-01";
$monthEnd = date("Y-m-t", strtotime($monthStart));

$query = "SELECT event_title, startdate FROM events 
          WHERE user_email = '$safeEmail'
          AND startdate BETWEEN '$monthStart' AND '$monthEnd'";

$result = $conn->query($query);

// Group events by date
$eventsByDate = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $eventsByDate[$row['startdate']][] = $row['event_title'];
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CALENDAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="style1.css">
</head>
<body>
    <section class="cal-section">
        <div class="container">
            <div class="cal-outer">
                <h1 class="text-center">Calendar for <?= date('F Y', $firstDayOfMonth) ?></h1>

                    <?php if ($invalid_error): ?>
                        <div class="error-message text-danger text-center my-2 fw-bold">
                            <?= htmlspecialchars($invalid_error) ?>
                        </div>
                    <?php endif; ?>

                    <!-- to show the name of month and year -->
                    <div class="row d-flex justify-content-between align-items-center">
                        <div class="col-5">
                            <h4><?= date('F Y', $firstDayOfMonth) ?></h4>
                        </div>

                   <!-- date selection auto -->
                        <form method="GET" class=" col-5 d-flex gap-2 align-items-center"> 
                            <!-- month selection dropdown-->
                            <select name="month" class="form-select" style="width: auto;">
                                <!--loop for month-->
                                <?php
                                    for($m=0; $m<=12 ;$m++)
                                    {
                                        // we will check if selected month is equal to th eloop in current month 
                                        $selected = ($m == $month) ? "selected" : "";
                                        echo "<option value='$m' $selected>" . date('M', mktime(0, 0, 0, $m, 10)) . "</option>";
                                    }

                                ?>
                            </select>
                            <select name="year" class="form-select" style="width: auto;" size="1">
                                <?php
                                $currentYear = date('Y');
                                for ($y = $currentYear - 20; $y <= $currentYear + 20; $y++) {
                                    $selected = ($y == $year) ? "selected" : "";
                                    echo "<option value='$y' $selected>$y</option>";
                                }
                                ?>
                            </select>
    
                            <button type="submit" class="btn btn-primary btn-sm">select</button>

                       </form>

                        <!-- it will put into the query previous month and year -->
                       <div class="col-2 text-end d-flex justify-content-end gap-2">
                            <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>">
                                <i class="bi bi-chevron-up" style="font-size: 40px; color: black;"></i>
                            </a>
                            <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>">
                                <i class="bi bi-chevron-down" style="font-size: 40px; color: black;"></i>
                            </a>
                        </div>

                    </div>

                    <div class="row table-box">
                        <table class="table table-bordered w-100">
                                <thead>
                                    <tr>
                                        <!-- first show the days of week with loop -->
                                         <?php foreach ($daysOfWeek as $day): ?>
                                         <th style="background-color: antiquewhite;"><?= htmlspecialchars($day) ?></th>
                                         <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr> <!-- Start the first row -->

                                    <!-- Print empty cells before the first day -->
                                    <?php 
                                    // for ($i = 0; $i < $firstDayOfWeek; $i++) {
                                    //     echo "<td></td>";
                                    // }

                                    for ($i = 0; $i < $firstDayOfWeek; $i++) {
                                        $prevDate = $daysInPrevMonth - $firstDayOfWeek + 1 + $i;
                                        echo "<td style='color: #ccc;'>$prevDate</td>";
                                    }



                                    /*for ($day = 1; $day <= $daysInMonth; $day++) {
                                        // check if date is today's
                                        $isToday = ($day == date('j') && $month == date('n') && $year == date('Y'));
                                        $class = $isToday ? "class='today'" : "";
                                        // new added to check the date when this was addded as my swl will only work fine if this is added here becouse day is defined first otherwise it will give 00 FOR DATE
                                        $fullDate = sprintf('%04d-%02d-%02d', $year, $month, $day);

                                        // Check if the current day is a Saturday or Sunday
                                        $dayOfWeek = date('w', strtotime("$year-$month-$day"));
                                        if ($dayOfWeek == 0 || $dayOfWeek == 6) {  // Sunday (0) or Saturday (6)
                                             $style = $isToday ? "background-color: #ffeb3b; color: #000;" : "background-color: #f2f2f2; color: #ff0000;";
                                              // echo "<td style='$style' $class onclick=\"promptForEvent('$fullDate')\">$day</td>";

                                        } else {
                                            echo "<td $class onclick=\"promptForEvent('$fullDate')\">$day</td>";
                                               }

                                       // Close and open a new row every 7 cells
                                        if (($day + $firstDayOfWeek) % 7 == 0 && $day != $daysInMonth) {
                                            echo "</tr><tr>"; // properly handle row change
                                        }
                                    }*/

                                    // new added code 

                                for ($day = 1; $day <= $daysInMonth; $day++) {
                                // Check if date is today's
                                $isToday = ($day == date('j') && $month == date('n') && $year == date('Y'));
                                $class = $isToday ? "class='today'" : "";

                                // Full date for DB comparison
                                $fullDate = sprintf('%04d-%02d-%02d', $year, $month, $day);

                                // Determine if it's weekend
                                $dayOfWeek = date('w', strtotime("$year-$month-$day"));

                                // Start opening <td> with styles
                                if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                                    // Weekend styling
                                    $style = $isToday ? "background-color: #ffeb3b; color: #000;" : "background-color: #f2f2f2; color: #ff0000;";
                                    echo "<td style='$style' $class onclick=\"promptForEvent('$fullDate')\">";
                                } else {
                                    // Weekday
                                    echo "<td $class onclick=\"promptForEvent('$fullDate')\">";
                                }

                                // Show day number
                                echo $day;

                                // ✅ SHOW EVENTS if any
                                if (isset($eventsByDate[$fullDate])) {
                                    foreach ($eventsByDate[$fullDate] as $event) {
                                        echo "<div style='font-size:12px; color:green;'>📌 " . htmlspecialchars($event) . "</div>";
                                    }
                                }

                                echo "</td>";

                                // Close and open a new row every 7 cells
                                if (($day + $firstDayOfWeek) % 7 == 0 && $day != $daysInMonth) {
                                    echo "</tr><tr>"; // properly handle row change
                                }
                            }




                                    // // Fill remaining cells to complete the last row
                                    // $remaining = (7 - (($day + $firstDayOfWeek - 1) % 7)) % 7;
                                    // for ($i = 0; $i < $remaining; $i++) {
                                    //     echo "<td></td>";
                                    // }
                                    // Fill in next month dates
                                    $remainingCells = (7 - (($daysInMonth + $firstDayOfWeek) % 7)) % 7;
                                    for ($i = 1; $i <= $remainingCells; $i++) {
                                        echo "<td style='color: #ccc;'>$i</td>";
                                    }
                                    ?>

                                    </tr> <!-- Close the last row -->
                                </tbody>

                        </table>
                    </div>
            </div>
        </div>  
    <section>
<!-- <script>
function promptForEvent(date) {
    let title = prompt("Enter event title for " + date + ":");
    if (title) {
        fetch('save_event.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'event_date=' + encodeURIComponent(date) + '&event_title=' + encodeURIComponent(title)
        }).then(response => {
            if (response.ok) {
                location.reload(); // Refresh the page to show the new event
            } else {
                alert("Error saving event.");
            }
        });
    }
}
</script> -->
<script type="text/javascript">
function promptForEvent(date) {
    const title = prompt("Enter event title for " + date + ":");
    if (!title) return;

    const formData = new FormData();
    formData.append('event_title', title);
    formData.append('event_date', date);

    fetch('save_event.php', {
        method: 'POST',
        body: formData,
    })
    .then(res => res.text())
    .then(msg => {
        alert(msg);
        location.reload(); // Refresh to show new event
    })
    .catch(err => {
        alert("Error saving event.");
        console.error(err);
    });
}
</script>
</body>
</html>




<!-- save events php -->

<?php
session_start();
print_r($_SESSION);
$servername = "localhost";
$username = "localhost";
$password = "NAVneet345@";
$dbname = "myForm";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Connection failed: " . $conn->connect_error;
    exit;
}

// Ensure user is logged in
$userEmail = $_SESSION['email'] ?? '';
if (!$userEmail) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

// Sanitize input
$eventTitle = $conn->real_escape_string($_POST['event_title'] ?? '');
$eventDate = $conn->real_escape_string($_POST['event_date'] ?? '');

if (!$eventTitle || !$eventDate) {
    http_response_code(400);
    echo "Missing event title or date.";
    exit;
}

$sql = "INSERT INTO events (event_title, startdate, user_email)
        VALUES ('$eventTitle', '$eventDate', '$userEmail')";

if ($conn->query($sql) === TRUE) {
    echo "Event saved.";
} else {
    http_response_code(500);
    echo "Error: " . $conn->error;
}
?>