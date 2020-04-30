<?php

$actionCode = isset($_REQUEST['action_code']) ? $_REQUEST['action_code'] : CODE_LOGIN_STUDENT;
switch ($actionCode) {
    case CODE_REGISTER_REGISTRAR: {
?>
            <div>
                <ul>
                    <li><a href="<?php echo getLocation(CODE_LOGIN_REGISTRAR) ?>">Login As Registrar</a></li>
                    <li><a href="<?php echo getLocation(CODE_LOGIN_STUDENT); ?>">Login As Student</a></li>
                </ul>
                <h3>Register Registrar</h3>
                <form action="" method="POST">
                    <p>Fields makred with astrick(*) are all required</p>
                    <input type="hidden" name="action_code" value="<?php echo CODE_REGISTER_REGISTRAR ?>" />
                    <div>
                        <p>Staff Code*</p>
                        <input type="text" name="staffcode" required />
                    </div>
                    <div>
                        <p>Password*</p>
                        <input type="password" name="password" required />
                    </div>
                    <div>
                        <p>First Name*</p>
                        <input type="text" name="fname" required />
                    </div>
                    <div>
                        <p>Last Name*</p>
                        <input type="text" name="lname" required />
                    </div>
                    <br />
                    <input type="submit" name="submit" value="Register" />
                </form>
            </div>

        <?php
        }
        break;
    case CODE_REGISTER_STUDENT: {
        ?>
            <div>
                <ul>
                    <li><a href="<?php echo getLocation(CODE_LOGIN_REGISTRAR) ?>">Login As Registrar</a></li>
                    <li><a href="<?php echo getLocation(CODE_LOGIN_STUDENT); ?>">Login As Student</a></li>
                </ul>
                <h3>Register Student</h3>
                <form action="" method="POST">
                    <p>Fields makred with astrick(*) are all required</p>
                    <input type="hidden" name="action_code" value="<?php echo CODE_REGISTER_STUDENT; ?>" />
                    <div>
                        <p>Registration Code*</p>
                        <input type="text" name="regcode" required />
                    </div>
                    <div>
                        <p>Password*</p>
                        <input type="password" name="password" required />
                    </div>
                    <div>
                        <p>First Name*</p>
                        <input type="text" name="fname" required />
                    </div>
                    <div>
                        <p>Last Name*</p>
                        <input type="text" name="lname" required />
                    </div>
                    <br />
                    <input type="submit" name="submit" value="Register" />
                </form>
            </div>
        <?php
        }
        break;
    case CODE_LOGIN_REGISTRAR: {
        ?>
            <div>
                <ul>
                    <li><a href="<?php echo getLocation(CODE_LOGIN_STUDENT) ?>">Login As Student</a></li>
                    <li><a href="<?php echo getLocation(CODE_REGISTER_REGISTRAR); ?>">Register Registrar</a></li>
                </ul>
                <h3>Registrar Login</h3>
                <form action="" method="POST">
                    <p>Fields makred with astrick(*) are all required</p>
                    <input type="hidden" name="action_code" value="<?php echo CODE_LOGIN_REGISTRAR; ?>" />
                    <div>
                        <p>Staff Code*</p>
                        <input type="text" name="staffcode" required />
                    </div>
                    <div>
                        <p>Password*</p>
                        <input type="password" name="password" required />
                    </div>
                    <br />
                    <input type="submit" name="submit" value="Log In" />
                </form>
            </div>
        <?php
        }
        break;
    case CODE_LOGIN_STUDENT: {
        ?>
            <div>
                <ul>
                    <li><a href="<?php echo getLocation(CODE_LOGIN_REGISTRAR) ?>">Login As Registrar</a></li>
                    <li><a href="<?php echo getLocation(CODE_REGISTER_STUDENT); ?>">Register Student</a></li>
                </ul>
                <h3>Student Login</h3>
                <form action="" method="POST">
                    <p>Fields makred with astrick(*) are all required</p>
                    <input type="hidden" name="action_code" value="<?php echo CODE_LOGIN_STUDENT; ?>" />
                    <div>
                        <p>Registration Code*</p>
                        <input type="text" name="regcode" required />
                    </div>
                    <div>
                        <p>Password*</p>
                        <input type="password" name="password" required />
                    </div>
                    <br />
                    <input type="submit" name="submit" value="Log In" />
                </form>
            </div>

        <?php
        }
        break;
    case CODE_CREATE_COURSE: {
        ?>
            <div>
                <p>
                    <h3> Hello: <?php echo $output[KEY_FULL_NAME]; ?></h3> (logged in as registrar)
                </p>
                <ul>
                    <li><a href="<?php echo getLocation(CODE_BROWSE_COURSES_REGISTRAR); ?>">Browse Course</a></li>
                    <li><a href="<?php echo getLocation(CODE_LOG_OUT); ?>">Log Out</a></li>
                </ul>
                <h3>Create Course</h3>
                <form action="" method="POST">
                    <p>All fields are required</p>
                    <p>
                        <span>Course Code</span> <input type="text" name="coursecode" required />
                    </p>
                    <p>
                        <span>Name</span> <input type="text" name="name" required />
                    </p>
                    <p>
                        <span>Credit</span> <input type="text" require name="credit" />
                    </p>
                    <p>
                        <span>Semester</span> <input type="text" name="semester" type="number" required />
                        <span>Year</span><input type="text" name="year" required />
                    </p>
                    <p>
                        <input type="submit" name="submit" value="Add" />
                </form>
            </div>
        <?php
        }
        break;
    case CODE_BROWSE_COURSES_REGISTRAR: {
        ?>
            <div>
                <p>
                    <h3> Hello: <?php echo $output[KEY_FULL_NAME]; ?></h3> (logged in as registrar)
                </p>
                <ul>
                    <li><a href="<?php echo getLocation(CODE_CREATE_COURSE); ?>">Create Course</a></li>
                    <li><a href="<?php echo getLocation(CODE_LOG_OUT); ?>">Log Out</a></li>
                </ul>

                <div>
                    <h3>Available Courses</h3>
                    <?php
                    $courses = $output[KEY_COURSES];
                    foreach ($courses as $course) {
                    ?>
                        <p>
                            <span><b><u>Course Code:</u></b></span> <?php echo $course['coursecode']; ?><br />
                            <span><b><u>Name:</u></b></span> <?php echo $course['name']; ?><br />
                            <span><b><u>Credit:</u></b></span> <?php echo $course['credit']; ?><br />
                            <span><b><u>Semester:</u></b></span> <?php echo $course['semester']; ?><br />
                            <span><b><u>Year:</u></b></span> <?php echo $course['year']; ?><br />
                            <a href="<?php echo getLocation(CODE_REMOVE_COURSE_REGISTRAR) . '&coursecode=' . $course['coursecode']; ?>">Delete Course</a>
                        </p>
                    <?php
                    }
                    ?>
                </div>
            </div>
        <?php
        }
        break;
    case CODE_APPLY_COURSE: {
        ?>
            <div>
                <p>
                    <h3> Hello: <?php echo $output[KEY_FULL_NAME]; ?></h3> (logged in as student)
                </p>
                <ul>
                    <li><a href="<?php echo getLocation(CODE_BROWSE_COURSES_STUDENT); ?>">My Courses</a></li>
                    <li><a href="<?php echo getLocation(CODE_LOG_OUT); ?>">Log Out</a></li>
                </ul>

                <div>
                    <h3>Apply For Course</h3>
                    <?php
                    $courses = $output[KEY_COURSES];
                    foreach ($courses as $course) {
                    ?>
                        <p>
                            <span><b><u>Course Code:</u></b></span> <?php echo $course['coursecode']; ?><br />
                            <span><b><u>Name:</u></b></span> <?php echo $course['name']; ?><br />
                            <span><b><u>Credit:</u></b></span> <?php echo $course['credit']; ?><br />
                            <span><b><u>Semester:</u></b></span> <?php echo $course['semester']; ?><br />
                            <span><b><u>Year:</u></b></span> <?php echo $course['year']; ?><br />
                            <a href="<?php echo getLocation(CODE_APPLY_COURSE) . '&coursecode=' . $course['coursecode']; ?>">Apply</a>
                        </p>
                    <?php
                    }
                    ?>
                </div>
            </div>
        <?php
        }
        break;
    case CODE_BROWSE_COURSES_STUDENT: {
        ?>
            <div>
                <p>
                    <h3> Hello: <?php echo $output[KEY_FULL_NAME]; ?></h3> (logged in as student)
                </p>
                <ul>
                    <li><a href="<?php echo getLocation(CODE_APPLY_COURSE); ?>">Apply For Course</a></li>
                    <li><a href="<?php echo getLocation(CODE_LOG_OUT); ?>">Log Out</a></li>
                </ul>
                <div>
                    <h3>Courses You Applied</h3>
                    <?php
                    $courses = $output[KEY_COURSES];
                    foreach ($courses as $course) {
                    ?>
                        <p>
                            <span><b><u>Course Code:</u></b></span> <?php echo $course['coursecode']; ?><br />
                            <span><b><u>Name:</u></b></span> <?php echo $course['name']; ?><br />
                            <span><b><u>Credit:</u></b></span> <?php echo $course['credit']; ?><br />
                            <span><b><u>Semester:</u></b></span> <?php echo $course['semester']; ?><br />
                            <span><b><u>Year:</u></b></span> <?php echo $course['year']; ?><br />
                            <a href="<?php echo getLocation(CODE_REMOVE_COURSE_STUDENT) . '&code=' . $course['code']; ?>">Cancel</a>
                        </p>
                    <?php
                    }
                    ?>
                </div>
            </div>
<?php
        }
}
?>