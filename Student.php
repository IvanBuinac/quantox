<?php
namespace Students;

require_once 'DatabaseConnection.php';

class Student
{
    private $db;
    private $student_id;

    public function __construct($student) {
        $this->student_id = $student;
        $this->db = new DatabaseConnection();
        if (!$this->db) {
            die("Could not create database!");
        }
    }

    public function get_average_grade()
    {
        $query=$this->db->query("SELECT * FROM student INNER JOIN schools ON schools.id = student.school_id WHERE student.id=$this->student_id");
        $grades=$this->db->query("SELECT * FROM grades WHERE student_id=$this->student_id");

        $counter=0;
        $gradess=0;
 
        $pass=FALSE;
        
        if ($query[0]['school_name'] == "CSM") 
        {           
            foreach($grades as $grade)
            {                           
                $gradess=$gradess+$grade['grade'];
                $counter++;
            }
            $avg = $gradess / $counter;
            $pass = $avg >= 7;
                    
        }       
        else if($query[0]['school_name'] == "CSMB")
        {
            foreach($grades as $grade)
            {            
                $gradess=$gradess+$grade["grade"];
                $counter++;
            }
            $count=count($grades);
            if($count>2)
            {
                $gradess-=min($grades['grade']);
                $counter--;
            }          
            $avg = $gradess / $counter;
            $pass = max($grades) > 8;
        }
        
        $formated_result=array(
            id => $this->student_id,
            name => $query[0]['name'],
            grades => $grades,
            average => $avg,
            pass => $pass
        );


        if($query[0]['format'] == "json")
        {
            
            header("Content-Type: application/json; charset=UTF-8");
            $resultat = json_encode($formated_result);
        }
        else if($query[0]['format'] == "xml")
        {
            header("Content-Type: application/xml; charset=UTF-8");
            $simplexml = new SimpleXMLElement('<student/>');

                $xmlid = $simplexml->addChild("id",   $formated_result['id']);
                $xmlname = $simplexml->addChild("name", $formated_result['name']);
                $xmlgrades = $simplexml->addChild("grades");
                foreach($grades as $grade)
                {            
                    $xmlgrade = $simplexml->addChild("grade", $grade['grade']);
                }
                $xmlavg = $simplexml->addChild("average", $formated_result['average']);
                $xmlresult = $simplexml->addChild("pass", $formated_result['pass']);
                $resultat = $simplexml->asXML();
        }
        else{
            $resultat="Wrong format inserted";
        }
        
        return $resultat;
    }
}

$student_id = $_GET['student'];
if (!$student_id) {
    die('No student id given');
}

$student = new Student($student_id);
$res = $student->get_average_grade();
echo $res;