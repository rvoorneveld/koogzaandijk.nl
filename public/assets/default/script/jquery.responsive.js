(function($){
    $.fn.extend({

    	responsive: function(options) {
 
	    	// Hide Program Extra info boxes
			jQuery("#match_info_program").css('display', 'none');
    	
            var defaults = {
            	items		:	0,
            };
            
            current			= '';
            
            var options 	= $.extend(defaults, options);
            
            var matchMedia 	= window.matchMedia || window.msMatchMedia; 
            
            var matchMediaMinWidthTabletPortrait	= matchMedia("(min-width: 1px)");
			var matchMediaMaxWidthTabletPortrait	= matchMedia("(max-width: 800px)");

			var matchMediaMinWidthTabletLandscape	= matchMedia("(min-width: 801px)");
			var matchMediaMaxWidthTabletLandscape	= matchMedia("(max-width: 1100px)");
			
			var matchMediaMinWidthDesktop			= matchMedia("(min-width: 1101px)");

			if(

					matchMediaMinWidthTabletPortrait.matches == true 
				&& 	matchMediaMaxWidthTabletPortrait.matches == true
				&& options.current != 'tablet_portrait'
			) {

				// Set Current Media view
				options.current		= 'tablet_portrait';

				// Add Body Class
				jQuery("body").attr('id', '');
				jQuery("body").attr('id', options.current);

				// Change Results Text
				if(jQuery('.clubplugin.standings').length > 0) {
					jQuery('.clubplugin.standings .gespeeld').html('GES');
					jQuery('.clubplugin.standings .punten').html('PUN');
					jQuery('.clubplugin.standings .winst').html('WIN');
					jQuery('.clubplugin.standings .verlies').html('VER');
					jQuery('.clubplugin.standings .gelijk').html('GEL');
					jQuery('.clubplugin.standings .voor').html('VOO');
					jQuery('.clubplugin.standings .tegen').html('TEG');
					jQuery('.clubplugin.standings .saldo').html('SAL');
					jQuery('.clubplugin.standings .mindering').html('MIN');
    			}
				
				// MODIFY MENU -> SIDERBAR
				jQuery('.content-container .menu-container-oncanvas').css('display', 'none');
				jQuery('.menu-container').css('display', 'block');
				
			} else if(
					matchMediaMinWidthTabletLandscape.matches == true 
				&& 	matchMediaMaxWidthTabletLandscape.matches == true
				&& options.current != 'tablet_landscape'
			) {
				
				// Set Current Media view
				options.current	= 'tablet_landscape';

				// Add Body Class
				jQuery("body").attr('id', '');
				jQuery("body").attr('id', options.current);
				
				// Change Results Text
				if(jQuery('.clubplugin.standings').length > 0) {
					jQuery('.clubplugin.standings .gespeeld').html('GES');
					jQuery('.clubplugin.standings .punten').html('PUN');
					jQuery('.clubplugin.standings .winst').html('WIN');
					jQuery('.clubplugin.standings .verlies').html('VER');
					jQuery('.clubplugin.standings .gelijk').html('GEL');
					jQuery('.clubplugin.standings .voor').html('VOO');
					jQuery('.clubplugin.standings .tegen').html('TEG');
					jQuery('.clubplugin.standings .saldo').html('SAL');
					jQuery('.clubplugin.standings .mindering').html('MIN');
    			}
				
				// MODIFY MENU -> SIDERBAR
				jQuery('.menu-container').css('display', 'none');
				jQuery('.content-container .menu-container-oncanvas').css('display', 'block');
				
				// Set Width of Nav items
				var intPercentage = (100 / options.items);
				jQuery(".content-container .menu-container-oncanvas nav ul.main").children().each(function(){
					jQuery(this).css('width', intPercentage + '%');
				});
				
			} else {
				
				if(
						jQuery(window).width() > 1083
					&& 	options.current != 'desktop'
				) {

					// Set Current Media view
					options.current	= 'desktop';

					// Add Body Class
					jQuery("body").attr('id', '');
					jQuery("body").attr('id', options.current);
					
					// Change Results Text
					if(jQuery('.clubplugin.standings').length > 0) {
						jQuery('.clubplugin.standings .gespeeld').html('GESPEELD');
						jQuery('.clubplugin.standings .punten').html('PUNTEN');
						jQuery('.clubplugin.standings .winst').html('WINST');
						jQuery('.clubplugin.standings .verlies').html('VERLIES');
						jQuery('.clubplugin.standings .gelijk').html('GELIJK');
						jQuery('.clubplugin.standings .voor').html('VOOR');
						jQuery('.clubplugin.standings .tegen').html('TEGEN');
						jQuery('.clubplugin.standings .saldo').html('SALDO');
						jQuery('.clubplugin.standings .mindering').html('MINDERING');
	    			}
					
					// MODIFY MENU -> SIDERBAR
					jQuery('.menu-container').css('display', 'none');
					jQuery('.content-container .menu-container-oncanvas').css('display', 'block');
					
					// Set Width of Nav items
					var intPercentage = (100 / options.items);
					jQuery(".content-container .menu-container-oncanvas nav ul.main").children().each(function(){
						jQuery(this).css('width', intPercentage + '%');
					});
					
					if(jQuery('nav ul.main li.main a.active').length == 0) {
						jQuery('nav ul.main li.main a.main').hover(function(){
							jQuery(this).css('height', '90px');							
						});
					}
					
				}

			}

			return options.current;
        }
    });
})(jQuery);