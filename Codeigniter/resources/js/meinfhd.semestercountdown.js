		// This File is only used to configure Semestercountdown.
		$(function(){
			
			var note = $('#note'),
				note2 = $('#note2')
				endOfSemester = new Date(2012, 5,20,19,2,0),//monat-1  This date should come from a DB
				examStartDay  = new  Date(2012,6,9,13,20,30)
				endOfSemesterReached = false;
				examStartDayReached = false;
			
			if((new Date()) > endOfSemester){
				// Notice the *1000 at the end - time must be in milliseconds
				//endOfSemester = (new Date()).getTime() + 10*24*60*60*1000;
				endOfSemesterReached = true;
				
			}
			if((new Date()) > examStartDay){
				// Notice the *1000 at the end - time must be in milliseconds
				//endOfSemester = (new Date()).getTime() + 10*24*60*60*1000;
				examStartDayReached = true;
				
			}
			
			
			$('#countdown').countdown({
				timestamp	: endOfSemester,
				callback	: function(days, hours, minutes, seconds){
					var $this = $(this);
				//	console.log($this);
					var message1 = "";
					
					message1 +=  " Tag" + ( days==1 ? '':'e' ) ;
					message1 +=  " Stunde" + ( hours==1 ? '':'n' )  ;
					message1 +=  " Minute" + ( minutes==1 ? '':'n' )  ;
					message1 +=  " Sekunde" + ( seconds==1 ? '':'n' ) + " <br />";
					
					if(endOfSemesterReached){
						message1 = "viel Spass.. No more Days left.";
						$this.hide();
						
					}
					
					
					note.html(message1);
				}
			});
			
			$('#countdown2').countdown({
				timestamp	: examStartDay ,
				callback	: function(days, hours, minutes, seconds){
					
					var message2 = "";
					
					message2 +=  " Tag" + ( days==1 ? '':'e' ) ;
					message2 +=  " Stunde" + ( hours==1 ? '':'n' )  ;
					message2 +=  " Minute" + ( minutes==1 ? '':'n' )  ;
					message2 +=  " Sekunde" + ( seconds==1 ? '':'n' ) + " <br />";
					
					if(examStartDayReached){
						message2 = "Hope you used your time";
						//$('#countdown').hide();
					}
					
					
					note2.html(message2);
				}
			});
			
		});
		
		function toggle_counter(c){
				
				
				//alert(c);
				
				if ($('#semesterende').attr('checked'))
				{
					$('#counter1').show();
				}
				else{
				$('#counter1').hide();
					
				}
				
				if ($('#klausurstart').attr('checked'))				{
					$('#counter2').show();
				}
				else{
				$('#counter2').hide();
					
				}
			
			}

	