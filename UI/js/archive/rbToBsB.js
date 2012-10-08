if(jQuery) {

  var $ = jQuery;
  
  function getRbByName( name ) {
    return $('input:radio[name="' + name + '"]');
  }

  function getButtonFromRb( radio, buttonClasses ) {
    var buttonClasses = (buttonClasses != undefined ) ? buttonClasses : 'btn';
    if( radio.is('input:radio') ) {
      return $('<button></button>', {
        'data-name': radio.attr('name'),
        'data-id': radio.attr('id'),
        'data-value': radio.val(),
        'type': 'button',
        'class': buttonClasses
      });
    }
    else {
      return undefined;
    }
  }

  function getButtonGroup( buttons, hiddenField ) {
    var btnGroup = $('<div></div>' {
      'class': 'btn-group',
      'data-toggle': 'buttons-radio',
      'click': function(event){
        $(this).on('click', 'button', function(event){
          hiddenField.val($(this).data('value'));
        });
      }
    });

    return btnGroup.append( buttons );
  }

  function rbToBsB( buttonClasses ){
    if(!buttonClasses) {
      var buttonClasses = 'btn';
    }

    var radios = $('input:radio'),
        names = new Array();

    radios.each(function() {
      var $this = $(this),
          inArray = false;
      if( names.length > 0 ) {
        for( var i = 0; i < names.length; i++ ) {
          if( $this.attr('name') == names[i] ) {
            inArray = true;
          }
        }
      }

      if( !inArray ) {
        names.push( $this.attr('name') );
      }
    });

    while( radios.length > 0 ) {
      var tempRadios = radios.filter('')
    }
  }

}