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
    
    
    /**
     * Queries the DB for the Studyplan-ID (Semesterplan-ID)
     * 
     * @return int
     */
    private function queryStudyplanId()
    {
        $id = 0;
        
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
    
    
    
    /**
     * Returns the current user semester
     * 
     * @return int 
     */
    private function queryCurrentSemester()
    {
        $currentSemester = 0;
        
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
    

    

    /**
     * Queries the Db for the Studyplan of the user
     * 
     * @return Array
     */
    public function queryStudyplan()
    { 
        $data = array();
        
        // query DB
        $this->db->select('studiengangkurs.KursID,
                            studiengangkurs.Semester AS regularSemester, 
                            studiengangkurs.Kursname,
                            studiengangkurs.kurs_kurz,
                            semesterkurs.Semester AS graduateSemester,
                            semesterkurs.KursHoeren, 
                            semesterkurs.KursSchreiben, 
                            semesterkurs.Notenpunkte,
                            semesterplan.Semesteranzahl');
        $this->db->from('studiengangkurs');
        $this->db->join('semesterkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
        $this->db->join('semesterplan', 'semesterplan.SemesterplanId = semesterkurs.SemesterplanID');
        $this->db->where('semesterkurs.SemesterplanID', $this->studyplanID);
        $this->db->order_by('regularSemester', 'ASC');
        $studyplan = $this->db->get();


        // initial zero semester
        $data['plan'][0][] = array(
            'regularSemester'   => null,
            'KursID'            => null,
            'Kursname'          => null,
            'Kurzname'          => null,
            'graduateSemester'  => null,
            'Teilnehmen'        => null,
            'Pruefen'           => null,
            'Notenpunkte'       => null
        );


        // group the resultset by semester in array
        foreach($studyplan->result() as $sq)
        {
            // if graduateSemester doesn't equals regularSemester, set 
            // graduateSemester as key
            if($sq->graduateSemester != $sq->regularSemester)
            {
                $data['plan'][$sq->regularSemester][] = array(
                    'regularSemester'   => $sq->regularSemester,
                    'KursID'            => null,
                    'Kursname'          => null,
                    'Kurzname'          => null,
                    'graduateSemester'  => null,
                    'Teilnehmen'        => null,
                    'Pruefen'           => null,
                    'Notenpunkte'       => null
                );
                
                $data['plan'][$sq->graduateSemester][] = array(
                    'regularSemester'   => $sq->regularSemester,
                    'KursID'            => $sq->KursID,
                    'Kursname'          => $sq->Kursname,
                    'Kurzname'          => $sq->kurs_kurz,
                    'graduateSemester'  => $sq->graduateSemester,
                    'Teilnehmen'        => $sq->KursHoeren,
                    'Pruefen'           => $sq->KursSchreiben,
                    'Notenpunkte'       => ($sq->Notenpunkte == 101) ? null : $this->calculateMark($sq->Notenpunkte)
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
                    'Teilnehmen'        => $sq->KursHoeren,
                    'Pruefen'           => $sq->KursSchreiben,
                    'Notenpunkte'       => ($sq->Notenpunkte == 101) ? null : $this->calculateMark($sq->Notenpunkte)
                );
            }
        }
        
        if($sq->Semesteranzahl > $sq->regularSemester)
        {
            $diff = $sq->Semesteranzahl - $sq->regularSemester;
            
            for($i=0; $i<$diff; $i++)
            {
                $data['plan'][$sq->regularSemester + $i][] = array(
                    'regularSemester'   => null,
                    'KursID'            => null,
                    'Kursname'          => null,
                    'Kurzname'          => null,
                    'graduateSemester'  => null,
                    'Teilnehmen'        => null,
                    'Pruefen'           => null,
                    'Notenpunkte'       => null
                );
            }
        }
        
        // sort the studyplan by semester
        ksort($data['plan']);
  
        return $data;
    }

    /**
     * Queries the whole studyplan of logged user. Desktop version.
     * @return mixed Studyplan in m-dim Arrays.
     */
    public function queryStudyplanDesktop()
    { 
        $data = array();
        
        // query DB
        $this->db->select('studiengangkurs.KursID,
                            studiengangkurs.Semester AS regularSemester, 
                            studiengangkurs.Kursname,
                            studiengangkurs.kurs_kurz,
                            semesterkurs.Semester AS graduateSemester,
                            semesterkurs.KursHoeren, 
                            semesterkurs.KursSchreiben, 
                            semesterkurs.Notenpunkte,
                            semesterplan.Semesteranzahl');
        $this->db->from('studiengangkurs');
        $this->db->join('semesterkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
        $this->db->join('semesterplan', 'semesterplan.SemesterplanId = semesterkurs.SemesterplanID');
        $this->db->where('semesterkurs.SemesterplanID', $this->studyplanID);
        $this->db->order_by('regularSemester', 'ASC');
        $studyplan = $this->db->get();

        // initial zero semester
        $data['plan'][0][] = array(
            'regularSemester'   => null,
            'KursID'            => null,
            'Kursname'          => null,
            'Kurzname'          => null,
            'graduateSemester'  => null,
            'Teilnehmen'        => null,
            'Pruefen'           => null,
            'Notenpunkte'       => null
        );


        // group the resultset by semester in array
        foreach($studyplan->result() as $sq)
        {
            // if graduateSemester doesn't equals regularSemester, set 
            // graduateSemester as key
            if($sq->graduateSemester != $sq->regularSemester)
            {
                $data['plan'][$sq->regularSemester][] = array(
                    'regularSemester'   => $sq->regularSemester,
                    'KursID'            => null,
                    'Kursname'          => null,
                    'Kurzname'          => null,
                    'graduateSemester'  => null,
                    'Teilnehmen'        => null,
                    'Pruefen'           => null,
                    'Notenpunkte'       => null
                );
                
                $data['plan'][$sq->graduateSemester][] = array(
                    'regularSemester'   => $sq->regularSemester,
                    'KursID'            => $sq->KursID,
                    'Kursname'          => $sq->Kursname,
                    'Kurzname'          => $sq->kurs_kurz,
                    'graduateSemester'  => $sq->graduateSemester,
                    'Teilnehmen'        => $sq->KursHoeren,
                    'Pruefen'           => $sq->KursSchreiben,
                    'Notenpunkte'       => ($sq->Notenpunkte == 101) ? null : $this->calculateMark($sq->Notenpunkte)
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
                    'Teilnehmen'        => $sq->KursHoeren,
                    'Pruefen'           => $sq->KursSchreiben,
                    'Notenpunkte'       => ($sq->Notenpunkte == 101) ? null : $this->calculateMark($sq->Notenpunkte)
                );
            }
        }
        
        if($sq->Semesteranzahl > $sq->regularSemester)
        {
            $diff = $sq->Semesteranzahl - $sq->regularSemester;
            
            for($i=0; $i<$diff; $i++)
            {
                $data['plan'][$sq->regularSemester + $i][] = array(
                    'regularSemester'   => null,
                    'KursID'            => null,
                    'Kursname'          => null,
                    'Kurzname'          => null,
                    'graduateSemester'  => null,
                    'Teilnehmen'        => null,
                    'Pruefen'           => null,
                    'Notenpunkte'       => null
                );
            }
        }

        // sort the studyplan by semester
        ksort($data['plan']);
    
        return $data;
    }

    public function add_approve_sem($semesterplan_id=0)
    {
        $this->db->update('semesterplan', array('HatAnerkennungsSemester'=>'1'), "SemesterplanID = {$semesterplan_id}");
    }

    public function query_approve_sem()
    {
        $this->db->select('HatAnerkennungsSemester')
                 ->from('semesterplan')
                 ->where('SemesterplanID', $this->studyplanID);
        return $this->db->get()->row_array();
    }
    
    /**
     * Creates a new Studyplan if it not already exists
     */
    public function createStudyplan()
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
    
    
    
    /**
     * Creates the Courses of the timetable
     */
    public function createTimetableCourses()
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
    
    
    
    /**
     * Adds a new coloumn (raises the coloumn Semesteranzahl)
     */
    public function createNewSemesterColoumn()
    {
        $semester = 0;
        
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
    


    /**
     * Deletes last coloumn
     */
    public function delete_last_semesterplan_coloumn()
    {
        $semester = 0;
        
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
        $semester--;

        // and update the coloumn Semesteranzahl
        $dataarray = array(
            'Semesteranzahl' => $semester
        );

        $this->db->where('SemesterplanID', $this->studyplanID);
        $this->db->update('semesterplan', $dataarray);
    }
    
    
    
    /**
     * If studyplan has more semester than Regelsemester and this coloumn has 
     * modules, this method updates the semestercoloumn in semesterkurs
     * 
     * @param int $module_id
     * @param int $semester 
     */
    /*public function shiftModuleMobile($module_id, $semester)
    {
        // update the Semester-coloumn 
        $dataarray = array(
            'Semester' => $semester
        );

        $this->db->where('SemesterplanID', $this->studyplanID);
        $this->db->where('KursID', $module_id);
        $this->db->update('semesterkurs', $dataarray);
    }*/
    
    
    
    
    /**
     * Shift a module Desktop-Version
     * 
     * @param Array $neue_reihenfolge
     * @param int $semesternr 
     */
    public function shiftModuleDesktop($neue_reihenfolge, $semesternr)
    {
        // counter für die Reihenfolge
        $counter = 1;
        // speichere neue Reihenfolge in die DB
        foreach ($neue_reihenfolge as $serialized_position) {
            $data = array(
               //'Semesterposition' => $counter,
               'Semester' => $semesternr
            );
 
            // FB::log($serialized_position);
 
            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->where('KursID', $serialized_position);
            $this->db->update('Semesterkurs', $data);
 
            $counter++;
        }
    }
    
    
    
    
    /**
     * Returns the mark 
     * 
     * @param int $markpoints
     * @return String
     */
    public function calculateMark($markpoints)
    {   
        $mark = intval($markpoints);
        
        if(!is_int($mark))
        {
            echo 'Bitte gib eine Punktzahl zwischen 0 und 100 ein.';
        }
        else
        {
            if($mark <= 100 && $mark >= 95) 
            {
                return '1';
            }
            elseif($mark < 95 && $mark >= 90)
            {
                return '1-';
            }
            elseif($mark < 90 && $mark >= 85)
            {
                return '2+';
            }
            elseif($mark < 85 && $mark >= 80) 
            {
                return '2';
            }
            elseif($mark < 80 && $mark >= 75)
            {
                return '2-';
            }
            elseif($mark < 75 && $mark >= 70)
            {
                return '3+';
            }
            elseif($mark < 70 && $mark >= 65) 
            {
                return '3';
            }
            elseif($mark < 65 && $mark >= 60)
            {
                return '3-';
            }
            elseif($mark < 60 && $mark >= 55)
            {
                return '4+';
            }
            elseif($mark < 55 && $mark >= 50)
            {
                return '4';
            }
            elseif($mark < 50)
            {
                return '5';
            }
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
    

    

    /**
     * Calculates the average mark
     * @return float 
     */
    public function calculateAverageMark()
    {
        $sum = 0;
        $credits = 0;
        
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
    
    
    
    
    /**
     * Returns the percentage of the Study
     * 
     * @return float
     */
    public function calculatePercentageOfStudy()
    {  
        $passedModules = 0;
        
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
    
    
    
    
    /**
     * Set the module with the $moduleID as accepted
     * 
     * @param int $moduleID
     */
    public function acceptMarks($moduleID)
    {
        $dataarray = array(
            'Notenpunkte' => 50
        );

        $this->db->where('SemesterplanID', $this->studyplanID);
        $this->db->where('KursID', $moduleID);
        $this->db->update('semesterkurs', $dataarray);
    }




    /**
     * Change the Status of the Pruefung
     * 
     * @param int $moduleID
     */
    public function changeModuleStatus($moduleID, $mark)
    {
        if($mark < 5)
        {
            $data = array(
                'PruefungsstatusID' => 4
            );

            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->where('KursID', $moduleID);
            $this->db->update('semesterkurs', $data);
        }
        else
        {
            $data = array(
                'PruefungsstatusID' => 3
            );

            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->where('KursID', $moduleID);
            $this->db->update('semesterkurs', $data);
        }
    }
    
    
    
    
    /**
     * Save the mark of the module
     * 
     * @param int $moduleID 
     */
    public function saveMark($moduleID, $mark)
    {
        // get number of tries
        $this->db->select('VersucheBislang');
        $this->db->from('semesterkurs');
        $this->db->where('SemesterplanID', $this->studyplanID);
        $this->db->where('KursID', $moduleID);
        $try = $this->db->get();

        foreach ($try->result() as $t) 
        {
            $tries = $t->VersucheBislang;
        }


        // if tries are greater than 3, the mark could not be saved
        if($tries < 3 && $mark != '')
        {
            $dataarray = array(
                'Notenpunkte' => $mark
            );

            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->where('KursID', $moduleID);
            $this->db->update('semesterkurs', $dataarray);
            
            // with every save increase the try of the module
            $this->increaseTry($moduleID);

            // change Pruefungstatus
            $this->changeModuleStatus($moduleID, $this->calculateMark($mark));
        }
        elseif(tries < 3)
        {
            $this->message->set(sprintf('Du kannst dieses Modul nicht mehr bearbeiten.'));
        }
    }
    
    
    
    
    /**
     * Resets ALL Studyplan-data of a user
     */
    public function resetSemestercourses()
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
    
    
    
    
    /**
     * Delete all data which reference a studyplan 
     */
    public function deleteAll()
    {
        // delete all entries in semesterkurs
        $this->db->where('SemesterplanID', $this->studyplanID);
        $this->db->delete('semesterkurs');
        
        // delete all entries in semesterplan
        $this->db->where('SemesterplanID', $this->studyplanID);
        $this->db->where('BenutzerID', $this->userID);
        $this->db->delete('semesterplan');
        
        // deletes all entries in benutzerkurs
        $this->db->where('BenutzerID', $this->userID);
        $this->db->delete('benutzerkurs');
        
        // reset the studyplanID
        $this->studyplanID = 0;
        
        // create a new studyplan
        $this->createStudyplan();
    }
    
    
    
    
    /**
     * Calculates the Sum of the SWS and the CP for each Semester
     * 
     * @return Array
     */
    public function calculateSwsAndCp()
    {
        // locale variables
        $swsSum = 0;
        $cpSum = 0;
        $data = array();
        $sumArray = array();
        
        // query DB
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
                            studiengangkurs.Semester AS regularSemester,
                            studiengang.Regelsemester,
                            semesterkurs.Semester AS graduateSemester,
                            semesterplan.Semesteranzahl');
        $this->db->from('studiengangkurs');
        $this->db->join('semesterkurs', 'semesterkurs.KursID = studiengangkurs.KursID');
        $this->db->join('semesterplan', 'semesterplan.SemesterplanID = semesterkurs.SemesterplanID');
        $this->db->join('studiengang', 'studiengang.StudiengangID = studiengangkurs.StudiengangID');
        $this->db->where('semesterkurs.SemesterplanID', $this->studyplanID);
        $this->db->order_by('semesterkurs.Semester', 'ASC');
        $swsCpResult = $this->db->get();
        
        
        // writes result in array
        foreach($swsCpResult->result() as $sc)
        {
            // if the graduateSemester unequals the regularSemester
            if($sc->graduateSemester != $sc->regularSemester)
            {
                // then put an empty array on the position in regularSemester
                $data[$sc->regularSemester][] = array(
                    'SWS_Vorlesung'         => null,
                    'SWS_Uebung'            => null,
                    'SWS_Praktikum'         => null,
                    'SWS_Projekt'           => null,
                    'SWS_Seminar'           => null,
                    'SWS_Seminarunterricht' => null,
                    'Creditpoints'          => null,
                    'Semester'              => $sc->regularSemester,
                );
                
                // and the module in the graduateSemester Array
                $data[$sc->graduateSemester][] = array(
                    'SWS_Vorlesung'         => $sc->SWS_Vorlesung,
                    'SWS_Uebung'            => $sc->SWS_Uebung,
                    'SWS_Praktikum'         => $sc->SWS_Praktikum,
                    'SWS_Projekt'           => $sc->SWS_Projekt,
                    'SWS_Seminar'           => $sc->SWS_Seminar,
                    'SWS_Seminarunterricht' => $sc->SWS_Seminarunterricht,
                    'Creditpoints'          => $sc->Creditpoints,
                    'Semester'              => $sc->graduateSemester,
                );
            }
            // put the regularSemester in the Array
            else
            {
                $data[$sc->regularSemester][] = array(
                    'SWS_Vorlesung'         => $sc->SWS_Vorlesung,
                    'SWS_Uebung'            => $sc->SWS_Uebung,
                    'SWS_Praktikum'         => $sc->SWS_Praktikum,
                    'SWS_Projekt'           => $sc->SWS_Projekt,
                    'SWS_Seminar'           => $sc->SWS_Seminar,
                    'SWS_Seminarunterricht' => $sc->SWS_Seminarunterricht,
                    'Creditpoints'          => $sc->Creditpoints,
                    'Semester'              => $sc->regularSemester,
                );
            }
        }
        
        // sort the array
        ksort($data);
        
        //initial zero values
        $sumArray[0]['SWS_Summe'] = floatval(0);
        $sumArray[0]['CP_Summe'] = floatval(0);
        
        // step through the ordered array and calculate the SWS & Creditpoints
        foreach($data as $semester)
        {
            foreach($semester as $module)
            {
                // Sum of SWS
                $swsSum += $module['SWS_Vorlesung'] + $module['SWS_Uebung'] + 
                            $module['SWS_Praktikum'] + $module['SWS_Projekt'] + 
                            $module['SWS_Seminar'] + $module['SWS_Seminarunterricht'];

                // Sum of Creditpoints
                $cpSum += $module['Creditpoints'];
            }
            
            // write sums in array
            if($swsSum == 0 && $cpSum == 0)
            {
                $sumArray[$module['Semester']]['SWS_Summe'] = floatval(0);
                $sumArray[$module['Semester']]['CP_Summe'] = floatval(0);
            }
            else
            {
                $sumArray[$module['Semester']]['SWS_Summe'] = floatval($swsSum);
                $sumArray[$module['Semester']]['CP_Summe'] = floatval($cpSum);
            }
            
            // reset locale variables
            $swsSum = 0;
            $cpSum = 0;
        }

        return $sumArray;
    }
    
    
    
    
    /**
     * Increase the try of a module if it's not passed
     * 
     * @param int $moduleId
     */
    public function increaseTry($moduleId)
    {
        $try = 0;
        
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
            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->update('semesterkurs', $dataarray);
        }
        else
        {
            header('Location: /meinFHD/Codeigniter/studienplan/');
        }
    }
    
    
    
    
    /**
     * Desktop: Saves all made changes
     * 
     * @param Array $dataarray  Array with changed Module-ID's
     */
    public function save($dataarray)
    {
        foreach($dataarray as $data)
        {
            $this->db->where('KursID', $data['KursID']);
            $this->db->where('SemesterplanID', $this->studyplanID);
            $this->db->update('semesterkurs', $data);
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
    
    
    
    
    /**
     * Returns an array of all participated groups
     * 
     * @return Array
     */
    public function groups()
    {
        $data = array();
        
        $this->db->select('stundenplankurs.KursID,
                            stundenplankurs.Raum,
                            stundenplankurs.StartID,
                            stundenplankurs.EndeID,
                            studiengangkurs.Kursname,
                            studiengangkurs.kurs_kurz,
                            veranstaltungsform.VeranstaltungsformName,
                            tag.TagName,
                            benutzer.Nachname');
        $this->db->from('stundenplankurs');
        $this->db->join('studiengangkurs', 'studiengangkurs.KursID = stundenplankurs.KursID');
        $this->db->join('veranstaltungsform', 'veranstaltungsform.VeranstaltungsformID = stundenplankurs.VeranstaltungsformID');
        $this->db->join('tag', 'tag.TagID = stundenplankurs.TagID');
        $this->db->join('benutzer', 'benutzer.BenutzerID = stundenplankurs.DozentID');
        //$this->db->join('benutzerkurs', 'benutzerkurs.KursID = stundenplankurs.KursID');
        $this->db->join('gruppenteilnehmer', 'gruppenteilnehmer.GruppeID = stundenplankurs.GruppeID');
        $this->db->where('gruppenteilnehmer.BenutzerID', $this->userID);
        //$this->db->where('benutzerkurs.aktiv', 1);
        $this->db->where('stundenplankurs.VeranstaltungsformAlternative != ""');
        $this->db->order_by('stundenplankurs.KursID', 'ASC');
        $groups = $this->db->get();
        
        
        foreach($groups->result() as $group)
        {
            $data['groups'][] = array(
                'KursID'                        => $group->KursID,
                'Raum'                          => $group->Raum,
                'StartID'                       => $group->StartID,
                'EndeId'                        => $group->EndeID,
                'Kursname'                      => $group->Kursname,
                'kurs_kurz'                     => $group->kurs_kurz,
                'VeranstaltungsformName'        => $group->VeranstaltungsformName,
                'TagName'                       => $group->TagName,
                'Nachname'                      => $group->Nachname
            );
        }

        return $data;
    }
    
    
    
    
    /**
     * Get an array with flags, which module should be written and which heard.
     * Already implemented in queryStudyplan
     * 
     * @return Array 
     */
    /*public function getPruefenTeilnehmen()
    {
        $data = array();
        
        $this->db->select('KursID, KursSchreiben, KursHoeren');
        $this->db->from('semesterkurs');
        $this->db->where('SemesterplanID', $this->studyplanID);
        $courses = $this->db->get();
        
        foreach($courses->result() as $course)
        {
            $data[$course->KursID] = array(
                'Pruefen'   => $course->KursSchreiben,
                'Teilnehmen'=> $course->KursHoeren
            );
        }
        
        return $data;
    }*/
    
    
    
    
    /**
     * Save the Status of testing or participating
     * 
     * @param int $module_id
     * @param int $pruefen
     * @param int $teilnehmen 
     */
    public function savePruefenTeilnehmen($module_id, $pruefen, $teilnehmen)
    {
        $dataarray = array(
            'KursSchreiben' => $pruefen,
            'KursHoeren'    => $teilnehmen
        );

        $this->db->where('SemesterplanID', $this->studyplanID);
        $this->db->where('KursID', $module_id);
        $this->db->update('semesterkurs', $dataarray);
    }
    
    
    
    
    /**
     * Save the changed Semester
     * 
     * @param int $module_id
     * @param int $semester 
     */
    public function saveSemester($module_id, $semester)
    {
        $dataarray = array(
            'Semester' => $semester,
        );

        $this->db->where('SemesterplanID', $this->studyplanID);
        $this->db->where('KursID', $module_id);
        $this->db->update('semesterkurs', $dataarray);
    }
    
    
    
    /**
     * Writes the choosable entries for the selectBox
     * 
     * @return Array 
     * @deprecated  Because the context will be static
     */
    /*public function getContextForSemesterSelectBox()
    {
        $data = array();
        $selectContext = array();
        
        $this->db->select('semesterkurs.KursID, semesterkurs.Semester, semesterplan.Semesteranzahl');
        $this->db->from('semesterplan');
        $this->db->join('semesterkurs ', 'semesterplan.SemesterplanID = semesterkurs.SemesterplanID');
        $this->db->where('semesterplan.SemesterplanID', $this->studyplanID);
        $contextResult = $this->db->get();
        
        foreach($contextResult->result() as $context)
        {     
            $data[] = array(
                'KursID'            => $context->KursID,
                'Semesteranzahl'    => $context->Semesteranzahl,
                'Semester'          => $context->Semester,
            );
        }
        
        
        foreach($data as $d)
        {
            for($i=0; $i<=$d['Semesteranzahl']; $i++)
            {
                $selectContext[$d['KursID']][] = ($i != $d['Semester']) ? 'Semester '.$i : null;
            }
        }
        
        //var_dump($selectContext);
        return $data;
    }*/


    
    
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