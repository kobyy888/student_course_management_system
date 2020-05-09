<?php

require_once("actions.php");

/**
 * Handles all incomming requests to the server. The action code is sent by the client as url parameter.
 * The action code is important to determine which kind of action to take
 */

$output = array('inputs' => $_POST);

if (isset($_REQUEST['action_code'])) {
    $actionCode = $_REQUEST['action_code'];

    switch ($actionCode) {
        case CODE_REGISTER_REGISTRAR: {
                $output[KEY_SUBMITTED] = isset($_POST['submit']);
                registerRegistrar();
            }
            break;
        case CODE_LOGIN_REGISTRAR: {
                $output[KEY_SUBMITTED] = isset($_POST['submit']);
                loginRegistrar();
            }
            break;
        case CODE_CREATE_COURSE: {
                $result = createCourse();
                $output = array_merge($output, $result);
            }
            break;
        case CODE_BROWSE_COURSES_REGISTRAR: {
                $result = browseCourse();
                $output = array_merge($output, $result);
            }
            break;
        case CODE_REMOVE_COURSE_REGISTRAR: {
                removeCourse();
            }
            break;
        case CODE_REGISTER_STUDENT: {
                $output[KEY_SUBMITTED] = isset($_POST['submit']);
                registerStudent();
            }
            break;
        case CODE_LOGIN_STUDENT: {
                $output[KEY_SUBMITTED] = isset($_POST['submit']);
                loginStudent();
            }
            break;
        case CODE_APPLY_COURSE: {
                $result = applyForCourse();
                $output = array_merge($output, $result);
            }
            break;
        case CODE_BROWSE_COURSES_STUDENT: {
                $result = myCourses();
                $output = array_merge($output, $result);
            }
            break;
        case CODE_REMOVE_COURSE_STUDENT: {
                cancelCourse();
            }
            break;
        case CODE_LOG_OUT: {
                logout();
            }
            break;
    }
}
