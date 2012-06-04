<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * meinFHD WebApp
 * 
 * @version 0.0.1
 * @copyright Fachhochschule Duesseldorf, 2012
 * @link http://www.fh-duesseldorf.de
 * @author Fabian Martinovic (FM), <fabian.martinovic@fh-duesseldorf.de>
 */

/**
 * Studienplan Model
 */
class Studienplan_Model extends CI_Model
{
    // ========================== Variable declaration ========================
    
    /**
     * UserID
     * @var int
     */
    private $userID;
    
    /**
     * ID of studycourse (Studiengang-ID)
     * @var Object 
     */
    private $studycourseID;
    
    /**
     * Contains the Semsterplan-ID of the user
     * @var int 
     */
    private $studyplanID;
    
    
    // ============================= User Methods ==============================

    /**
     * Constructs the Studienplan-Object 
     */
    public function __construct()
    {
        // write all membervariables
        parent::__construct();
        $this->userID = $this->authentication->user_id();
        $this->studycourseID = $this->queryStudycourseId();
        $this->studyplanID = $this->queryStudyplanId();
    }
    
    
    /**
     * Queries the DB for the Studycourse-ID (Studiengang-ID)
     * @return int 
     */
    public function queryStudycourseId()
    {
       $id = 0;
        
       try
        {
            $this->db->select('StudiengangID');
            $this->db->from('benutzer');
            $this->db->where('BenutzerID', $this->userID);
            $studycourseID = $this->db->get();
            $numRows = $studycourseID->num_rows();
            
            foreach($studycourseID->result() as $row)
            {
                if($numRows != null)
                {
                    $id = $row->StudiengangID;
                }
            }
            
            return $id;
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        } 
    }
    
    
    /**
     * Queries the DB for the Studyplan-ID (Semesterplan-ID)
     * @return int 
     */
    public function queryStudyplanId()
    {
        $id = 0;
        
        try
        {
            $this->db->select('SemesterplanID');
            $this->db->from('semesterplan');
            $this->db->where('BenutzerID', $this->userID);
            $semesterplanID = $this->db->get();
            $numRows = $semesterplanID->num_rows();
            
            foreach($semesterplanID->result() as $row)
            {
                if($numRows != null)
                {
                    $id = $row->SemesterplanID;
                }
            }
            
            return $id;
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }


    /**
     * Queries the Db for the Studyplan of the user
     * @return Array 
     */
    public function queryStudyplan()
    { 
        // try to query the database
        try
        {
            // query DB
            $this->db->select('studiengangkurs.Semester, 
                                studiengangkurs.Kursname, 
                                semesterkurs.KursHoeren, 
                                semesterkurs.KursSchreiben, 
                                semesterkurs.Notenpunkte');
            $this->db->from('studiengangkurs');
            $this->db->join('semesterkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->order_by('studiengangkurs.Semester', 'ASC');
            $studyplan = $this->db->get();

            
            // initial zero semester
            $data['plan'][0][] = array(
                'Semester'      => 0,
                'Kursname'      => '',
                'Hoeren'        => '',
                'Schreiben'     => '',
                'Notenpunkte'   => ''
            );
            
            
            // group the resultset by semester in an array
            foreach($studyplan->result() as $sq)
            {
                // if semester equals count, add it to the array on position count
                // TODO: Flag für aktuelles Semester eintragen
                $data['plan'][$sq->Semester][] = array(
                    'Semester'      => $sq->Semester,
                    'Kursname'      => $sq->Kursname,
                    'Hoeren'        => $sq->KursHoeren,
                    'Schreiben'     => $sq->KursSchreiben,
                    'Notenpunkte'   => $sq->Notenpunkte
                );
            }
            
            return $data;
        }
        // catch all exceptions and echo the Exception-message
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    
    
    /**
     * Creates an object with the course of study data
     */
    /*public function createCourseOfStudy()
    {
        try
        {
            // query database for Studiengang
            $course_of_study_query = $this->db->query("SELECT * 
                                                        FROM studiengang 
                                                        WHERE StudiengangID = ".$this->studycourseID);

            // fetch result
            $this->courseOfStudy = $course_of_study_query->result();
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
        
    }*/


    
    /**
     * Creates a new Studyplan if it not already exists
     */
    public function createStudyplan()
    {
        try
        {
            // Query the Semesterplan of an user
            /*$semesterplan_query = $this->db->query("SELECT *
                                                    FROM semesterplan
                                                    WHERE BenutzerID = ".$this->userID);*/
            // $semesterplan_result = $semesterplan_query->result();
            
            $this->db->select('*');
            $this->db->from('semesterplan');
            $this->db->where('BenutzerID', $this->userID);
            
            $semesterplan_query = $this->db->get();


            // if query has no result (wenn studiengang und startsemester bereits ausgewählt wurden) create a new Semesterplan
            if($semesterplan_query == null)
            {
                // query DB for the Regelsemester
                /*$regelsemester_query = $this->db->query("SELECT Regelsemester
                                                        FROM studiengang
                                                        WHERE StudiengangID = ".$this->studycourseID."");*/
                //$regelsemester_result = $regelsemester_query->result();
                
                $this->db->select('Regelsemester');
                $this->db->from('studiengang');
                $this->db->where('StudiengangID', $this->studycourseID);
                
                $regelsemester_result = $this->db->get();
                
                
                // insert new Semesterplan in database
                /*$insert_query = $this->db->query("
                    INSERT INTO semesterplan (
                        BenutzerID, 
                        Semesteranzahl) 
                    VALUES (".
                        $this->userID.", ".
                        $regelsemester_result.")"
                );
                $insert_query->result();*/
                
                $dataarray = array(
                    'BenutzerID'    => $this->userID,
                    'Semesteranzahl'=> $regelsemester_result
                );
                
                $this->db->insert('semesterplan', $dataarray);
                unset($dataarray);


                // query database for KursID & Semester of the user
                /*$kurs_semester_query = $this->db->query("SELECT KursID, Semester 
                                                            FROM studiengangkurs 
                                                            WHERE StudiengangID = ".$this->studycourseID);
                $kurs_semester_result = $kurs_semester_query->result();*/
                
                $this->db->select('KursID', 'Semester');
                $this->db->from('studiengangkurs');
                $this->db->where('StudiengangID', $this->studycourseID);
                $kurs_semester_result = $this->db->get();

                // insert in semesterkurs all data from the query above
                foreach($kurs_semester_result as $kurs_semester)
                {
                    /*$insert_query = $this->db->query("
                        INSERT INTO semesterkurs (
                            SemesterplanID, 
                            KursID, 
                            Semester) 
                        VALUES (".
                            $this->studyplanID.", ".
                            $kurs_semester->KursID.", ".
                            $kurs_semester->Semester.")"
                    );
                    $insert_query->result();*/
                    
                    $dataarray = array(
                        'SemesterplanID'=> $this->studyplanID,
                        'KursID'        => $kurs_semester->KursID,
                        'Semester'      => $kurs_semester->Semester
                    );
                
                    $this->db->insert('semesterkurs', $dataarray);
                }
                unset($dataarray);

                // Eexecute the createTimetablCourses method
                $this->createTimetableCourses();
            }
            // if query has no result
            else
            {
                // TODO: NACHRICHT AUSGEBEN, BESPRECHEN WIE DAS GEMACHT WIRD
            }
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }

    }
    
    
    
    /**
     * Creates the Courses of the timetable 
     */
    public function createTimetableCourses()
    {
        try
        {
            // query the database for courses of the timetable
            /*$timetable_query = $this->db->query("
                SELECT a.*, b.Semester
                FROM stundenplankurs AS a
                INNER JOIN kursreferenz AS c
                ON c.ReferenzKursID = a.KursID
                INNER JOIN semesterkurs AS b
                ON b.KursID = c.KursID
                INNER JOIN semesterplan AS d
                ON d.SemesterplanID = b.SemesterplanID
                WHERE d.BenutzerID = ".$this->userID."
                AND d.SemesterplanID = ".$this->studyplanID."");

            $timetable_result = $timetable_query->result();*/
            
            $this->db->select('stundenplankurs.*', 'semesterkurs.Semester');
            $this->db->from('stundenplankurs');
            $this->db->join('kursreferenz', 'kursreferenz.ReferenzKursID = stundenplankurs.KursID');
            $this->db->join('semesterkurs', 'semesterkurs.KursID = kursreferenz.KursID');
            $this->db->join('semesterplan', 'semesterplan.SemesterplanID = semesterkurs.SemesterplanID');
            $this->db->where('semesterplan.BenutzerID', $this->userID);
            $this->db->where('semesterplan.SemesterplanID', $this->studyplanID);
            $timetable_result = $this->db->get();
            
            // insert in benutzerkurs all data from the query above
            foreach($timetable_result as $time)
            {
                /*$this->db->query("
                    INSERT INTO benutzerkurs (
                        BenutzerID, 
                        KursID, 
                        SPKursID, 
                        SemesterID, 
                        aktiv, 
                        changed_at, 
                        edited_by)
                    VALUES (".
                        $this->userID.", ".
                        $time->KursID.", ".
                        $time->SPKursID.", ".
                        $time->Semester.", ".
                        (($time->VeranstaltungsformID == 1 || $time->VeranstaltungsformID == 6) ? '1' : '0').", 
                        'studienplan_semesterplan: create benutzerkurs', ".
                        $_SESSION['userid']);*/
                
                $dataarray = array(
                    'BenutzerID'    => $this->userID,
                    'KursID'        => $time->KursID,
                    'SPKursID'      => $time->SPKursID,
                    'SemesterID'    => $time->Semester,
                    'aktiv'         => ($time->VeranstaltungsformID == 1 || $time->VeranstaltungsformID == 6) ? '1' : '0',
                    'changed_at'    => 'studienplan_semesterplan: create benutzerkurs',
                    'edited_by'     => $this->userID
                );

                $this->db->insert('benutzerkurs', $dataarray);
                unset($dataarray);
                
            }

            // TODO: NACHRICHT AUSGEBEN, BESPRECHEN WIE DAS GEMACHT WIRD
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    
    /**
     * Adds a new coloumn
     * raises the coloumn Semesteranzahl
     */
    public function createNewSemesterColoumn()
    {
        try
        {
            /*$getSemestercount = $this->db_query("SELECT Semesteranzahl 
                                                FROM semesterplan 
                                                WHERE SemesterplanID = ".$this->studyplanID);
            $semestercount = $getSemestercount->result();*/
            
            $this->db->select('Semesteranzahl');
            $this->db->from('semesterplan');
            $this->db->where('SemesterplanID', $this->studyplanID);
            $semestercount = $this->db->get();

            $newSemestercount = $semestercount++;

            /*$this->db->query("UPDATE semesterplan 
                                SET Semesteranzahl = ".$newSemestercount."
                                WHERE SemesterplanID = ".$this->studyplanID);*/
            
            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->update('benutzerkurs', array('Semesteranzahl' => $newSemestercount));
            
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    
    
    /**
     * Returns the mark 
     * @param int|float $markpoints
     * @return int
     */
    public function calculateMark($markpoints)
    {   
        switch($markpoints)
        {
            // if $markpoints are Points
            case is_int($markpoints):
                if($markpoints >=90 && $markpoints <=100)
                {
                    return 1;
                }
                elseif($markpoints >=75 && $markpoints <=90) 
                {
                    return 2;
                }
                elseif($markpoints >=60 && $markpoints <=75)
                {
                    return 3;
                }
                elseif($markpoints >=50 && $markpoints <=60)
                {
                    return 4;
                }
                elseif($markpoints <50)
                {
                    return 5;
                }
            break;
               
            // if $markpoints is mark
            case is_float($markpoints):
                if($markpoints <=1.3 && $markpoints >=1.0)
                {
                    return 1;
                }
                elseif($markpoints >=1.7 && $markpoints <=2.3) 
                {
                    return 2;
                }
                elseif($markpoints >=2.7 && $markpoints <=3.3)
                {
                    return 3;
                }
                elseif($markpoints >=3.7 && $markpoints <=4.0)
                {
                    return 4;
                }
                elseif($markpoints >=4.3)
                {
                    return 5;
                }
            break;
        }
        
    }
    
    
    /**
     * Queries all Modules
     * @return Object 
     */
    public function queryAllModules()
    {
        try
        {
            $this->db->select('semesterkurs.SemesterplanID', 
                                'semesterkurs.KursID', 
                                'semesterkurs.Notenpunkte', 
                                'studiengangkurs.Creditpoints');
            $this->db->from('semesterkurs');
            $this->db->join('studiengangkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
            $this->db->where('SemesterplanID', $this->studyplanID);
            
            return $this->db->get();
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }


    
    
    /**
     * Returns all passed modules
     * @return Object 
     */
    /*public function queryDbForMarks()
    {
        try
        {
            //TODO: Wie muss hören/schreiben beachtet werden
            // query all modules from a user
            $modules_query = $this->db->query("SELECT a.SemesterplanID, a.KursID, a.Notenpunkte, b.Creditpoints 
                                                FROM semesterkurs as a
                                                INNER JOIN studiengangkurs as b
                                                ON a.KursID = b.KursID
                                                WHERE SemesterplanID = ".$this->studyplanID."");
            // WHERE PruefungsstatusID = 4 wird nach neuem Verfahren nicht  mehr benötigt
            //return $modules_query->result();
            
            
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }*/



    /**
     * Calculates the average mark
     * @param boolean $returnAsString
     * @return float 
     */
    public function calculateAverageMark($returnAsString)
    {
        $sum = 0;
        
        try
        {
            $modules = $this->queryAllModules();
            
            // query sum of creditpoints of studycourse
            /*$cp_query = $this->db->query("SELECT Creditpoints
                                            FROM studiengang
                                            WHERE StudiengangID = ".$this->studycourseID);
            $cp = $cp_query->result();*/
            $this->db->select('Creditpoints');
            $this->db->from(studiengang);
            $this->db->where('StudiengangID', $this->studycourseID);
            $cp = $this->db->get();

            foreach($modules as $mod)
            {
                $sum += $mod->Notenpunkte * $mod->Creditpoints;
            }


            if($returnAsString)
            {
                return calculateMark($sum/$cp);
            }
            else
            {
                return $sum/$cp;
            }
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
        
        // delete variable
        unset($sum);
    }
    
    
    // TODO: METHODE ÜBERARBEITEN, DA SIE NICH DAS ERGEBNIS ZURÜCKLIEFERT WELCHES SIE SOLLTE
    /**
     * Returns the percentage of the Study
     * @param boolean $returnAsString
     * @return boolean or int dependent on $returnAsString 
     */
    public function calculatePercentageOfStudy($returnAsString)
    {  
        try
        {
            // query ALL modules
            /*$modules_query = $this->db->query("SELECT a.SemesterplanID, a.KursID, a.Notenpunkte, b.Creditpoints 
                                                FROM semesterkurs as a
                                                INNER JOIN studiengangkurs as b
                                                ON a.KursID = b.KursID
                                                WHERE SemesterplanID = ".$this->studyplanID."");
            $allModules = $modules_query->result();*/
            
            $allModules = $this->queryAllModules();

            // query passed modules
            //$modules = $this->queryAllModules();
            // get affected rows
            $numberOfModules = $allModules->num_rows();


            if($returnAsString)
            {
                return round(($numberOfModules/$allModules)*100, 0)." %";
            }
            else
            {
                return round(($numberOfModules/$allModules)*100, 0);
            }
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    // TODO: MIT WELCHER NOTE WERDEN DIE MODULE ANERKANNT???????
    /**
     * Set the module with the $moduleID as accepted
     * @param int $moduleID 
     */
    public function acceptMarks($moduleID)
    {
        try
        {
            $updateQuery = $this->db->query("UPDATE semesterkurs 
                                        SET Notenpunkte = 50
                                        WHERE SemesterplanID = ".$this->studyplanID."
                                        AND KursID = ".$moduleID."");
            $updateQuery->result();
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    
    /**
     * Resets all Studyplan-data of a user 
     */
    public function reset()
    {
        try
        {
            /*$studyplan_query = $this->db->query("SELECT * 
                                                FROM semesterkurs
                                                WHERE SemesterplanID = ".$this->studyplanID."");
            $modules = $studyplan_query->result();*/
            
            $this->db->select('*');
            $this->db->from('semesterkurs');
            $this->db->where('SemesterplanID', $this->studyplanID);
            $modules = $this->db->get();
            
            
            foreach($modules as $module)
            {
                /*$resetQuery = $this->db->query("UPDATE semesterkurs
                                                SET KursHoeren = 1, 
                                                    KursSchreiben = 1, 
                                                    PruefungsstatusID = 1, 
                                                    VersucheBislang = 0, 
                                                    Notenpunkte = 101
                                                WHERE SemesterplanID = ".$this->studyplanID."
                                                AND KursID = ".$module->KursID."");
                $resetQuery->result();*/
                
                $dataarray = array(
                    'KursHoeren'        => 1,
                    'KursSchreiben'     => 1,
                    'PruefungsstatusID' => 1,
                    'VersucheBislang'   => 0,
                    'Notenpunkte'       => 101
                );
                
                $this->db->where('SemesterplanID', $this->studyplanID);
                $this->db->where('KursID', $module->KursID);
                $this->db->update('semesterkurs', $dataarray);
            }
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    
    /**
     * Calculates the Sum of the SWS and the CP for each Semester
     * @return Array 
     */
    public function calculateSwsAndCp()
    {
        // Semestercounter
        $counter = 1;
        $swsSum = 0;
        $cpSum = 0;
        $sumArray = array();
        
        try
        {
            /*$swsCpQuery = $this->db->query("SELECT a.KursID, 
                                        a.Kursname, 
                                        a.kurs_kurz, 
                                        a.Creditpoints, 
                                        a.SWS_Vorlesung, 
                                        a.SWS_Uebung, 
                                        a.SWS_Praktikum, 
                                        a.SWS_Projekt, 
                                        a.SWS_Seminar,
                                        a.SWS_Seminarunterricht,
                                        b.Semester
                                FROM studiengangkurs AS a
                                INNER JOIN semesterkurs AS b
                                ON b.KursID = a.KursID
                                WHERE b.SemesterplanID = ".$this->studyplanID."
                                ORDER BY b.Semester ASC");
            $swsCpResult = $swsCpQuery->result();*/
            
            $this->db->select('studiengangkurs.KursID',
                                'studiengangkurs.Kursname',
                                'studiengangkurs.kurs_kurz',
                                'studiengangkurs.Creditpoints',
                                'studiengangkurs.SWS_Vorlesung',
                                'studiengangkurs.SWS_Uebung',
                                'studiengangkurs.SWS_Praktikum',
                                'studiengangkurs.SWS_Projekt',
                                'studiengangkurs.SWS_Seminar',
                                'studiengangkurs.SWS_Seminarunterricht',
                                'semesterkurs.Semester');
            $this->db->from('studiengangkurs');
            $this->db->join('semesterkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
            $this->db->where('semesterkurs.SemesterplanID', $this->studyplanID);
            $this->db->order('semesterkurs.Semester', 'ASC');
            $swsCpResult = $this->db->get();
            
            // calculate each sum
            foreach($swsCpResult as $swsCp)
            {
                if($counter == $swsCp->Semester)
                {
                    // Sum of SWS
                    $swsSum = $swsSum + 
                            $swsCp->SWS_Vorlesung + 
                            $swsCp->SWS_Uebung + 
                            $swsCp->SWS_Praktikum + 
                            $swsCp->SWS_Projekt + 
                            $swsCp->SWS_Seminar + 
                            $swsCp->SWS_Seminarunterricht;
                    
                    // Sum of Creditpoints
                    $cpSum += $swsCp->Creditpoints;
                    
                    // write sums in array
                    $sumArray[$counter]['SWS_Summe'] = $swsSum;
                    $sumArray[$counter]['CP_Summe'] = $cpSum;
                    
                    // raise counter
                    $counter++;
                    
                    // unset locale varibales for next turn
                    unset($swsSum);
                    unset($cpSum);
                }
            }
            
            return $sumArray;
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        } 
    }  
    
    
    // Infos über das Modul. Was wird hier benötigt?
    public function moduleInfo(){}
    // Modul duplizieren, nachdem man durchgefallen ist + Versuch erhöhen
    public function duplicateModule($id){}
    // Änderungen abspeichern
    public function save(){}


    
    
    // ============================ Getter & Setter ============================
    

}

/* End of file studienplan.php */
/* Location: ./application/models/studienplan.php */