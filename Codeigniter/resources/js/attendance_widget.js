/*
 Attendance Widget JS

 JS / jQuery Stuff to get the attendance widget working

 (c) Christian Kundruss (CK), 2012
 <christian.kundruss@fh-duesseldorf.de>
 */

/*
 *  Track the users attendance for the actual displayed course.
 *  User pressed the 'Ich bin hier' Button
 */
$("#attendButton").click(function() {

    // get the running course
    var course_data = {
        running_course_id: $(this).parent().parent().parent().data("id")
    };

    // if the clicked button is not disabled, track the attendance
    if(!$("#attendButton").hasClass('disabled')){
        // track attendance with the help of ajax
        $.ajax({
            url: CI.base_url + 'attendance/save_new_attendance',
            type: 'GET',
            data: course_data,
            cache: false,
            success: function(success_data){
                $('#attendanceWidget').html(success_data); // display the result / refresh the widget container
                // check if an new attendance widget has been unlocked
                checkForAttendanceAchievement(course_data);
            }
        });


    }


});

/*
 * The user wants to load the logbook content for the currently attended course.
 * If there isn`t a logbook so far, present a modal view otherwise the user will be redirected to his logbook.
 */
$('#switchToLogbookButton').live("click", function() {
   // get the id of the running course
    var data = {
        course_id: $(this).parent().parent().parent().data("id")
    };
    // send request for the logbook to the controller
    $.ajax({
       url: CI.base_url + 'attendance/ajax_search_logbook_for_course',
       type: 'POST',
       data: data,
       success: function(success_data){
           if (success_data == 'no_logbook'){ // there is no logbook for the act course, so print out a modal box
               var myModal = createAddLogbookModalDialog('Kein Logbuch vorhanden','Zu diesem Kurs existiert noch kein Logbuch. Soll jetzt ein Logbuch zu diesem Kurs angelegt werden?');
               $('#modalcontent').html(myModal);
               $('#myModal').modal({
                   keyboard: false
               }).on('hide', function () {

               }).modal('show');
           }
           else { // the url where the user should go is returned, so redirect him
                window.location.href = success_data;
           }

           /*
            *  the user accepted to create a logbook for the active course
            */
           $('#createAcceptedBtn').click(function() { // 'Ja'
               $('#myModal').modal('hide');
               // set location to the correct controller method
               window.location.href = CI.base_url + 'logbuch/create_logbook/' + data.course_id;
           });
       }
    });
});

/*
 * function to create a simple bootstrap modal dialog
  */
function createAddLogbookModalDialog(title, text) {
    var $myModalDialog = $('<div class="modal hide" id="myModal"></div>')
        .html('<div class="modal-header"><button type="button" class="close" data-dismiss="modal">×</button><h3>'+title+'</h3></div>')
        .append('<div class="modal-body"><p>'+text+'</p></div>')
        .append('<div class="modal-footer"><a href="#" class="btn" data-dismiss="modal">Nein</a><a href="#" id="createAcceptedBtn" class="btn btn-primary" data-accept="modal">Ja</a></div>');
    return $myModalDialog;
}

/*
 * Function that checks if an new achievement has been unlocked.
 * If an achievement has been unlocked an modal view will be displayed
 */
function checkForAttendanceAchievement(course_data) {

    // send (ajax)request to the achievement controller
   $.ajax({
        url: CI.base_url + 'achievement/ajax_check_for_new_attendance_achievement/' + course_data.running_course_id,
        type: 'GET',
        success: function(success_data){ // function that handels the ajax response
            if(success_data != 'no_achievement_unlocked'){ // an achievement has been unlocked by the user. show him the modal
                // render out the success data to the modal markup
                $('#modalcontent').html(success_data);

                // show the modal
                $('#achievementModal').modal({
                    keyboard: false
                }).modal('show');
            }
        }
    });
}