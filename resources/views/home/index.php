<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(config('app.name', 'SHILPA LMS')) ?> — Open Virtual Learning</title>
    <link href='https://fonts.googleapis.com/css?family=Inter:400,500,600,700,800&display=swap' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Css/index/index.css">
    <style>
        .ancor { text-decoration: none; color: inherit; display: block; }
        .course-card { transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: pointer; }
        .course-card:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,0.12); }
        .all-c-b { cursor: pointer; transition: all 0.2s ease; }
        .all-c-b:hover { background-color: #f8fafc; transform: translateY(-1px); }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0; }
        .site-logo { max-height: 48px; width: auto; object-fit: contain; }
    </style>
</head>

<body>
    <!--Header-->
    <header class="shil-header">
        <div class="header-search site-header-content align-items-center ">
            <div id="branding" class="branding-elements-desktop">
                <div id="logo" class="shilpa-logo">
                    <div class="shilpa-logo-heading">
                        <a href="/" __tracked="true">
                            <img class="site-logo image-style-none " src="/Images/index/shilpaLogo.png" alt="shilpa logo">
                        </a>
                    </div>
                </div>
            </div>
            <!--Primary menu-->
            <div id="primary-menu-bar" class="nav align-items-center justify-content-between">
                <nav class="main-menu-nav" aria-label="Main">
                    <ul class="menu primary-menu header-search clearfix list-group list-unstyled">
                        <!--Courses Button-->
                        <li class="menu-link menu-open visible-desktop" role="presentation">
                            <a class="menu-item" href="/courses" __tracked="true">
                                Courses
                            </a>
                            <div class="nav-bar-group">
                                <button aria-label="Courses menu" class="btn-icon btn-icon-primary btn-icon-md menu-btn h-100" type="button" id="menu-trigger-courses" aria-expanded="false">
                                    <span class="btn-icon__icon-container">
                                        <span class="pgn__icon btn-icon__icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" focusable="false" aria-hidden="true">
                                                <path d="M16.59 8.59 12 13.17 7.41 8.59 6 10l6 6 6-6-1.41-1.41Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                    </span>
                                </button>
                            </div>
                            <div class="header degreesMenu">
                                <div class="header-text">
                                    <div>
                                        <h1>Top Courses in shilpa</h1>
                                        <p style="font-size: 0.8125rem; color: #64748b; margin-top: 0.25rem;">Explore Course Categories</p>
                                    </div>
                                    <div class="compare-programs-container"></div>
                                </div>
                                <hr>
                                <div class="four-column-list">
                                    <ul>
                                        <li>Web Designing</li>
                                        <li>Web Developing</li>
                                        <li>Fullstack Dev</li>
                                        <li>CSS &amp; HTML</li>
                                    </ul>
                                    <ul>
                                        <li>Algorithms</li>
                                        <li>Frameworks</li>
                                        <li>Programming</li>
                                        <li>Python</li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <!--Shilpa programs Button-->
                        <li class="menu-link menu-open visible-desktop" role="presentation">
                            <a class="menu-item" href="/courses" __tracked="true">
                                Shilpa &nbsp; Programs
                            </a>
                            <div class="nav-bar-group">
                                <button aria-label="Programs &amp; Degrees menu" class="btn-icon btn-icon-primary btn-icon-md menu-btn h-100" type="button" id="menu-trigger-programs" aria-expanded="false">
                                    <span class="btn-icon__icon-container">
                                        <span class="pgn__icon btn-icon__icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" focusable="false" aria-hidden="true">
                                                <path d="M16.59 8.59 12 13.17 7.41 8.59 6 10l6 6 6-6-1.41-1.41Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                    </span>
                                </button>
                            </div>
                            <div class="header degreesMenu">
                                <div class="header-text">
                                    <div>
                                        <h1>Upcoming Shilpa Events</h1>
                                    </div>
                                </div>
                                <hr>
                                <ul class="degreeList">
                                    <li><a href="#"><p class="degree-title">Software Engineering Track<span class="badge">New</span></p></a><p>Empowering virtual computing education</p></li>
                                    <li><a href="#"><p class="degree-title">NSBM Green University</p></a><p>Accredited computing and open learning coursework</p></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
            <!--mobile logo -->
            <div id="branding" class="branding-elements-mobile">
                <div id="logo" class="shilpa-logo">
                    <div class="shilpa-logo-heading">
                        <a href="/" __tracked="true">
                            <img class="site-logo image-style-none" src="/Images/index/shilpaLogo.png" alt="Shilpa Online">
                        </a>
                    </div>
                </div>
            </div>
            <!--Sign in and register-->
            <div class="flex-grow-desktop"></div>
            <div class="user-menu-parallelogram-wrapper">
                <nav class="user-cta" aria-label="Account">
                    <?php if (session()->has('user')): ?>
                        <a class="btn btn-tertiary user-link sign-in-link" href="/workspace" __tracked="true">
                            Workspace
                        </a>
                        <a class="btn btn-brand user-link mx-1 register-link" href="/logout" __tracked="true">
                            <div class="register-link-text">
                                Logout
                            </div>
                        </a>
                    <?php else: ?>
                        <a class="btn btn-tertiary user-link sign-in-link" href="/login" __tracked="true">
                            Login
                        </a>
                        <a class="btn btn-brand user-link mx-1 register-link" href="/register" __tracked="true">
                            <div class="register-link-text">
                                Register for free
                            </div>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    <!--End of the Header-->

    <!--main banner-->
    <div class="main-banner">
        <div class="main-banner-content">
            <div class="main-banner-text">
                <p class="main-banner-p">Start Your Journey With</p>
                <span class="sr-only">Master Modern Computing</span>
            </div>
            <div class="main-banner-logo">
                <img src="/Images/index/shilpaLogo.png" alt="Shilpa Logo" class="banner-logo">
            </div>

            <!--mobile navigate to all courses (only showing mobile version)-->
            <a href="/courses">
                <div class="mob-nav-all">
                    <p class="mob-nav-t">Find All Courses</p>
                </div>
            </a>
        </div>
        <div class="rectangle-1">
            <img src="/Images/index/rectangle.png" alt="Students">
        </div>
        <div class="rectangle-text-area">
            <p class="rectangle-text">Fully functional Learing Site on your finger tips</p>
        </div>
    </div>
    <!--main banner end-->

    <!--Logo-area-->
    <div class="logo-area">
        <div class="logo-set">
            <div>
                <img src="/Images/index/yt.png" alt="YouTube">
            </div>
            <div>
                <img src="/Images/index/dp.png" alt="DP Education">
            </div>
            <div>
                <img src="/Images/index/mora.png" alt="University of Moratuwa">
            </div>
        </div>
        <p class="logo-set-text">All Free Learning Materials at one place , get set and go , nothing to worry</p>
    </div>
    <!--Logo-area-end-->

    <!--New Courses area-->
    <div class="courses-area-container">
        <div class="new-courses-area">
            <div class="new-courses-content">
                <div class="new-courses-text">
                    <p class="new-courses-p">New <span class="new-courses-p-span">Courses</span> </p>
                </div>
                <div class="card-top-line"></div>
                <div class="new-courses-card-area">
                    <?php foreach ($latestCourses as $course): 
                        $img = $course->C_CARD_IMAGE ?: '/Images/allCourses/kiddo.png';
                        if (!str_starts_with($img, 'http') && !str_starts_with($img, '/')) {
                            $img = '/' . $img;
                        }
                    ?>
                        <a class="ancor" href="/courses/<?= (int)$course->C_ID ?>">
                            <div class="course-card">
                                <img src="<?= e($img) ?>" alt="<?= e($course->C_NAME) ?>" class="course-card-head" onerror="this.onerror=null; this.src='/Images/allCourses/kiddo.png';">
                                <div class="card-container">
                                    <div class="card-text">                                           
                                        <p class="ct1"><?= e($course->C_NAME) ?></p>
                                        <p class="ct2"><?= e($course->C_CARD_DESCRIPTION ?: substr((string)$course->C_DESCRIPTION, 0, 90)) ?></p>
                                        <p class="ct3">Done By: <?= e($course->C_DONE_BY ?: 'SHILPA Faculty') ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="all-c">
                <a href="/courses" style="text-decoration: none;">
                    <button class="all-c-b"> Find all courses in Shilpa !</button>
                </a>
            </div>
        </div>
    </div>
    <!--New Courses area end-->

    <!--top Courses area-->
    <div class="courses-area-container">
        <div class="top-courses-area">
            <div class="new-courses-content">
                <div class="new-courses-text">
                    <p class="new-courses-p"><span class="new-courses-p-span">Top Rated Courses in Shilpa!</span> </p>
                </div>
                <div class="card-top-line"></div>
                <div class="new-courses-card-area">
                    <?php foreach ($topCourses as $course): 
                        $img = $course->C_CARD_IMAGE ?: '/Images/allCourses/kiddo.png';
                        if (!str_starts_with($img, 'http') && !str_starts_with($img, '/')) {
                            $img = '/' . $img;
                        }
                    ?>
                        <a class="ancor" href="/courses/<?= (int)$course->C_ID ?>">
                            <div class="course-card">
                                <img src="<?= e($img) ?>" alt="<?= e($course->C_NAME) ?>" class="course-card-head" onerror="this.onerror=null; this.src='/Images/allCourses/kiddo.png';">
                                <div class="card-container">
                                    <div class="card-text">                                           
                                        <p class="ct1"><?= e($course->C_NAME) ?></p>
                                        <p class="ct2"><?= e($course->C_CARD_DESCRIPTION ?: substr((string)$course->C_DESCRIPTION, 0, 90)) ?></p>
                                        <p class="ct3">Done By: <?= e($course->C_DONE_BY ?: 'SHILPA Faculty') ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="all-c">
                <a href="/courses" style="text-decoration: none;">
                    <button class="all-c-b"> Find all courses in Shilpa !</button>
                </a>
            </div>
        </div>
    </div>
    <!--top Courses area end-->

    <!--main 4 cards area -->
    <!--area 1-->
    <div class="card-container card-area">
        <div class="card-text-1">
            <div>
                <h1 class="text-1">We Help You To Grow And <br>
                    <span class="text-1-span">Evolve EVERYDAY!</span>
                </h1>
            </div>
            <div class="at-s">
                <h1 class="text-2">At Shilpa you can access over <span class="text-2-span">30+ courses</span><br> from
                    the humble stepping stones of Coding<br> to learning to be a <span class="text-2-span">Full Stack Developer!</span></h1>
            </div>
            <div class="card-div-area">
                <div class="card-squares">
                    <div class="background-square"></div>
                    <div class="top-square"></div>
                    <div class="sqr-text-cls">
                        <h1 class="sqr-txt">30+ Courses</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--area 2-->
    <div class="card-container card-area-2">
        <div class="card-text-2">
            <div>
                <h1 class="text-3">It’s <span class="text-3-span">Free,</span> Simple <br>
                    and Recognised.
                </h1>
            </div>
            <div>
                <h1 class="text-4">It doesn’t cost you a dime to access any of<br>
                    our courses! Shilpa makes it easier for you<br>
                    by methodically introducing you to the<br>
                    qualifications step by step. We bring you<br>
                    recognised certifications locally and <br>
                    internationally which are online at the<br>
                    comfort of your own home!
                </h1>
            </div>
            <h1 class="text-line-2"></h1>
        </div>
        <div class="card-img-2">
            <img src="/Images/index/coffeGirl.png" alt="Student Studying">
        </div>
    </div>

    <!--area 3-->
    <div class="card-container card-area-3">
        <div class="card-img-3">
            <img src="/Images/index/earth (1).png" alt="Global Learning">
        </div>
        <div class="card-text-3">
            <div>
                <h1 class="text-5">Language is not a barrier at <br><span class="text-5-span">Shilpa!</span>
                </h1>
            </div>
            <div>
                <h1 class="text-6">We believe language should not be a barrier for learning!<br>
                    That’s why we offer a wide range of courses for you to<br> select from in the medium you prefer.
                    English or <span class="sinhala">සිංහල</span>
                </h1>
                <div class="text-line-3"></div>
            </div>
        </div>
    </div>

    <!--area 4-->
    <div class="card-container card-area-4">
        <div class="card-text-4">
            <div>
                <h1 class="text-7">START TODAY!</h1>
                <h1 class="text-8">So why wait, Start today...</h1>
            </div>
            <div class="text-9-back">
                <h1 class="text-9">
                    “Develop a passion for learning, if<br>
                    you do you will never cease to grow”
                </h1>
                <h1 class="text-10">
                    -Anthony J D’ Angelo
                </h1>
            </div>
            <a href="/register" style="text-decoration: none;">
                <div class="red-reg-banner">
                    <div class="prt-1"></div>
                    <div class="prt-2"></div>
                    <div class="prt-3">
                        <p class="ban-t">Register for FREE!</p>
                    </div>
                    <div class="prt-4"></div>
                </div>
            </a>
        </div>
        <div class="card-img-4">
            <img src="/Images/index/bookGirl.png" alt="Start Today">
        </div>
    </div>
    <!--main 4 cards area end -->

    <!--Responsive part start (only showing on mobile versions)-->
    <div class="res-handle">
        <div class="res-card-1">
            <p class="res-card-1-text1">We Help You To Grow And Evolve EVERYDAY!</p>
            <p class="res-card-1-text2">
                At Shilpa you can access over 30+ courses from the humble stepping stones of Coding to learning to be a Full Stack Developer!
            </p>
        </div>
        <div class="res-card-2">
            <div class="res-csrd-2-text-area">
                <p class="res-card-2-text1">It’s Free, Simple and Recognised. </p>
                <p class="res-card-2-text2">
                    It doesn’t cost you a dime to access any of our courses! Shilpa makes it easier for you by methodically introducing you to the qualifications step by step. We bring you recognised certifications locally and internationally which are online at the comfort of your own home!
                </p>
            </div>
        </div>
    </div>
    <!--Responsive part end (only showing on mobile versions)-->

    <!--Footer-->
    <footer>
        <div class="row primary">
            <div class="column about">
                <h3>We Are Shilpa</h3>
                <p>
                    Bringing you the best of virtual learning and empowering future minds.<br> We are committed to make
                    learning open source, and free to anyone and everyone.<br> Shilpa; Powering you to be the best
                    version of Yourself.
                </p>
            </div>
            <div class="column links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="/courses">All Courses</a></li>
                    <li><a href="/dev/developers.html">Developer Team</a></li>
                    <li><a href="/login/terms.html">Terms Of Service</a></li>
                </ul>
            </div>
        </div>

        <div class="row secondary">
            <div>
                <p>+94 701752744</p>
            </div>
            <div>
                <p>mail@shilpa.lk</p>
            </div>
            <div>
                <a href="https://www.nsbm.ac.lk/" target="_blank" rel="noopener">
                    <p>NSBM Green University</p>
                </a>
            </div>
        </div>
        <div class="row copyright">
            <p>Copyright &copy; <?= date('Y') ?> Shilpa | All Rights Reserved</p>
        </div>
    </footer>
</body>
</html>
