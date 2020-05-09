<?php

/**
 * Flowwing constants are related to the database connections. Don't modify any of the following
 */
define("DB_HOST", "student-course-management-system.czsbm3vhtjkw.us-east-2.rds.amazonaws.com");
define("DB_PORT", 3306);
define("DB_USER", "admin");
define("DB_PASS", "password");
define("DB_NAME", "student_course_management_db");

/**
* An utility class to handle all database related tasks.
* This is does not autometically close the databse connection,
* so the close method should the called properly when the database
* is no longer used. This class connects to a mysql database using the
* PHP PDO_MYSQL driver. Before running this app, make sure PDO_MYSQL
* driver is installed and enabled.
*/
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

    /**
     * Contains the actual code to connect to the datanase using PDO_MYSQL driver
     */
    private function init()
    {
        $DNS = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        $this->db = new PDO($DNS, DB_USER, DB_PASS);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Inserts a new record in the registrars table
     * 
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
     * Query the registrars table to match a record with the given $staggcode and $password.
     * If any record matches both the staffcode and passwors then it return the record as 
     * associative array, else returns null
     *  
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
     * Inserts a new record into students table.
     * 
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
     * Query studetns table to fetch record that matches both the given 
     * $registrationCode and $password. If any matched record found then the
     * whole record is returned as associative array, else null is returned.
     * 
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
     * Inserts a new course into the courses table
     * 
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
     * Query the courses tables and returns all the records as associative array,
     * If the query fails the returns null
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
     * Inserts a record into the student_courses table. $studentCourse associative array
     * contains the registration code of the student who is applying for the course and
     * the course code for which the student is applying
     * 
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
     * Query the student_courses table and returns the all the courses student applyed
     * whose registration code is given as parameter
     * 
     * @param $registrationCode the registration code of the student
     * @return an array of associative arrays of the course destails the student applied,
     *           or null if the query fails
     */
    public function getCoursesForStudent($registrationCode) {
        $sql  = "SELECT code, ".Dao::TABLE_COURSES.".* FROM ".Dao::TABLE_STUDENT_COURSES.
                " INNER JOIN ".Dao::TABLE_COURSES." ON ".Dao::TABLE_STUDENT_COURSES.".coursecode = ".Dao::TABLE_COURSES.".coursecode".
                " WHERE regcode = ?;";
        $params = array($registrationCode);
        $results = $this->queryAll($sql, $params);
        if(!is_null($results) && is_array($results)) return $results;
        return null;
    }

    /**
     * Deletes a record from the students_courses table whose record code is $code
     * 
     * @param $code the record code
     * @return true if record is deleted successfully, false otherwise
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

    /**
     * Helper method that executes a SQL INSERT query
     * 
     * @param $sql the SQL INSERT query
     * @param $values sql insert query parameters
     * @return true if the insert quesry is executed successfully,
     *               false otherwise
     */
    private function insert($sql, $values)
    {
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute($values);
        $stmt->closeCursor();
        return $success;
    }

    /**
     * Helper method to execute a SQL SELECT query and returns the first result only
     * 
     * @param $sql the SQL SELECT query
     * @param $param the sql query parameters
     * @return an associative array containing the first result, or null if the query returns
     *          no result or query fails to execute
     */
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

    /**
     * Helper method to execute a SQL SELECT query and returns all the results 
     * as array of associative arrays, or null if query fails
     * 
     * @param $sql the SQL SELECT query
     * @param $param the sql query parameters
     * @return an array of associative arrays containing the results, 
     *          or null if the query fails to execute
     */
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

    /**
     * Helper method to execute a SQL UPDATE query
     * 
     * @param $sql the SQL UPDATE query
     * @param $param the sql query parameters
     * @return true if the query is executed successfully,
     *         false otherwise
     */
    private function delete($sql, $param=null){
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute($param);
        $stmt->closeCursor();
        return $success;
    }
}
