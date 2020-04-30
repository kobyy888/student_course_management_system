<?php

define("DB_HOST", "student-course-management-system.czsbm3vhtjkw.us-east-2.rds.amazonaws.com");
define("DB_PORT", 3306);
define("DB_USER", "admin");
define("DB_PASS", "password");
define("DB_NAME", "student_course_management_db");

/*define("DB_HOST", "localhost");
define("DB_PORT", 3306);
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "student_course_management_db");*/

class Dao
{
    const TABLE_REGISTRARS = "registrars";
    const TABLE_STUDENTS = "students";
    const TABLE_COURSES = "courses";
    const TABLE_STUDENT_COURSES = "student_courses";

    private $db;

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $DNS = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        $this->db = new PDO($DNS, DB_USER, DB_PASS);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param $newRegistrar an array containing the values of the new registrar
     * @return true if the registration succeed, false otherwise
     */
    public function registerRegistrar($newRegistrar)
    {
        $sql = "INSERT INTO " . Dao::TABLE_REGISTRARS .
            " (staffcode, password, fname, lname)" .
            " VALUES (?, ?, ?, ?);";
        $values = array($newRegistrar['staffcode'], $newRegistrar['password'], 
                        $newRegistrar['fname'], $newRegistrar['lname']);
        return $this->insert($sql, $values);
    }

    /**
     * @param $staffCode the staff code of the registrar
     * @param $password login password
     * @return if found then an array containing the all column values of the regustrar,
     *          null otherwise
     */
    public function loginRegistrar($staffCode, $password)
    {
        $sql = "SELECT * FROM " . Dao::TABLE_REGISTRARS . " WHERE staffcode = ? and password = ?;";
        $params = array($staffCode, $password);
        $result = $this->querySingle($sql, $params);
        if(!is_null($result) && is_array($result)) return $result;
        return null;
    }

    /**
     * @param $newStudent an array containing all the values of the new student
     * @param true if registration succeed, false otherwise
     */
    public function registerStudent($newStudent)
    {
        $sql = "INSERT INTO " . Dao::TABLE_STUDENTS .
            " (regcode, password, fname, lname)" .
            " VALUES (?, ?, ?, ?);";
        $values = array($newStudent['regcode'], $newStudent['password'], 
                        $newStudent['fname'], $newStudent['lname']);
        return $this->insert($sql, $values);
    }

    /**
     * @param $registrationCode the registration code of the student
     * @param $password login password
     * @return if found then an array containing the all column values of the student,
     *          null otherwise
     */
    public function loginStudent($registrationCode, $password)
    {
        $sql = "SELECT * FROM " . Dao::TABLE_STUDENTS . " WHERE regcode = ? and password = ?";
        $params = array($registrationCode, $password);
        $result = $this->querySingle($sql, $params);
        if(!is_null($result) && is_array($result)) return $result;
        return null;
    }

    /**
     * @param $newCourse a array containing all the values of the new course
     * @return true if successfully added, false otherwise
     */
    public function addCourse($newCourse)
    {
        $sql = "INSERT INTO " . Dao::TABLE_COURSES .
            " (coursecode, name, credit, semester, year)" .
            " VALUES (?, ?, ?, ?, ?)";
        $values = array($newCourse['coursecode'], $newCourse['name'], 
                        $newCourse['credit'], $newCourse['semester'], $newCourse['year']);
        return $this->insert($sql, $values);
    }

    /**
     * Get all the available courses
     * 
     * @param $filter an array of applied filter values
     * @return an array of associative array of rows if any result found, null otherwise
     */
    public function getCourses($filter=null){
        $sql = "SELECT * FROM ".Dao::TABLE_COURSES;
        $results = $this->queryAll($sql);
        if(!is_null($results) && is_array($results)) return $results;
        return null;
    }

    /**
     * Deletes a the course with course code given as the paramter
     * 
     * @param $courseCode the course code the course to delete
     * @return true if successfully deleted, false otherwise
     */
    public function removeCourse($coursecode){
        $sql = "DELETE FROM ".Dao::TABLE_COURSES." WHERE coursecode = ?;";
        $params = array($coursecode);
        return $this->delete($sql, $params);
    }

    /**
     * @param $studentCourse an array containing all the values of the applying course details
     * @return true if successfully added, false otherwise
     */
    public function applyForCourse($studentCourse)
    {
        $sql = "INSERT INTO " . Dao::TABLE_STUDENT_COURSES .
            " (regcode, coursecode)" .
            " VALUES (?, ?);";
        $values = array($studentCourse['regcode'], $studentCourse['coursecode']);
        return $this->insert($sql, $values);
    }

    /**
     * @param $registrationCode
     * @return
     */
    public function getCoursesForStudent($registrationCode, $filter=null) {
        $sql  = "SELECT code, ".Dao::TABLE_COURSES.".* FROM ".Dao::TABLE_STUDENT_COURSES.
                " INNER JOIN ".Dao::TABLE_COURSES." ON ".Dao::TABLE_STUDENT_COURSES.".coursecode = ".Dao::TABLE_COURSES.".coursecode".
                " WHERE regcode = ?;";
        $params = array($registrationCode);
        $results = $this->queryAll($sql, $params);
        if(!is_null($results) && is_array($results)) return $results;
        return null;
    }

    /**
     * 
     */
    public function removeCourseForStudent($code) {
        $sql = "DELETE FROM ".Dao::TABLE_STUDENT_COURSES." WHERE code = ?;";
        $params = array($code);
        return $this->delete($sql, $params);
    }

    /**
     * close the db connection
     */
    public function close()
    {
        unset($this->db);
        $this->db = null;
    }

    private function insert($sql, $values)
    {
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute($values);
        $stmt->closeCursor();
        return $success;
    }

    private function querySingle($sql, $params = null)
    {
        $result = null;
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute($params)) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        $stmt->closeCursor();
        return $result;
    }

    private function queryAll($sql, $params=null)
    {
        $results = array();
        $stmt = $this->db->prepare($sql);
        if($stmt->execute($params)){
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $stmt->closeCursor();
        return $results;
    }

    private function delete($sql, $param=null){
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute($param);
        $stmt->closeCursor();
        return $success;
    }
}
