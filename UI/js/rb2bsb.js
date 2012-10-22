/* 
  transforms radiobuttons bearing the passed variable name into fully 
  functional twitter bootstrap buttons. affected radiobuttons and their 
  respective labels are removed from the dom.
  @param varName variable name of the radiobuttons which should be transformed
  @param buttonClass class generated buttons will get on creation
  @returns undefined when varname is not associated with radiobuttons
  @returns jquery object the created button group when varname is associated 
  with radio buttons
*/  
function radio2BSButtons (radios, buttonClasses) {
  // when radios' length is lower than or equal zero, there are no radiobuttons in passed set, so return undefined
  if(radios.length <= 0) return undefined;
  // creating a new button group conforming to bootstraps rules
  var btnGroup = $('<div></div>', {
        'class': 'btn-group',
        'data-toggle': 'buttons-radio'
      }),
      // creating a new hidden field to store radiobutton value
      hiddenField = $('<input />', {
        'type': 'hidden',
        'name': radios.attr('name'),
        // figure out if one radiobutton is checked already and set value appropriately when true, elsewise to -1
        'value': ((radios.filter(':checked').length > 0) ? radios.filter(':checked').first().val() : -1)
      });

  // add created button group to the dom
  var last = radios.last();

  if(last.parent().is('label')) {
    btnGroup.insertAfter(last.parent());
  }
  else {
    btnGroup.insertAfter('label[for="' + last.attr('id') + '"]');
  }

  // insert new hidden field ot the dom right behind the button group
  hiddenField.insertAfter(btnGroup);
  
  // create a bootstrap button for each radio button, add it to the button group, finally remove radio button and label from the dom
  radios.each(function(){ 
    var $this = $(this),
        label = $('label[for="' + $this.attr('id') + '"]')
        button = $('<button></button>', {
          'type': 'button',
          // use respective labels text as button text
          'text': label.text(),
          'data-value': $this.val(),
          // figure out if current radio button is checked and set class appropriately following bootstraps rules
          'class': ((this.checked) ? buttonClasses + ' active' : buttonClasses)
        });
    btnGroup.append(button);
    label.remove();
  });

  // create event handler for click events on buttons inside respective group; set value of hidden field
  return btnGroup.on('click', 'button', function(event) {
    hiddenField.val($(this).data('value'));
  });
}