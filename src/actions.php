<?php

require_once("Dao.php");

const LOGIN_COOKIE_NAME = "login-token";
const KEY_UID = "uid";
const KEY_FULL_NAME = "fullname";
const KEY_ROLE = "role";
const KEY_SUCCESS = 'success';
const KEY_SUBMITTED = "submitted";
const KEY_COURSES = "courses";
const CODE_REGISTER_REGISTRAR = 100;
const CODE_REGISTER_STUDENT = 105;
const CODE_LOGIN_REGISTRAR = 200;
const CODE_LOGIN_STUDENT = 205;
const CODE_CREATE_COURSE = 300;
const CODE_BROWSE_COURSES_REGISTRAR = 500;
const CODE_REMOVE_COURSE_REGISTRAR = 600;
const CODE_APPLY_COURSE = 405;
const CODE_BROWSE_COURSES_STUDENT = 505;
const CODE_REMOVE_COURSE_STUDENT = 605;
const CODE_LOG_OUT = 70;
const ROLE_REGISTRAR = 0;
const ROLE_STUDENT = 5;

function getLocation($actionCode)
{
    return $_SERVER['SCRIPT_NAME'] . "?action_code=$actionCode";
}

function gotoLocation($location)
{
    header("location: $location");
}

function checkAuthenticatedRegistrar()
{
    if (isset($_COOKIE[LOGIN_COOKIE_NAME])) {
        $data = json_decode($_COOKIE[LOGIN_COOKIE_NAME], true);
        if (ROLE_REGISTRAR == $data[KEY_ROLE]) {
            return $data;
        }
    }
    gotoLocation(getLocation(CODE_LOGIN_REGISTRAR));
}

function checkAuthenticatedStudent()
{
    if (isset($_COOKIE[LOGIN_COOKIE_NAME])) {
        $data = json_decode($_COOKIE[LOGIN_COOKIE_NAME], true);
        if (ROLE_STUDENT == $data[KEY_ROLE]) {
            return $data;
        }
    }
    gotoLocation(getLocation(CODE_LOGIN_STUDENT));
}

function registerRegistrar()
{
    if (
        isset($_POST['staffcode']) && isset($_POST['password'])
        && isset($_POST['fname']) && isset($_POST['lname'])
    ) {
        $dao = new Dao();
        $success = $dao->registerRegistrar($_POST);
        $dao->close();
        return $success;
    }
    return false;
}

function loginRegistrar()
{
    if (isset($_POST['staffcode']) && isset($_POST['password'])) {
        $staffCode = $_POST['staffcode'];
        $password = $_POST['password'];
        $dao = new Dao();
        $registrar = $dao->loginRegistrar($staffCode, $password);
        $dao->close();
        if (!is_null($registrar)) {
            $data = array(
                KEY_UID => $staffCode,
                KEY_FULL_NAME => $registrar['fname'] . " " . $registrar['lname'],
                KEY_ROLE => ROLE_REGISTRAR
            );
            setcookie(LOGIN_COOKIE_NAME, json_encode($data), time() + 3600 * 5);
            gotoLocation(getLocation(CODE_BROWSE_COURSES_REGISTRAR));
        }
    }
    return false;
}

function createCourse()
{
    $data = checkAuthenticatedRegistrar();
    $output[KEY_FULL_NAME] = $data[KEY_FULL_NAME];
    $output[KEY_SUCCESS] = false;
    if (
        isset($_POST['coursecode']) && isset($_POST['name'])
        && isset($_POST['credit']) && isset($_POST['semester'])
        && isset($_POST['year'])
    ) {
        $dao = new Dao();
        $output[KEY_SUCCESS] = $dao->addCourse($_POST);
        $dao->close();
    }
    return $output;
}

function browseCourse()
{
    $data = checkAuthenticatedRegistrar();
    $output[KEY_FULL_NAME] = $data[KEY_FULL_NAME];
    $dao = new Dao();
    $output[KEY_COURSES] = $dao->getCourses();
    $dao->close();
    return $output;
}

function removeCourse()
{
    checkAuthenticatedRegistrar();
    if (isset($_REQUEST['coursecode'])) {
        $coursecode = $_REQUEST['coursecode'];
        $dao = new Dao();
        $dao->removeCourse($coursecode);
        $dao->close();
    }
    gotoLocation(getLocation(CODE_BROWSE_COURSES_REGISTRAR));
}

function registerStudent()
{
    if (
        isset($_POST['regcode']) && isset($_POST['password'])
        && isset($_POST['fname']) && isset($_POST['lname'])
    ) {
        $dao = new Dao();
        $success = $dao->registerStudent($_POST);
        $dao->close();
        return $success;
    }
    return false;
}

function loginStudent()
{
    if (isset($_POST['regcode']) && $_POST['password']) {
        $regCode = $_POST['regcode'];
        $password = $_POST['password'];
        $dao = new Dao();
        $student = $dao->loginStudent($regCode, $password);
        $dao->close();
        if (!is_null($student)) {
            $data = array(
                KEY_UID => $regCode,
                KEY_FULL_NAME => $student['fname'] . " " . $student['lname'],
                KEY_ROLE => ROLE_STUDENT
            );
            setcookie(LOGIN_COOKIE_NAME, json_encode($data), time() + 3600 * 5);
            gotoLocation(getLocation(CODE_BROWSE_COURSES_STUDENT));
        }
    }
    return false;
}

function applyForCourse()
{
    $data = checkAuthenticatedStudent();
    $output[KEY_FULL_NAME] = $data[KEY_FULL_NAME];
    $dao = new Dao();
    if (isset($_REQUEST['coursecode'])) {
        $values = array('regcode' => $data[KEY_UID], 'coursecode' => $_REQUEST['coursecode']);
        $dao->applyForCourse($values);
    }
    $output[KEY_COURSES] = $dao->getCourses();
    $dao->close();
    return $output;
}

function myCourses()
{
    $data = checkAuthenticatedStudent();
    $output[KEY_FULL_NAME] = $data[KEY_FULL_NAME];
    $dao = new Dao();
    $output[KEY_COURSES] = $dao->getCoursesForStudent($data[KEY_UID]);
    $dao->close();
    return $output;
}

function cancelCourse()
{
    checkAuthenticatedStudent();
    if (isset($_REQUEST['code'])) {
        $code = $_REQUEST['code'];
        $dao = new Dao();
        $dao->removeCourseForStudent($code);
        $dao->close();
    }
    gotoLocation(getLocation(CODE_BROWSE_COURSES_STUDENT));
}

function logout()
{
    if (isset($_COOKIE[LOGIN_COOKIE_NAME])) {
        $data = json_decode($_COOKIE[LOGIN_COOKIE_NAME], true);
        setcookie(LOGIN_COOKIE_NAME, null, time() - 1);
        $role = $data[KEY_ROLE];
        if (ROLE_REGISTRAR == $role) {
            gotoLocation(getLocation(CODE_LOGIN_REGISTRAR));
        } else if (ROLE_STUDENT == $role) {
            gotoLocation(getLocation(CODE_LOGIN_STUDENT));
        }
    }
}
