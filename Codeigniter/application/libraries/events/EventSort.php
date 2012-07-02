<?php

class EventSort {
	
	/**
	 * Holds all events in a flat array
	 */
	private $events = array();
	
	/**
	 * Marks all reserved regions
	 */
	private $marker = array();
	
	/**
	 * Constructor
	 */
	public function __construct($events = array())
	{
		// Check if we got an array with more than 1 entry
		if (is_array($events) && count($events) > 1)
		{
			// Assign to instance variable
			$this->events = $events;
			
			// Init the marker
			$this->_initMarker();
		}
	}
	
	/**
	 * Init the marker
	 * 
	 * The max size of the marker is calculated
	 * filled with boolean FALSE values to indicate
	 * that there is no event placed yet.
	 *
	 * @access private
	 * @return void
	 */
	private function _initMarker()
	{
		// Get the max length for the marker
		$length = $this->_maxLength();
		
		// Fill marker with empty cells.
		for ($column = 0; $column < count($this->events); $column++)
		{
			for ($row = 0; $row <= $length; $row++)
			{
				$this->marker[$row][$column] = FALSE;
			}
		}
		
		// Reserve place for each event
		foreach ($this->events as $column => $event)
		{
			$event->setColumn($column);
			$event->mark($this->marker);
		}
	}
	
	/**
	 * Calculate the max size of the marker
	 * 
	 * This is done by lookinf at each event and
	 * getting its end. Comparing it to the other
	 * event's endings let's us find the lates end.
	 *
	 * @access private
	 * @return The maximal length the array should have
	 */
	private function _maxLength()
	{
		// Init the length
		$length = 0;
		// Check the end for every event
		foreach ($this->events as $event)
		{
			// We saved the latest end ind $length.
			// If the current event ends later, we save that-
			if (($end = $event->getEnd()) > $length)
			{
				$length = $end;
			}
		}
		// Decrease the length by 1 because
		// the array index starts at 0
		return $length - 1;
	}
	
	private function _neededCols()
	{
		$cols = 0;
		// Check the end for every event
		foreach ($this->events as $event)
		{
			// We saved the latest end ind $length.
			// If the current event ends later, we save that-
			if (($col = $event->getColumn()) > $cols)
			{
				$cols = $col;
			}
		}

		return $cols + 1;
	}
	
	/**
	 * Rearranges the events
	 * 
	 * Events are moved to the most left possible
	 * place. Free places are indicated in the marker.
	 *
	 * @access public
	 * @return Optimized events
	 */
	public function optimize()
	{	
		// Initialize the return array
		$optimized = array();
		
		foreach ($this->events as $event)
		{
			$event->optimizePosition($this->marker);
		}
		
		// Get the number of needed cols
		$needed_cols = $this->_neededCols();
		
		foreach ($this->marker as $index => $row)
		{
			$this->marker[$index] = array_slice($row, 0, $needed_cols);
		}
		
		foreach ($this->events as $index => $event)
		{
			$event->optimizeWidth($this->marker, $needed_cols);
			
			// Build all information
			$optimized[$index] = $event->data;
			$optimized[$index]['display_data'] = array(
				'start' => $event->getStart(),
				'duration' => $event->getDuration(),
				'column' => $event->getColumn(),
				'width' => $event->getWidth(),
				'color' => $event->getColor(),
			);
		}
		
		return $optimized;
	}
	
	/**
	 * Prints debugging informations
	 */
	public function printMarker()
	{
		print '<div style="display:block;margin-bottom:50px;width:100%;">';
		
		foreach ($this->marker as $row_index => $row)
		{
			print '<div style="width:100%;overflow:hidden;height:19px;">';
			
			print '<div style="display:inline-block;width:15px;height:15px;margin:2px;background-color:#fff;font-family:Arial;font-size:10px;line-height:20px;">' . $row_index . '</div>';
			
			foreach ($row as $col)
			{
				$color = ($col) ? '#'.dechex($col->data['Farbe']) : '#ccc' ;
				$multi = ($col) ? $col->getWidth() : '&nbsp;' ;

				print '<div style="display:inline-block;width:15px;height:15px;margin:2px;background-color:' . $color . ';">' . $multi . '</div>';
			}
			print '</div>';
		}
		
		print '</div>';
	}
}