<?php
session_start();
include "dbcon.php";

if (!isset($_SESSION['control_number'])) {
    die("User session not set.");
}

$control_number = $_SESSION['control_number'];


if (!isset($_SESSION['exam_start_time'])) {
    $_SESSION['exam_start_time'] = time(); // Store current timestamp
}


if (!isset($_SESSION['remaining_time'])) {
    $_SESSION['remaining_time'] = 3600; // Initial remaining time (in seconds)
}

$page_size = 18; 
$page_number = isset($_GET['page']) ? $_GET['page'] : 1; 


$offset = ($page_number - 1) * $page_size;


$sql_reading = "SELECT questionID, questionText, ChoiceA, ChoiceB, ChoiceC, ChoiceD 
             FROM admin_reading_comprehension LIMIT $offset, $page_size";
$result_reading = $conn->query($sql_reading);

if (!$result_reading) {
    die("Query failed (Math): " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Exam Reading Comprehension</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap">
    <link rel="stylesheet" href="SPERC.css">
</head>

<body>
    <div class="d-flex">
        <div class="sidebar bg-dark-green" id="sidebar">
            <div class="logo-and-campus">
                <img src="public/CvSU_LOGO.png" alt="School Logo" class="school-logo" onclick="toggleSidebar()">
                <h4 class="SchoolName" style="display: none;">CvSU - Imus Campus</h4>
            </div>
            <div class="iconsandLabel">
                <a href="#">
                    <img src="./public/home1.svg" alt="Dashboard Icon" class="menu-icon">
                    <span class="menu-label">Dashboard</span>
                </a>
                <a href="#">
                    <img src="./public/vector2.svg" alt="Profile Icon" class="menu-icon">
                    <span class="menu-label">Profile</span>
                </a>
                <a href="#" style="border-right: 5px solid white;">
                    <img src="./public/union1.svg" alt="Exam Icon" class="menu-icon">
                    <span class="menu-label">Exam</span>
                </a>
                <a href="#">
                    <img src="./public/icon3.svg" alt="Result Icon" class="menu-icon">
                    <span class="menu-label">Result</span>
                </a>
            </div>
        </div>
        <div class="flex-grow-1">
            <div class="header d-flex justify-content-between align-items-center">
                <button class="btn btn-primary d-md-none" onclick="toggleSidebar()">☰</button>
                <h4>Exam</h4>
                <div class="header-icons">
                    <img src="public/icons8-notification-48.png" alt="Notification Icon" class="notification-icon">
                    <script src="Redirect.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
                    <button class="Btn">
                        <div class="sign">
                            <svg viewBox="0 0 512 512">
                                <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path>
                            </svg>
                        </div>
                        <div class="text" onclick="logoutRedirect()">Logout</div>
                    </button>
                </div>
            </div>
            <div class="content" id="content">
                <div class="d-flex align-items-center">
                    <div class="vertical-line"></div>
                    <div class="header-title">
                        <h1>Cavite State University Imus Campus</h1>
                        <p>Online Entrance Exam</p>
                    </div>
                </div>
                <div class="exam-category d-flex justify-content-between align-items-center">
                    <div class="exam-header">
                        <p>Test IV. Reading Comprehension</p>
                    </div>
                    <span id="timer" class="timer">01:00:00</span>
                </div>

                <div class="main-exam-container">
                    <div class="exam-instructions">
                        <?php

                        if ($page_number == 1) {
                            echo '<p>Read each passage carefully, please choose the best answer for the given question.</p>';
                            echo '<p style="font-style: italic;">"Butterflies are beautiful insects that can be found in many parts of the world. They have four wings covered with tiny scales. Butterflies go through a life cycle that includes four stages: egg, larva (caterpillar), pupa (chrysalis), and adult. They are known for their vibrant colors and patterns, which can serve as camouflage or warning signals to predators. Butterflies typically feed on nectar from flowers using their long, coiled proboscis."</p>';
                        } elseif ($page_number == 2) {
                            echo '<p>Read each passage carefully, please choose the best answer for the given question.</p>';
                            echo '<p style="font-style: italic;">"Rainforests are dense, tropical forests that receive high amounts of rainfall each year. They are home to a vast diversity of plant and animal species, many of which cannot be found anywhere else. Rainforests play a crucial role in regulating the Earth\'s climate by absorbing carbon dioxide and producing oxygen. The layers of a rainforest include the emergent layer, the canopy, the understory, and the forest floor. Unfortunately, rainforests are being destroyed at an alarming rate due to logging, agriculture, and urbanization."</p>';
                        }
                        ?>
                    </div>

                    <div class="exam-container">
                        <!-- content here -->
                        <form method="post" action="StudentExamReading.php">
                            <input type="hidden" name="category" value="<?php echo $current_category; ?>">
                            <?php
                            $question_number = ($page_number - 1) * $page_size + 1; 
                            while ($row = $result_reading->fetch_assoc()) {
                                echo '<div class="question-box">
                                    <div class="question-header">
                                        <span class="question-number">' . $question_number . '/18</span> <!-- Adjust the total number of questions dynamically -->
                                    </div>
                                    <hr>
                                    <p class="question-text">' . htmlspecialchars($row['questionText']) . '</p>
                                    <div class="radio-button">
                                        <ul class="choices">
                                            <li>
                                                <input type="radio" required  class="radio-button__input" id="radio1-q' . $question_number . '" name="answers[' . $row['questionID'] . ']" value="' . htmlspecialchars($row['ChoiceA']) . '">
                                                <label class="radio-button__label" for="radio1-q' . $question_number . '">
                                                    <span class="radio-button__custom"></span>
                                                    ' . htmlspecialchars($row['ChoiceA']) . '
                                                </label>
                                            </li>
                                            <li>
                                                <input type="radio" required  class="radio-button__input" id="radio2-q' . $question_number . '" name="answers[' . $row['questionID'] . ']" value="' . htmlspecialchars($row['ChoiceB']) . '">
                                                <label class="radio-button__label" for="radio2-q' . $question_number . '">
                                                    <span class="radio-button__custom"></span>
                                                    ' . htmlspecialchars($row['ChoiceB']) . '
                                                </label>
                                            </li>
                                            <li>
                                                <input type="radio" required  class="radio-button__input" id="radio3-q' . $question_number . '" name="answers[' . $row['questionID'] . ']" value="' . htmlspecialchars($row['ChoiceC']) . '">
                                                <label class="radio-button__label" for="radio3-q' . $question_number . '">
                                                    <span class="radio-button__custom"></span>
                                                    ' . htmlspecialchars($row['ChoiceC']) . '
                                                </label>
                                            </li>
                                            <li>
                                                <input type="radio" required class="radio-button__input" id="radio4-q' . $question_number . '" name="answers[' . $row['questionID'] . ']" value="' . htmlspecialchars($row['ChoiceD']) . '">
                                                <label class="radio-button__label" for="radio4-q' . $question_number . '">
                                                    <span class="radio-button__custom"></span>
                                                    ' . htmlspecialchars($row['ChoiceD']) . '
                                                </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>';

                                $question_number++;
                            }
                            ?>


                            <!-- End of loop -->

                            <!-- Pagination -->
                            <div class="pagination">
                                <?php
                                // Determine total number of pages based on total questions
                                // $sql_count = "SELECT COUNT(*) AS total FROM admin_reading_comprehension";
                                // $result_count = $conn->query($sql_count);
                                // $row_count = $result_count->fetch_assoc();
                                // $total_questions = $row_count['total'];
                                // $total_pages = ceil($total_questions / $page_size);


                                ?>
                            </div>
                            <div class="navigation-buttons">
                                <?php

                                $categories = ['logic', 'math', 'science', 'reading'];

                                $current_category = isset($_GET['category']) ? $_GET['category'] : $categories[1]; // Default to 'math'


                                $current_category_index = array_search($current_category, $categories);


                                $sql_count = "SELECT COUNT(*) AS total FROM admin_{$current_category}";
                                $result_count = $conn->query($sql_count);
                                $row_count = $result_count->fetch_assoc();
                                $total_questions = $row_count['total'];
                                $total_pages = ceil($total_questions / $page_size);


                                if ($current_category_index > 0) {
                                    $prev_category = $categories[$current_category_index - 1];

                                    $sql_prev_count = "SELECT COUNT(*) AS total FROM admin_{$prev_category}";
                                    $result_prev_count = $conn->query($sql_prev_count);
                                    $row_prev_count = $result_prev_count->fetch_assoc();
                                    $prev_total_questions = $row_prev_count['total'];
                                    $prev_total_pages = ceil($prev_total_questions / $page_size);
                                } else {
                                    $prev_category = null;
                                    $prev_total_pages = 1;
                                }


                                if ($page_number < $total_pages) {
                                    $next_page = $page_number + 1;
                                    $next_category = $current_category;
                                } else {
                                    $next_page = 1;
                                    $next_category = isset($categories[$current_category_index + 1]) ? $categories[$current_category_index + 1] : null;
                                }

                                // Display "Back" button
                                // if ($page_number > 1) {
                                //     $prev_page = $page_number - 1;
                                //     echo '<button class="back-button" onclick="location.href=\'?category=' . $current_category . '&page=' . $prev_page . '\'">Back</button>';
                                // } else if ($prev_category !== null) {
                                //     echo '<button class="back-button" onclick="location.href=\'StudentExam' . ucfirst($prev_category) . '.php?category=' . $prev_category . '&page=' . $prev_total_pages . '\'">Back</button>';
                                // } else {
                                //     echo '<button class="back-button" onclick="description()">Back</button>';
                                // }

                                // Display "Submit" button on page 2

                                if ($page_number == 1) {

                                    echo '<button type="submit" name="submitAnswers" class="next-button">Submit Answers</button>';
                                } else if ($next_category !== null) {
                                    $next_page_url = $next_category === $current_category ? '?category=' . $current_category . '&page=' . $next_page : 'StudentExam' . ucfirst($next_category) . '.php?category=' . $next_category . '&page=' . $next_page;
                                    echo '<button class="next-button" onclick="window.location.href=\'' . $next_page_url . '\'">Next</button>';
                                } else {
                                    echo '<button class="next-button" disabled>Next</button>';
                                }
                                ?>
                            </div>
                        </form>



                    </div>
                </div>
            </div>

            <?php


            if (isset($_SESSION['control_number'])) {

                $control_number = $_SESSION['control_number'];


                $stmt = $conn->prepare("SELECT control_number FROM useraccount WHERE control_number = ?");
                $stmt->bind_param("i", $control_number);
                $stmt->execute();
                $result = $stmt->get_result();


                if ($result->num_rows > 0) {

                    $row = $result->fetch_assoc();
                    $student_id = $row['control_number'];


                    if (isset($_POST['submitAnswers'])) {
                        $answers = isset($_POST['answers']) ? $_POST['answers'] : [];
                        $category = isset($_POST['category']) ? $_POST['category'] : '';


                        $answers_valid = true;
                        foreach ($answers as $questionID => $answer) {
                            $answer = trim($answer);

                            if (empty($answer)) {
                                $answers_valid = false;
                                break;
                            }
                        }


                        if ($answers_valid && !empty($answers)) {

                            $stmt_insert = $conn->prepare("INSERT INTO student_answer_reading_comprehension (control_number, Answer, questionID) VALUES (?, ?, ?)");

                            foreach ($answers as $questionID => $answer) {

                                $stmt_insert->bind_param("iss", $control_number, $answer, $questionID);

                                if (!$stmt_insert->execute()) {
                                    die("Insertion failed: " . $stmt_insert->error);
                                }
                            }

                            $sql_answer_key = "SELECT questionID, AnswerKey FROM admin_reading_comprehension";
                            $result_answer_key = $conn->query($sql_answer_key);


                            $answer_key = [];
                            while ($row_key = $result_answer_key->fetch_assoc()) {
                                $answer_key[$row_key['questionID']] = $row_key['AnswerKey'];
                            }


                            $correct_answers = 0;

                            foreach ($answers as $questionID => $student_answer) {
                                if (isset($answer_key[$questionID])) {
                                    $correct_answer = $answer_key[$questionID];


                                    if ($student_answer === $correct_answer) {
                                        $correct_answers++;
                                    }
                                }
                            }


                            $total_questions = count($answers);
                            $total_score = $correct_answers . '/' . $total_questions;


                            $stmt_update = $conn->prepare("UPDATE student_examination_score SET reading_id = ? WHERE control_number = ?");
                            $stmt_update->bind_param("si", $total_score, $control_number);


                            if (!$stmt_update->execute()) {
                                die("Update failed: " . $stmt_update->error);
                            }

                            $stmt_sum = $conn->prepare("SELECT SUM(logic_id + math_id + reading_id + science_id) AS total_sum FROM student_examination_score WHERE control_number = ?");
                            $stmt_sum->bind_param("i", $control_number);
                            $stmt_sum->execute();
                            $stmt_sum->bind_result($total_sum);
                            $stmt_sum->fetch();
                            $stmt_sum->close();


                            $total_score = $total_sum;
                            $stmt_update_total = $conn->prepare("UPDATE student_examination_score SET total_score = ? WHERE control_number = ?");
                            $stmt_update_total->bind_param("si", $total_score, $control_number);


                            if (!$stmt_update_total->execute()) {
                                die("Total score update failed: " . $stmt_update_total->error);
                            }

                            $stmt_fetch_status = $conn->prepare("SELECT status FROM student_examination_score WHERE control_number = ?");
                            $stmt_fetch_status->bind_param("i", $control_number);
                            $stmt_fetch_status->execute();
                            $stmt_fetch_status->bind_result($status);
                            $stmt_fetch_status->fetch();
                            $stmt_fetch_status->close();

                            $stmt_fetch_status = $conn->prepare("SELECT Schedule FROM admin_booking WHERE control_number = ?");
                            $stmt_fetch_status->bind_param("i", $control_number);
                            $stmt_fetch_status->execute();
                            $stmt_fetch_status->bind_result($status);
                            $stmt_fetch_status->fetch();
                            $stmt_fetch_status->close();


                            // $stmt_update_status = $conn->prepare("UPDATE Schedule SET status = ? WHERE student_id = ?");
                            // $stmt_update_status->bind_param("si", "DONE", $student_id);
                            // Determine the status based on total_score
                            $status = ($total_score >= 60) ? "PASSED" : "FAILED";
                            $stmt_update_status = $conn->prepare("UPDATE student_examination_score SET status = ? WHERE control_number = ?");
                            $stmt_update_status->bind_param("si", $status, $control_number);

                            // $stmt_update_admin_status = $conn->prepare("UPDATE admin_booking SET status = ? WHERE student_id = ?");
                            // $stmt_update_admin_status->bind_param("si", $new_admin_status, $student_id);
                            // $stmt_update_admin_status->execute();
                            // $stmt_update_admin_status->close();
                            // Execute the update statement for status
                            if (!$stmt_update_status->execute()) {
                                die("Status update failed: " . $stmt_update_status->error);
                            }


                            echo "<script>
                    Swal.fire({
                        title: 'Fourth Category Completed!',
                        text: 'Answers submitted successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'http://localhost/OnlineExam/StudentExamSubmitted.php';
                    });
                </script>";
                        } else {

                            echo "<script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please answer all questions before submitting.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                </script>";
                        }
                    }
                } else {
                    echo "No student found for examinee_id: $examinee_id";
                }


                $stmt->close();
                $conn->close();
            } else {
                echo "Session user_id not set.";
            }

            ?>



            <script>
                function toggleSidebar() {
                    var sidebar = document.getElementById('sidebar');
                    var content = document.getElementById('content');
                    var schoolName = document.querySelector('.SchoolName');

                    sidebar.classList.toggle('show');
                    content.classList.toggle('sidebar-show');

                    if (sidebar.classList.contains('show')) {
                        schoolName.style.display = 'block';
                    } else {
                        schoolName.style.display = 'none';
                    }
                }

                function startTimer(duration, display) {
                    let timer = duration,
                        hours, minutes, seconds;
                    setInterval(function() {
                        hours = parseInt(timer / 3600, 10);
                        minutes = parseInt((timer % 3600) / 60, 10);
                        seconds = parseInt(timer % 60, 10);

                        hours = hours < 10 ? "0" + hours : hours;
                        minutes = minutes < 10 ? "0" + minutes : minutes;
                        seconds = seconds < 10 ? "0" + seconds : seconds;

                        display.textContent = hours + ":" + minutes + ":" + seconds;

                        if (--timer < 0) {
                            timer = 0;
                        }
                    }, 1000);
                }

                function startTimer(duration, display) {
                    let timer = duration;
                    let hours, minutes, seconds;

                    // Update remaining time in session storage
                    sessionStorage.setItem('remainingTime', timer);

                    let intervalId = setInterval(function() {
                        hours = parseInt(timer / 3600, 10);
                        minutes = parseInt((timer % 3600) / 60, 10);
                        seconds = parseInt(timer % 60, 10);

                        hours = hours < 10 ? "0" + hours : hours;
                        minutes = minutes < 10 ? "0" + minutes : minutes;
                        seconds = seconds < 10 ? "0" + seconds : seconds;

                        display.textContent = hours + ":" + minutes + ":" + seconds;


                        sessionStorage.setItem('remainingTime', timer);

                        if (--timer < 0) {
                            clearInterval(intervalId);
                            timer = 0;

                            alert("Time's up! Automatically submitting your exam.");
                        }
                    }, 1000);
                }

                function getRemainingTime() {

                    let remainingTime = sessionStorage.getItem('remainingTime');

                    return remainingTime ? parseInt(remainingTime, 10) : 3600;
                }

                function updateRemainingTime(remainingTime) {

                    sessionStorage.setItem('remainingTime', remainingTime);


                }


                window.onload = function() {
                    let remainingTime = getRemainingTime();
                    let display = document.querySelector('#timer');
                    startTimer(remainingTime, display);


                    window.addEventListener('scroll', function() {
                        var timerContainer = document.querySelector('.exam-category');
                        var timerRect = timerContainer.getBoundingClientRect();
                        var contentRect = document.getElementById('content').getBoundingClientRect();

                        if (contentRect.top <= 0) {
                            display.classList.add('fixed-timer');
                        } else {
                            display.classList.remove('fixed-timer');
                        }
                    });
                };




                document.addEventListener("DOMContentLoaded", function() {

                    var radioButtons = document.querySelectorAll('.radio-button__input');


                    var examinee_id = <?php echo json_encode($_SESSION['user_id']); ?>;

                    radioButtons.forEach(function(button) {

                        button.addEventListener('change', function() {
                            if (button.checked) {

                                sessionStorage.setItem(button.name, button.id);

                                var questionID = button.name.match(/\d+/)[0];


                                // alert('Sending to server:\nQuestion ID: ' + questionID + '\nAnswer: ' + button.value);
                                console.log('Sending to server:', {
                                    examinee_id: examinee_id,
                                    category: '<?php echo $current_category; ?>',
                                    questionID: questionID,
                                    answer: button.value
                                });


                                fetch('store_answers.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            examinee_id: examinee_id,
                                            category: '<?php echo $current_category; ?>',
                                            questionID: questionID,
                                            answer: button.value
                                        })
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Network response was not ok');
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        console.log('Response from server:', data);

                                    })
                                    .catch(error => {
                                        console.error('Fetch error:', error);

                                    });
                            }
                        });


                        var savedId = sessionStorage.getItem(button.name);
                        if (savedId === button.id) {

                            button.checked = true;
                        }
                    });
                });
            </script>
</body>

</html>