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
     * 
     * method -> checked
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
     * 
     * method -> checked
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
     * 
     * method -> checked
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
     * 
     * method -> checked
     */
    public function queryStudyplan()
    { 
        // try to query the database
        try
        {
            // query DB
            $this->db->select('studiengangkurs.Semester, 
                                studiengangkurs.Kursname,
                                studiengangkurs.kurs_kurz,
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
                'Semester'      => 0,
                'Kursname'      => '',
                'Kurzname'      => '',
                'Hoeren'        => '',
                'Schreiben'     => '',
                'Notenpunkte'   => ''
            );
            
            
            // group the resultset by semester in array
            foreach($studyplan->result() as $sq)
            {
                $data['plan'][$sq->Semester][] = array(
                    'Semester'      => $sq->Semester,
                    'Kursname'      => $sq->Kursname,
                    'Kurzname'      => $sq->kurs_kurz,
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
     * Creates a new Studyplan if it not already exists
     * 
     * method -> checked
     */
    public function createStudyplan()
    {
        try
        {
            if($this->studyplanID == 0)
            {
                $this->db->select('Regelsemester');
                $this->db->from('studiengang');
                $this->db->where('StudiengangID', $this->studycourseID);
                $regelsemester_result = $this->db->get();
                
                
                foreach($regelsemester_result->result() as $regel)
                {
                    $dataarray = array(
                        'BenutzerID'    => $this->userID,
                        'Semesteranzahl'=> $regel->Regelsemester
                    );

                    $this->db->insert('semesterplan', $dataarray);
                    
                    // query studycourseID
                    $this->studyplanID = $this->queryStudyplanId();
                    
                    
                    $this->db->select('KursID, Semester');
                    $this->db->from('studiengangkurs');
                    $this->db->where('StudiengangID', $this->studycourseID);
                    $kurs_semester = $this->db->get();
                    
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
                
                // Eexecute  createTimetableCourses method
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
     * 
     * method -> checked
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
            
            // insert in benutzerkurs all data from the query above
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
     * 
     * method -> checked
     */
    public function createNewSemesterColoumn()
    {
        try
        {
            $this->db->select('Semesteranzahl');
            $this->db->from('semesterplan');
            $this->db->where('SemesterplanID', $this->studyplanID);
            $semestercount = $this->db->get();
            
            foreach($semestercount->result() as $semcount)
            {
                $semester = $semcount->Semesteranzahl;
            }

            $semester++;
            
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
     * @param int $kurs_id 
     *
     * method -> checked
     */
    public function updateSemesterColoumn($kurs_id, $semester)
    {
        try
        {   
            $dataarray = array(
                'Semester' => $semester
            );
            
            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->where('KursID', $kurs_id);
            $this->db->update('semesterkurs', $dataarray);
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
     * 
     * method -> checked
     */
    public function queryAllModules()
    {
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
     * @return float
     * 
     * method -> checked
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
     * @param int $moduleID
     * 
     * method -> checked 
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
     * Resets all Studyplan-data of a user 
     * 
     * method -> checked
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
     * @return Array 
     * 
     * method -> checked
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
    
    
    
    // ERROR: Datensatz kann nicht eingefügt werden, da Tabelle dann doppelten 
    // Datensatz besitzt. Deswegen muss eine eindeutige ID mit vergeben werden
    /**
     * Duplicate a module if it's not passed
     * @param int $moduleId 
     */
    public function duplicateModule($moduleId)
    {
        try 
        {
            $this->db->select('*');
            $this->db->from('semesterkurs');
            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->where('KursID', $moduleId);
            $this->db->order_by('VersucheBislang');
            $module = $this->db->get();
            
            foreach($module->result() as $mod)
            {
                if($mod->VersucheBislang <= 3)
                {
                    $data = array(
                        'SemesterplanID'    => $mod->SemesterplanID,
                        'KursID'            => $mod->KursID,
                        'Semester'          => $mod->Semester,
                        'KursHoeren'        => $mod->KursHoeren,
                        'KursSchreiben'     => $mod->KursSchreiben,
                        'PruefungsstatusID' => $mod->PruefungsstatusID,
                        'VersucheBislang'   => $mod->VersucheBislang + 1,
                        'Notenpunkte'       => $mod->Notenpunkte
                    );
                }
                else
                {
                    echo 'Du kannst diese Prüfung nicht mehr wiederholen';
                }
            }
            
            $this->db->insert('semesterkurs', $data);
        }
        catch(Exception $e)
        {
            echo 'Exception: ', $e->getMessage();
        } 
    }
    
    
    // Änderungen abspeichern
    // TODO: Spezifizieren, wie das Array aufgebaut werden soll
    /**
     * Saves all made changes
     * @param Array $dataarray  Array with changed Module-ID's
     */
    public function save($dataarray)
    {
        
    }
    
    
    // Infos über das Modul. Was wird hier benötigt?
    public function moduleInfo(){}


    
    
    // ============================ Getter & Setter ============================
    

}

/* End of file studienplan.php */
/* Location: ./application/models/studienplan.php */