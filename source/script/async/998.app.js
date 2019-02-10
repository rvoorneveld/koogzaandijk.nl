jQuery(document).ready(function () {

    // Form Validation by Ketchup
    jQuery('.ketchup').ketchup();

    //INIT TWEETS
    jQuery(function () {

        if (jQuery(".tweet").length > 0) {
            jQuery(".tweet").each(function () {
                jQuery(this).tweet({
                    join_text: "auto",
                    username: jQuery(this).attr('rel'),
                    /*avatar_size: 60,*/
                    count: 6,
                    auto_join_text_default: "plaatste <a href=\"http://www.twitter.com/" + jQuery(this).attr('rel') + "\" target=\"_blank\">@" + jQuery(this).attr('rel') + "</a>,",
                    auto_join_text_ed: jQuery(this).attr('rel'),
                    auto_join_text_ing: jQuery(this).attr('rel') + " was",
                    auto_join_text_reply: jQuery(this).attr('rel') + " antwoordde",
                    auto_join_text_url: "plaatste <a href=\"http://www.twitter.com/" + jQuery(this).attr('rel') + "\" target=\"_blank\">@" + jQuery(this).attr('rel') + "</a>,",
                    loading_text: "Tweets laden..."
                });
            });
        }
    });

    jQuery('#main-slider').flexslider({
        minItems: 1,
        maxItems: 1,
        move: 1,
        animation: 'fade',
        animationSpeed: 1500,
        startAt: 0,
        directionNav: false,
        pauseOnHover: true
    });

    jQuery('#widget-slider').flexslider({
        minItems: 1,
        maxItems: 2,
        itemWidth: 105,
        itemMargin: 0,
        animation: 'slide',
        animationSpeed: 1500,
        startAt: 0,
        directionNav: false,
        controlNav: true,
        pauseOnHover: true,
    });

    jQuery('li.main ul').hover(function () {
        jQuery(this).parent('li').children('a').addClass('hover');
    });

    jQuery('li.main ul').mouseleave(function () {

        jQuery(this).parent('li').children('a').removeClass('hover');

    });

    // Search Suggestions
    jQuery('#suggestions').css('display', 'none');

    jQuery("#search").blur(function () {
        jQuery('#suggestions').fadeOut();
        jQuery(this).val('');
    });

    var matchMedia = window.matchMedia || window.msMatchMedia;

    if (matchMedia) {

        // Calculate width of navigation
        var intTotalNavigationItems = jQuery("nav ul.main").children().length;

        // Resize Navigation Slider
        jQuery('#widget-slider').resize();

        currentResponsiveView = jQuery().responsive({
            items: (intTotalNavigationItems / 2)
        });

        var currentResponsiveView = '';

        jQuery(window).resize(function () {

            currentResponsiveView = jQuery().responsive({
                current: currentResponsiveView,
                items: (intTotalNavigationItems / 2)
            });

        });

    }

    // Get Match Program Info
    jQuery('ol.clubplugin.program li.widget').click(function () {

        jQuery('ol.clubplugin.program li.widget').removeClass('active');
        jQuery('ul.row li.schedule div#match_info_program').html('');
        jQuery('ul.row li.schedule #match_info_program').css('display', 'none');

        jQuery(this).addClass('active');
        jQuery('#match_info_program').html('');

        var position = jQuery(this).position();
        var elementHeight = jQuery(this).outerHeight();
        var elementWidth = jQuery(this).outerWidth();
        var elementMatchID = jQuery(this).attr('rel');

        jQuery('#match_info_program').css('display', 'block');
        jQuery('#match_info_program').css('top', Math.floor(position.top) + elementHeight);
        jQuery('#match_info_program').css('left', Math.round(position.left));
        jQuery('#match_info_program').css('width', elementWidth);

        jQuery.get("/tools/teaminfo", {match_id: elementMatchID}).done(function (data) {
            jQuery('#match_info_program').html(data);
        });

    });

    // Match Program Infobox
    jQuery("div#match_info_program").live({
        click: function () {
            jQuery('ol.clubplugin.program li.widget').removeClass('active');
            jQuery('ul.row li.schedule div#match_info_program').html('');
            jQuery('ul.row li.schedule #match_info_program').css('display', 'none');
        }
    });

    // Get Match Results Info
    jQuery('ol.clubplugin.results li.widget').click(function () {

        if (true == jQuery(this).parent().hasClass('js-volunteers')) {
            return false;
        }

        jQuery('ol.clubplugin.results li.widget').removeClass('active');
        jQuery('ul.row li.results div#match_info_results').html('');
        jQuery('ul.row li.results #match_info_results').css('display', 'none');

        jQuery(this).addClass('active');
        jQuery('#match_info_results').html('');

        var position = jQuery(this).position();
        var elementHeight = jQuery(this).outerHeight();
        var elementWidth = jQuery(this).outerWidth();
        var elementMatchID = jQuery(this).attr('rel');

        jQuery('#match_info_results').css('display', 'block');
        jQuery('#match_info_results').css('top', Math.floor(position.top) + elementHeight);
        jQuery('#match_info_results').css('left', Math.round(position.left));
        jQuery('#match_info_results').css('width', elementWidth);

        jQuery.get("/tools/teaminfo", {match_id: elementMatchID}).done(function (data) {
            jQuery('#match_info_results').html(data);
        });

    });

    // Match Results Infobox
    jQuery("div#match_info_results").live({
        click: function () {
            jQuery('ol.clubplugin.results li.widget').removeClass('active');
            jQuery('ul.row li.results div#match_info_results').html('');
            jQuery('ul.row li.results #match_info_results').css('display', 'none');
        }
    });

    // Login Profiles
    jQuery('.openProfiles').click(function () {
        jQuery('.l-wrapper--profiles').show();
        jQuery(this).hide();
    });

    jQuery('.login_auto').click(function(){
        jQuery.ajax({
            type    : 'POST',
            url     : '/tools/loginauto/',
            data    : {
                data        : jQuery(this).attr('rel')
            }
        }).done(function(message){

            if(jQuery.isNumeric(message)) {
                window.location.href = "/profiel/";
            }

        });
    });

    jQuery('.login').click(function(){

        jQuery('.profilePassword').hide();
        jQuery(this).parent().parent().parent().find('.profilePassword').show();
        jQuery('.newProfileForm').hide();

        jQuery('.loginWithPassword').click(function(e){

            e.preventDefault();

            var profilePassword = jQuery(this).parent().find('.loginPassword').val();
            var profileRel      = jQuery(this).attr('rel');

            jQuery.ajax({
                type    : 'POST',
                url     : '/tools/loginpassword/',
                data    : {
                    password    : profilePassword,
                    data        : profileRel
                }
            }).done(function(message){

                if(jQuery.isNumeric(message)) {
                    window.location.href = "/profiel/";
                } else {
                    jQuery('.user-feedback').html(message);
                    jQuery('.user-feedback').addClass('user-feedback--error');
                    jQuery('.user-feedback').show();

                    setTimeout(function() {
                        jQuery('.user-feedback').removeClass('user-feedback--error');
                        jQuery('.user-feedback').hide();
                    }, 5000);

                }

            });
        });
    });

    jQuery('.newProfile').click(function(){
        jQuery('.profilePassword').hide();
        jQuery('.newProfileForm').show();
    });

    jQuery('.submit').click(function(e){

        // Prevent form from submitting
        e.preventDefault();

        jQuery.ajax({
            type    : 'POST',
            url     : '/tools/login/',
            data    : {
                email       : jQuery('.email').val(),
                password    : jQuery('.password').val()
            }
        }).done(function(message){

            if(jQuery.isNumeric(message)) {
                window.location.href = "/profiel/";
            } else {
                jQuery('.user-feedback').html(message);
                jQuery('.user-feedback').addClass('user-feedback--error');
                jQuery('.user-feedback').show();

                setTimeout(function() {
                    jQuery('.user-feedback').removeClass('user-feedback--error');
                    jQuery('.user-feedback').hide();
                }, 5000);

            }

        });

    });

    jQuery('.close_profiles').click(function(){
        jQuery('.l-wrapper--profiles').hide();
        jQuery('.profilePassword').hide();
        jQuery('.newProfileForm').hide();
        jQuery('.user-feedback').hide();
        jQuery('.user-feedback').removeClass('user-feedback--error');
        jQuery('.openProfiles').show();
    });

    jQuery('#newsDropdown ul li').click(function(){

        jQuery('span.dropdown_button').html(jQuery(this).html());
        jQuery('span.dropdown_button').append('<span class="downarrow"><span aria-hidden="true">&#xe000;</span></span>');
        jQuery('span.dropdown_button').removeClass('dropdown-open');
        jQuery('#newsDropdown').hide();

        var categoryID = jQuery(this).attr('rel');

        jQuery.get('/tools/newsfilter/id/'+categoryID+'/',

            function(returnData) {

                jQuery('div.news_wrapper li.news ol').html('');

                var arrModel = eval( '(' + returnData + ')' );

                jQuery.each(arrModel, function(intKeyModel) {

                    var prefixUrl       = 'undefined' == typeof(arrModel[intKeyModel].news_id) ? arrModel[intKeyModel].blogSlug : 'nieuws'
                    var itemName		= arrModel[intKeyModel].name;
                    var	itemCategory	= arrModel[intKeyModel].category;
                    var	itemColor		= arrModel[intKeyModel].color;
                    var itemUrl			= arrModel[intKeyModel].nameSlug;

                    itemCreated		= jQuery.format.date(arrModel[intKeyModel].date + ' 00:00:00', "dd-MM");
                    itemTimeTag		= jQuery.format.date(arrModel[intKeyModel].date + ' 00:00:00', "dd-MM-yyyy");

                    var strList		= '' +
                        '<li>' +
                        '   <div class="date_container">' +
                        '       <span class="bullet" title="'+itemCategory+'" style="background: '+itemColor+';"></span>' +
                        '       <span class="date newstag"><time datetime="'+itemTimeTag+'">'+itemCreated+'</time></span>' +
                        '   </div>' +
                        '   <a class="news_title" title="'+itemName+'" href="/'+prefixUrl+'/'+itemUrl+'">'+itemName+'</a>' +
                        '</li>';

                    jQuery('div.news_wrapper li.news ol').prepend(strList);

                });

            }

        );

    });

    var	defaultSponsorUrl	= "https://www.sponsorportaal.nl/marquee/?id=510041347d247";
    var parentWidth 		= jQuery('#sponsors').parent().width();
    var iframeWidth 		= parentWidth - 47;
    var iframeSource 		= defaultSponsorUrl + "&amp;width=" + iframeWidth;

    jQuery('#sponsors').css('width', parentWidth + 'px');
    jQuery('#sponsors').append('<iframe id="sponsors_horizontal" src="'+iframeSource+'" frameborder="0" scrolling="no" width="'+parentWidth+'px" height="86px"></iframe>');

    jQuery(window).resize(function() {
        if(this.resizeTO) clearTimeout(this.resizeTO);
        this.resizeTO = setTimeout(function() {
            $(this).trigger('resizeEnd');
        }, 500);
    });

    jQuery(window).bind('resizeEnd', function() {

        var	defaultSponsorUrl	= "https://www.sponsorportaal.nl/marquee/?id=510041347d247";
        var parentWidth 		= jQuery('#sponsors').parent().width();
        var iframeWidth 		= parentWidth - 47;
        var iframeSource 		= defaultSponsorUrl + "&amp;width=" + iframeWidth;

        jQuery('#sponsors').css('width', parentWidth + 'px');
        jQuery('#sponsors').html('<iframe id="sponsors_horizontal" src="'+iframeSource+'" frameborder="0" scrolling="no" width="'+parentWidth+'px" height="86px"></iframe>');

    });

    jQuery(".video").click(function () {
        jQuery.fancybox({
            'padding': 0,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'title': this.title,
            'width': 640,
            'height': 385,
            'href': this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
            'type': 'swf',
            'swf': {
                'wmode': 'transparent',
                'allowfullscreen': 'true'
            }
        });
        return false;
    });

});

function searchSuggestions(searchValue) {
    if (searchValue.length < 2) {
        jQuery('#suggestions').fadeOut(); // Hide the suggestions box
    } else {
        jQuery.post("/tools/search/", {search: "" + searchValue + ""}, function (data) { // Do an AJAX call
            jQuery('#suggestions').fadeIn(); // Show the suggestions box
            jQuery('#suggestions').html(data); // Fill the suggestions box
        });
    }
}