<?php

require_once("Dao.php");

/**
 * The methods bellow performs the actual actions. RequestHandler calls these methods
 * depending on the action code sent by the client. The valid action codes are given below
 */

const LOGIN_COOKIE_NAME = "login-token";
const KEY_UID = "uid";
const KEY_FULL_NAME = "fullname";
const KEY_ROLE = "role";
const KEY_SUCCESS = 'success';
const KEY_SUBMITTED = "submitted";
const KEY_COURSES = "courses";

/**
 * The action codes which actions are handled by the below method
 */
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

/**
 * Returns the webpage url for the actioncode given
 * 
 * @param $actionCode the action code for the action
 * @return the url of the webpage as string
 */
function getLocation($actionCode)
{
    return $_SERVER['SCRIPT_NAME'] . "?action_code=$actionCode";
}

/**
 * Forwars the client to the new location
 * 
 * @param $location the url of the location to forwarn
 */
function gotoLocation($location)
{
    header("location: $location");
}

/**
 * Checks weather the registrar is logged in or not.
 * Cookie based authentication is used to check if a registrar
 * is logged in or not
 * 
 * @return true if the registrar is logged in, false otherwise
 */
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

/**
 * Checkes wheather the student is logged in or not.
 * Cooke based authtication is used to check if a student
 * is logged in or not
 * 
 * @return true if student is logged in, false otherwise
 */
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

/**
 * Handles a register registrar action. This method is also responsible for input validation.
 * It checkes weather all the required inputs are given or not, and also the inputs are valid or not.
 * In both cases i.e. if the all required inputs are not found or one or more inputs are invalid, then it
 * returns false
 * 
 * @return true if the registrar is registred successfully,
 *          false otherwise
 */
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

/**
 * handles the registrar login action. The authenticated registrar authentication values
 * are stored in the cookies. If the registrar login successfully then it is forward to
 * the registrar profile home page
 * 
 */
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
}

/**
 * Handles the create course action. This action is performed only when the registrar is loggin.
 * If the registrar is not logged in, then forwarded to the registrar login page automatically.
 * 
 * @return array
 */
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

/**
 * Handles the action browse courses. Only the logged in registrar is allwed for the request.
 * 
 * @return array
 */
function browseCourse()
{
    $data = checkAuthenticatedRegistrar();
    $output[KEY_FULL_NAME] = $data[KEY_FULL_NAME];
    $dao = new Dao();
    $output[KEY_COURSES] = $dao->getCourses();
    $dao->close();
    return $output;
}

/**
 * Handles the remove available cousrses action. It checkes if the course code
 * is sent with the request and if found then perform a database delete operation.
 * Only the logged in registrar is allowed to do this request
 */
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

/**
 * Handles the action for registring new student. This method also responsible the
 * input validation
 * 
 * @return bool true if the registration is successful, false otherwise
 */
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

/**
 * Handles the student login action. This method checkes weather all the required inputs are given
 * and also if the inputs are valid. If all the above conditions are satisfied and any student is found
 * then the authticaion details is stored into the cookie and forwared the student home page.
 */
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
}

/**
 * Handles the action apply for course. Only logged in student are allwed to do this request.
 * The student registration code is retrived from the cookie. If the student is not logged in
 * then forwared to student login page. 
 * 
 * @return array
 * 
 * @see loginStudnt()
 */
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

/**
 * Handles the action for listing all the coures applied by the
 * student. Only logged in student are allowed for this reques. The student registration code
 * is retrived from the cookie, which is stored during the student login. If the
 * student is not logged in then forwared to the student login page
 * 
 * @return array
 * 
 * @see loginStudnt()
 */
function myCourses()
{
    $data = checkAuthenticatedStudent();
    $output[KEY_FULL_NAME] = $data[KEY_FULL_NAME];
    $dao = new Dao();
    $output[KEY_COURSES] = $dao->getCoursesForStudent($data[KEY_UID]);
    $dao->close();
    return $output;
}

/**
 * Handels the cancel course action. This actions can only be performed
 * by th student if the student is logged in. If not logged in then forwarded
 * to student login page
 * 
 * @return array
 * 
 * @see loginStudnt()
 */
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

/**
 * Handles the log out action. It handles both the registrar log out
 * and student log out. After logging out this method also removed the
 * authtication cookie.
 */
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
