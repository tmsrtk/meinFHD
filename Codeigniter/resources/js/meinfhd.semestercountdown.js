		// This File is only used to configure Semestercountdown.
		$(function(){
			
			var note = $('#note'),
				note2 = $('#note2'),
				endOfSemester = new Date(2012, 6,1,17,0,2),//monat-1  This date should come from a DB
				examStartDay1  = new  Date(2012,6,9,13,20,30),
				examStartDay2  = new  Date(2012,7,4,9,11,4),
				examStartDay3  = new  Date(2012,9,8,16,18,46),
				endOfSemesterReached = false;
				examStartDay1Reached = false;
			
			if((new Date()) > endOfSemester){
				// Notice the *1000 at the end - time must be in milliseconds
				//endOfSemester = (new Date()).getTime() + 10*24*60*60*1000;
				endOfSemesterReached = true;
				
			}
			if((new Date()) > examStartDay1){
				// Notice the *1000 at the end - time must be in milliseconds
				//endOfSemester = (new Date()).getTime() + 10*24*60*60*1000;
				examStartDay1Reached = true;
				
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
					/*
					if(endOfSemesterReached){
						message1 = "viel Spass.. No more Days left.";
						$this.hide();
						
					}
					*/
					
					note.html(message1);
				}
			});
			
			$('#countdown2').countdown({
				timestamp	: examStartDay1 ,
				callback	: function(days, hours, minutes, seconds){
					
					var message2 = "";
					
					message2 +=  " Tag" + ( days==1 ? '':'e' ) ;
					message2 +=  " Stunde" + ( hours==1 ? '':'n' )  ;
					message2 +=  " Minute" + ( minutes==1 ? '':'n' )  ;
					message2 +=  " Sekunde" + ( seconds==1 ? '':'n' ) + " <br />";
					
					/*
					if(examStartDay1Reached){
						message2 = "Hope you used your time";
						//$('#countdown').hide();
					}
*/
					
					
					note2.html(message2);
				}
			});
			
			$('#countdown3').countdown({
				timestamp	: examStartDay2 ,
				callback	: function(days, hours, minutes, seconds){
					
					var message2 = "";
					
					message2 +=  " Tag" + ( days==1 ? '':'e' ) ;
					message2 +=  " Stunde" + ( hours==1 ? '':'n' )  ;
					message2 +=  " Minute" + ( minutes==1 ? '':'n' )  ;
					message2 +=  " Sekunde" + ( seconds==1 ? '':'n' ) + " <br />";
					/*
					if(examStartDay1Reached){
						message2 = "Hope you used your time";
						//$('#countdown').hide();
					}
					*/
					
					note2.html(message2);
				}
			});
			
			$('#countdown4').countdown({
				timestamp	: examStartDay3 ,
				callback	: function(days, hours, minutes, seconds){
					
					var message2 = "";
					
					message2 +=  " Tag" + ( days==1 ? '':'e' ) ;
					message2 +=  " Stunde" + ( hours==1 ? '':'n' )  ;
					message2 +=  " Minute" + ( minutes==1 ? '':'n' )  ;
					message2 +=  " Sekunde" + ( seconds==1 ? '':'n' ) + " <br />";
					/*
					if(examStartDay1Reached){
						message2 = "Hope you used your time";
						//$('#countdown').hide();
					}
					*/
					
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
				
				if ($('#klausurstart1').attr('checked'))				{
					$('#counter2').show();
				}
				else{
				$('#counter2').hide();
					
				}
				
				if ($('#klausurstart2').attr('checked'))				{
					$('#counter3').show();
				}
				else{
				$('#counter3').hide();
					
				}
				if ($('#klausurstart3').attr('checked'))				{
					$('#counter4').show();
				}
				else{
				$('#counter4').hide();
					
				}
			
			}

			function toggle_credits(c){
				$('#leistungsContainer').toggle();
				
			
			}
	