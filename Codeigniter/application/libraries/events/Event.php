<?php

class Event {

	/**
	 * The event's start time as integer
	 * starting at 0.
	 */
	private $start = 0;
	
	/**
	 * The event's duration as integers
	 */
	private $duration = 0;
	
	/**
	 * The column number the event is placed
	 */
	private $column = 0;
	
	/**
	 * The column's width
	 */
	private $width = 1;
	
	/**
	 * The event color
	 */
	private $color = 0;
	
	/**
	 * Event data
	 */
	var $data;
	
	/**
	 * Constructor
	 *
	 * @access public
	 * @param integer $start
	 * @param integer $duration
	 * @return void
	 */
	public function __construct($start = -1, $duration = 0, $color = 0, &$data)
	{
		// Check if we got valid values
		if (is_integer($start) && $start >= 0 && is_integer($duration) && $duration >= 1)
		{
			// Add data
			$this->data = $data;
			
			// Assign parameters to instance variables
			$this->start = $start;
			$this->duration = $duration;
			$this->color = $this->_codeToColor($color);
		}
	}
	
	/**
	 * Calculates the end of an event
	 */
	public function getEnd()
	{
		return $this->getStart() + $this->getDuration();
	}
	
	/**
	 * Marks the event in an array
	 *
	 * @access public
	 * @param array $marker
	 * @return void
	 */
	public function mark(&$marker)
	{
		for ($row = $this->getStart(); $row < $this->getEnd(); $row++)
		{
			$marker[$row][$this->getColumn()] = &$this;
		}
	}
	
	/**
	 * Marks the event in an array
	 *
	 * @access public
	 * @param array $marker
	 * @return void
	 */
	public function unmark(&$marker)
	{
		for ($row = $this->getStart(); $row < $this->getEnd(); $row++)
		{
			$marker[$row][$this->getColumn()] = FALSE;
		}
	}
	
	/**
	 * Finds the best place for the event
	 * 
	 * By trying to move the event to another
	 * place we can find the best position for it.
	 *
	 * @access public
	 * @param array $marker
	 * @return void
	 */
	public function optimizePosition(&$marker)
	{
		// The event is on the most left side,
		// so we don't need to calculate a new position.
		if ($this->getColumn() != 0)
		{
			// We check all columns that are smaller
			// than the current event's column.
			for ($col = 0; $col < $this->getColumn(); $col++)
			{	
				// Check if there's free space in the current column
				if ($this->_isFree($marker, $col))
				{
					$this->unmark($marker);
					$this->setColumn($col);
					$this->mark($marker);
				}
			}
		}
	}
	
	public function optimizeWidth(&$marker, $max_cols)
	{
		// Don't do anything, if it's the last event in the
		// column, because it can't be extended to the right.
		if ($this->getColumn() != ($max_cols - 1))
		{
			for ($col = $this->getColumn() + 1; $col < $max_cols; $col++)
			{	
				if ($this->_isFree($marker, $col))
				{
					$this->width++;
				}
				else
				{
					break;
				}
			}
		}

		$this->width = $this->width / $max_cols;
	}
	
	/**
	 * Check if the event can be in a row
	 *
	 * @access private
	 * @param array $marker
	 * @param integer $col
	 * @return TRUE if the event can be in the row
	 *		   FALSE if there's no place in that row
	 */
	private function _isFree(&$marker, $col)
	{
		// We check every field that we would need for the event.
		// If it's already blocked - indicated by not FALSE -
		// the function returns FALSE which says, that there's not
		// enough space. If the event passes all checks, we know
		// that there's enough room and return TRUE.
		for ($row = $this->getStart(); $row < $this->getEnd(); $row++)
		{
			if ( ! $marker[$row][$col] === FALSE)
			{
				// There's NOT enough space
				return FALSE;
			}
		}
		// There IS enough space
		return TRUE;
	}
	
	/**
	 * Just debug color funk...
	 */
	private function _randomColor()
	{
    	mt_srand((double)microtime() * 1000000);
	    $c = '';
	    while (strlen($c) < 6)
	    {
	        $c .= sprintf("%02X", mt_rand(0, 255));
	    }
	    
	    $this->color = '#' . $c;
	}
	
	/**
	 *
	 */
	private function _codeToColor($col, $light = FALSE)
	{

		### SUPER DIRTY HACK ###
		$light = ($this->data['VeranstaltungsformName'] == 'Ãœbung') ? TRUE : FALSE;
		### SUPER DIRTY HACK END ###
		
		if($light) 
		{		
			switch($col)
			{
				case 0: 		$ret = "000000"; break;	// schwarz
				case 16777215: 	$ret = "ffffff"; break;	// weiß
				case 255: 		$ret = "ffaaaa"; break;	// rot
				case 65280: 	$ret = "aaffaa"; break;	// grün
				case 16711680: 	$ret = "aaaaff"; break;	// blau
				case 65535: 	$ret = "ffffaa"; break;	// gelb
				case 16711935: 	$ret = "ffaaff"; break;	// magenta
				case 16776960: 	$ret = "ccffff"; break;	// cyan
				case 128: 		$ret = "baaaaa"; break;
				case 32768: 	$ret = "aabaaa"; break;
				case 8388608: 	$ret = "aaaaba"; break;
				case 32896: 	$ret = "babaaa"; break;
				case 8388736: 	$ret = "baaaba"; break;
				case 8421376: 	$ret = "aababa"; break;
				case 12632256: 	$ret = "cacaca"; break;
				case 8421504: 	$ret = "8a8a8a"; break;
				case 16751001: 	$ret = "ccccff"; break;
				case 6697881: 	$ret = "cc6699"; break;
				case 13434879: 	$ret = "fffff0"; break;
				case 16777164: 	$ret = "f0ffff"; break;
				case 6684774: 	$ret = "99aa99"; break;
				case 8421631: 	$ret = "ffbaba"; break;
				case 13395456: 	$ret = "aa99ff"; break;
				case 16764108: 	$ret = "f0f0ff"; break;
				case 16763904: 	$ret = "aaf0ff"; break;
				case 16777164: 	$ret = "f0ffff"; break;
				case 13434828: 	$ret = "f0fff0"; break;
				case 10092543: 	$ret = "ffff00"; break;
				case 16764057: 	$ret = "ccf0ff"; break;
				case 13408767: 	$ret = "ffccff"; break;
				case 16751052: 	$ret = "f0ccff"; break;
				case 10079487: 	$ret = "fff5cc"; break;
				case 16737843: 	$ret = "6699ff"; break;
				case 13421619: 	$ret = "66f0f0"; break;
				case 52377: 	$ret = "ccf0aa"; break;
				case 52479: 	$ret = "fff0aa"; break;
				case 39423: 	$ret = "ffcc33"; break;
				case 26367: 	$ret = "ffcc66"; break;
				case 10053222: 	$ret = "9999cc"; break;
				case 9868950: 	$ret = "c9c9c9"; break;
				case 6697728: 	$ret = "aa6699"; break;
				case 6723891: 	$ret = "66cc99"; break;
				case 13209: 	$ret = "cc66aa"; break;
				case 6697881: 	$ret = "cc6699"; break;
				case 10040115: 	$ret = "6666cc"; break;
			}
		} 
		else 
		{		
			switch($col) 
			{
				case 0: 		$ret = "000000"; break;	// schwarz
				case 16777215: 	$ret = "ffffff"; break;	// weiß
				case 255: 		$ret = "ff0000"; break;	// rot
				case 65280: 	$ret = "00ff00"; break;	// grün
				case 16711680: 	$ret = "0000ff"; break;	// blau
				case 65535: 	$ret = "ffff00"; break;	// gelb
				case 16711935: 	$ret = "ff00ff"; break;	// magenta
				case 16776960: 	$ret = "00ffff"; break;	// cyan
				case 128: 		$ret = "800000"; break;
				case 32768: 	$ret = "008000"; break;
				case 8388608: 	$ret = "000080"; break;
				case 32896: 	$ret = "808000"; break;
				case 8388736: 	$ret = "800080"; break;
				case 8421376: 	$ret = "008080"; break;
				case 12632256: 	$ret = "c0c0c0"; break;
				case 8421504: 	$ret = "808080"; break;
				case 16751001: 	$ret = "9999ff"; break;
				case 6697881: 	$ret = "993366"; break;
				case 13434879: 	$ret = "ffffcc"; break;
				case 16777164: 	$ret = "ccffff"; break;
				case 6684774: 	$ret = "660066"; break;
				case 8421631: 	$ret = "ff8080"; break;
				case 13395456: 	$ret = "0066cc"; break;
				case 16764108: 	$ret = "ccccff"; break;
				case 16763904: 	$ret = "00ccff"; break;
				case 16777164: 	$ret = "ccffff"; break;
				case 13434828: 	$ret = "ccffcc"; break;
				case 10092543: 	$ret = "ffff99"; break;
				case 16764057: 	$ret = "99ccff"; break;
				case 13408767: 	$ret = "ff99cc"; break;
				case 16751052: 	$ret = "cc99ff"; break;
				case 10079487: 	$ret = "ffcc99"; break;
				case 16737843: 	$ret = "3366ff"; break;
				case 13421619: 	$ret = "33cccc"; break;
				case 52377: 	$ret = "99cc00"; break;
				case 52479: 	$ret = "ffcc00"; break;
				case 39423: 	$ret = "ff9900"; break;
				case 26367: 	$ret = "ff6600"; break;
				case 10053222: 	$ret = "666699"; break;
				case 9868950: 	$ret = "969696"; break;
				case 6697728: 	$ret = "003366"; break;
				case 6723891: 	$ret = "339966"; break;
				case 13209: 	$ret = "993300"; break;
				case 6697881: 	$ret = "993366"; break;
				case 10040115: 	$ret = "333399"; break;
			}		
		}
		return '#' . $ret; 
	}
	
	/**
	 * Getter
	 */
	public function getStart()
	{
		return $this->start;
	}
	
	public function getDuration()
	{
		return $this->duration;
	}
	
	public function getColumn()
	{
		return $this->column;
	}
	
	public function getWidth()
	{
		return $this->width;
	}
	
	public function getColor()
	{
		return $this->color;
	}
	
	/**
	 * Setter
	 */
	public function setColumn($column)
	{
		$this->column = $column;
	}
	
}