
<?php
// print_r($_SESSION);
session_start();

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

// database coneection for events table
$servername = "localhost";
$username = "localhost";
$password = "NAVneet345@";
$dbname = "myForm";

// DB connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create events table if not exists
$createTableSql = "CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_title VARCHAR(255) NOT NULL,
    startdate DATE DEFAULT NULL,
    enddate DATE DEFAULT NULL,
    adddate DATETIME DEFAULT NULL,
    editdate DATETIME DEFAULT NULL,
    user_email VARCHAR(100) NOT NULL
)";

$conn->query($createTableSql);

// Get current user's email safely
$userEmail = $_SESSION['email'] ?? '';
$safeEmail = $conn->real_escape_string($userEmail);
echo "<!-- Session email: $safeEmail -->";

// Logic Get all events for the selected month
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

// echo "<pre>";
// print_r($eventsByDate);
// echo "</pre>";
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
    <link rel="stylesheet" type="text/css" href="assests/css/style.css">
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

				   <!-- date selection auto like year and month by scrolling-->
					    <form method="GET" class=" col-5 d-flex gap-2 align-items-center"> 
					    	<!-- month selection dropdown-->
					    	<select name="month" class="form-select" style="width: auto;">
					    		<!--loop for month-->
					    		<?php
					    			for($m=0; $m<=12 ;$m++)
					    			{
					    				// we will check if selected month is equal to the loop in current month 
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
						<table class="table table-bordered w-100 table-fixed">
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

								    for ($day = 1; $day <= $daysInMonth; $day++) {
								    	// check if date is today's
								        $isToday = ($day == date('j') && $month == date('n') && $year == date('Y'));
								        $class = $isToday ? "class='today'" : "";

								        // new added to check the date when this was addded as my swl will only work fine if this is added here becouse day is defined first otherwise it will give 00 FOR DATE
                                        $fullDate = sprintf('%04d-%02d-%02d', $year, $month, $day);

								        // Check if the current day is a Saturday or Sunday
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

		                                // show events if there exist any in same cell div
		                                if (isset($eventsByDate[$fullDate])) {
		                                    foreach ($eventsByDate[$fullDate] as $event) {
		                                    	// first div for strip and inside it are span elements for text and two buttons
		                                        echo "<div class='event-strip'>";
		                                        echo "<span class='event-text'>". htmlspecialchars($event) ."</span>";
		                                        echo "<span class='event-actions'>
		                                        	 '<button class='edit-btn'><i class='fa fa-pencil'></i></button>'
		                                        	 '<button class='dlt-btn' onclick=\"deleteEvent($eventId)\">
		                                        	  <i class='fa fa-remove'></i></button>'
		                                        	</span>";
		                                        echo"</div>";
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
                location.reload();
            } else {
                alert("Error saving event.");
            }
        });
    }
}
</script> -->

<!-- Script for add event -->
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
        // alert(msg);
        location.reload(); // Refresh to show new event
    })
    .catch(err => {
        alert("Error saving event.");
        console.error(err);
    });
}
</script>

<!-- Script for DELETE event -->
<script >
function deleteEvent(eventId) {
	if(!confirm("Do you want to delete event?")) return;
	
	const formData = new FormData();
	formData.append('event_id',eventId);

	fetch('delete_event.php',
	{
		method:'POST',
		body: formData,
	})
	.then(res =>res.text())
	.then(msg =>{

		location.reload(); //Refresh the page to show deleted event 
	})
	.catch(err=>{
		alert("Error deleting event.Please try again!.");
		console.error(err);

	});
	
}
</script>


</body>
</html>