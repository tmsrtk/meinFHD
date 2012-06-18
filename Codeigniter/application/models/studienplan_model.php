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
    
    /**
     * Current Semester
     * @var int 
     */
    private $currentSemester;
    
    
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
        $this->currentSemester = $this->queryCurrentSemester();
    }
    
    
    /**
     * Queries the DB for the Studycourse-ID (Studiengang-ID)
     * 
     * @return int
     */
    private function queryStudycourseId()
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
     * 
     * @return int
     */
    private function queryStudyplanId()
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
     * Returns the current user semester
     * 
     * @return int 
     */
    private function queryCurrentSemester()
    {
        $currentSemester = 0;
        
        try
        {
            $this->db->select('Semester');
            $this->db->from('benutzer');
            $this->db->where('BenutzerID', $this->userID);
            $semester = $this->db->get();
            
            foreach($semester->result() as $sem)
            {
                $currentSemester = $sem->Semester;
            }
            
            return $currentSemester;
        }
        catch(Exceptoin $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    


    /**
     * Queries the Db for the Studyplan of the user
     * 
     * @return Array
     */
    public function queryStudyplan()
    { 
        $data = array();
        
        // try to query the database
        try
        {
            // query DB
            $this->db->select('studiengangkurs.KursID,
                                studiengangkurs.Semester AS regularSemester, 
                                studiengangkurs.Kursname,
                                studiengangkurs.kurs_kurz,
                                semesterkurs.Semester AS graduateSemester,
                                semesterkurs.KursHoeren, 
                                semesterkurs.KursSchreiben, 
                                semesterkurs.Notenpunkte');
            $this->db->from('studiengangkurs');
            $this->db->join('semesterkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
            $this->db->where('semesterkurs.SemesterplanID', $this->studyplanID);
            $this->db->order_by('studiengangkurs.Semester', 'ASC');
            $studyplan = $this->db->get();

            
            // initial zero semester
            $data['plan'][0][] = array(
                'regularSemester'   => 0,
                'KursID'            => 0,
                'Kursname'          => '',
                'Kurzname'          => '',
                'graduateSemester'  => 0,
                'Hoeren'            => '',
                'Schreiben'         => '',
                'Notenpunkte'       => ''
            );
            
            
            // group the resultset by semester in array
            foreach($studyplan->result() as $sq)
            {
                // if graduateSemester doesn't equals regularSemester, set 
                // graduateSemester as key
                if($sq->graduateSemester != $sq->regularSemester)
                {
                    $data['plan'][$sq->graduateSemester][] = array(
                        'regularSemester'   => $sq->regularSemester,
                        'KursID'            => $sq->KursID,
                        'Kursname'          => $sq->Kursname,
                        'Kurzname'          => $sq->kurs_kurz,
                        'graduateSemester'  => $sq->graduateSemester,
                        'Hoeren'            => $sq->KursHoeren,
                        'Schreiben'         => $sq->KursSchreiben,
                        'Notenpunkte'       => $sq->Notenpunkte
                    );
                }
                // else set regularSemester as key
                else
                {
                    $data['plan'][$sq->regularSemester][] = array(
                        'regularSemester'   => $sq->regularSemester,
                        'KursID'            => $sq->KursID,
                        'Kursname'          => $sq->Kursname,
                        'Kurzname'          => $sq->kurs_kurz,
                        'graduateSemester'  => $sq->graduateSemester,
                        'Hoeren'            => $sq->KursHoeren,
                        'Schreiben'         => $sq->KursSchreiben,
                        'Notenpunkte'       => $sq->Notenpunkte
                    );
                }
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
     * Creates a new Studyplan if it not already exists
     */
    public function createStudyplan()
    {
        try
        {
            // if no styudyplan exists
            if($this->studyplanID == 0)
            {
                // query DB for the Regelsemester
                $this->db->select('Regelsemester');
                $this->db->from('studiengang');
                $this->db->where('StudiengangID', $this->studycourseID);
                $regelsemester_result = $this->db->get();
                
                
                foreach($regelsemester_result->result() as $regel)
                {
                    // create a new semsterplan and insert the Regelsemester
                    $dataarray = array(
                        'BenutzerID'    => $this->userID,
                        'Semesteranzahl'=> $regel->Regelsemester
                    );

                    $this->db->insert('semesterplan', $dataarray);
                    
                    
                    // query new studycourseID and set the classvariable
                    $this->studyplanID = $this->queryStudyplanId();
                    
                    
                    // query DB for all courses for the studycourse
                    $this->db->select('KursID, Semester');
                    $this->db->from('studiengangkurs');
                    $this->db->where('StudiengangID', $this->studycourseID);
                    $kurs_semester = $this->db->get();
                    
                    // insert all courses of the studycourse in semesterkurs
                    foreach($kurs_semester->result() as $ks)
                    {
                        $dataarray = array(
                            'SemesterplanID'    => $this->studyplanID,
                            'KursID'            => $ks->KursID,
                            'Semester'          => $ks->Semester,
                            'KursHoeren'        => 1,
                            'KursSchreiben'     => 1,
                            'PruefungsstatusID' => 1,
                            'VersucheBislang'   => 0,
                            'Notenpunkte'       => 101
                        );
                        
                        $this->db->insert('semesterkurs', $dataarray);
                    }
                }
                
                // Eexecute createTimetableCourses method
                $this->createTimetableCourses();
            }
            else
            {
                echo 'Studienplan existiert bereits';
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
            $this->db->select('stundenplankurs.*, semesterkurs.Semester');
            $this->db->from('stundenplankurs');
            $this->db->join('kursreferenz', 'kursreferenz.ReferenzKursID = stundenplankurs.KursID');
            $this->db->join('semesterkurs', 'semesterkurs.KursID = kursreferenz.KursID');
            $this->db->join('semesterplan', 'semesterplan.SemesterplanID = semesterkurs.SemesterplanID');
            $this->db->where('semesterplan.BenutzerID', $this->userID);
            $this->db->where('semesterplan.SemesterplanID', $this->studyplanID);
            $timetable_result = $this->db->get();
            
            // insert in benutzerkurs all data from the query above => new timetable
            foreach($timetable_result->result() as $time)
            {
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
            }

            echo 'Stundenplan erfolgreich erstellt';
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    
    /**
     * Adds a new coloumn (raises the coloumn Semesteranzahl)
     */
    public function createNewSemesterColoumn()
    {
        $semester = 0;
        
        try
        {
            // query DB for semestercount from semesterplan
            $this->db->select('Semesteranzahl');
            $this->db->from('semesterplan');
            $this->db->where('SemesterplanID', $this->studyplanID);
            $semestercount = $this->db->get();
            
            foreach($semestercount->result() as $semcount)
            {
                $semester = $semcount->Semesteranzahl;
            }

            // raise the count
            $semester++;
            
            // and update the coloumn Semesteranzahl
            $dataarray = array(
                'Semesteranzahl' => $semester
            );
            
            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->update('semesterplan', $dataarray);
            
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    /**
     * If studyplan has more semester than Regelsemester and this coloumn has 
     * modules, this method updates the semestercoloumn in semesterkurs
     * 
     * @param int $module_id
     * @param int $semester 
     */
    public function shiftModule($module_id, $semester)
    {
        try
        {   
            // update the Semester-coloumn 
            $dataarray = array(
                'Semester' => $semester
            );
            
            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->where('KursID', $module_id);
            $this->db->update('semesterkurs', $dataarray);
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    
    
    /**
     * Returns the mark 
     * 
     * @param int|float $markpoints
     * @return int
     */
    public function calculateMark($markpoints)
    {   
        switch($markpoints)
        {
            // if $markpoints are points
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
     * 
     * @return Object
     */
    private function queryAllModules()
    {
        $data = array();
        
        try
        {
            $this->db->select('semesterkurs.SemesterplanID, 
                                semesterkurs.KursID,
                                semesterkurs.Notenpunkte,
                                studiengangkurs.Creditpoints');
            $this->db->from('semesterkurs');
            $this->db->join('studiengangkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
            $this->db->where('SemesterplanID', $this->studyplanID);
            $moduleQuery = $this->db->get();
            
            
            foreach($moduleQuery->result() as $mod)
            {
                $data[] = array(
                    'SemesterplanID'    => $mod->SemesterplanID,
                    'KursID'            => $mod->KursID,
                    'Notenpunkte'       => $mod->Notenpunkte,
                    'Creditpoints'      => $mod->Creditpoints,
                );
            }
            
            return $data;
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    


    /**
     * Calculates the average mark
     * @return float 
     * 
     * methos -> checked
     */
    public function calculateAverageMark()
    {
        $sum = 0;
        $credits = 0;
        
        try
        {
            $modules = $this->queryAllModules();
                     
            $this->db->select('Creditpoints');
            $this->db->from('studiengang');
            $this->db->where('StudiengangID', $this->studycourseID);
            $wholeCpQuery = $this->db->get();
            
            foreach($wholeCpQuery->result() as $wholeCp)
            {
                $credits = $wholeCp->Creditpoints;
            }

            // if markpoints are not default (101) than calculate the sum 
            // of markpoints*creditpoints
            foreach($modules as $mod)
            {
                if($mod['Notenpunkte'] != 101)
                {
                    $sum += $mod['Notenpunkte'] * $mod['Creditpoints'];
                }
            }
            
            return $sum/$credits;
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    
    /**
     * Returns the percentage of the Study
     * 
     * @return float
     */
    public function calculatePercentageOfStudy()
    {  
        $passedModules = 0;
        
        try
        {
            $allModules = $this->queryAllModules();
            $moduleCount = count($this->queryAllModules());
            
            foreach($allModules as $mod)
            {
                if($mod['Notenpunkte'] < 101 && $mod['Notenpunkte'] >= 50)
                {
                    $passedModules ++;
                }
            }
            
            return round(($passedModules/$moduleCount)*100, 0);
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    
    
    // TODO: MIT WELCHER NOTE WERDEN DIE MODULE ANERKANNT???????
    /**
     * Set the module with the $moduleID as accepted
     * 
     * @param int $moduleID
     */
    public function acceptMarks($moduleID)
    {
        try
        {
            $dataarray = array(
                'Notenpunkte' => 50
            );
            
            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->where('KursID', $moduleID);
            $this->db->update('semesterkurs', $dataarray);
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    
    /**
     * Resets ALL Studyplan-data of a user
     */
    public function reset()
    {
        try
        {
            $this->db->select('studiengangkurs.KursID, studiengangkurs.Semester');
            $this->db->from('studiengangkurs');
            $this->db->join('semesterkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
            $this->db->where('semesterkurs.SemesterplanID', $this->studyplanID);
            $this->db->order_by('studiengangkurs.Semester', 'ASC');
            $modules = $this->db->get();
            
            
            foreach($modules->result() as $mod)
            {
                $dataarray = array(
                    'Semester'          => $mod->Semester,
                    'KursHoeren'        => 1,
                    'KursSchreiben'     => 1,
                    'PruefungsstatusID' => 1,
                    'VersucheBislang'   => 0,
                    'Notenpunkte'       => 101
                );
                
                $this->db->where('SemesterplanID', $this->studyplanID);
                $this->db->where('KursID', $mod->KursID);
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
     * 
     * @return Array
     */
    public function calculateSwsAndCp()
    {
        $swsSum = 0;
        $cpSum = 0;
        $counter = 1;
        $data = array();
        $sumArray = array();
         
        try
        {
            $this->db->select('studiengangkurs.KursID,
                                studiengangkurs.Kursname,
                                studiengangkurs.kurs_kurz,
                                studiengangkurs.Creditpoints,
                                studiengangkurs.SWS_Vorlesung,
                                studiengangkurs.SWS_Uebung,
                                studiengangkurs.SWS_Praktikum,
                                studiengangkurs.SWS_Projekt,
                                studiengangkurs.SWS_Seminar,
                                studiengangkurs.SWS_Seminarunterricht,
                                semesterkurs.Semester');
            $this->db->from('studiengangkurs');
            $this->db->join('semesterkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
            $this->db->where('semesterkurs.SemesterplanID', $this->studyplanID);
            $this->db->order_by('semesterkurs.Semester', 'ASC');
            $swsCpResult = $this->db->get();
            
            
            // order modules by semester in array
            foreach($swsCpResult->result() as $swsCp)
            {
                $data[$swsCp->Semester][] = array(
                    'SWS_Vorlesung'         => $swsCp->SWS_Vorlesung,
                    'SWS_Uebung'            => $swsCp->SWS_Uebung,
                    'SWS_Praktikum'         => $swsCp->SWS_Praktikum,
                    'SWS_Projekt'           => $swsCp->SWS_Projekt,
                    'SWS_Seminar'           => $swsCp->SWS_Seminar,
                    'SWS_Seminarunterricht' => $swsCp->SWS_Seminarunterricht,
                    'Creditpoints'          => $swsCp->Creditpoints
                );
            }
            
            // step through the ordered array
            foreach($data as $semester)
            {
                foreach($semester as $module)
                {
                    // Sum of SWS
                    $swsSum += $module['SWS_Vorlesung'] + 
                                $module['SWS_Uebung'] + 
                                $module['SWS_Praktikum'] + 
                                $module['SWS_Projekt'] + 
                                $module['SWS_Seminar'] + 
                                $module['SWS_Seminarunterricht'];

                    // Sum of Creditpoints
                    $cpSum += $module['Creditpoints'];

                    // write sums in array
                    $sumArray[$counter]['SWS_Summe'] = intval($swsSum);
                    $sumArray[$counter]['CP_Summe'] = intval($cpSum);
                }
                
                // reset locale variables
                $swsSum = 0;
                $cpSum = 0;
                $counter++;
            }
            
            return $sumArray;
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        } 
    } 
    
    
    
    /**
     * Increase the try of a module if it's not passed
     * 
     * @param int $moduleId
     */
    public function increaseTry($moduleId)
    {
        $try = 0;
        
        try 
        {
            $this->db->select('VersucheBislang');
            $this->db->from('semesterkurs');
            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->where('KursID', $moduleId);
            $tries = $this->db->get();
            
            foreach($tries->result() as $t)
            {
                $try = $t->VersucheBislang;
            }
            
            
            if($try < 3)
            {
                $try++;
                
                $dataarray = array(
                    'VersucheBislang' => $try
                );

                $this->db->where('KursID', $moduleId);
                $this->db->update('semesterkurs', $dataarray);
            }
            else
            {
                echo 'Du kannst diese Prüfung nicht mehr wiederholen';
            }
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        } 
    }
    
    
    
    
    /**
     * Saves all made changes
     * 
     * @param Array $dataarray  Array with changed Module-ID's
     */
    public function save($dataarray)
    {
        try 
        {
            foreach($dataarray as $data)
            {
                $this->db->where('KursID', $data['KursID']);
                $this->db->update('semesterkurs', $data);
            }
        } 
        catch (Exception $e) 
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    /**
     * Returns an array set with information about all courses
     * 
     * @return Array
     */
    public function moduleInfo()
    {
        $data = array();
        
        try
        {
            $this->db->select('studiengangkurs.*');
            $this->db->from('studiengangkurs');
            $this->db->join('semesterkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
            $this->db->where('semesterkurs.SemesterplanID', $this->studyplanID);
            $this->db->order_by('studiengangkurs.KursID', 'ASC');
            $studiengang = $this->db->get();
            
            foreach($studiengang->result() as $stdg)
            {
                $data[] = array(
                    'Kursname'          => $stdg->Kursname,
                    'Creditpoints'      => $stdg->Creditpoints,
                    'Vorlesung'         => $stdg->SWS_Vorlesung,
                    'Uebung'            => $stdg->SWS_Uebung,
                    'Praktikum'         => $stdg->SWS_Praktikum,
                    'Projekt'           => $stdg->SWS_Projekt,
                    'Seminar'           => $stdg->SWS_Seminar,
                    'Seminarunterricht' => $stdg->SWS_SeminarUnterricht
                );
            }
            
            // delete zero-values
            foreach($data as &$info)
            {
                foreach($info as $entry => $value)
                {
                    // don't know why, but if you check for 0 and not for '0', 
                    // the Kursname will be deleted too
                    if($value == '0')
                    {
                        unset($info[$entry]);
                    }
                }
            }
            
            return $data;
        }
        catch(Exceptoin $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }
    
    
    
    /**
     * Returns an array of all participated groups
     * 
     * @return Array
     */
    public function groups()
    {
        $data = array();
        
        try
        {
            $this->db->select('stundenplankurs.VeranstaltungsformAlternative,
                                stundenplankurs.Raum,
                                stundenplankurs.StartID,
                                stundenplankurs.EndeID,
                                studiengangkurs.Kursname,
                                studiengangkurs.kurs_kurz,
                                veranstaltungsform.VeranstaltungsformName,
                                tag.TagName,
                                benutzer.Nachname,
                                gruppenteilnehmer.GruppeID');
            $this->db->from('stundenplankurs');
            $this->db->join('studiengangkurs', 'stundenplankurs.KursID = studiengangkurs.KursID');
            $this->db->join('veranstaltungsform', 'stundenplankurs.VeranstaltungsformID = veranstaltungsform.VeranstaltungsformID');
            $this->db->join('tag', 'stundenplankurs.TagID = tag.TagID');
            $this->db->join('benutzer', 'stundenplankurs.DozentID = benutzer.BenutzerID');
            $this->db->join('gruppenteilnehmer', 'benutzer.BenutzerID = gruppenteilnehmer.BenutzerID');
            $this->db->where('benutzer.BenutzerID', $this->userID);
            $this->db->where('benutzer.Semester', $this->currentSemester);
            $groups = $this->db->get();
            
            
            foreach($groups->result() as $group)
            {
                $data['groups'][] = array(
                    'VeranstaltungsformAlternative' => $group->VeranstaltungsformAlternative,
                    'Raum'                          => $group->Raum,
                    'StartID'                       => $group->StartID,
                    'EndeId'                        => $group->EndeID,
                    'Kursname'                      => $group->Kursname,
                    'kurs_kurz'                     => $group->kurs_kurz,
                    'VeranstaltungsformName'        => $group->VeranstaltungsformName,
                    'TagName'                       => $group->TagName,
                    'Nachname'                      => $group->Nachname,
                    'GruppeID'                      => $group->GruppeID
                );
            }
            
            return $data;
        }
        catch(Exceptoin $e)
        {
            echo 'Exception: ', $e->getMessage();
        }
    }


    
    
    // ============================ Getter & Setter ============================
    
    public function getUserID() {
        return $this->userID;
    }

    public function setUserID($userID) {
        $this->userID = $userID;
    }

    public function getStudycourseID() {
        return $this->studycourseID;
    }

    public function setStudycourseID($studycourseID) {
        $this->studycourseID = $studycourseID;
    }

    public function getStudyplanID() {
        return $this->studyplanID;
    }

    public function setStudyplanID($studyplanID) {
        $this->studyplanID = $studyplanID;
    }

    public function getCurrentSemester() {
        return $this->currentSemester;
    }

    public function setCurrentSemester($currentSemester) {
        $this->currentSemester = $currentSemester;
    }
}


/* End of file studienplan_model.php */
/* Location: ./application/models/studienplan.php */