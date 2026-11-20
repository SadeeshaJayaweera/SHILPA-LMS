<?php

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
require_once __DIR__ . '/shilpaDatabase.php';

use App\Models\Course;

// .....................................................new courses display.....................................................
function getLatestCourse() {
    $courses = Course::getLatest(4);
    foreach ($courses as $c) {
        $id = (int)$c->C_ID;
        $name = htmlspecialchars((string)$c->C_NAME, ENT_QUOTES, 'UTF-8');
        $csd = htmlspecialchars((string)$c->C_CARD_DESCRIPTION, ENT_QUOTES, 'UTF-8');
        $cd = htmlspecialchars((string)$c->C_DESCRIPTION, ENT_QUOTES, 'UTF-8');
        $cci = htmlspecialchars((string)$c->C_CARD_IMAGE, ENT_QUOTES, 'UTF-8');
        $cdb = htmlspecialchars((string)$c->C_DONE_BY, ENT_QUOTES, 'UTF-8');
        $c1 = htmlspecialchars((string)$c->C_LINK, ENT_QUOTES, 'UTF-8');

        echo '
        <a class="ancor" href="coursepage.php?ID=' . $id . '&name=' . urlencode((string)$c->C_NAME) . '&description=' . urlencode((string)$c->C_DESCRIPTION) . '&doneby=' . urlencode((string)$c->C_DONE_BY) . '&originalUrl=' . urlencode((string)$c->C_LINK) . '">
            <div class="course-card">
                <img src="' . $cci . '" alt="' . $name . '" class="course-card-head">
                <div class="card-container">
                    <div class="card-text">                                           
                        <p class="ct1">' . $name . '</p>
                        <p class="ct2">' . $csd . '</p>
                        <p class="ct3">Done By: ' . $cdb . '</p>
                    </div>
                </div>
            </div>
        </a>';
    }
}

// ........................................................top courses.......................................................... 
function getTopCourse() {
    $courses = Course::getTopCourses(4);
    foreach ($courses as $c) {
        $id = (int)$c->C_ID;
        $name = htmlspecialchars((string)$c->C_NAME, ENT_QUOTES, 'UTF-8');
        $csd = htmlspecialchars((string)$c->C_CARD_DESCRIPTION, ENT_QUOTES, 'UTF-8');
        $cd = htmlspecialchars((string)$c->C_DESCRIPTION, ENT_QUOTES, 'UTF-8');
        $cci = htmlspecialchars((string)$c->C_CARD_IMAGE, ENT_QUOTES, 'UTF-8');
        $cdb = htmlspecialchars((string)$c->C_DONE_BY, ENT_QUOTES, 'UTF-8');
        $c1 = htmlspecialchars((string)$c->C_LINK, ENT_QUOTES, 'UTF-8');

        echo '
        <a class="ancor" href="coursepage.php?ID=' . $id . '&name=' . urlencode((string)$c->C_NAME) . '&description=' . urlencode((string)$c->C_DESCRIPTION) . '&doneby=' . urlencode((string)$c->C_DONE_BY) . '&originalUrl=' . urlencode((string)$c->C_LINK) . '">
            <div class="course-card">
                <img src="' . $cci . '" alt="' . $name . '" class="course-card-head">
                <div class="card-container">
                    <div class="card-text">                                           
                        <p class="ct1">' . $name . '</p>
                        <p class="ct2">' . $csd . '</p>
                        <p class="ct3">Done By: ' . $cdb . '</p>
                    </div>
                </div>
            </div>
        </a>';
    }
}

// ....................................................all courses display.......................................................
function getallCoruse() {
    $courses = Course::all();
    foreach ($courses as $c) {
        $id = (int)$c->C_ID;
        $name = htmlspecialchars((string)$c->C_NAME, ENT_QUOTES, 'UTF-8');
        $csd = htmlspecialchars((string)$c->C_CARD_DESCRIPTION, ENT_QUOTES, 'UTF-8');
        $cd = htmlspecialchars((string)$c->C_DESCRIPTION, ENT_QUOTES, 'UTF-8');
        $cci = htmlspecialchars((string)$c->C_CARD_IMAGE, ENT_QUOTES, 'UTF-8');
        $cdb = htmlspecialchars((string)$c->C_DONE_BY, ENT_QUOTES, 'UTF-8');
        $c1 = htmlspecialchars((string)$c->C_LINK, ENT_QUOTES, 'UTF-8');

        echo '
        <a class="ancor" href="coursepage.php?ID=' . $id . '&name=' . urlencode((string)$c->C_NAME) . '&description=' . urlencode((string)$c->C_DESCRIPTION) . '&doneby=' . urlencode((string)$c->C_DONE_BY) . '&originalUrl=' . urlencode((string)$c->C_LINK) . '">
            <div class="course-card">
                <img src="' . $cci . '" alt="' . $name . '" class="course-card-head">
                <div class="card-container">
                    <div class="card-text">                                           
                        <p class="ct1">' . $name . '</p>
                        <p class="ct2">' . $csd . '</p>
                        <p class="ct3">Done By: ' . $cdb . '</p>
                    </div>
                </div>
            </div>
        </a>';
    }
}
