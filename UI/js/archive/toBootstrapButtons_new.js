function toBootstrapButtons( buttonClasses ) {
	var $ = jQuery,
			btnClasses = ((buttonClasses == null) ? buttonClasses : 'btn'),
			formFields = $('input:radio, input:checkbox'),

			varNames = (function(col){				
				var varNames = [];				
				col.each(function(){
					var name = $(this).attr('name');
					if(varNames.indexOf(name) == -1) varNames.push(name);
				});
				
				return varNames;
			})(formFields);
			
			rbToBsButton = function(mySet, btnClasses) {
				var buttons = new jQuery(),
						checked = mySet.is(':checked'),
						name = mySet.first().attr('name'),
						hiddenField = $('<input />', {
							'name': name,
							'id': name,
							'value': ((checked) ? mySet.filter(':checked').val(): -1),
							'type': 'hidden'
						}),
						btnGroup = $('<div></div>', {
							'class': 'btn-group',
							'data-toggle': 'buttons-radio',
							'data-name': name
						});

				mySet.each(function(){
					var $this = $(this),
							label = $('label[for="' + $this.attr('id') + '"]'), 
							temp = buttons.add($('<button></button>', {
												'type': 'button',
												// use respective labels text as button text
												'text': label.text(),
												'data-value': $this.val(),
												// figure out if current radio button is checked and set class appropriately following bootstraps rules
												'class': ((this.checked) ? btnClasses + ' active' : btnClasses),
												'id': this.id
											}));
					
					buttons = temp;
					label.hide();
				});

				return {
					buttonGrp: btnGroup.append(buttons),
					hiddenFields: [hiddenField]
				};
			},
			
			cbToBsButton = function(mySet, btnClasses) {
				console.log('cbToBsButton: not yet implemented');
				return null;
			};

	var elements, bsButtons, last, lastParent;

	for(var i = 0; i < varNames.length; i++ ) {
		elements = $('input[name="%%%"]'.replace('%%%', varNames[i])),
		bsButtons = 0;

		if(elements.filter('input:radio').length > 0) {
			bsButtons = rbToBsButton(elements, btnClasses);
		}
		else if(elements.filter('input:checkbox').length > 0) {
			bsButtons = cbToBsButton(elements, btnClasses);
		}

		if(bsButtons != null) {
			last = elements.last(),
			lastParent = last.parent();
			if( lastParent.is('label') ) {
				bsButtons.buttonGrp.insertAfter( lastParent );
			}
			else {
				bsButtons.buttonGrp.insertAfter( last );	
			}

			for( var j = 0; j < bsButtons.hiddenFields.length; j++ ) {
				bsButtons.hiddenFields[j].insertAfter(bsButtons.buttonGrp);
			}
		}

		elements.remove();
	}
}