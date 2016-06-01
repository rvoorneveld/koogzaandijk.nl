$(function () {

	// Notification Close Button
	$('.close-notification').click(
		function () {
			$(this).parent().fadeTo(350, 0, function () {$(this).slideUp(600);});
			return false;
		}
	);

	// jQuery Tipsy
	$('[rel=tooltip], #main-nav span, .loader').tipsy({gravity:'e', fade: true}); // Tooltip Gravity Orientation: n | w | e | s

	// jQuery Facebox Modal
	$('.open-modal').nyroModal();

	// jQuery jWYSIWYG Editor
	//$('.wysiwyg').wysiwyg({ iFrameClass:'wysiwyg-iframe' });
	
	// jQuery dataTables
	var columnID		= 0;
	var columnOrder		= 'asc';
	$('.datatable').each( function() {
		var sortColumn 	= $('.datatable').find('th#isSort');
		if(sortColumn.length > 0) {
			if(sortColumn.attr('class') != '') {
				columnOrder	= sortColumn.attr('class');
			}
			columnID		= sortColumn.index();
		}
	});
	
	var datatableParams		= {
		'iDisplayLength'	: 25,
		'sPaginationType'	: "full_numbers",
		'aaSorting'			: [[ columnID, columnOrder ]]	
	}
	$('.datatable').dataTable(datatableParams);

	// jQuery Custome File Input
	$('.fileupload').customFileInput();

	// jQuery DateInput
	$('.datepicker').datepick({
		pickerClass: 'jq-datepicker',
		dateFormat: 'dd-mm-yyyy'
	});
	
	// jQuery Data Visualize
	$('table.data').each(function() {
		var chartWidth = $(this).parent().width()*0.90; // Set chart width to 90% of its parent
		var chartType = ''; // Set chart type
			
		if ($(this).attr('data-chart')) { // If exists chart-chart attribute
			chartType = $(this).attr('data-chart'); // Get chart type from data-chart attribute
		} else {
			chartType = 'area'; // If data-chart attribute is not set, use 'area' type as default. Options: 'bar', 'area', 'pie', 'line'
		}
		
		if(chartType == 'line' || chartType == 'pie') {
			$(this).hide().visualize({
				type: chartType,
				width: chartWidth,
				height: '240px',
				lineDots: 'double',
				interaction: true,
				multiHover: 5,
				tooltip: true,
				tooltiphtml: function(data) {
					var html ='';
					for(var i=0; i<data.point.length; i++){
						html += '<p class="chart_tooltip"><strong>'+data.point[i].value+'</strong> '+data.point[i].yLabels[0]+'</p>';
					}	
					return html;
				}
			});
		} else {
			$(this).hide().visualize({
				type: chartType,
				width: chartWidth,
				height: '240px'
			});
		}
	});

	// Check all checkboxes
	$('.check-all').click(
		function(){
			$(this).parents('form').find('input:checkbox').attr('checked', $(this).is(':checked'));
		}
	)

	// IE7 doesn't support :disabled
	$('.ie7').find(':disabled').addClass('disabled');

	// Menu Dropdown
	$('#main-nav li ul').hide(); //Hide all sub menus
	$('#main-nav li.current a').parent().find('ul').slideToggle('slow'); // Slide down the current sub menu
	$('#main-nav li a').click(
		function () {
			$(this).parent().siblings().find('ul').slideUp('normal'); // Slide up all menus except the one clicked
			$(this).parent().find('ul').slideToggle('normal'); // Slide down the clicked sub menu
			return false;
		}
	);
	$('#main-nav li a.no-submenu, #main-nav li li a').click(
		function () {
			window.location.href=(this.href); // Open link instead of a sub menu
			return false;
		}
	);

	// Widget Close Button
	$('.close-widget').click(
		function () {
			$(this).parent().fadeTo(350, 0, function () {$(this).slideUp(600);}); // Hide widgets
			return false;
		}
	);

	// Table actions
	$('.table-switch').hide();
	$('.toggle-table-switch').click(
		function () {
			$(this).parent().parent().siblings().find('.toggle-table-switch').removeClass('active').next().slideUp(); // Hide all menus expect the one clicked
			$(this).toggleClass('active').next().slideToggle(); // Toggle clicked menu
			$(document).click(function() { // Hide menu when clicked outside of it
				$('.table-switch').slideUp();
				$('.toggle-table-switch').removeClass('active')
			});
			return false;
		}
	);

	// Image actions
	$('.image-frame').hover(
		function() { $(this).find('.image-actions').css('display', 'none').fadeIn('fast').css('display', 'block'); }, // Show actions menu
		function() { $(this).find('.image-actions').fadeOut(100); } // Hide actions menu
	);

		// Tickets
	$('.tickets .ticket-details').hide(); // Hide all ticket details
	$('.tickets .ticket-open-details').click( // On click hide all ticket details content and open clicked one
		function() {
			//$('.tickets .ticket-details').slideUp()
			$(this).parent().parent().parent().parent().siblings().find('.ticket-details').slideUp(); // Hide all ticket details expect the one clicked
			$(this).parent().parent().parent().parent().find('.ticket-details').slideToggle();
			return false;
		}
	);

	// Wizard
	$('.wizard-content').hide(); // Hide all steps
	$('.wizard-content:first').show(); // Show default step
	$('.wizard-steps li:first-child').find('a').addClass('current');
	$('.wizard-steps a').click(
		function() { 
			var step = $(this).attr('href'); // Set variable 'step' to the value of href of clicked wizard step
			$('.wizard-steps a').removeClass('current');
			$(this).addClass('current');
			$(this).parent().prevAll().find('a').addClass('done'); // Mark all prev steps as done
			$(this).parent().nextAll().find('a').removeClass('done'); // Mark all next steps as undone
			$(step).siblings('.wizard-content').hide(); // Hide all content divs
			$(step).fadeIn(); // Show the content div with the id equal to the id of clicked step
			return false;
		}
	);
	$('.wizard-next').click(
		function() { 
			var step = $(this).attr('href'); // Set variable 'step' to the value of href of clicked wizard step
			$('.wizard-steps a').removeClass('current');
			$('.wizard-steps a[href="'+step+'"]').addClass('current');
			$('.wizard-steps a[href="'+step+'"]').parent().prevAll().find('a').addClass('done'); // Mark all prev steps as done
			$('.wizard-steps a[href="'+step+'"]').parent().nextAll().find('a').removeClass('done'); // Mark all next steps as undone
			$(step).siblings('.wizard-content').hide(); // Hide all content divs
			$(step).fadeIn(); // Show the content div with the id equal to the id of clicked step
			return false;
		}
	);

	// Content box tabs and sidetabs
	$('.tab, .sidetab').hide(); // Hide the content divs
	$('.default-tab, .default-sidetab').show(); // Show the div with class 'default-tab'
	$('.tab-switch a.default-tab, .sidetab-switch a.default-sidetab').addClass('current'); // Set the class of the default tab link to 'current'

	if (window.location.hash && window.location.hash.match(/^#tab\d+$/)) {
		var tabID = window.location.hash;
		
		$('.tab-switch a[href='+tabID+']').addClass('current').parent().siblings().find('a').removeClass('current');
		$('div'+tabID).parent().find('.tab').hide();
		$('div'+tabID).show();
	} else if (window.location.hash && window.location.hash.match(/^#sidetab\d+$/)) {
		var sidetabID = window.location.hash;
		
		$('.sidetab-switch a[href='+sidetabID+']').addClass('current');
		$('div'+sidetabID).parent().find('.sidetab').hide();
		$('div'+sidetabID).show();
	}
	
	$('.tab-switch a').click(
		function() { 
			var tab = $(this).attr('href'); // Set variable 'tab' to the value of href of clicked tab
			$(this).parent().siblings().find('a').removeClass('current'); // Remove 'current' class from all tabs
			$(this).addClass('current'); // Add class 'current' to clicked tab
			$(tab).siblings('.tab').hide(); // Hide all content divs
			$(tab).show(); // Show the content div with the id equal to the id of clicked tab
			$(tab).find('.visualize').trigger('visualizeRefresh'); // Refresh jQuery Visualize
			$('.fullcalendar').fullCalendar('render'); // Refresh jQuery FullCalendar
			return false;
		}
	);

	$('.sidetab-switch a').click(
		function() { 
			var sidetab = $(this).attr('href'); // Set variable 'sidetab' to the value of href of clicked sidetab
			$(this).parent().siblings().find('a').removeClass('current'); // Remove 'current' class from all sidetabs
			$(this).addClass('current'); // Add class 'current' to clicked sidetab
			$(sidetab).siblings('.sidetab').hide(); // Hide all content divs
			$(sidetab).show(); // Show the content div with the id equal to the id of clicked tab
			$(sidetab).find('.visualize').trigger('visualizeRefresh'); // Refresh jQuery Visualize
			$('.fullcalendar').fullCalendar('render'); // Refresh jQuery FullCalendar
			
			return false;
		}
	);
	
	// Content box accordions
	$('.accordion li div').hide();
	$('.accordion li:first-child div').show();
	$('.accordion .accordion-switch').click(
		function() {
			$(this).parent().siblings().find('div').slideUp();
			$(this).next().slideToggle();
			return false;
		}
	);
	
	//Minimize Content Article
	$('article header h2').css({ 'cursor':'s-resize' }); // Minizmie is not available without javascript, so we don't change cursor style with CSS
	$('article header h2').click( // Toggle the Box Content
		function () {
			$(this).parent().find('nav').toggle();
			$(this).parent().parent().find('section, footer').toggle();
		}
	);
	
	// Progress bar animation
	$('.progress-bar').each(function() {
		var progress = $(this).children().width();
		$(this).children().css({ 'width':0 }).animate({width:progress},3000);
	});
	
	//jQuery Full Calendar
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	
	$('.fullcalendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,basicWeek,basicDay'
		},
		editable: true,
		events: [
			{
				title: 'All Day Event',
				start: new Date(y, m, 1)
			},
			{
				title: 'Long Event',
				start: new Date(y, m, d-5),
				end: new Date(y, m, d-2)
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: new Date(y, m, d-3, 16, 0),
				allDay: false
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: new Date(y, m, d+4, 16, 0),
				allDay: false
			},
			{
				title: 'Meeting',
				start: new Date(y, m, d, 10, 30),
				allDay: false
			},
			{
				title: 'Lunch',
				start: new Date(y, m, d, 12, 0),
				end: new Date(y, m, d, 14, 0),
				allDay: false
			},
			{
				title: 'Birthday Party',
				start: new Date(y, m, d+1, 19, 0),
				end: new Date(y, m, d+1, 22, 30),
				allDay: false
			},
			{
				title: 'Click for Walking Pixels',
				start: new Date(y, m, 28),
				end: new Date(y, m, 29),
				url: 'http://www.walkingpixels.com/'
			}
		]
	});
	
});

// Create a new Folder at Images or Files
function createNewFolder(folderaction) {
	// Get the Folder name
	var getFolderName = jQuery('#createFolder').find('input#foldername').val();

	if (getFolderName != '') {
		// Add folder name to the url
		folderaction += 'foldername/' + getFolderName + '/';

		// Create the Folder, if exists error will be returned
		jQuery.get(folderaction,function(returnData) {
			// If all OK reload the Page, else alert the error
			if (returnData == 1) {
				window.location.reload();
				return true;
			} else {
				alert(returnData);
				return false;
			}
		});
	}
}

// Get the Selected Folder Content
function getFolderContent(obj,controller,type,parent) {
	var foldername = jQuery(obj).html();

	if (typeof(type) == 'undefined') {
		type = 'images';
	}

	if (typeof(controller) == 'undefined') {
		controller = 'library';
	}
	// Close all other folder
	jQuery('div.folders li').each(function() {
		var folderClass = jQuery(this).find('span').attr('class');

		if ((typeof(folderClass) != 'undefined') && (folderClass.indexOf('icon-folder') != -1)) {
			jQuery(this).find('span').attr('class','icon-folder-close');
		}
	});

	// Set the Selected folder with a new class
	jQuery(obj).parent().children('span').attr('class','icon-folder-open');

	// Remove the up icon
	jQuery('div.folders li.default_dir').find('span').removeAttr('class');

	// Get the Files and set them to the files div
	var getfolder = ((foldername == '..') ? '' : foldername);

	if (typeof(parent) != 'undefined') {
		// Get the Parent folder of the sub
		getfolder = parent + '|' + getfolder;
	}

	// Set the Up icon
	var fileurl = '/admin/' + controller + '/' + type + '/';
	var currentUrl = '';
	if (getfolder != '') {
		fileurl += 'folder/' + getfolder + '/';
		currentUrl += getfolder.replace(/\|/g,'/');
		jQuery('div.folders li.default_dir').find('span').attr('class','up_folder');
	}

	// Get the Files
	jQuery('body').addClass("loading");
	jQuery.get(fileurl,function(returnData) {
		jQuery('div.files').html(returnData);
	}).complete(function() {
		jQuery('body').removeClass("loading");
	})
	;

	// Add the Foldername to the current_folder url
	jQuery('div.current_folder span.url').html(currentUrl);
	jQuery('div#createFolder').find('.modalurl').html(currentUrl);
}

// Show the SubFolder(s)
function showSubFolder(obj) {
	var iconClass = ((jQuery(obj).attr('class') == 'icon-minus') ? 'icon-plus' : 'icon-minus');
	jQuery(obj).attr('class',iconClass);

	jQuery(obj).parent().children('ul').each(function() {
		var currentClass = jQuery(this).attr('class');
		if (currentClass.indexOf('hidden') != -1) {
			jQuery(this).removeClass('hidden');
		} else {
			jQuery(this).addClass('hidden');
		}
	});
}

// Set the Active Folder
function setActiveFolder(obj,current_location) {
	// Check if we need to show (sub) sub folders
	if (current_location != '') {

		// Set the Folder to the Selected Folder text
		jQuery('div.current_folder span.url').html(current_location);

		// Create the Current Folder array
		var arrCurrentFolder = current_location.split('/');

		// Set the Defaults
		var folder = '';
		var subfolder = '';
		var subsubfolder = '';

		// Check if we have sub and/or subsub folders
		if (arrCurrentFolder.length == 1) {
			folder = arrCurrentFolder[0];
		} else if (arrCurrentFolder.length == 2) {
			folder = arrCurrentFolder[0];
			subfolder = arrCurrentFolder[1];
		} else if (arrCurrentFolder.length == 3) {
			folder = arrCurrentFolder[0];
			subfolder = arrCurrentFolder[1];
			subsubfolder = arrCurrentFolder[2];
		}

		// Loop over the Folder and (sub)sub folders
		jQuery(obj).children('ul').children('li').each(function() {
			var liclass = jQuery(this).attr('class');

			// Check if the Class is not the Default Directory
			if (liclass != 'default_dir') {

				// Check if Folder has Sub or SubSub Folders
				if (jQuery(this).children('ul').length > 0) {

					// Set the Folder Object
					var folderobj = jQuery(this).children('ul');

					// Get the Foldername of the parent
					var foldername = folderobj.parent().children('a').html();

					// Only set the Correct Folder to the Open state
					if ((foldername == folder) && (folder != '')) {

						// Change the Icon of the folder parent to Minus
						var iconClass = ((folderobj.parent().children('p').attr('class') == 'icon-minus') ? 'icon-plus' : 'icon-minus');
						folderobj.parent().children('p').attr('class',iconClass);

						// Set the Folder icon to Open
						var iconFolderClass = ((folderobj.parent().children('span').attr('class') == 'icon-folder-close') ? 'icon-folder-open' : 'icon-folder');
						folderobj.parent().find('span').attr('class',iconFolderClass);

						// Show the SubFolders
						folderobj.parent().find('ul').removeClass('hidden');

						// Show the SubFolder if Selected and available
						if ((folderobj.children('li').children('ul').length > 0)) {

							// Show the SubFolders
							folderobj.removeClass('hidden');

							// Set the SubFolder Object
							var subfolderobj = folderobj.children('li').children('ul');

							if ((subfolderobj.attr('class').indexOf('hidden') != -1) && (subfolder != '')) {

								// Change the Icon of the folder parent to Minus
								var iconClass = ((subfolderobj.parent().children('p').attr('class') == 'icon-minus') ? 'icon-plus' : 'icon-minus');
								subfolderobj.parent().children('p').attr('class',iconClass);

								// Set the Folder icon to Open
								var iconFolderClass = ((subfolderobj.parent().children('span').attr('class') == 'icon-folder-close') ? 'icon-folder-open' : 'icon-folder-close');
								subfolderobj.parent().children('span').attr('class',iconFolderClass);

								// Show the SubSubFolders
								subfolderobj.removeClass('hidden');

								if ((subfolderobj.children('li').length > 0)) {
									var subsubfolders = subfolderobj.children('li');

									for (var i = 0; i < subsubfolders.length; i++) {
										var subsubfoldername = jQuery(subsubfolders[i]).children('a').html();
										if (subsubfolder != '' && subsubfoldername == subsubfolder) {
											// Set the Folder icon to Open
											var iconFolderClass = ((jQuery(subsubfolders[i]).children('span').attr('class') == 'icon-folder-close') ? 'icon-folder-open' : 'icon-folder-close');
											jQuery(subsubfolders[i]).children('span').attr('class',iconFolderClass);
										}
									}
								}
							}
						}
					}
				} else {
					// No SubFolder found

					// Get the FolderName of Folders with NO Subs
					var foldername = jQuery(this).children('a').html();

					// Set the Folder icon to Open, only if foldernames are the same
					if ((folder == foldername) && (folder != '')) {
						var iconFolderClass = ((jQuery(this).children('span').attr('class') == 'icon-folder-close') ? 'icon-folder-open' : 'icon-folder-close');
						jQuery(this).children('span').attr('class',iconFolderClass);
					}
				}
			}
		});
	}
}

// Set the Remove Image
function confirmRemoveItem(obj,msg,type) {
	if (typeof(type) == 'undefined') {
		type = 'image';
	}

	var deleteUrl = '/admin/tools/removefromserver/';
	if (type == 'image') {
		var documenturl = jQuery(obj).parent().parent().find('a.thumbnail').attr('rel');
		var data = {image: encodeURI(documenturl)}
	} else {
		var documenturl = jQuery(obj).parent().parent().find('a.file').attr('rel');
		var data = {file: encodeURI(documenturl)}
	}

	if (confirm(msg)) {
		if (typeof(documenturl) != 'undefined' && documenturl != '') {
			var deleteUrl = '/admin/tools/removefromserver/';
			jQuery.post(deleteUrl,data).success(function() {
				jQuery(obj).parent().parent().remove();
			})
		}
		return true;
	} else {
		return false;
	}
}

// Remove the Selected folder
function removeFolder(obj, foldertype, msg, folder) {
	if(confirm(msg)) {
		if(typeof(folder) != 'undefined' && folder != '') {
			var deleteUrl   = '/admin/tools/removefolder/';
			jQuery.post(deleteUrl, {
				folder: folder,
				type: foldertype
			}).success( function() 	{
				jQuery(obj).parent().remove();
			})
		}
		return true;
	} else {
		return false;
	}
}