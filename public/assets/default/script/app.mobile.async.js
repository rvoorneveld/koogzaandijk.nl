/*
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 *
 * Uses the built in easing capabilities added In jQuery 1.1
 * to offer multiple easing options
 *
 * TERMS OF USE - jQuery Easing
 * 
 * Open source under the BSD License. 
 * 
 * Copyright Â© 2008 George McGinley Smith
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other materials 
 * provided with the distribution.
 * 
 * Neither the name of the author nor the names of contributors may be used to endorse 
 * or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
*/

// t: current time, b: begInnIng value, c: change In value, d: duration
jQuery.easing['jswing'] = jQuery.easing['swing'];

jQuery.extend( jQuery.easing,
{
	def: 'easeOutQuad',
	swing: function (x, t, b, c, d) {
		//alert(jQuery.easing.default);
		return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
	},
	easeInQuad: function (x, t, b, c, d) {
		return c*(t/=d)*t + b;
	},
	easeOutQuad: function (x, t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	easeInOutQuad: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t + b;
		return -c/2 * ((--t)*(t-2) - 1) + b;
	},
	easeInCubic: function (x, t, b, c, d) {
		return c*(t/=d)*t*t + b;
	},
	easeOutCubic: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t + 1) + b;
	},
	easeInOutCubic: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t + b;
		return c/2*((t-=2)*t*t + 2) + b;
	},
	easeInQuart: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t + b;
	},
	easeOutQuart: function (x, t, b, c, d) {
		return -c * ((t=t/d-1)*t*t*t - 1) + b;
	},
	easeInOutQuart: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
		return -c/2 * ((t-=2)*t*t*t - 2) + b;
	},
	easeInQuint: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t*t + b;
	},
	easeOutQuint: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t*t*t + 1) + b;
	},
	easeInOutQuint: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
		return c/2*((t-=2)*t*t*t*t + 2) + b;
	},
	easeInSine: function (x, t, b, c, d) {
		return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
	},
	easeOutSine: function (x, t, b, c, d) {
		return c * Math.sin(t/d * (Math.PI/2)) + b;
	},
	easeInOutSine: function (x, t, b, c, d) {
		return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
	},
	easeInExpo: function (x, t, b, c, d) {
		return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
	},
	easeOutExpo: function (x, t, b, c, d) {
		return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
	},
	easeInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	},
	easeInCirc: function (x, t, b, c, d) {
		return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
	},
	easeOutCirc: function (x, t, b, c, d) {
		return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
	},
	easeInOutCirc: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
		return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
	},
	easeInElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	},
	easeOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	},
	easeInOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	},
	easeInBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*(t/=d)*t*((s+1)*t - s) + b;
	},
	easeOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
	},
	easeInOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158; 
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	},
	easeInBounce: function (x, t, b, c, d) {
		return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
	},
	easeOutBounce: function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	},
	easeInOutBounce: function (x, t, b, c, d) {
		if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
		return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
	}
});

/*
 *
 * TERMS OF USE - EASING EQUATIONS
 * 
 * Open source under the BSD License. 
 * 
 * Copyright Â© 2001 Robert Penner
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other materials 
 * provided with the distribution.
 * 
 * Neither the name of the author nor the names of contributors may be used to endorse 
 * or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
 */
// jquery.tweet.js - See http://tweet.seaofclouds.com/ or https://github.com/seaofclouds/tweet for more info
// Copyright (c) 2008-2012 Todd Matthews & Steve Purcell
// Modified by Stan Scates for https://github.com/StanScates/Tweet.js-Mod

(function (factory) {
	if (typeof define === 'function' && define.amd)
	define(['jquery'], factory); // AMD support for RequireJS etc.
	else
	factory(jQuery);
}(function ($) {
	$.fn.tweet = function(o){
		var s = $.extend({
			modpath: "/assets/default/twitter/",      // [string]   relative URL to Tweet.js mod (see https://github.com/StanScates/Tweet.js-Mod)
			username: null,                           // [string or array] required unless using the 'query' option; one or more twitter screen names (use 'list' option for multiple names, where possible)
			list_id: null,                            // [integer]  ID of list to fetch when using list functionality
			list: null,                               // [string]   optional slug of list belonging to username
			favorites: false,                         // [boolean]  display the user's favorites instead of his tweets
			query: null,                              // [string]   optional search query (see also: http://search.twitter.com/operators)
			avatar_size: null,                        // [integer]  height and width of avatar if displayed (48px max)
			count: 3,                                 // [integer]  how many tweets to display?
			fetch: null,                              // [integer]  how many tweets to fetch via the API (set this higher than 'count' if using the 'filter' option)
			page: 1,                                  // [integer]  which page of results to fetch (if count != fetch, you'll get unexpected results)
			retweets: true,                           // [boolean]  whether to fetch (official) retweets (not supported in all display modes)
			intro_text: null,                         // [string]   do you want text BEFORE your your tweets?
			outro_text: null,                         // [string]   do you want text AFTER your tweets?
			join_text:  null,                         // [string]   optional text in between date and tweet, try setting to "auto"
			auto_join_text_default: "ik zei,",        // [string]   auto text for non verb: "i said" bullocks
			auto_join_text_ed: "ik",                  // [string]   auto text for past tense: "i" surfed
			auto_join_text_ing: "ik was",             // [string]   auto tense for present tense: "i was" surfing
			auto_join_text_reply: "ik reageerde op",  // [string]   auto tense for replies: "i replied to" @someone "with"
			auto_join_text_url: "ik keek naar",   	  // [string]   auto tense for urls: "i was looking at" http:...
			loading_text: null,                       // [string]   optional loading text, displayed while tweets load
			refresh_interval: null ,                  // [integer]  optional number of seconds after which to reload tweets
			twitter_url: "twitter.com",               // [string]   custom twitter url, if any (apigee, etc.)
			twitter_api_url: "api.twitter.com",       // [string]   custom twitter api url, if any (apigee, etc.)
			twitter_search_url: "search.twitter.com", // [string]   custom twitter search url, if any (apigee, etc.)
			template: "{avatar}{time}{join}{text}",   // [string or function] template used to construct each tweet <li> - see code for available vars
			comparator: function(tweet1, tweet2) {    // [function] comparator used to sort tweets (see Array.sort)
				return tweet2["tweet_time"] - tweet1["tweet_time"];
			},
			filter: function(tweet) {                 // [function] whether or not to include a particular tweet (be sure to also set 'fetch')
				return true;
			}
		// You can attach callbacks to the following events using jQuery's standard .bind() mechanism:
		//   "loaded" -- triggered when tweets have been fetched and rendered
		}, o);

		// See http://daringfireball.net/2010/07/improved_regex_for_matching_urls
		var url_regexp = /\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?Â«Â»â€œâ€�â€˜â€™]))/gi;

		// Expand values inside simple string templates with {placeholders}
		function t(template, info) {
			if (typeof template === "string") {
				var result = template;
				for(var key in info) {
					var val = info[key];
					result = result.replace(new RegExp('{'+key+'}','g'), val === null ? '' : val);
				}
				return result;
			} else return template(info);
		}
		// Export the t function for use when passing a function as the 'template' option
		$.extend({tweet: {t: t}});

		function replacer (regex, replacement) {
			return function() {
				var returning = [];
				this.each(function() {
					returning.push(this.replace(regex, replacement));
				});
				return $(returning);
			};
		}

		function escapeHTML(s) {
			return s.replace(/</g,"&lt;").replace(/>/g,"^&gt;");
		}

		$.fn.extend({
			linkUser: replacer(/(^|[\W])@(\w+)/gi, "$1<span class=\"at\">@</span><a target=\"_blank\" href=\"http://"+s.twitter_url+"/$2\">$2</a>"),
			// Support various latin1 (\u00**) and arabic (\u06**) alphanumeric chars
			linkHash: replacer(/(?:^| )[\#]+([\w\u00c0-\u00d6\u00d8-\u00f6\u00f8-\u00ff\u0600-\u06ff]+)/gi,
				' <a target="_blank" href="http://'+s.twitter_search_url+'/search?q=&tag=$1&lang=all'+((s.username && s.username.length == 1 && !s.list) ? '&from='+s.username.join("%2BOR%2B") : '')+'" class="tweet_hashtag">#$1</a>'),
			makeHeart: replacer(/(&lt;)+[3]/gi, "<tt class='heart'>&#x2665;</tt>")
		});

		function linkURLs(text, entities) {
			return text.replace(url_regexp, function(match) {
				var url = (/^[a-z]+:/i).test(match) ? match : "http://"+match;
				var text = match;
				for(var i = 0; i < entities.length; ++i) {
					var entity = entities[i];
					if (entity.url == url && entity.expanded_url) {
						url = entity.expanded_url;
						text = entity.display_url;
						break;
					}
				}
				return "<a target=\"_blank\" href=\""+escapeHTML(url)+"\">"+escapeHTML(text)+"</a>";
			});
		}

		function parse_date(date_str) {
			// The non-search twitter APIs return inconsistently-formatted dates, which Date.parse
			// cannot handle in IE. We therefore perform the following transformation:
			// "Wed Apr 29 08:53:31 +0000 2009" => "Wed, Apr 29 2009 08:53:31 +0000"
			return Date.parse(date_str.replace(/^([a-z]{3})( [a-z]{3} \d\d?)(.*)( \d{4})$/i, '$1,$2$4$3'));
		}

		function relative_time(date) {
		      var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
		      var delta = parseInt((relative_to.getTime() - date) / 1000, 10);
		      var r = '';
		      if (delta < 60) {
		        r = delta + ' seconden geleden';
		      } else if(delta < 120) {
		        r = ' een minuut geleden';
		      } else if(delta < (45*60)) {
		        r = (parseInt(delta / 60, 10)).toString() + ' minuten geleden';
		      } else if(delta < (2*60*60)) {
		        r = 'een uur geleden';
		      } else if(delta < (24*60*60)) {
		        r = '' + (parseInt(delta / 3600, 10)).toString() + ' uur geleden';
		      } else if(delta < (48*60*60)) {
		        r = 'een dag geleden';
		      } else {
		        r = (parseInt(delta / 86400, 10)).toString() + ' dagen geleden';
		      }
		      return 'ongeveer ' + r;
		    }
		
		/*function relative_time(date) {
			var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
			var delta = parseInt((relative_to.getTime() - date) / 1000, 10);
			var r = '';
			if (delta < 1) {
				r = 'just now';
			} else if (delta < 60) {
				r = delta + ' seconds ago';
			} else if(delta < 120) {
				r = 'about a minute ago';
			} else if(delta < (45*60)) {
				r = 'about ' + (parseInt(delta / 60, 10)).toString() + ' minutes ago';
			} else if(delta < (2*60*60)) {
				r = 'about an hour ago';
			} else if(delta < (24*60*60)) {
				r = 'about ' + (parseInt(delta / 3600, 10)).toString() + ' hours ago';
			} else if(delta < (48*60*60)) {
				r = 'about a day ago';
			} else {
				r = 'about ' + (parseInt(delta / 86400, 10)).toString() + ' days ago';
			}
			return r;
		}*/

		function build_auto_join_text(text) {
			if (text.match(/^(@([A-Za-z0-9-_]+)) .*/i)) {
				return s.auto_join_text_reply;
			} else if (text.match(url_regexp)) {
				return s.auto_join_text_url;
			} else if (text.match(/^((\w+ed)|just) .*/im)) {
				return s.auto_join_text_ed;
			} else if (text.match(/^(\w*ing) .*/i)) {
				return s.auto_join_text_ing;
			} else {
				return s.auto_join_text_default;
			}
		}

		function build_api_request() {
			var modpath = s.modpath,
				count = (s.fetch === null) ? s.count : s.fetch,
				defaults = {
					include_entities: 1
				};

			if (s.list) {
				return {
					host: s.twitter_api_url,
					url: "/1.1/lists/statuses.json",
					parameters: $.extend({}, defaults, {
						list_id: s.list_id,
						slug: s.list,
						owner_screen_name: s.username,
						page: s.page,
						count: count,
						include_rts: (s.retweets ? 1 : 0)
					})
				};
			} else if (s.favorites) {
				return {
					host: s.twitter_api_url,
					url: "/1.1/favorites/list.json",
					parameters: $.extend({}, defaults, {
						list_id: s.list_id,
						screen_name: s.username,
						page: s.page,
						count: count
					})
				};
			} else if (s.query === null && s.username.length === 1) {
				return {
					host: s.twitter_api_url,
					url: "/1.1/statuses/user_timeline.json",
					parameters: $.extend({}, defaults, {
						screen_name: s.username,
						page: s.page,
						count: count,
						include_rts: (s.retweets ? 1 : 0)
					})
				};
			} else {
				var query = (s.query || 'from:'+s.username.join(' OR from:'));
				return {
					host: s.twitter_search_url,
					url: "/search.json",
					parameters: $.extend({}, defaults, {
						page: s.page,
						q: query,
						rpp: count
					})
				};
			}
		}

		function extract_avatar_url(item, secure) {
			if (secure) {
				return ('user' in item) ?
					item.user.profile_image_url_https :
					extract_avatar_url(item, false).
					replace(/^http:\/\/[a-z0-9]{1,3}\.twimg\.com\//, "https://s3.amazonaws.com/twitter_production/");
			} else {
				return item.profile_image_url || item.user.profile_image_url;
			}
		}

		// Convert twitter API objects into data available for
		// constructing each tweet <li> using a template
		function extract_template_data(item) {
			var o = {};
			o.item = item;
			o.source = item.source;
			// The actual user name is not returned by all Twitter APIs, so please do not file an issue if it is empty.
			o.name = item.from_user_name || item.user.name;
			o.screen_name = item.from_user || item.user.screen_name;
			o.avatar_size = s.avatar_size;
			o.avatar_url = extract_avatar_url(item, (document.location.protocol === 'https:'));
			o.retweet = typeof(item.retweeted_status) != 'undefined';
			o.tweet_time = parse_date(item.created_at);
			o.join_text = s.join_text == "auto" ? build_auto_join_text(item.text) : s.join_text;
			o.tweet_id = item.id_str;
			o.twitter_base = "http://"+s.twitter_url+"/";
			o.user_url = o.twitter_base+o.screen_name;
			o.tweet_url = o.user_url+"/status/"+o.tweet_id;
			o.reply_url = o.twitter_base+"intent/tweet?in_reply_to="+o.tweet_id;
			o.retweet_url = o.twitter_base+"intent/retweet?tweet_id="+o.tweet_id;
			o.favorite_url = o.twitter_base+"intent/favorite?tweet_id="+o.tweet_id;
			o.retweeted_screen_name = o.retweet && item.retweeted_status.user.screen_name;
			o.tweet_relative_time = relative_time(o.tweet_time);
			o.entities = item.entities ? (item.entities.urls || []).concat(item.entities.media || []) : [];
			o.tweet_raw_text = o.retweet ? ('RT @'+o.retweeted_screen_name+' '+item.retweeted_status.text) : item.text; // avoid '...' in long retweets
			o.tweet_text = $([linkURLs(o.tweet_raw_text, o.entities)]).linkUser().linkHash()[0];
			o.tweet_text_fancy = $([o.tweet_text]).makeHeart()[0];

			// Default spans, and pre-formatted blocks for common layouts
			o.user = t('<a target="_blank" class="tweet_user" href="{user_url}">{screen_name}</a>', o);
			o.join = s.join_text ? t(' <span class="tweet_join">{join_text}</span> ', o) : ' ';
			o.avatar = o.avatar_size ?
				t('<a target="_blank" class="tweet_avatar" href="{user_url}"><img src="{avatar_url}" height="{avatar_size}" width="{avatar_size}" alt="{screen_name}\'s avatar" title="{screen_name}\'s avatar" border="0"/></a>', o) : '';
			o.time = t('<span class="tweet_time"><a target="_blank" href="{tweet_url}" title="view tweet on twitter">{tweet_relative_time}</a></span>', o);
			o.text = t('<span class="tweet_text">{tweet_text_fancy}</span>', o);
			o.reply_action = t('<a target="_blank" class="tweet_action tweet_reply" href="{reply_url}">reply</a>', o);
			o.retweet_action = t('<a target="blank" class="tweet_action tweet_retweet" href="{retweet_url}">retweet</a>', o);
			o.favorite_action = t('<a target="_blank" class="tweet_action tweet_favorite" href="{favorite_url}">favorite</a>', o);
			return o;
		}

		return this.each(function(i, widget){
			var list = $('<ul class="tweet_list">');
			var intro = '<p class="tweet_intro">'+s.intro_text+'</p>';
			var outro = '<p class="tweet_outro">'+s.outro_text+'</p>';
			var loading = $('<p class="loading">'+s.loading_text+'</p>');

			if(s.username && typeof(s.username) == "string"){
				s.username = [s.username];
			}

			$(widget).unbind("tweet:load").bind("tweet:load", function(){
				if (s.loading_text) $(widget).empty().append(loading);

				$.ajax({
					dataType: "json",
					type: "post",
					async: false,
					url: s.modpath || "/twitter/",
					data: { request: build_api_request() },
					success: function(data, status) {

						if(data.message) {
							console.log(data.message);
						}

						var response = data.response;
						$(widget).empty().append(list);
						if (s.intro_text) list.before(intro);
						list.empty();

						if(response.statuses !== undefined) {
							resp = response.statuses;
						} else if(response.results !== undefined) {
							resp = response.results;
						} else {
							resp = response;
						}

						var tweets = $.map(resp, extract_template_data);
							tweets = $.grep(tweets, s.filter).sort(s.comparator).slice(0, s.count);

						list.append($.map(tweets, function(o) { return "<li>" + t(s.template, o) + "</li>"; }).join('')).
							children('li:first').addClass('tweet_first').end().
							children('li:odd').addClass('tweet_even').end().
							children('li:even').addClass('tweet_odd');

						if (s.outro_text) list.after(outro);
						$(widget).trigger("loaded").trigger((tweets ? "empty" : "full"));
						if (s.refresh_interval) {
							window.setTimeout(function() { $(widget).trigger("tweet:load"); }, 1000 * s.refresh_interval);
						}
					}
				});
			}).trigger("tweet:load");
		});
	};
}));

/*
 * jQuery FlexSlider v2.2.0
 * Copyright 2012 WooThemes
 * Contributing Author: Tyler Smith
 */(function(e){e.flexslider=function(t,n){var r=e(t);r.vars=e.extend({},e.flexslider.defaults,n);var i=r.vars.namespace,s=window.navigator&&window.navigator.msPointerEnabled&&window.MSGesture,o=("ontouchstart"in window||s||window.DocumentTouch&&document instanceof DocumentTouch)&&r.vars.touch,u="click touchend MSPointerUp",a="",f,l=r.vars.direction==="vertical",c=r.vars.reverse,h=r.vars.itemWidth>0,p=r.vars.animation==="fade",d=r.vars.asNavFor!=="",v={},m=!0;e.data(t,"flexslider",r);v={init:function(){r.animating=!1;r.currentSlide=parseInt(r.vars.startAt?r.vars.startAt:0);isNaN(r.currentSlide)&&(r.currentSlide=0);r.animatingTo=r.currentSlide;r.atEnd=r.currentSlide===0||r.currentSlide===r.last;r.containerSelector=r.vars.selector.substr(0,r.vars.selector.search(" "));r.slides=e(r.vars.selector,r);r.container=e(r.containerSelector,r);r.count=r.slides.length;r.syncExists=e(r.vars.sync).length>0;r.vars.animation==="slide"&&(r.vars.animation="swing");r.prop=l?"top":"marginLeft";r.args={};r.manualPause=!1;r.stopped=!1;r.started=!1;r.startTimeout=null;r.transitions=!r.vars.video&&!p&&r.vars.useCSS&&function(){var e=document.createElement("div"),t=["perspectiveProperty","WebkitPerspective","MozPerspective","OPerspective","msPerspective"];for(var n in t)if(e.style[t[n]]!==undefined){r.pfx=t[n].replace("Perspective","").toLowerCase();r.prop="-"+r.pfx+"-transform";return!0}return!1}();r.vars.controlsContainer!==""&&(r.controlsContainer=e(r.vars.controlsContainer).length>0&&e(r.vars.controlsContainer));r.vars.manualControls!==""&&(r.manualControls=e(r.vars.manualControls).length>0&&e(r.vars.manualControls));if(r.vars.randomize){r.slides.sort(function(){return Math.round(Math.random())-.5});r.container.empty().append(r.slides)}r.doMath();r.setup("init");r.vars.controlNav&&v.controlNav.setup();r.vars.directionNav&&v.directionNav.setup();r.vars.keyboard&&(e(r.containerSelector).length===1||r.vars.multipleKeyboard)&&e(document).bind("keyup",function(e){var t=e.keyCode;if(!r.animating&&(t===39||t===37)){var n=t===39?r.getTarget("next"):t===37?r.getTarget("prev"):!1;r.flexAnimate(n,r.vars.pauseOnAction)}});r.vars.mousewheel&&r.bind("mousewheel",function(e,t,n,i){e.preventDefault();var s=t<0?r.getTarget("next"):r.getTarget("prev");r.flexAnimate(s,r.vars.pauseOnAction)});r.vars.pausePlay&&v.pausePlay.setup();r.vars.slideshow&&r.vars.pauseInvisible&&v.pauseInvisible.init();if(r.vars.slideshow){r.vars.pauseOnHover&&r.hover(function(){!r.manualPlay&&!r.manualPause&&r.pause()},function(){!r.manualPause&&!r.manualPlay&&!r.stopped&&r.play()});if(!r.vars.pauseInvisible||!v.pauseInvisible.isHidden())r.vars.initDelay>0?r.startTimeout=setTimeout(r.play,r.vars.initDelay):r.play()}d&&v.asNav.setup();o&&r.vars.touch&&v.touch();(!p||p&&r.vars.smoothHeight)&&e(window).bind("resize orientationchange focus",v.resize);r.find("img").attr("draggable","false");setTimeout(function(){r.vars.start(r)},200)},asNav:{setup:function(){r.asNav=!0;r.animatingTo=Math.floor(r.currentSlide/r.move);r.currentItem=r.currentSlide;r.slides.removeClass(i+"active-slide").eq(r.currentItem).addClass(i+"active-slide");if(!s)r.slides.click(function(t){t.preventDefault();var n=e(this),s=n.index(),o=n.offset().left-e(r).scrollLeft();if(o<=0&&n.hasClass(i+"active-slide"))r.flexAnimate(r.getTarget("prev"),!0);else if(!e(r.vars.asNavFor).data("flexslider").animating&&!n.hasClass(i+"active-slide")){r.direction=r.currentItem<s?"next":"prev";r.flexAnimate(s,r.vars.pauseOnAction,!1,!0,!0)}});else{t._slider=r;r.slides.each(function(){var t=this;t._gesture=new MSGesture;t._gesture.target=t;t.addEventListener("MSPointerDown",function(e){e.preventDefault();e.currentTarget._gesture&&e.currentTarget._gesture.addPointer(e.pointerId)},!1);t.addEventListener("MSGestureTap",function(t){t.preventDefault();var n=e(this),i=n.index();if(!e(r.vars.asNavFor).data("flexslider").animating&&!n.hasClass("active")){r.direction=r.currentItem<i?"next":"prev";r.flexAnimate(i,r.vars.pauseOnAction,!1,!0,!0)}})})}}},controlNav:{setup:function(){r.manualControls?v.controlNav.setupManual():v.controlNav.setupPaging()},setupPaging:function(){var t=r.vars.controlNav==="thumbnails"?"control-thumbs":"control-paging",n=1,s,o;r.controlNavScaffold=e('<ol class="'+i+"control-nav "+i+t+'"></ol>');if(r.pagingCount>1)for(var f=0;f<r.pagingCount;f++){o=r.slides.eq(f);s=r.vars.controlNav==="thumbnails"?'<img src="'+o.attr("data-thumb")+'"/>':"<a>"+n+"</a>";if("thumbnails"===r.vars.controlNav&&!0===r.vars.thumbCaptions){var l=o.attr("data-thumbcaption");""!=l&&undefined!=l&&(s+='<span class="'+i+'caption">'+l+"</span>")}r.controlNavScaffold.append("<li>"+s+"</li>");n++}r.controlsContainer?e(r.controlsContainer).append(r.controlNavScaffold):r.append(r.controlNavScaffold);v.controlNav.set();v.controlNav.active();r.controlNavScaffold.delegate("a, img",u,function(t){t.preventDefault();if(a===""||a===t.type){var n=e(this),s=r.controlNav.index(n);if(!n.hasClass(i+"active")){r.direction=s>r.currentSlide?"next":"prev";r.flexAnimate(s,r.vars.pauseOnAction)}}a===""&&(a=t.type);v.setToClearWatchedEvent()})},setupManual:function(){r.controlNav=r.manualControls;v.controlNav.active();r.controlNav.bind(u,function(t){t.preventDefault();if(a===""||a===t.type){var n=e(this),s=r.controlNav.index(n);if(!n.hasClass(i+"active")){s>r.currentSlide?r.direction="next":r.direction="prev";r.flexAnimate(s,r.vars.pauseOnAction)}}a===""&&(a=t.type);v.setToClearWatchedEvent()})},set:function(){var t=r.vars.controlNav==="thumbnails"?"img":"a";r.controlNav=e("."+i+"control-nav li "+t,r.controlsContainer?r.controlsContainer:r)},active:function(){r.controlNav.removeClass(i+"active").eq(r.animatingTo).addClass(i+"active")},update:function(t,n){r.pagingCount>1&&t==="add"?r.controlNavScaffold.append(e("<li><a>"+r.count+"</a></li>")):r.pagingCount===1?r.controlNavScaffold.find("li").remove():r.controlNav.eq(n).closest("li").remove();v.controlNav.set();r.pagingCount>1&&r.pagingCount!==r.controlNav.length?r.update(n,t):v.controlNav.active()}},directionNav:{setup:function(){var t=e('<ul class="'+i+'direction-nav"><li><a class="'+i+'prev" href="#">'+r.vars.prevText+'</a></li><li><a class="'+i+'next" href="#">'+r.vars.nextText+"</a></li></ul>");if(r.controlsContainer){e(r.controlsContainer).append(t);r.directionNav=e("."+i+"direction-nav li a",r.controlsContainer)}else{r.append(t);r.directionNav=e("."+i+"direction-nav li a",r)}v.directionNav.update();r.directionNav.bind(u,function(t){t.preventDefault();var n;if(a===""||a===t.type){n=e(this).hasClass(i+"next")?r.getTarget("next"):r.getTarget("prev");r.flexAnimate(n,r.vars.pauseOnAction)}a===""&&(a=t.type);v.setToClearWatchedEvent()})},update:function(){var e=i+"disabled";r.pagingCount===1?r.directionNav.addClass(e).attr("tabindex","-1"):r.vars.animationLoop?r.directionNav.removeClass(e).removeAttr("tabindex"):r.animatingTo===0?r.directionNav.removeClass(e).filter("."+i+"prev").addClass(e).attr("tabindex","-1"):r.animatingTo===r.last?r.directionNav.removeClass(e).filter("."+i+"next").addClass(e).attr("tabindex","-1"):r.directionNav.removeClass(e).removeAttr("tabindex")}},pausePlay:{setup:function(){var t=e('<div class="'+i+'pauseplay"><a></a></div>');if(r.controlsContainer){r.controlsContainer.append(t);r.pausePlay=e("."+i+"pauseplay a",r.controlsContainer)}else{r.append(t);r.pausePlay=e("."+i+"pauseplay a",r)}v.pausePlay.update(r.vars.slideshow?i+"pause":i+"play");r.pausePlay.bind(u,function(t){t.preventDefault();if(a===""||a===t.type)if(e(this).hasClass(i+"pause")){r.manualPause=!0;r.manualPlay=!1;r.pause()}else{r.manualPause=!1;r.manualPlay=!0;r.play()}a===""&&(a=t.type);v.setToClearWatchedEvent()})},update:function(e){e==="play"?r.pausePlay.removeClass(i+"pause").addClass(i+"play").html(r.vars.playText):r.pausePlay.removeClass(i+"play").addClass(i+"pause").html(r.vars.pauseText)}},touch:function(){var e,n,i,o,u,a,f=!1,d=0,v=0,m=0;if(!s){t.addEventListener("touchstart",g,!1);function g(s){if(r.animating)s.preventDefault();else if(window.navigator.msPointerEnabled||s.touches.length===1){r.pause();o=l?r.h:r.w;a=Number(new Date);d=s.touches[0].pageX;v=s.touches[0].pageY;i=h&&c&&r.animatingTo===r.last?0:h&&c?r.limit-(r.itemW+r.vars.itemMargin)*r.move*r.animatingTo:h&&r.currentSlide===r.last?r.limit:h?(r.itemW+r.vars.itemMargin)*r.move*r.currentSlide:c?(r.last-r.currentSlide+r.cloneOffset)*o:(r.currentSlide+r.cloneOffset)*o;e=l?v:d;n=l?d:v;t.addEventListener("touchmove",y,!1);t.addEventListener("touchend",b,!1)}}function y(t){d=t.touches[0].pageX;v=t.touches[0].pageY;u=l?e-v:e-d;f=l?Math.abs(u)<Math.abs(d-n):Math.abs(u)<Math.abs(v-n);var s=500;if(!f||Number(new Date)-a>s){t.preventDefault();if(!p&&r.transitions){r.vars.animationLoop||(u/=r.currentSlide===0&&u<0||r.currentSlide===r.last&&u>0?Math.abs(u)/o+2:1);r.setProps(i+u,"setTouch")}}}function b(s){t.removeEventListener("touchmove",y,!1);if(r.animatingTo===r.currentSlide&&!f&&u!==null){var l=c?-u:u,h=l>0?r.getTarget("next"):r.getTarget("prev");r.canAdvance(h)&&(Number(new Date)-a<550&&Math.abs(l)>50||Math.abs(l)>o/2)?r.flexAnimate(h,r.vars.pauseOnAction):p||r.flexAnimate(r.currentSlide,r.vars.pauseOnAction,!0)}t.removeEventListener("touchend",b,!1);e=null;n=null;u=null;i=null}}else{t.style.msTouchAction="none";t._gesture=new MSGesture;t._gesture.target=t;t.addEventListener("MSPointerDown",w,!1);t._slider=r;t.addEventListener("MSGestureChange",E,!1);t.addEventListener("MSGestureEnd",S,!1);function w(e){e.stopPropagation();if(r.animating)e.preventDefault();else{r.pause();t._gesture.addPointer(e.pointerId);m=0;o=l?r.h:r.w;a=Number(new Date);i=h&&c&&r.animatingTo===r.last?0:h&&c?r.limit-(r.itemW+r.vars.itemMargin)*r.move*r.animatingTo:h&&r.currentSlide===r.last?r.limit:h?(r.itemW+r.vars.itemMargin)*r.move*r.currentSlide:c?(r.last-r.currentSlide+r.cloneOffset)*o:(r.currentSlide+r.cloneOffset)*o}}function E(e){e.stopPropagation();var n=e.target._slider;if(!n)return;var r=-e.translationX,s=-e.translationY;m+=l?s:r;u=m;f=l?Math.abs(m)<Math.abs(-r):Math.abs(m)<Math.abs(-s);if(e.detail===e.MSGESTURE_FLAG_INERTIA){setImmediate(function(){t._gesture.stop()});return}if(!f||Number(new Date)-a>500){e.preventDefault();if(!p&&n.transitions){n.vars.animationLoop||(u=m/(n.currentSlide===0&&m<0||n.currentSlide===n.last&&m>0?Math.abs(m)/o+2:1));n.setProps(i+u,"setTouch")}}}function S(t){t.stopPropagation();var r=t.target._slider;if(!r)return;if(r.animatingTo===r.currentSlide&&!f&&u!==null){var s=c?-u:u,l=s>0?r.getTarget("next"):r.getTarget("prev");r.canAdvance(l)&&(Number(new Date)-a<550&&Math.abs(s)>50||Math.abs(s)>o/2)?r.flexAnimate(l,r.vars.pauseOnAction):p||r.flexAnimate(r.currentSlide,r.vars.pauseOnAction,!0)}e=null;n=null;u=null;i=null;m=0}}},resize:function(){if(!r.animating&&r.is(":visible")){h||r.doMath();if(p)v.smoothHeight();else if(h){r.slides.width(r.computedW);r.update(r.pagingCount);r.setProps()}else if(l){r.viewport.height(r.h);r.setProps(r.h,"setTotal")}else{r.vars.smoothHeight&&v.smoothHeight();r.newSlides.width(r.computedW);r.setProps(r.computedW,"setTotal")}}},smoothHeight:function(e){if(!l||p){var t=p?r:r.viewport;e?t.animate({height:r.slides.eq(r.animatingTo).height()},e):t.height(r.slides.eq(r.animatingTo).height())}},sync:function(t){var n=e(r.vars.sync).data("flexslider"),i=r.animatingTo;switch(t){case"animate":n.flexAnimate(i,r.vars.pauseOnAction,!1,!0);break;case"play":!n.playing&&!n.asNav&&n.play();break;case"pause":n.pause()}},pauseInvisible:{visProp:null,init:function(){var e=["webkit","moz","ms","o"];if("hidden"in document)return"hidden";for(var t=0;t<e.length;t++)e[t]+"Hidden"in document&&(v.pauseInvisible.visProp=e[t]+"Hidden");if(v.pauseInvisible.visProp){var n=v.pauseInvisible.visProp.replace(/[H|h]idden/,"")+"visibilitychange";document.addEventListener(n,function(){v.pauseInvisible.isHidden()?r.startTimeout?clearTimeout(r.startTimeout):r.pause():r.started?r.play():r.vars.initDelay>0?setTimeout(r.play,r.vars.initDelay):r.play()})}},isHidden:function(){return document[v.pauseInvisible.visProp]||!1}},setToClearWatchedEvent:function(){clearTimeout(f);f=setTimeout(function(){a=""},3e3)}};r.flexAnimate=function(t,n,s,u,a){!r.vars.animationLoop&&t!==r.currentSlide&&(r.direction=t>r.currentSlide?"next":"prev");d&&r.pagingCount===1&&(r.direction=r.currentItem<t?"next":"prev");if(!r.animating&&(r.canAdvance(t,a)||s)&&r.is(":visible")){if(d&&u){var f=e(r.vars.asNavFor).data("flexslider");r.atEnd=t===0||t===r.count-1;f.flexAnimate(t,!0,!1,!0,a);r.direction=r.currentItem<t?"next":"prev";f.direction=r.direction;if(Math.ceil((t+1)/r.visible)-1===r.currentSlide||t===0){r.currentItem=t;r.slides.removeClass(i+"active-slide").eq(t).addClass(i+"active-slide");return!1}r.currentItem=t;r.slides.removeClass(i+"active-slide").eq(t).addClass(i+"active-slide");t=Math.floor(t/r.visible)}r.animating=!0;r.animatingTo=t;n&&r.pause();r.vars.before(r);r.syncExists&&!a&&v.sync("animate");r.vars.controlNav&&v.controlNav.active();h||r.slides.removeClass(i+"active-slide").eq(t).addClass(i+"active-slide");r.atEnd=t===0||t===r.last;r.vars.directionNav&&v.directionNav.update();if(t===r.last){r.vars.end(r);r.vars.animationLoop||r.pause()}if(!p){var m=l?r.slides.filter(":first").height():r.computedW,g,y,b;if(h){g=r.vars.itemMargin;b=(r.itemW+g)*r.move*r.animatingTo;y=b>r.limit&&r.visible!==1?r.limit:b}else r.currentSlide===0&&t===r.count-1&&r.vars.animationLoop&&r.direction!=="next"?y=c?(r.count+r.cloneOffset)*m:0:r.currentSlide===r.last&&t===0&&r.vars.animationLoop&&r.direction!=="prev"?y=c?0:(r.count+1)*m:y=c?(r.count-1-t+r.cloneOffset)*m:(t+r.cloneOffset)*m;r.setProps(y,"",r.vars.animationSpeed);if(r.transitions){if(!r.vars.animationLoop||!r.atEnd){r.animating=!1;r.currentSlide=r.animatingTo}r.container.unbind("webkitTransitionEnd transitionend");r.container.bind("webkitTransitionEnd transitionend",function(){r.wrapup(m)})}else r.container.animate(r.args,r.vars.animationSpeed,r.vars.easing,function(){r.wrapup(m)})}else if(!o){r.slides.eq(r.currentSlide).css({zIndex:1}).animate({opacity:0},r.vars.animationSpeed,r.vars.easing);r.slides.eq(t).css({zIndex:2}).animate({opacity:1},r.vars.animationSpeed,r.vars.easing,r.wrapup)}else{r.slides.eq(r.currentSlide).css({opacity:0,zIndex:1});r.slides.eq(t).css({opacity:1,zIndex:2});r.wrapup(m)}r.vars.smoothHeight&&v.smoothHeight(r.vars.animationSpeed)}};r.wrapup=function(e){!p&&!h&&(r.currentSlide===0&&r.animatingTo===r.last&&r.vars.animationLoop?r.setProps(e,"jumpEnd"):r.currentSlide===r.last&&r.animatingTo===0&&r.vars.animationLoop&&r.setProps(e,"jumpStart"));r.animating=!1;r.currentSlide=r.animatingTo;r.vars.after(r)};r.animateSlides=function(){!r.animating&&m&&r.flexAnimate(r.getTarget("next"))};r.pause=function(){clearInterval(r.animatedSlides);r.animatedSlides=null;r.playing=!1;r.vars.pausePlay&&v.pausePlay.update("play");r.syncExists&&v.sync("pause")};r.play=function(){r.playing&&clearInterval(r.animatedSlides);r.animatedSlides=r.animatedSlides||setInterval(r.animateSlides,r.vars.slideshowSpeed);r.started=r.playing=!0;r.vars.pausePlay&&v.pausePlay.update("pause");r.syncExists&&v.sync("play")};r.stop=function(){r.pause();r.stopped=!0};r.canAdvance=function(e,t){var n=d?r.pagingCount-1:r.last;return t?!0:d&&r.currentItem===r.count-1&&e===0&&r.direction==="prev"?!0:d&&r.currentItem===0&&e===r.pagingCount-1&&r.direction!=="next"?!1:e===r.currentSlide&&!d?!1:r.vars.animationLoop?!0:r.atEnd&&r.currentSlide===0&&e===n&&r.direction!=="next"?!1:r.atEnd&&r.currentSlide===n&&e===0&&r.direction==="next"?!1:!0};r.getTarget=function(e){r.direction=e;return e==="next"?r.currentSlide===r.last?0:r.currentSlide+1:r.currentSlide===0?r.last:r.currentSlide-1};r.setProps=function(e,t,n){var i=function(){var n=e?e:(r.itemW+r.vars.itemMargin)*r.move*r.animatingTo,i=function(){if(h)return t==="setTouch"?e:c&&r.animatingTo===r.last?0:c?r.limit-(r.itemW+r.vars.itemMargin)*r.move*r.animatingTo:r.animatingTo===r.last?r.limit:n;switch(t){case"setTotal":return c?(r.count-1-r.currentSlide+r.cloneOffset)*e:(r.currentSlide+r.cloneOffset)*e;case"setTouch":return c?e:e;case"jumpEnd":return c?e:r.count*e;case"jumpStart":return c?r.count*e:e;default:return e}}();return i*-1+"px"}();if(r.transitions){i=l?"translate3d(0,"+i+",0)":"translate3d("+i+",0,0)";n=n!==undefined?n/1e3+"s":"0s";r.container.css("-"+r.pfx+"-transition-duration",n)}r.args[r.prop]=i;(r.transitions||n===undefined)&&r.container.css(r.args)};r.setup=function(t){if(!p){var n,s;if(t==="init"){r.viewport=e('<div class="'+i+'viewport"></div>').css({overflow:"hidden",position:"relative"}).appendTo(r).append(r.container);r.cloneCount=0;r.cloneOffset=0;if(c){s=e.makeArray(r.slides).reverse();r.slides=e(s);r.container.empty().append(r.slides)}}if(r.vars.animationLoop&&!h){r.cloneCount=2;r.cloneOffset=1;t!=="init"&&r.container.find(".clone").remove();r.container.append(r.slides.first().clone().addClass("clone").attr("aria-hidden","true")).prepend(r.slides.last().clone().addClass("clone").attr("aria-hidden","true"))}r.newSlides=e(r.vars.selector,r);n=c?r.count-1-r.currentSlide+r.cloneOffset:r.currentSlide+r.cloneOffset;if(l&&!h){r.container.height((r.count+r.cloneCount)*200+"%").css("position","absolute").width("100%");setTimeout(function(){r.newSlides.css({display:"list-item"});r.doMath();r.viewport.height(r.h);r.setProps(n*r.h,"init")},t==="init"?100:0)}else{r.container.width((r.count+r.cloneCount)*200+"%");r.setProps(n*r.computedW,"init");setTimeout(function(){r.doMath();r.newSlides.css({width:r.computedW,"float":"left",display:"list-item"});r.vars.smoothHeight&&v.smoothHeight()},t==="init"?100:0)}}else{r.slides.css({width:"100%","float":"left",marginRight:"-100%",position:"relative"});t==="init"&&(o?r.slides.css({opacity:0,display:"list-item",webkitTransition:"opacity "+r.vars.animationSpeed/1e3+"s ease",zIndex:1}).eq(r.currentSlide).css({opacity:1,zIndex:2}):r.slides.css({opacity:0,display:"list-item",zIndex:1}).eq(r.currentSlide).css({zIndex:2}).animate({opacity:1},r.vars.animationSpeed,r.vars.easing));r.vars.smoothHeight&&v.smoothHeight()}h||r.slides.removeClass(i+"active-slide").eq(r.currentSlide).addClass(i+"active-slide")};r.doMath=function(){var e=r.slides.first(),t=r.vars.itemMargin,n=r.vars.minItems,i=r.vars.maxItems;r.w=r.viewport===undefined?r.width():r.viewport.width();r.h=e.height();r.boxPadding=e.outerWidth()-e.width();if(h){r.itemT=r.vars.itemWidth+t;r.minW=n?n*r.itemT:r.w;r.maxW=i?i*r.itemT-t:r.w;r.itemW=r.minW>r.w?(r.w-t*(n-1))/n:r.maxW<r.w?(r.w-t*(i-1))/i:r.vars.itemWidth>r.w?r.w:r.vars.itemWidth;r.visible=Math.floor(r.w/r.itemW);r.move=r.vars.move>0&&r.vars.move<r.visible?r.vars.move:r.visible;r.pagingCount=Math.ceil((r.count-r.visible)/r.move+1);r.last=r.pagingCount-1;r.limit=r.pagingCount===1?0:r.vars.itemWidth>r.w?r.itemW*(r.count-1)+t*(r.count-1):(r.itemW+t)*r.count-r.w-t}else{r.itemW=r.w;r.pagingCount=r.count;r.last=r.count-1}r.computedW=r.itemW-r.boxPadding};r.update=function(e,t){r.doMath();if(!h){e<r.currentSlide?r.currentSlide+=1:e<=r.currentSlide&&e!==0&&(r.currentSlide-=1);r.animatingTo=r.currentSlide}if(r.vars.controlNav&&!r.manualControls)if(t==="add"&&!h||r.pagingCount>r.controlNav.length)v.controlNav.update("add");else if(t==="remove"&&!h||r.pagingCount<r.controlNav.length){if(h&&r.currentSlide>r.last){r.currentSlide-=1;r.animatingTo-=1}v.controlNav.update("remove",r.last)}r.vars.directionNav&&v.directionNav.update()};r.addSlide=function(t,n){var i=e(t);r.count+=1;r.last=r.count-1;l&&c?n!==undefined?r.slides.eq(r.count-n).after(i):r.container.prepend(i):n!==undefined?r.slides.eq(n).before(i):r.container.append(i);r.update(n,"add");r.slides=e(r.vars.selector+":not(.clone)",r);r.setup();r.vars.added(r)};r.removeSlide=function(t){var n=isNaN(t)?r.slides.index(e(t)):t;r.count-=1;r.last=r.count-1;isNaN(t)?e(t,r.slides).remove():l&&c?r.slides.eq(r.last).remove():r.slides.eq(t).remove();r.doMath();r.update(n,"remove");r.slides=e(r.vars.selector+":not(.clone)",r);r.setup();r.vars.removed(r)};v.init()};e(window).blur(function(e){focused=!1}).focus(function(e){focused=!0});e.flexslider.defaults={namespace:"flex-",selector:".slides > li",animation:"fade",easing:"swing",direction:"horizontal",reverse:!1,animationLoop:!0,smoothHeight:!1,startAt:0,slideshow:!0,slideshowSpeed:7e3,animationSpeed:600,initDelay:0,randomize:!1,thumbCaptions:!1,pauseOnAction:!0,pauseOnHover:!1,pauseInvisible:!0,useCSS:!0,touch:!0,video:!1,controlNav:!0,directionNav:!0,prevText:"Previous",nextText:"Next",keyboard:!0,multipleKeyboard:!1,mousewheel:!1,pausePlay:!1,pauseText:"Pause",playText:"Play",controlsContainer:"",manualControls:"",sync:"",asNavFor:"",itemWidth:0,itemMargin:0,minItems:1,maxItems:0,move:0,allowOneSlide:!0,start:function(){},before:function(){},after:function(){},end:function(){},added:function(){},removed:function(){}};e.fn.flexslider=function(t){t===undefined&&(t={});if(typeof t=="object")return this.each(function(){var n=e(this),r=t.selector?t.selector:".slides > li",i=n.find(r);if(i.length===1&&t.allowOneSlide===!0||i.length===0){i.fadeIn(400);t.start&&t.start(n)}else n.data("flexslider")===undefined&&new e.flexslider(this,t)});var n=e(this).data("flexslider");switch(t){case"play":n.play();break;case"pause":n.pause();break;case"stop":n.stop();break;case"next":n.flexAnimate(n.getTarget("next"),!0);break;case"prev":case"previous":n.flexAnimate(n.getTarget("prev"),!0);break;default:typeof t=="number"&&n.flexAnimate(t,!0)}}})(jQuery);
/*
 * jQuery dropdown: A simple dropdown plugin
 *
 * Inspired by Bootstrap: http://twitter.github.com/bootstrap/javascript.html#dropdowns
 *
 * Copyright 2011 Cory LaViska for A Beautiful Site, LLC. (http://abeautifulsite.net/)
 *
 * Dual licensed under the MIT or GPL Version 2 licenses
 *
*/
if(jQuery) (function($) {
	
	$.extend($.fn, {
		dropdown: function(method, data) {
			
			switch( method ) {
				case 'hide':
					hideDropdowns();
					return $(this);
				case 'attach':
					return $(this).attr('data-dropdown', data);
				case 'detach':
					hideDropdowns();
					return $(this).removeAttr('data-dropdown');
				case 'disable':
					return $(this).addClass('dropdown-disabled');
				case 'enable':
					hideDropdowns();
					return $(this).removeClass('dropdown-disabled');
			}
			
		}
	});
	
	function showMenu(event) {
		
		var trigger = $(this),
			dropdown = $( $(this).attr('data-dropdown') ),
			isOpen = trigger.hasClass('dropdown-open');
		
		event.preventDefault();
		event.stopPropagation();
		
		hideDropdowns();
		
		if( isOpen || trigger.hasClass('dropdown-disabled') ) return;
		
		dropdown
			.css({
				left: dropdown.hasClass('anchor-right') ? 
					trigger.offset().left - (dropdown.outerWidth() - trigger.outerWidth()) : trigger.offset().left,
				top: trigger.offset().top + trigger.outerHeight()
			})
			.show();
		
		trigger.addClass('dropdown-open');
		
	};
	
	function hideDropdowns(event) {
		
		var targetGroup = event ? $(event.target).parents().andSelf() : null;
		if( targetGroup && targetGroup.is('.dropdown-menu') && !targetGroup.is('A') ) return;
		
		$('BODY')
			.find('.dropdown-menu').hide().end()
			.find('[data-dropdown]').removeClass('dropdown-open');
	};
	
	$(function () {
		$('BODY').on('click.dropdown', '[data-dropdown]', showMenu);
		$('HTML').on('click.dropdown', hideDropdowns);
		// Hide on resize (IE7/8 trigger this when any element is resized...)
		if( !$.browser.msie || ($.browser.msie && $.browser.version >= 9) ) {
			$(window).on('resize.dropdown', hideDropdowns);
		}
	});
	
})(jQuery);
/*
  jQuery Ketchup Plugin - Tasty Form Validation
  ---------------------------------------------
  
  Version 0.3.2 - 31. Jan 2011
    - Fixed another IE problem (by https://github.com/philippbosch)
  Version 0.3.1 - 12. Jan 2011
    - Check if error-container exists (by Emil Marashliev)
    - Make it work in IE6/7 (by https://github.com/hellokingdom)
  Version 0.3   - 06. Jan 2011
    - Rewritten from scratch
  Version 0.1   - 12. Feb 2010
    - Initial release
  
  Copyright (c) 2011 by Sebastian Senf:
    http://mustardamus.com/
    http://usejquery.com/
    http://twitter.com/mustardamus

  Dual licensed under the MIT and GPL licenses:
    http://www.opensource.org/licenses/mit-license.php
    http://www.gnu.org/licenses/gpl.html

  Demo: http://demos.usejquery.com/ketchup-plugin/
  Repo: http://github.com/mustardamus/ketchup-plugin
*/

(function($) {
  $.ketchup = {
    defaults: {
      attribute           : 'data-validate',                //look in that attribute for an validation string
      validateIndicator   : 'validate',                     //in the validation string this indicates the validations eg validate(required)
      eventIndicator      : 'on',                           //in the validation string this indicates the events when validations get fired eg on(blur)
      validateEvents      : 'blur',                         //the default event when validations get fired on every field
      validateElements    : ['input', 'textarea', 'select'],//check this fields in the form for a validation string on the attribute
      createErrorContainer: null,                           //function to create the error container (can also be set via $.ketchup.createErrorContainer(fn))
      showErrorContainer  : null,                           //function to show the error container (can also be set via $.ketchup.showErrorContainer(fn))
      hideErrorContainer  : null,                           //function to hide the error container (can also be set via $.ketchup.hideErrorContainer(fn))
      addErrorMessages    : null                            //function to add error messages to the error container (can also be set via $.ketchup.addErrorMessages(fn))
    },
    dataNames: {
      validationString    : 'ketchup-validation-string',
      validations         : 'ketchup-validations',
      events              : 'ketchup-events',
      elements            : 'ketchup-validation-elements',
      container           : 'ketchup-container'
    },
    validations           : {},
    helpers               : {},
    
    
    validation: function() {
      var message, func,
          arg1 = arguments[1];
      
      if(typeof arg1 == 'function') {
        func    = arg1;
      } else {
        message = arg1;
        func    = arguments[2];
      }
          
      this.validations[arguments[0]] = {
        message: message,
        func   : func,
        init   : arguments[3] || function(form, el) {}
      };
      
      return this;
    },
    
    
    message: function(name, message) {
      this.addMessage(name, message);
      return this;
    },
    
    
    messages: function(messages) {
      for(name in messages) {
        this.addMessage(name, messages[name]);
      }
      
      return this;
    },
    
    
    addMessage: function(name, message) {
      if(this.validations[name]) {
        this.validations[name].message = message;
      }
    },
    
    
    helper: function(name, func) {
      this.helpers[name] = func;
      return this;
    },
    
    
    init: function(form, options, fields) {      
          this.options = options;
      var self         = this,
          valEls       = this.initFunctions().initFields(form, fields);
      
      valEls.each(function() {
        var el = $(this);
        
        self.bindValidationEvent(form, el)
            .callInitFunctions(form, el);
      });
          
      form.data(this.dataNames.elements, valEls);
      this.bindFormSubmit(form);
    },
    
    
    initFunctions: function() {
      var opt       = this.options,
          initFuncs = [
                        'createErrorContainer',
                        'showErrorContainer',
                        'hideErrorContainer',
                        'addErrorMessages'
                      ];

      for(f = 0; f < initFuncs.length; f++) {
        var funcName = initFuncs[f];
    
        if(!opt[funcName]) {
          opt[funcName] = this[funcName];
        }
      }
      
      return this;
    },
    
    
    initFields: function(form, fields) {
      var self      = this,
          dataNames = this.dataNames,
          valEls    = $(!fields ? this.fieldsFromForm(form) : this.fieldsFromObject(form, fields));
      
      valEls.each(function() {
        var el   = $(this),
            vals = self.extractValidations(el.data(dataNames.validationString), self.options.validateIndicator);
        
        el.data(dataNames.validations, vals);
      });
      
      return valEls;
    },
    
    
    callInitFunctions: function(form, el) {
      var vals = el.data(this.dataNames.validations);
      
      for(i = 0; i < vals.length; i++) {
        vals[i].init.apply(this.helpers, [form, el]);
      }
    },
    
    
    fieldsFromForm: function(form) {
      var self      = this,
          opt       = this.options,
          dataNames = this.dataNames,
          valEls    = opt.validateElements,
          retArr    = [];
          valEls    = typeof valEls == 'string' ? [valEls] : valEls;
      
      for(i = 0; i < valEls.length; i++) {
        var els = form.find(valEls[i] + '[' + opt.attribute + '*=' + opt.validateIndicator + ']');
        
        els.each(function() {
          var el     = $(this),
              attr   = el.attr(opt.attribute),
              events = self.extractEvents(attr, opt.eventIndicator);

          el.data(dataNames.validationString, attr).data(dataNames.events, events ? events : opt.validateEvents);
        });
        
        retArr.push(els.get());
      } 
      
      return this.normalizeArray(retArr);
    },
    
    
    fieldsFromObject: function(form, fields) {
      var opt       = this.options,
          dataNames = this.dataNames,
          retArr    = [];
      
      for(s in fields) {
        var valString, events;
        
        if(typeof fields[s] == 'string') {
          valString = fields[s];
          events    = opt.validateEvents;
        } else {
          valString = fields[s][0];
          events    = fields[s][1];
        }
        
        var valEls    = form.find(s);
            valString = this.mergeValidationString(valEls, valString);
            events    = this.mergeEventsString(valEls, events);
        
        valEls.data(dataNames.validationString, opt.validateIndicator + '(' + valString + ')')
              .data(dataNames.events, events);

        retArr.push(valEls.get());
      }
      
      return this.normalizeArray(retArr);
    },
    
    
    mergeEventsString: function(valEls, events) {
      var oldEvents = valEls.data(this.dataNames.events),
          newEvents = '';
      
      if(oldEvents) {
        var eveArr = oldEvents.split(' ');
        
        for(i = 0; i < eveArr.length; i++) {
          if(events.indexOf(eveArr[i]) == -1) {
            newEvents += ' ' + eveArr[i];
          }
        }
      }
      
      return $.trim(events + newEvents);
    },
    
    
    mergeValidationString: function(valEls, newValString) {
      var opt          = this.options,
          valString    = valEls.data(this.dataNames.validationString),
          buildValFunc = function(validation) {
                           var ret = validation.name;
                           
                           if(validation.arguments.length) {
                             ret = ret + '(' + validation.arguments.join(',') + ')';
                           }
                           
                           return ret;
                         },
          inVals       = function(valsToCheck, val) {
                           for(i = 0; i < valsToCheck.length; i++) {
                             if(valsToCheck[i].name == val.name) {
                               return true;
                             }
                           }
                         };
      
      if(valString) {
        var newVals      = this.extractValidations(opt.validateIndicator + '(' + newValString + ')', opt.validateIndicator),
            oldVals      = this.extractValidations(valString, opt.validateIndicator);
            newValString = '';
        
        for(o = 0; o < oldVals.length; o++) {
          newValString += buildValFunc(oldVals[o]) + ',';
        }
        
        for(n = 0; n < newVals.length; n++) {
          if(!inVals(oldVals, newVals[n])) {
            newValString += buildValFunc(newVals[n]) + ',';
          }
        }
      }
      
      return newValString;
    },
    
    
    bindFormSubmit: function(form) {
      var self = this,
          opt  = this.options;
      
      form.submit(function() {
        return self.allFieldsValid(form, true);
      });
    },
    
    
    allFieldsValid: function(form, triggerEvents) {
      var self  = this,
          tasty = true;
      
      form.data(this.dataNames.elements).each(function() {          
        var el = $(this);
        
        if(self.validateElement(el, form) != true) {
          if(triggerEvents == true) {
            self.triggerValidationEvents(el);
          }
          
          tasty = false;
        }
      });

      form.trigger('formIs' + (tasty ? 'Valid' : 'Invalid'), [form]);
      
      return tasty;
    },
    
    
    bindValidationEvent: function(form, el) {      
      var self      = this,
          opt       = this.options,
          dataNames = this.dataNames,
          events    = el.data(dataNames.events).split(' ');
      
      for(i = 0; i < events.length; i++) {
        el.bind('ketchup.' + events[i], function() {
          var tasty     = self.validateElement(el, form),
              container = el.data(dataNames.container);

          if(tasty != true) {
            if(!container) {
              container = opt.createErrorContainer(form, el);
              el.data(dataNames.container, container);
            }

            opt.addErrorMessages(form, el, container, tasty);	        
            opt.showErrorContainer(form, el, container);
          } else {
            if(container){
              opt.hideErrorContainer(form, el, container);
            }
          }
        });
        
        this.bindValidationEventBridge(el, events[i]);
      }
      
      return this;
    },
    
    
    bindValidationEventBridge: function(el, event) {
      el.bind(event, function() {
        el.trigger('ketchup.' + event);
      });
    },
    
    
    validateElement: function(el, form) {
      var tasty = [],
          vals  = el.data(this.dataNames.validations),
          args  = [form, el, el.val()];

      for(i = 0; i < vals.length; i++) {
        if(!vals[i].func.apply(this.helpers, args.concat(vals[i].arguments))) {
          tasty.push(vals[i].message);
        }
      }
      
      form.trigger('fieldIs' + (tasty.length ? 'Invalid' : 'Valid'), [form, el]);
      
      return tasty.length ? tasty : true;
    },
    
    
    elementIsValid: function(el) {
      var dataNames = this.dataNames;
      
      if(el.data(dataNames.validations)) {
        var form = el.parentsUntil('form').last().parent();
        
        return (this.validateElement(el, form) == true ? true : false);
      } else if(el.data(dataNames.elements)) {
        return this.allFieldsValid(el);
      }
      
      return null;
    },
    
    
    triggerValidationEvents: function(el) {
      var events = el.data(this.dataNames.events).split(' ');
      
      for(var e = 0; e < events.length; e++) {
        el.trigger('ketchup.' + events[e]);
      }
    },
    
    
    extractValidations: function(toExtract, indicator) { //I still don't know regex
      var fullString   = toExtract.substr(toExtract.indexOf(indicator) + indicator.length + 1),
          tempStr      = '',
          tempArr      = [],
          openBrackets = 0,
          validations  = [];
      
      for(var i = 0; i < fullString.length; i++) {
        switch(fullString.charAt(i)) {
          case '(':
            tempStr += '(';
            openBrackets++;
            break;
          case ')':
            if(openBrackets) {
              tempStr += ')';
              openBrackets--;
            } else {
              tempArr.push($.trim(tempStr));
            }
            break;
          case ',':
            if(openBrackets) {
              tempStr += ',';
            } else {
              tempArr.push($.trim(tempStr));
              tempStr = '';
            }
            break;
          default:
            tempStr += fullString.charAt(i);
            break;
        }
      }
      
      for(v = 0; v < tempArr.length; v++) {
        var hasArgs = tempArr[v].indexOf('('),
            valName = tempArr[v],
            valArgs = [];
            
        if(hasArgs != -1) {
          valName = $.trim(tempArr[v].substr(0, hasArgs));          
          valArgs = $.map(tempArr[v].substr(valName.length).split(','), function(n) {
            return $.trim(n.replace('(', '').replace(')', ''));
          });
        }

        var valFunc = this.validations[valName];
        
        if(valFunc && valFunc.message) {
          var message = valFunc.message;
          
          for(a = 1; a <= valArgs.length; a++) {
            message = message.replace('{arg' + a + '}', valArgs[a - 1]);
          }
          
          validations.push({
            name     : valName,
            arguments: valArgs,
            func     : valFunc.func,
            message  : message,
            init     : valFunc.init
          });
        }
      }
      
      return validations;
    },
    
    
    extractEvents: function(toExtract, indicator) {
      var events = false,
          pos    = toExtract.indexOf(indicator + '(');
      
      if(pos != -1) {
        events = toExtract.substr(pos + indicator.length + 1).split(')')[0];
      }

      return events;
    },
    
    
    normalizeArray: function(array) {
      var returnArr = [];
      
      for(i = 0; i < array.length; i++) {
        for(e = 0; e < array[i].length; e++) {
          if(array[i][e]) {
            returnArr.push(array[i][e]);
          }
        }
      }
      
      return returnArr;
    },
    
    
    createErrorContainer: function(form, el) {      
      if(typeof form == 'function') {
        this.defaults.createErrorContainer = form;
        return this;
      } else {
        var elOffset = el.offset();
            
        return $('<div/>', {
                 html   : '<ul></ul><span></span>',
                 'class': 'ketchup-error',
                 css    : {
                            top : elOffset.top,
                            left: elOffset.left + el.outerWidth() - 20
                          }
               }).appendTo('body');
      }
    },
    
    
    showErrorContainer: function(form, el, container) {
      if(typeof form == 'function') {
        this.defaults.showErrorContainer = form;
        return this;
      } else {        
        container.show().animate({
          top    : el.offset().top - container.height(),
          opacity: 1
        }, 'fast');
      }
    },
    
    
    hideErrorContainer: function(form, el, container) {
      if(typeof form == 'function') {
        this.defaults.hideErrorContainer = form;
        return this;
      } else {
        container.animate({
          top    : el.offset().top,
          opacity: 0
        }, 'fast', function() {
          container.hide();
        });
      }
    },
    
    
    addErrorMessages: function(form, el, container, messages) {
      if(typeof form == 'function') {
        this.defaults.addErrorMessages = form;
        return this;
      } else {
        var list = container.children('ul');
        
        list.html('');
        
        for(i = 0; i < messages.length; i++) {
          $('<li/>', {
            text: messages[i]
          }).appendTo(list);
        }
      }
    }
  };
  
  
  $.fn.ketchup = function(options, fields) {
    var el = $(this);
    
    if(typeof options == 'string') {
      switch(options) {
        case 'validate':
          $.ketchup.triggerValidationEvents(el);
          break;
        case 'isValid':
          return $.ketchup.elementIsValid(el);
          break;
      }
    } else {
      this.each(function() {
        $.ketchup.init(el, $.extend({}, $.ketchup.defaults, options), fields);
      });
    }
    
    return this;
  };
})(jQuery);
jQuery.ketchup

.validation('required', 'Dit veld is verplicht.', function(form, el, value) {
  var type = el.attr('type').toLowerCase();
  
  if(type == 'checkbox' || type == 'radio') {
    return (el.attr('checked') == true);
  } else {
    return (value.length != 0);
  }
})

.validation('minlength', 'This field must have a minimal length of {arg1}.', function(form, el, value, min) {
  return (value.length >= +min);
})

.validation('maxlength', 'This field must have a maximal length of {arg1}.', function(form, el, value, max) {
  return (value.length <= +max);
})

.validation('rangelength', 'This field must have a length between {arg1} and {arg2}.', function(form, el, value, min, max) {
  return (value.length >= min && value.length <= max);
})

.validation('min', 'Must be at least {arg1}.', function(form, el, value, min) {
  return (this.isNumber(value) && +value >= +min);
})

.validation('max', 'Can not be greater than {arg1}.', function(form, el, value, max) {
  return (this.isNumber(value) && +value <= +max);
})

.validation('range', 'Must be between {arg1} and {arg2}.', function(form, el, value, min, max) {
  return (this.isNumber(value) && +value >= +min && +value <= +max);
})

.validation('number', 'Must be a number.', function(form, el, value) {
  return this.isNumber(value);
})

.validation('digits', 'Must be digits.', function(form, el, value) {
  return /^\d+$/.test(value);
})

.validation('email', 'E-mail adres is niet geldig.', function(form, el, value) {
  return this.isEmail(value);
})

.validation('url', 'Must be a valid URL.', function(form, el, value) {
  return this.isUrl(value);
})

.validation('username', 'Must be a valid username.', function(form, el, value) {
  return this.isUsername(value);
})

.validation('match', 'Beveiligingscode is niet correct.', function(form, el, value, word) {
  return (el.val() == word);
})

.validation('contain', 'Must contain {arg1}', function(form, el, value, word) {
  return this.contains(value, word);
})

.validation('date', 'Must be a valid date.', function(form, el, value) {
  return this.isDate(value);
})

.validation('minselect', 'Select at least {arg1} checkboxes.', function(form, el, value, min) {
  return (min <= this.inputsWithName(form, el).filter(':checked').length);
}, function(form, el) {
  this.bindBrothers(form, el);
})

.validation('maxselect', 'Select not more than {arg1} checkboxes.', function(form, el, value, max) {
  return (max >= this.inputsWithName(form, el).filter(':checked').length);
}, function(form, el) {
  this.bindBrothers(form, el);
})

.validation('rangeselect', 'Select between {arg1} and {arg2} checkboxes.', function(form, el, value, min, max) {
  var checked = this.inputsWithName(form, el).filter(':checked').length;
  
  return (min <= checked && max >= checked);
}, function(form, el) {
  this.bindBrothers(form, el);
});
jQuery.ketchup

.helper('isNumber', function(value) {
  return /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(value);
})

.helper('contains', function(value, word) {
  return value.indexOf(word) != -1;
})

.helper('isEmail', function(value) {
  return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(value);
})

.helper('isUrl', function(value) {
  return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
})

.helper('isUsername', function(value) {
  return /^([a-zA-Z])[a-zA-Z_-]*[\w_-]*[\S]$|^([a-zA-Z])[0-9_-]*[\S]$|^[a-zA-Z]*[\S]$/.test(value);
})

.helper('isDate', function(value) {
  return (!/Invalid|NaN/.test(new Date(value)));
})

.helper('inputsWithName', function(form, el) {
  return $('input[name="' + el.attr('name') + '"]', form);
})

.helper('inputsWithNameNotSelf', function(form, el) {
  return this.inputsWithName(form, el).filter(function() {
           return ($(this).index() != el.index());
         });
})

.helper('getKetchupEvents', function(el) {
  var events = el.data('events').ketchup,
      retArr = [];
  
  for(i = 0; i < events.length; i++) {
    retArr.push(events[i].namespace);
  }
      
  return retArr.join(' ');
})

.helper('bindBrothers', function(form, el) {
  this.inputsWithNameNotSelf(form, el).bind(this.getKetchupEvents(el), function() {
    el.ketchup('validate');
  });
});
(function (jQuery) {
		
		var daysInWeek 			= ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
		var shortMonthsInYear 	= ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		var longMonthsInYear 	= ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
		var shortMonthsToNumber = [];
		
		shortMonthsToNumber["Jan"] = "01";
		shortMonthsToNumber["Feb"] = "02";
		shortMonthsToNumber["Mar"] = "03";
		shortMonthsToNumber["Apr"] = "04";
		shortMonthsToNumber["May"] = "05";
		shortMonthsToNumber["Jun"] = "06";
		shortMonthsToNumber["Jul"] = "07";
		shortMonthsToNumber["Aug"] = "08";
		shortMonthsToNumber["Sep"] = "09";
		shortMonthsToNumber["Oct"] = "10";
		shortMonthsToNumber["Nov"] = "11";
		shortMonthsToNumber["Dec"] = "12";
	
    jQuery.format = (function () {
        function strDay(value) {
 						return daysInWeek[parseInt(value, 10)] || value;
        }

        function strMonth(value) {
						var monthArrayIndex = parseInt(value, 10) - 1;
 						return shortMonthsInYear[monthArrayIndex] || value;
        }

        function strLongMonth(value) {
					var monthArrayIndex = parseInt(value, 10) - 1;
					return longMonthsInYear[monthArrayIndex] || value;					
        }

        var parseMonth = function (value) {
					return shortMonthsToNumber[value] || value;
        };

        var parseTime = function (value) {
                var retValue = value;
                var millis = "";
                if (retValue.indexOf(".") !== -1) {
                    var delimited = retValue.split('.');
                    retValue = delimited[0];
                    millis = delimited[1];
                }

                var values3 = retValue.split(":");

                if (values3.length === 3) {
                    hour = values3[0];
                    minute = values3[1];
                    second = values3[2];

                    return {
                        time: retValue,
                        hour: hour,
                        minute: minute,
                        second: second,
                        millis: millis
                    };
                } else {
                    return {
                        time: "",
                        hour: "",
                        minute: "",
                        second: "",
                        millis: ""
                    };
                }
            };

        return {
            date: function (value, format) {
                /* 
					value = new java.util.Date()
                 	2009-12-18 10:54:50.546 
				*/
                try {
                    var date = null;
                    var year = null;
                    var month = null;
                    var dayOfMonth = null;
                    var dayOfWeek = null;
                    var time = null;
										if (typeof value == "number"){
											return this.date(new Date(value), format);
										} else if (typeof value.getFullYear == "function") {
                        year = value.getFullYear();
                        month = value.getMonth() + 1;
                        dayOfMonth = value.getDate();
                        dayOfWeek = value.getDay();
                        time = parseTime(value.toTimeString());
										} else if (value.search(/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.?\d{0,3}[-+]?\d{2}:?\d{2}/) != -1) { /* 2009-04-19T16:11:05+02:00 */											
                        var values = value.split(/[T\+-]/);
                        year = values[0];
                        month = values[1];
                        dayOfMonth = values[2];
                        time = parseTime(values[3].split(".")[0]);
                        date = new Date(year, month - 1, dayOfMonth);
                        dayOfWeek = date.getDay();
                    } else {
                        var values = value.split(" ");
                        switch (values.length) {
                        case 6:
                            /* Wed Jan 13 10:43:41 CET 2010 */
                            year = values[5];
                            month = parseMonth(values[1]);
                            dayOfMonth = values[2];
                            time = parseTime(values[3]);
                            date = new Date(year, month - 1, dayOfMonth);
                            dayOfWeek = date.getDay();
                            break;
                        case 2:
                            /* 2009-12-18 10:54:50.546 */
                            var values2 = values[0].split("-");
                            year = values2[0];
                            month = values2[1];
                            dayOfMonth = values2[2];
                            time = parseTime(values[1]);
                            date = new Date(year, month - 1, dayOfMonth);
                            dayOfWeek = date.getDay();
                            break;
                        case 7:
                            /* Tue Mar 01 2011 12:01:42 GMT-0800 (PST) */
                        case 9:
                            /*added by Larry, for Fri Apr 08 2011 00:00:00 GMT+0800 (China Standard Time) */
                        case 10:
                            /* added by Larry, for Fri Apr 08 2011 00:00:00 GMT+0200 (W. Europe Daylight Time) */
                            year = values[3];
                            month = parseMonth(values[1]);
                            dayOfMonth = values[2];
                            time = parseTime(values[4]);
                            date = new Date(year, month - 1, dayOfMonth);
                            dayOfWeek = date.getDay();
                            break;
                        case 1:
                            /* added by Jonny, for 2012-02-07CET00:00:00 (Doctrine Entity -> Json Serializer) */
                            var values2 = values[0].split("");
                            year=values2[0]+values2[1]+values2[2]+values2[3];
                            month= values2[5]+values2[6];
                            dayOfMonth = values2[8]+values2[9];
                            time = parseTime(values2[13]+values2[14]+values2[15]+values2[16]+values2[17]+values2[18]+values2[19]+values2[20])
                            date = new Date(year, month - 1, dayOfMonth);
                            dayOfWeek = date.getDay();
                            break;
                        default:
                            return value;
                        }
                    }

                    var pattern = "";
                    var retValue = "";
                    var unparsedRest = "";
                    /*
						Issue 1 - variable scope issue in format.date 
                    	Thanks jakemonO
					*/
                    for (var i = 0; i < format.length; i++) {
                        var currentPattern = format.charAt(i);
                        pattern += currentPattern;
                        unparsedRest = "";
                        switch (pattern) {
                        case "ddd":
                            retValue += strDay(dayOfWeek);
                            pattern = "";
                            break;
                        case "dd":
                            if (format.charAt(i + 1) == "d") {
                                break;
                            }
                            if (String(dayOfMonth).length === 1) {
                                dayOfMonth = '0' + dayOfMonth;
                            }
                            retValue += dayOfMonth;
                            pattern = "";
                            break;
                        case "d":
                            if (format.charAt(i + 1) == "d") {
                                break;
                            }
                            retValue += parseInt(dayOfMonth, 10);
                            pattern = "";
                            break;
                        case "MMMM":
                            retValue += strLongMonth(month);
                            pattern = "";
                            break;
                        case "MMM":
                            if (format.charAt(i + 1) === "M") {
                                break;
                            }
                            retValue += strMonth(month);
                            pattern = "";
                            break;
                        case "MM":
                            if (format.charAt(i + 1) == "M") {
                                break;
                            }
                            if (String(month).length === 1) {
                                month = '0' + month;
                            }
                            retValue += month;
                            pattern = "";
                            break;
                        case "M":
                            if (format.charAt(i + 1) == "M") {
                                break;
                            }
                            retValue += parseInt(month, 10);
                            pattern = "";
                            break;
                        case "yyyy":
                            retValue += year;
                            pattern = "";
                            break;
                        case "yy":
                            if (format.charAt(i + 1) == "y" &&
                           	format.charAt(i + 2) == "y") {
                            	break;
                      	    }
                            retValue += String(year).slice(-2);
                            pattern = "";
                            break;
                        case "HH":
                            retValue += time.hour;
                            pattern = "";
                            break;
                        case "hh":
                            /* time.hour is "00" as string == is used instead of === */
                            var hour = (time.hour == 0 ? 12 : time.hour < 13 ? time.hour : time.hour - 12);
                            hour = String(hour).length == 1 ? '0' + hour : hour;
                            retValue += hour;
                            pattern = "";
                            break;
												case "h":
												    if (format.charAt(i + 1) == "h") {
												        break;
												    }
												    var hour = (time.hour == 0 ? 12 : time.hour < 13 ? time.hour : time.hour - 12);                           
												    retValue += parseInt(hour, 10);
														// Fixing issue https://github.com/phstc/jquery-dateFormat/issues/21
														// retValue = parseInt(retValue, 10);
												    pattern = "";
												    break;
                        case "mm":
                            retValue += time.minute;
                            pattern = "";
                            break;
                        case "ss":
                            /* ensure only seconds are added to the return string */
                            retValue += time.second.substring(0, 2);
                            pattern = "";
                            break;
                        case "SSS":
                            retValue += time.millis.substring(0, 3);
                            pattern = "";
                            break;
                        case "a":
                            retValue += time.hour >= 12 ? "PM" : "AM";
                            pattern = "";
                            break;
                        case " ":
                            retValue += currentPattern;
                            pattern = "";
                            break;
                        case "/":
                            retValue += currentPattern;
                            pattern = "";
                            break;
                        case ":":
                            retValue += currentPattern;
                            pattern = "";
                            break;
                        default:
                            if (pattern.length === 2 && pattern.indexOf("y") !== 0 && pattern != "SS") {
                                retValue += pattern.substring(0, 1);
                                pattern = pattern.substring(1, 2);
                            } else if ((pattern.length === 3 && pattern.indexOf("yyy") === -1)) {
                                pattern = "";
                            } else {
                            	unparsedRest = pattern;
                            }
                        }
                    }
                    retValue += unparsedRest;
                    return retValue;
                } catch (e) {
                    console.log(e);
                    return value;
                }
            }
        };
    }());
}(jQuery));

jQuery.format.date.defaultShortDateFormat = "dd/MM/yyyy";
jQuery.format.date.defaultLongDateFormat = "dd/MM/yyyy hh:mm:ss";

jQuery(document).ready(function () {
    jQuery(".shortDateFormat").each(function (idx, elem) {
        if (jQuery(elem).is(":input")) {
            jQuery(elem).val(jQuery.format.date(jQuery(elem).val(), jQuery.format.date.defaultShortDateFormat));
        } else {
            jQuery(elem).text(jQuery.format.date(jQuery(elem).text(), jQuery.format.date.defaultShortDateFormat));
        }
    });
    jQuery(".longDateFormat").each(function (idx, elem) {
        if (jQuery(elem).is(":input")) {
            jQuery(elem).val(jQuery.format.date(jQuery(elem).val(), jQuery.format.date.defaultLongDateFormat));
        } else {
            jQuery(elem).text(jQuery.format.date(jQuery(elem).text(), jQuery.format.date.defaultLongDateFormat));
        }
    });
});
/**
 * jquery.Jcrop.min.js v0.9.12 (build:20130202)
 * jQuery Image Cropping Plugin - released under MIT License
 * Copyright (c) 2008-2013 Tapmodo Interactive LLC
 * https://github.com/tapmodo/Jcrop
 */
(function(a){a.Jcrop=function(b,c){function i(a){return Math.round(a)+"px"}function j(a){return d.baseClass+"-"+a}function k(){return a.fx.step.hasOwnProperty("backgroundColor")}function l(b){var c=a(b).offset();return[c.left,c.top]}function m(a){return[a.pageX-e[0],a.pageY-e[1]]}function n(b){typeof b!="object"&&(b={}),d=a.extend(d,b),a.each(["onChange","onSelect","onRelease","onDblClick"],function(a,b){typeof d[b]!="function"&&(d[b]=function(){})})}function o(a,b,c){e=l(D),bc.setCursor(a==="move"?a:a+"-resize");if(a==="move")return bc.activateHandlers(q(b),v,c);var d=_.getFixed(),f=r(a),g=_.getCorner(r(f));_.setPressed(_.getCorner(f)),_.setCurrent(g),bc.activateHandlers(p(a,d),v,c)}function p(a,b){return function(c){if(!d.aspectRatio)switch(a){case"e":c[1]=b.y2;break;case"w":c[1]=b.y2;break;case"n":c[0]=b.x2;break;case"s":c[0]=b.x2}else switch(a){case"e":c[1]=b.y+1;break;case"w":c[1]=b.y+1;break;case"n":c[0]=b.x+1;break;case"s":c[0]=b.x+1}_.setCurrent(c),bb.update()}}function q(a){var b=a;return bd.watchKeys
    (),function(a){_.moveOffset([a[0]-b[0],a[1]-b[1]]),b=a,bb.update()}}function r(a){switch(a){case"n":return"sw";case"s":return"nw";case"e":return"nw";case"w":return"ne";case"ne":return"sw";case"nw":return"se";case"se":return"nw";case"sw":return"ne"}}function s(a){return function(b){return d.disabled?!1:a==="move"&&!d.allowMove?!1:(e=l(D),W=!0,o(a,m(b)),b.stopPropagation(),b.preventDefault(),!1)}}function t(a,b,c){var d=a.width(),e=a.height();d>b&&b>0&&(d=b,e=b/a.width()*a.height()),e>c&&c>0&&(e=c,d=c/a.height()*a.width()),T=a.width()/d,U=a.height()/e,a.width(d).height(e)}function u(a){return{x:a.x*T,y:a.y*U,x2:a.x2*T,y2:a.y2*U,w:a.w*T,h:a.h*U}}function v(a){var b=_.getFixed();b.w>d.minSelect[0]&&b.h>d.minSelect[1]?(bb.enableHandles(),bb.done()):bb.release(),bc.setCursor(d.allowSelect?"crosshair":"default")}function w(a){if(d.disabled)return!1;if(!d.allowSelect)return!1;W=!0,e=l(D),bb.disableHandles(),bc.setCursor("crosshair");var b=m(a);return _.setPressed(b),bb.update(),bc.activateHandlers(x,v,a.type.substring
    (0,5)==="touch"),bd.watchKeys(),a.stopPropagation(),a.preventDefault(),!1}function x(a){_.setCurrent(a),bb.update()}function y(){var b=a("<div></div>").addClass(j("tracker"));return g&&b.css({opacity:0,backgroundColor:"white"}),b}function be(a){G.removeClass().addClass(j("holder")).addClass(a)}function bf(a,b){function t(){window.setTimeout(u,l)}var c=a[0]/T,e=a[1]/U,f=a[2]/T,g=a[3]/U;if(X)return;var h=_.flipCoords(c,e,f,g),i=_.getFixed(),j=[i.x,i.y,i.x2,i.y2],k=j,l=d.animationDelay,m=h[0]-j[0],n=h[1]-j[1],o=h[2]-j[2],p=h[3]-j[3],q=0,r=d.swingSpeed;c=k[0],e=k[1],f=k[2],g=k[3],bb.animMode(!0);var s,u=function(){return function(){q+=(100-q)/r,k[0]=Math.round(c+q/100*m),k[1]=Math.round(e+q/100*n),k[2]=Math.round(f+q/100*o),k[3]=Math.round(g+q/100*p),q>=99.8&&(q=100),q<100?(bh(k),t()):(bb.done(),bb.animMode(!1),typeof b=="function"&&b.call(bs))}}();t()}function bg(a){bh([a[0]/T,a[1]/U,a[2]/T,a[3]/U]),d.onSelect.call(bs,u(_.getFixed())),bb.enableHandles()}function bh(a){_.setPressed([a[0],a[1]]),_.setCurrent([a[2],
    a[3]]),bb.update()}function bi(){return u(_.getFixed())}function bj(){return _.getFixed()}function bk(a){n(a),br()}function bl(){d.disabled=!0,bb.disableHandles(),bb.setCursor("default"),bc.setCursor("default")}function bm(){d.disabled=!1,br()}function bn(){bb.done(),bc.activateHandlers(null,null)}function bo(){G.remove(),A.show(),A.css("visibility","visible"),a(b).removeData("Jcrop")}function bp(a,b){bb.release(),bl();var c=new Image;c.onload=function(){var e=c.width,f=c.height,g=d.boxWidth,h=d.boxHeight;D.width(e).height(f),D.attr("src",a),H.attr("src",a),t(D,g,h),E=D.width(),F=D.height(),H.width(E).height(F),M.width(E+L*2).height(F+L*2),G.width(E).height(F),ba.resize(E,F),bm(),typeof b=="function"&&b.call(bs)},c.src=a}function bq(a,b,c){var e=b||d.bgColor;d.bgFade&&k()&&d.fadeTime&&!c?a.animate({backgroundColor:e},{queue:!1,duration:d.fadeTime}):a.css("backgroundColor",e)}function br(a){d.allowResize?a?bb.enableOnly():bb.enableHandles():bb.disableHandles(),bc.setCursor(d.allowSelect?"crosshair":"default"),bb
    .setCursor(d.allowMove?"move":"default"),d.hasOwnProperty("trueSize")&&(T=d.trueSize[0]/E,U=d.trueSize[1]/F),d.hasOwnProperty("setSelect")&&(bg(d.setSelect),bb.done(),delete d.setSelect),ba.refresh(),d.bgColor!=N&&(bq(d.shade?ba.getShades():G,d.shade?d.shadeColor||d.bgColor:d.bgColor),N=d.bgColor),O!=d.bgOpacity&&(O=d.bgOpacity,d.shade?ba.refresh():bb.setBgOpacity(O)),P=d.maxSize[0]||0,Q=d.maxSize[1]||0,R=d.minSize[0]||0,S=d.minSize[1]||0,d.hasOwnProperty("outerImage")&&(D.attr("src",d.outerImage),delete d.outerImage),bb.refresh()}var d=a.extend({},a.Jcrop.defaults),e,f=navigator.userAgent.toLowerCase(),g=/msie/.test(f),h=/msie [1-6]\./.test(f);typeof b!="object"&&(b=a(b)[0]),typeof c!="object"&&(c={}),n(c);var z={border:"none",visibility:"visible",margin:0,padding:0,position:"absolute",top:0,left:0},A=a(b),B=!0;if(b.tagName=="IMG"){if(A[0].width!=0&&A[0].height!=0)A.width(A[0].width),A.height(A[0].height);else{var C=new Image;C.src=A[0].src,A.width(C.width),A.height(C.height)}var D=A.clone().removeAttr("id").
    css(z).show();D.width(A.width()),D.height(A.height()),A.after(D).hide()}else D=A.css(z).show(),B=!1,d.shade===null&&(d.shade=!0);t(D,d.boxWidth,d.boxHeight);var E=D.width(),F=D.height(),G=a("<div />").width(E).height(F).addClass(j("holder")).css({position:"relative",backgroundColor:d.bgColor}).insertAfter(A).append(D);d.addClass&&G.addClass(d.addClass);var H=a("<div />"),I=a("<div />").width("100%").height("100%").css({zIndex:310,position:"absolute",overflow:"hidden"}),J=a("<div />").width("100%").height("100%").css("zIndex",320),K=a("<div />").css({position:"absolute",zIndex:600}).dblclick(function(){var a=_.getFixed();d.onDblClick.call(bs,a)}).insertBefore(D).append(I,J);B&&(H=a("<img />").attr("src",D.attr("src")).css(z).width(E).height(F),I.append(H)),h&&K.css({overflowY:"hidden"});var L=d.boundary,M=y().width(E+L*2).height(F+L*2).css({position:"absolute",top:i(-L),left:i(-L),zIndex:290}).mousedown(w),N=d.bgColor,O=d.bgOpacity,P,Q,R,S,T,U,V=!0,W,X,Y;e=l(D);var Z=function(){function a(){var a={},b=["touchstart"
    ,"touchmove","touchend"],c=document.createElement("div"),d;try{for(d=0;d<b.length;d++){var e=b[d];e="on"+e;var f=e in c;f||(c.setAttribute(e,"return;"),f=typeof c[e]=="function"),a[b[d]]=f}return a.touchstart&&a.touchend&&a.touchmove}catch(g){return!1}}function b(){return d.touchSupport===!0||d.touchSupport===!1?d.touchSupport:a()}return{createDragger:function(a){return function(b){return d.disabled?!1:a==="move"&&!d.allowMove?!1:(e=l(D),W=!0,o(a,m(Z.cfilter(b)),!0),b.stopPropagation(),b.preventDefault(),!1)}},newSelection:function(a){return w(Z.cfilter(a))},cfilter:function(a){return a.pageX=a.originalEvent.changedTouches[0].pageX,a.pageY=a.originalEvent.changedTouches[0].pageY,a},isSupported:a,support:b()}}(),_=function(){function h(d){d=n(d),c=a=d[0],e=b=d[1]}function i(a){a=n(a),f=a[0]-c,g=a[1]-e,c=a[0],e=a[1]}function j(){return[f,g]}function k(d){var f=d[0],g=d[1];0>a+f&&(f-=f+a),0>b+g&&(g-=g+b),F<e+g&&(g+=F-(e+g)),E<c+f&&(f+=E-(c+f)),a+=f,c+=f,b+=g,e+=g}function l(a){var b=m();switch(a){case"ne":return[
    b.x2,b.y];case"nw":return[b.x,b.y];case"se":return[b.x2,b.y2];case"sw":return[b.x,b.y2]}}function m(){if(!d.aspectRatio)return p();var f=d.aspectRatio,g=d.minSize[0]/T,h=d.maxSize[0]/T,i=d.maxSize[1]/U,j=c-a,k=e-b,l=Math.abs(j),m=Math.abs(k),n=l/m,r,s,t,u;return h===0&&(h=E*10),i===0&&(i=F*10),n<f?(s=e,t=m*f,r=j<0?a-t:t+a,r<0?(r=0,u=Math.abs((r-a)/f),s=k<0?b-u:u+b):r>E&&(r=E,u=Math.abs((r-a)/f),s=k<0?b-u:u+b)):(r=c,u=l/f,s=k<0?b-u:b+u,s<0?(s=0,t=Math.abs((s-b)*f),r=j<0?a-t:t+a):s>F&&(s=F,t=Math.abs(s-b)*f,r=j<0?a-t:t+a)),r>a?(r-a<g?r=a+g:r-a>h&&(r=a+h),s>b?s=b+(r-a)/f:s=b-(r-a)/f):r<a&&(a-r<g?r=a-g:a-r>h&&(r=a-h),s>b?s=b+(a-r)/f:s=b-(a-r)/f),r<0?(a-=r,r=0):r>E&&(a-=r-E,r=E),s<0?(b-=s,s=0):s>F&&(b-=s-F,s=F),q(o(a,b,r,s))}function n(a){return a[0]<0&&(a[0]=0),a[1]<0&&(a[1]=0),a[0]>E&&(a[0]=E),a[1]>F&&(a[1]=F),[Math.round(a[0]),Math.round(a[1])]}function o(a,b,c,d){var e=a,f=c,g=b,h=d;return c<a&&(e=c,f=a),d<b&&(g=d,h=b),[e,g,f,h]}function p(){var d=c-a,f=e-b,g;return P&&Math.abs(d)>P&&(c=d>0?a+P:a-P),Q&&Math.abs
    (f)>Q&&(e=f>0?b+Q:b-Q),S/U&&Math.abs(f)<S/U&&(e=f>0?b+S/U:b-S/U),R/T&&Math.abs(d)<R/T&&(c=d>0?a+R/T:a-R/T),a<0&&(c-=a,a-=a),b<0&&(e-=b,b-=b),c<0&&(a-=c,c-=c),e<0&&(b-=e,e-=e),c>E&&(g=c-E,a-=g,c-=g),e>F&&(g=e-F,b-=g,e-=g),a>E&&(g=a-F,e-=g,b-=g),b>F&&(g=b-F,e-=g,b-=g),q(o(a,b,c,e))}function q(a){return{x:a[0],y:a[1],x2:a[2],y2:a[3],w:a[2]-a[0],h:a[3]-a[1]}}var a=0,b=0,c=0,e=0,f,g;return{flipCoords:o,setPressed:h,setCurrent:i,getOffset:j,moveOffset:k,getCorner:l,getFixed:m}}(),ba=function(){function f(a,b){e.left.css({height:i(b)}),e.right.css({height:i(b)})}function g(){return h(_.getFixed())}function h(a){e.top.css({left:i(a.x),width:i(a.w),height:i(a.y)}),e.bottom.css({top:i(a.y2),left:i(a.x),width:i(a.w),height:i(F-a.y2)}),e.right.css({left:i(a.x2),width:i(E-a.x2)}),e.left.css({width:i(a.x)})}function j(){return a("<div />").css({position:"absolute",backgroundColor:d.shadeColor||d.bgColor}).appendTo(c)}function k(){b||(b=!0,c.insertBefore(D),g(),bb.setBgOpacity(1,0,1),H.hide(),l(d.shadeColor||d.bgColor,1),bb.
    isAwake()?n(d.bgOpacity,1):n(1,1))}function l(a,b){bq(p(),a,b)}function m(){b&&(c.remove(),H.show(),b=!1,bb.isAwake()?bb.setBgOpacity(d.bgOpacity,1,1):(bb.setBgOpacity(1,1,1),bb.disableHandles()),bq(G,0,1))}function n(a,e){b&&(d.bgFade&&!e?c.animate({opacity:1-a},{queue:!1,duration:d.fadeTime}):c.css({opacity:1-a}))}function o(){d.shade?k():m(),bb.isAwake()&&n(d.bgOpacity)}function p(){return c.children()}var b=!1,c=a("<div />").css({position:"absolute",zIndex:240,opacity:0}),e={top:j(),left:j().height(F),right:j().height(F),bottom:j()};return{update:g,updateRaw:h,getShades:p,setBgColor:l,enable:k,disable:m,resize:f,refresh:o,opacity:n}}(),bb=function(){function k(b){var c=a("<div />").css({position:"absolute",opacity:d.borderOpacity}).addClass(j(b));return I.append(c),c}function l(b,c){var d=a("<div />").mousedown(s(b)).css({cursor:b+"-resize",position:"absolute",zIndex:c}).addClass("ord-"+b);return Z.support&&d.bind("touchstart.jcrop",Z.createDragger(b)),J.append(d),d}function m(a){var b=d.handleSize,e=l(a,c++
).css({opacity:d.handleOpacity}).addClass(j("handle"));return b&&e.width(b).height(b),e}function n(a){return l(a,c++).addClass("jcrop-dragbar")}function o(a){var b;for(b=0;b<a.length;b++)g[a[b]]=n(a[b])}function p(a){var b,c;for(c=0;c<a.length;c++){switch(a[c]){case"n":b="hline";break;case"s":b="hline bottom";break;case"e":b="vline right";break;case"w":b="vline"}e[a[c]]=k(b)}}function q(a){var b;for(b=0;b<a.length;b++)f[a[b]]=m(a[b])}function r(a,b){d.shade||H.css({top:i(-b),left:i(-a)}),K.css({top:i(b),left:i(a)})}function t(a,b){K.width(Math.round(a)).height(Math.round(b))}function v(){var a=_.getFixed();_.setPressed([a.x,a.y]),_.setCurrent([a.x2,a.y2]),w()}function w(a){if(b)return x(a)}function x(a){var c=_.getFixed();t(c.w,c.h),r(c.x,c.y),d.shade&&ba.updateRaw(c),b||A(),a?d.onSelect.call(bs,u(c)):d.onChange.call(bs,u(c))}function z(a,c,e){if(!b&&!c)return;d.bgFade&&!e?D.animate({opacity:a},{queue:!1,duration:d.fadeTime}):D.css("opacity",a)}function A(){K.show(),d.shade?ba.opacity(O):z(O,!0),b=!0}function B
    (){F(),K.hide(),d.shade?ba.opacity(1):z(1),b=!1,d.onRelease.call(bs)}function C(){h&&J.show()}function E(){h=!0;if(d.allowResize)return J.show(),!0}function F(){h=!1,J.hide()}function G(a){a?(X=!0,F()):(X=!1,E())}function L(){G(!1),v()}var b,c=370,e={},f={},g={},h=!1;d.dragEdges&&a.isArray(d.createDragbars)&&o(d.createDragbars),a.isArray(d.createHandles)&&q(d.createHandles),d.drawBorders&&a.isArray(d.createBorders)&&p(d.createBorders),a(document).bind("touchstart.jcrop-ios",function(b){a(b.currentTarget).hasClass("jcrop-tracker")&&b.stopPropagation()});var M=y().mousedown(s("move")).css({cursor:"move",position:"absolute",zIndex:360});return Z.support&&M.bind("touchstart.jcrop",Z.createDragger("move")),I.append(M),F(),{updateVisible:w,update:x,release:B,refresh:v,isAwake:function(){return b},setCursor:function(a){M.css("cursor",a)},enableHandles:E,enableOnly:function(){h=!0},showHandles:C,disableHandles:F,animMode:G,setBgOpacity:z,done:L}}(),bc=function(){function f(b){M.css({zIndex:450}),b?a(document).bind("touchmove.jcrop"
    ,k).bind("touchend.jcrop",l):e&&a(document).bind("mousemove.jcrop",h).bind("mouseup.jcrop",i)}function g(){M.css({zIndex:290}),a(document).unbind(".jcrop")}function h(a){return b(m(a)),!1}function i(a){return a.preventDefault(),a.stopPropagation(),W&&(W=!1,c(m(a)),bb.isAwake()&&d.onSelect.call(bs,u(_.getFixed())),g(),b=function(){},c=function(){}),!1}function j(a,d,e){return W=!0,b=a,c=d,f(e),!1}function k(a){return b(m(Z.cfilter(a))),!1}function l(a){return i(Z.cfilter(a))}function n(a){M.css("cursor",a)}var b=function(){},c=function(){},e=d.trackDocument;return e||M.mousemove(h).mouseup(i).mouseout(i),D.before(M),{activateHandlers:j,setCursor:n}}(),bd=function(){function e(){d.keySupport&&(b.show(),b.focus())}function f(a){b.hide()}function g(a,b,c){d.allowMove&&(_.moveOffset([b,c]),bb.updateVisible(!0)),a.preventDefault(),a.stopPropagation()}function i(a){if(a.ctrlKey||a.metaKey)return!0;Y=a.shiftKey?!0:!1;var b=Y?10:1;switch(a.keyCode){case 37:g(a,-b,0);break;case 39:g(a,b,0);break;case 38:g(a,0,-b);break;
    case 40:g(a,0,b);break;case 27:d.allowSelect&&bb.release();break;case 9:return!0}return!1}var b=a('<input type="radio" />').css({position:"fixed",left:"-120px",width:"12px"}).addClass("jcrop-keymgr"),c=a("<div />").css({position:"absolute",overflow:"hidden"}).append(b);return d.keySupport&&(b.keydown(i).blur(f),h||!d.fixedSupport?(b.css({position:"absolute",left:"-20px"}),c.append(b).insertBefore(D)):b.insertBefore(D)),{watchKeys:e}}();Z.support&&M.bind("touchstart.jcrop",Z.newSelection),J.hide(),br(!0);var bs={setImage:bp,animateTo:bf,setSelect:bg,setOptions:bk,tellSelect:bi,tellScaled:bj,setClass:be,disable:bl,enable:bm,cancel:bn,release:bb.release,destroy:bo,focus:bd.watchKeys,getBounds:function(){return[E*T,F*U]},getWidgetSize:function(){return[E,F]},getScaleFactor:function(){return[T,U]},getOptions:function(){return d},ui:{holder:G,selection:K}};return g&&G.bind("selectstart",function(){return!1}),A.data("Jcrop",bs),bs},a.fn.Jcrop=function(b,c){var d;return this.each(function(){if(a(this).data("Jcrop")){if(
    b==="api")return a(this).data("Jcrop");a(this).data("Jcrop").setOptions(b)}else this.tagName=="IMG"?a.Jcrop.Loader(this,function(){a(this).css({display:"block",visibility:"hidden"}),d=a.Jcrop(this,b),a.isFunction(c)&&c.call(d)}):(a(this).css({display:"block",visibility:"hidden"}),d=a.Jcrop(this,b),a.isFunction(c)&&c.call(d))}),this},a.Jcrop.Loader=function(b,c,d){function g(){f.complete?(e.unbind(".jcloader"),a.isFunction(c)&&c.call(f)):window.setTimeout(g,50)}var e=a(b),f=e[0];e.bind("load.jcloader",g).bind("error.jcloader",function(b){e.unbind(".jcloader"),a.isFunction(d)&&d.call(f)}),f.complete&&a.isFunction(c)&&(e.unbind(".jcloader"),c.call(f))},a.Jcrop.defaults={allowSelect:!0,allowMove:!0,allowResize:!0,trackDocument:!0,baseClass:"jcrop",addClass:null,bgColor:"black",bgOpacity:.6,bgFade:!1,borderOpacity:.4,handleOpacity:.5,handleSize:null,aspectRatio:0,keySupport:!0,createHandles:["n","s","e","w","nw","ne","se","sw"],createDragbars:["n","s","e","w"],createBorders:["n","s","e","w"],drawBorders:!0,dragEdges
    :!0,fixedSupport:!0,touchSupport:null,shade:null,boxWidth:0,boxHeight:0,boundary:2,fadeTime:400,animationDelay:20,swingSpeed:3,minSelect:[0,0],maxSize:[0,0],minSize:[0,0],onChange:function(){},onSelect:function(){},onDblClick:function(){},onRelease:function(){}}})(jQuery);
/*!
 * fancyBox - jQuery Plugin
 * version: 2.1.5 (Fri, 14 Jun 2013)
 * @requires jQuery v1.6 or later
 *
 * Examples at http://fancyapps.com/fancybox/
 * License: www.fancyapps.com/fancybox/#license
 *
 * Copyright 2012 Janis Skarnelis - janis@fancyapps.com
 *
 */

(function (window, document, $, undefined) {
	"use strict";

	var H = $("html"),
		W = $(window),
		D = $(document),
		F = $.fancybox = function () {
			F.open.apply( this, arguments );
		},
		IE =  navigator.userAgent.match(/msie/i),
		didUpdate	= null,
		isTouch		= document.createTouch !== undefined,

		isQuery	= function(obj) {
			return obj && obj.hasOwnProperty && obj instanceof $;
		},
		isString = function(str) {
			return str && $.type(str) === "string";
		},
		isPercentage = function(str) {
			return isString(str) && str.indexOf('%') > 0;
		},
		isScrollable = function(el) {
			return (el && !(el.style.overflow && el.style.overflow === 'hidden') && ((el.clientWidth && el.scrollWidth > el.clientWidth) || (el.clientHeight && el.scrollHeight > el.clientHeight)));
		},
		getScalar = function(orig, dim) {
			var value = parseInt(orig, 10) || 0;

			if (dim && isPercentage(orig)) {
				value = F.getViewport()[ dim ] / 100 * value;
			}

			return Math.ceil(value);
		},
		getValue = function(value, dim) {
			return getScalar(value, dim) + 'px';
		};

	$.extend(F, {
		// The current version of fancyBox
		version: '2.1.5',

		defaults: {
			padding : 15,
			margin  : 20,

			width     : 800,
			height    : 600,
			minWidth  : 100,
			minHeight : 100,
			maxWidth  : 9999,
			maxHeight : 9999,
			pixelRatio: 1, // Set to 2 for retina display support

			autoSize   : true,
			autoHeight : false,
			autoWidth  : false,

			autoResize  : true,
			autoCenter  : !isTouch,
			fitToView   : true,
			aspectRatio : false,
			topRatio    : 0.5,
			leftRatio   : 0.5,

			scrolling : 'auto', // 'auto', 'yes' or 'no'
			wrapCSS   : '',

			arrows     : true,
			closeBtn   : true,
			closeClick : false,
			nextClick  : false,
			mouseWheel : true,
			autoPlay   : false,
			playSpeed  : 3000,
			preload    : 3,
			modal      : false,
			loop       : true,

			ajax  : {
				dataType : 'html',
				headers  : { 'X-fancyBox': true }
			},
			iframe : {
				scrolling : 'auto',
				preload   : true
			},
			swf : {
				wmode: 'transparent',
				allowfullscreen   : 'true',
				allowscriptaccess : 'always'
			},

			keys  : {
				next : {
					13 : 'left', // enter
					34 : 'up',   // page down
					39 : 'left', // right arrow
					40 : 'up'    // down arrow
				},
				prev : {
					8  : 'right',  // backspace
					33 : 'down',   // page up
					37 : 'right',  // left arrow
					38 : 'down'    // up arrow
				},
				close  : [27], // escape key
				play   : [32], // space - start/stop slideshow
				toggle : [70]  // letter "f" - toggle fullscreen
			},

			direction : {
				next : 'left',
				prev : 'right'
			},

			scrollOutside  : true,

			// Override some properties
			index   : 0,
			type    : null,
			href    : null,
			content : null,
			title   : null,

			// HTML templates
			tpl: {
				wrap     : '<div class="fancybox-wrap" tabIndex="-1"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>',
				image    : '<img class="fancybox-image" src="{href}" alt="" />',
				iframe   : '<iframe id="fancybox-frame{rnd}" name="fancybox-frame{rnd}" class="fancybox-iframe" frameborder="0" vspace="0" hspace="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen' + (IE ? ' allowtransparency="true"' : '') + '></iframe>',
				error    : '<p class="fancybox-error">The requested content cannot be loaded.<br/>Please try again later.</p>',
				closeBtn : '<a title="Close" class="fancybox-item fancybox-close" href="javascript:;"></a>',
				next     : '<a title="Next" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
				prev     : '<a title="Previous" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
			},

			// Properties for each animation type
			// Opening fancyBox
			openEffect  : 'fade', // 'elastic', 'fade' or 'none'
			openSpeed   : 250,
			openEasing  : 'swing',
			openOpacity : true,
			openMethod  : 'zoomIn',

			// Closing fancyBox
			closeEffect  : 'fade', // 'elastic', 'fade' or 'none'
			closeSpeed   : 250,
			closeEasing  : 'swing',
			closeOpacity : true,
			closeMethod  : 'zoomOut',

			// Changing next gallery item
			nextEffect : 'elastic', // 'elastic', 'fade' or 'none'
			nextSpeed  : 250,
			nextEasing : 'swing',
			nextMethod : 'changeIn',

			// Changing previous gallery item
			prevEffect : 'elastic', // 'elastic', 'fade' or 'none'
			prevSpeed  : 250,
			prevEasing : 'swing',
			prevMethod : 'changeOut',

			// Enable default helpers
			helpers : {
				overlay : true,
				title   : true
			},

			// Callbacks
			onCancel     : $.noop, // If canceling
			beforeLoad   : $.noop, // Before loading
			afterLoad    : $.noop, // After loading
			beforeShow   : $.noop, // Before changing in current item
			afterShow    : $.noop, // After opening
			beforeChange : $.noop, // Before changing gallery item
			beforeClose  : $.noop, // Before closing
			afterClose   : $.noop  // After closing
		},

		//Current state
		group    : {}, // Selected group
		opts     : {}, // Group options
		previous : null,  // Previous element
		coming   : null,  // Element being loaded
		current  : null,  // Currently loaded element
		isActive : false, // Is activated
		isOpen   : false, // Is currently open
		isOpened : false, // Have been fully opened at least once

		wrap  : null,
		skin  : null,
		outer : null,
		inner : null,

		player : {
			timer    : null,
			isActive : false
		},

		// Loaders
		ajaxLoad   : null,
		imgPreload : null,

		// Some collections
		transitions : {},
		helpers     : {},

		/*
		 *	Static methods
		 */

		open: function (group, opts) {
			if (!group) {
				return;
			}

			if (!$.isPlainObject(opts)) {
				opts = {};
			}

			// Close if already active
			if (false === F.close(true)) {
				return;
			}

			// Normalize group
			if (!$.isArray(group)) {
				group = isQuery(group) ? $(group).get() : [group];
			}

			// Recheck if the type of each element is `object` and set content type (image, ajax, etc)
			$.each(group, function(i, element) {
				var obj = {},
					href,
					title,
					content,
					type,
					rez,
					hrefParts,
					selector;

				if ($.type(element) === "object") {
					// Check if is DOM element
					if (element.nodeType) {
						element = $(element);
					}

					if (isQuery(element)) {
						obj = {
							href    : element.data('fancybox-href') || element.attr('href'),
							title   : element.data('fancybox-title') || element.attr('title'),
							isDom   : true,
							element : element
						};

						if ($.metadata) {
							$.extend(true, obj, element.metadata());
						}

					} else {
						obj = element;
					}
				}

				href  = opts.href  || obj.href || (isString(element) ? element : null);
				title = opts.title !== undefined ? opts.title : obj.title || '';

				content = opts.content || obj.content;
				type    = content ? 'html' : (opts.type  || obj.type);

				if (!type && obj.isDom) {
					type = element.data('fancybox-type');

					if (!type) {
						rez  = element.prop('class').match(/fancybox\.(\w+)/);
						type = rez ? rez[1] : null;
					}
				}

				if (isString(href)) {
					// Try to guess the content type
					if (!type) {
						if (F.isImage(href)) {
							type = 'image';

						} else if (F.isSWF(href)) {
							type = 'swf';

						} else if (href.charAt(0) === '#') {
							type = 'inline';

						} else if (isString(element)) {
							type    = 'html';
							content = element;
						}
					}

					// Split url into two pieces with source url and content selector, e.g,
					// "/mypage.html #my_id" will load "/mypage.html" and display element having id "my_id"
					if (type === 'ajax') {
						hrefParts = href.split(/\s+/, 2);
						href      = hrefParts.shift();
						selector  = hrefParts.shift();
					}
				}

				if (!content) {
					if (type === 'inline') {
						if (href) {
							content = $( isString(href) ? href.replace(/.*(?=#[^\s]+$)/, '') : href ); //strip for ie7

						} else if (obj.isDom) {
							content = element;
						}

					} else if (type === 'html') {
						content = href;

					} else if (!type && !href && obj.isDom) {
						type    = 'inline';
						content = element;
					}
				}

				$.extend(obj, {
					href     : href,
					type     : type,
					content  : content,
					title    : title,
					selector : selector
				});

				group[ i ] = obj;
			});

			// Extend the defaults
			F.opts = $.extend(true, {}, F.defaults, opts);

			// All options are merged recursive except keys
			if (opts.keys !== undefined) {
				F.opts.keys = opts.keys ? $.extend({}, F.defaults.keys, opts.keys) : false;
			}

			F.group = group;

			return F._start(F.opts.index);
		},

		// Cancel image loading or abort ajax request
		cancel: function () {
			var coming = F.coming;

			if (!coming || false === F.trigger('onCancel')) {
				return;
			}

			F.hideLoading();

			if (F.ajaxLoad) {
				F.ajaxLoad.abort();
			}

			F.ajaxLoad = null;

			if (F.imgPreload) {
				F.imgPreload.onload = F.imgPreload.onerror = null;
			}

			if (coming.wrap) {
				coming.wrap.stop(true, true).trigger('onReset').remove();
			}

			F.coming = null;

			// If the first item has been canceled, then clear everything
			if (!F.current) {
				F._afterZoomOut( coming );
			}
		},

		// Start closing animation if is open; remove immediately if opening/closing
		close: function (event) {
			F.cancel();

			if (false === F.trigger('beforeClose')) {
				return;
			}

			F.unbindEvents();

			if (!F.isActive) {
				return;
			}

			if (!F.isOpen || event === true) {
				$('.fancybox-wrap').stop(true).trigger('onReset').remove();

				F._afterZoomOut();

			} else {
				F.isOpen = F.isOpened = false;
				F.isClosing = true;

				$('.fancybox-item, .fancybox-nav').remove();

				F.wrap.stop(true, true).removeClass('fancybox-opened');

				F.transitions[ F.current.closeMethod ]();
			}
		},

		// Manage slideshow:
		//   $.fancybox.play(); - toggle slideshow
		//   $.fancybox.play( true ); - start
		//   $.fancybox.play( false ); - stop
		play: function ( action ) {
			var clear = function () {
					clearTimeout(F.player.timer);
				},
				set = function () {
					clear();

					if (F.current && F.player.isActive) {
						F.player.timer = setTimeout(F.next, F.current.playSpeed);
					}
				},
				stop = function () {
					clear();

					D.unbind('.player');

					F.player.isActive = false;

					F.trigger('onPlayEnd');
				},
				start = function () {
					if (F.current && (F.current.loop || F.current.index < F.group.length - 1)) {
						F.player.isActive = true;

						D.bind({
							'onCancel.player beforeClose.player' : stop,
							'onUpdate.player'   : set,
							'beforeLoad.player' : clear
						});

						set();

						F.trigger('onPlayStart');
					}
				};

			if (action === true || (!F.player.isActive && action !== false)) {
				start();
			} else {
				stop();
			}
		},

		// Navigate to next gallery item
		next: function ( direction ) {
			var current = F.current;

			if (current) {
				if (!isString(direction)) {
					direction = current.direction.next;
				}

				F.jumpto(current.index + 1, direction, 'next');
			}
		},

		// Navigate to previous gallery item
		prev: function ( direction ) {
			var current = F.current;

			if (current) {
				if (!isString(direction)) {
					direction = current.direction.prev;
				}

				F.jumpto(current.index - 1, direction, 'prev');
			}
		},

		// Navigate to gallery item by index
		jumpto: function ( index, direction, router ) {
			var current = F.current;

			if (!current) {
				return;
			}

			index = getScalar(index);

			F.direction = direction || current.direction[ (index >= current.index ? 'next' : 'prev') ];
			F.router    = router || 'jumpto';

			if (current.loop) {
				if (index < 0) {
					index = current.group.length + (index % current.group.length);
				}

				index = index % current.group.length;
			}

			if (current.group[ index ] !== undefined) {
				F.cancel();

				F._start(index);
			}
		},

		// Center inside viewport and toggle position type to fixed or absolute if needed
		reposition: function (e, onlyAbsolute) {
			var current = F.current,
				wrap    = current ? current.wrap : null,
				pos;

			if (wrap) {
				pos = F._getPosition(onlyAbsolute);

				if (e && e.type === 'scroll') {
					delete pos.position;

					wrap.stop(true, true).animate(pos, 200);

				} else {
					wrap.css(pos);

					current.pos = $.extend({}, current.dim, pos);
				}
			}
		},

		update: function (e) {
			var type = (e && e.type),
				anyway = !type || type === 'orientationchange';

			if (anyway) {
				clearTimeout(didUpdate);

				didUpdate = null;
			}

			if (!F.isOpen || didUpdate) {
				return;
			}

			didUpdate = setTimeout(function() {
				var current = F.current;

				if (!current || F.isClosing) {
					return;
				}

				F.wrap.removeClass('fancybox-tmp');

				if (anyway || type === 'load' || (type === 'resize' && current.autoResize)) {
					F._setDimension();
				}

				if (!(type === 'scroll' && current.canShrink)) {
					F.reposition(e);
				}

				F.trigger('onUpdate');

				didUpdate = null;

			}, (anyway && !isTouch ? 0 : 300));
		},

		// Shrink content to fit inside viewport or restore if resized
		toggle: function ( action ) {
			if (F.isOpen) {
				F.current.fitToView = $.type(action) === "boolean" ? action : !F.current.fitToView;

				// Help browser to restore document dimensions
				if (isTouch) {
					F.wrap.removeAttr('style').addClass('fancybox-tmp');

					F.trigger('onUpdate');
				}

				F.update();
			}
		},

		hideLoading: function () {
			D.unbind('.loading');

			$('#fancybox-loading').remove();
		},

		showLoading: function () {
			var el, viewport;

			F.hideLoading();

			el = $('<div id="fancybox-loading"><div></div></div>').click(F.cancel).appendTo('body');

			// If user will press the escape-button, the request will be canceled
			D.bind('keydown.loading', function(e) {
				if ((e.which || e.keyCode) === 27) {
					e.preventDefault();

					F.cancel();
				}
			});

			if (!F.defaults.fixed) {
				viewport = F.getViewport();

				el.css({
					position : 'absolute',
					top  : (viewport.h * 0.5) + viewport.y,
					left : (viewport.w * 0.5) + viewport.x
				});
			}
		},

		getViewport: function () {
			var locked = (F.current && F.current.locked) || false,
				rez    = {
					x: W.scrollLeft(),
					y: W.scrollTop()
				};

			if (locked) {
				rez.w = locked[0].clientWidth;
				rez.h = locked[0].clientHeight;

			} else {
				// See http://bugs.jquery.com/ticket/6724
				rez.w = isTouch && window.innerWidth  ? window.innerWidth  : W.width();
				rez.h = isTouch && window.innerHeight ? window.innerHeight : W.height();
			}

			return rez;
		},

		// Unbind the keyboard / clicking actions
		unbindEvents: function () {
			if (F.wrap && isQuery(F.wrap)) {
				F.wrap.unbind('.fb');
			}

			D.unbind('.fb');
			W.unbind('.fb');
		},

		bindEvents: function () {
			var current = F.current,
				keys;

			if (!current) {
				return;
			}

			// Changing document height on iOS devices triggers a 'resize' event,
			// that can change document height... repeating infinitely
			W.bind('orientationchange.fb' + (isTouch ? '' : ' resize.fb') + (current.autoCenter && !current.locked ? ' scroll.fb' : ''), F.update);

			keys = current.keys;

			if (keys) {
				D.bind('keydown.fb', function (e) {
					var code   = e.which || e.keyCode,
						target = e.target || e.srcElement;

					// Skip esc key if loading, because showLoading will cancel preloading
					if (code === 27 && F.coming) {
						return false;
					}

					// Ignore key combinations and key events within form elements
					if (!e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey && !(target && (target.type || $(target).is('[contenteditable]')))) {
						$.each(keys, function(i, val) {
							if (current.group.length > 1 && val[ code ] !== undefined) {
								F[ i ]( val[ code ] );

								e.preventDefault();
								return false;
							}

							if ($.inArray(code, val) > -1) {
								F[ i ] ();

								e.preventDefault();
								return false;
							}
						});
					}
				});
			}

			if ($.fn.mousewheel && current.mouseWheel) {
				F.wrap.bind('mousewheel.fb', function (e, delta, deltaX, deltaY) {
					var target = e.target || null,
						parent = $(target),
						canScroll = false;

					while (parent.length) {
						if (canScroll || parent.is('.fancybox-skin') || parent.is('.fancybox-wrap')) {
							break;
						}

						canScroll = isScrollable( parent[0] );
						parent    = $(parent).parent();
					}

					if (delta !== 0 && !canScroll) {
						if (F.group.length > 1 && !current.canShrink) {
							if (deltaY > 0 || deltaX > 0) {
								F.prev( deltaY > 0 ? 'down' : 'left' );

							} else if (deltaY < 0 || deltaX < 0) {
								F.next( deltaY < 0 ? 'up' : 'right' );
							}

							e.preventDefault();
						}
					}
				});
			}
		},

		trigger: function (event, o) {
			var ret, obj = o || F.coming || F.current;

			if (!obj) {
				return;
			}

			if ($.isFunction( obj[event] )) {
				ret = obj[event].apply(obj, Array.prototype.slice.call(arguments, 1));
			}

			if (ret === false) {
				return false;
			}

			if (obj.helpers) {
				$.each(obj.helpers, function (helper, opts) {
					if (opts && F.helpers[helper] && $.isFunction(F.helpers[helper][event])) {
						F.helpers[helper][event]($.extend(true, {}, F.helpers[helper].defaults, opts), obj);
					}
				});
			}

			D.trigger(event);
		},

		isImage: function (str) {
			return isString(str) && str.match(/(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp|svg)((\?|#).*)?$)/i);
		},

		isSWF: function (str) {
			return isString(str) && str.match(/\.(swf)((\?|#).*)?$/i);
		},

		_start: function (index) {
			var coming = {},
				obj,
				href,
				type,
				margin,
				padding;

			index = getScalar( index );
			obj   = F.group[ index ] || null;

			if (!obj) {
				return false;
			}

			coming = $.extend(true, {}, F.opts, obj);

			// Convert margin and padding properties to array - top, right, bottom, left
			margin  = coming.margin;
			padding = coming.padding;

			if ($.type(margin) === 'number') {
				coming.margin = [margin, margin, margin, margin];
			}

			if ($.type(padding) === 'number') {
				coming.padding = [padding, padding, padding, padding];
			}

			// 'modal' propery is just a shortcut
			if (coming.modal) {
				$.extend(true, coming, {
					closeBtn   : false,
					closeClick : false,
					nextClick  : false,
					arrows     : false,
					mouseWheel : false,
					keys       : null,
					helpers: {
						overlay : {
							closeClick : false
						}
					}
				});
			}

			// 'autoSize' property is a shortcut, too
			if (coming.autoSize) {
				coming.autoWidth = coming.autoHeight = true;
			}

			if (coming.width === 'auto') {
				coming.autoWidth = true;
			}

			if (coming.height === 'auto') {
				coming.autoHeight = true;
			}

			/*
			 * Add reference to the group, so it`s possible to access from callbacks, example:
			 * afterLoad : function() {
			 *     this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
			 * }
			 */

			coming.group  = F.group;
			coming.index  = index;

			// Give a chance for callback or helpers to update coming item (type, title, etc)
			F.coming = coming;

			if (false === F.trigger('beforeLoad')) {
				F.coming = null;

				return;
			}

			type = coming.type;
			href = coming.href;

			if (!type) {
				F.coming = null;

				//If we can not determine content type then drop silently or display next/prev item if looping through gallery
				if (F.current && F.router && F.router !== 'jumpto') {
					F.current.index = index;

					return F[ F.router ]( F.direction );
				}

				return false;
			}

			F.isActive = true;

			if (type === 'image' || type === 'swf') {
				coming.autoHeight = coming.autoWidth = false;
				coming.scrolling  = 'visible';
			}

			if (type === 'image') {
				coming.aspectRatio = true;
			}

			if (type === 'iframe' && isTouch) {
				coming.scrolling = 'scroll';
			}

			// Build the neccessary markup
			coming.wrap = $(coming.tpl.wrap).addClass('fancybox-' + (isTouch ? 'mobile' : 'desktop') + ' fancybox-type-' + type + ' fancybox-tmp ' + coming.wrapCSS).appendTo( coming.parent || 'body' );

			$.extend(coming, {
				skin  : $('.fancybox-skin',  coming.wrap),
				outer : $('.fancybox-outer', coming.wrap),
				inner : $('.fancybox-inner', coming.wrap)
			});

			$.each(["Top", "Right", "Bottom", "Left"], function(i, v) {
				coming.skin.css('padding' + v, getValue(coming.padding[ i ]));
			});

			F.trigger('onReady');

			// Check before try to load; 'inline' and 'html' types need content, others - href
			if (type === 'inline' || type === 'html') {
				if (!coming.content || !coming.content.length) {
					return F._error( 'content' );
				}

			} else if (!href) {
				return F._error( 'href' );
			}

			if (type === 'image') {
				F._loadImage();

			} else if (type === 'ajax') {
				F._loadAjax();

			} else if (type === 'iframe') {
				F._loadIframe();

			} else {
				F._afterLoad();
			}
		},

		_error: function ( type ) {
			$.extend(F.coming, {
				type       : 'html',
				autoWidth  : true,
				autoHeight : true,
				minWidth   : 0,
				minHeight  : 0,
				scrolling  : 'no',
				hasError   : type,
				content    : F.coming.tpl.error
			});

			F._afterLoad();
		},

		_loadImage: function () {
			// Reset preload image so it is later possible to check "complete" property
			var img = F.imgPreload = new Image();

			img.onload = function () {
				this.onload = this.onerror = null;

				F.coming.width  = this.width / F.opts.pixelRatio;
				F.coming.height = this.height / F.opts.pixelRatio;

				F._afterLoad();
			};

			img.onerror = function () {
				this.onload = this.onerror = null;

				F._error( 'image' );
			};

			img.src = F.coming.href;

			if (img.complete !== true) {
				F.showLoading();
			}
		},

		_loadAjax: function () {
			var coming = F.coming;

			F.showLoading();

			F.ajaxLoad = $.ajax($.extend({}, coming.ajax, {
				url: coming.href,
				error: function (jqXHR, textStatus) {
					if (F.coming && textStatus !== 'abort') {
						F._error( 'ajax', jqXHR );

					} else {
						F.hideLoading();
					}
				},
				success: function (data, textStatus) {
					if (textStatus === 'success') {
						coming.content = data;

						F._afterLoad();
					}
				}
			}));
		},

		_loadIframe: function() {
			var coming = F.coming,
				iframe = $(coming.tpl.iframe.replace(/\{rnd\}/g, new Date().getTime()))
					.attr('scrolling', isTouch ? 'auto' : coming.iframe.scrolling)
					.attr('src', coming.href);

			// This helps IE
			$(coming.wrap).bind('onReset', function () {
				try {
					$(this).find('iframe').hide().attr('src', '//about:blank').end().empty();
				} catch (e) {}
			});

			if (coming.iframe.preload) {
				F.showLoading();

				iframe.one('load', function() {
					$(this).data('ready', 1);

					// iOS will lose scrolling if we resize
					if (!isTouch) {
						$(this).bind('load.fb', F.update);
					}

					// Without this trick:
					//   - iframe won't scroll on iOS devices
					//   - IE7 sometimes displays empty iframe
					$(this).parents('.fancybox-wrap').width('100%').removeClass('fancybox-tmp').show();

					F._afterLoad();
				});
			}

			coming.content = iframe.appendTo( coming.inner );

			if (!coming.iframe.preload) {
				F._afterLoad();
			}
		},

		_preloadImages: function() {
			var group   = F.group,
				current = F.current,
				len     = group.length,
				cnt     = current.preload ? Math.min(current.preload, len - 1) : 0,
				item,
				i;

			for (i = 1; i <= cnt; i += 1) {
				item = group[ (current.index + i ) % len ];

				if (item.type === 'image' && item.href) {
					new Image().src = item.href;
				}
			}
		},

		_afterLoad: function () {
			var coming   = F.coming,
				previous = F.current,
				placeholder = 'fancybox-placeholder',
				current,
				content,
				type,
				scrolling,
				href,
				embed;

			F.hideLoading();

			if (!coming || F.isActive === false) {
				return;
			}

			if (false === F.trigger('afterLoad', coming, previous)) {
				coming.wrap.stop(true).trigger('onReset').remove();

				F.coming = null;

				return;
			}

			if (previous) {
				F.trigger('beforeChange', previous);

				previous.wrap.stop(true).removeClass('fancybox-opened')
					.find('.fancybox-item, .fancybox-nav')
					.remove();
			}

			F.unbindEvents();

			current   = coming;
			content   = coming.content;
			type      = coming.type;
			scrolling = coming.scrolling;

			$.extend(F, {
				wrap  : current.wrap,
				skin  : current.skin,
				outer : current.outer,
				inner : current.inner,
				current  : current,
				previous : previous
			});

			href = current.href;

			switch (type) {
				case 'inline':
				case 'ajax':
				case 'html':
					if (current.selector) {
						content = $('<div>').html(content).find(current.selector);

					} else if (isQuery(content)) {
						if (!content.data(placeholder)) {
							content.data(placeholder, $('<div class="' + placeholder + '"></div>').insertAfter( content ).hide() );
						}

						content = content.show().detach();

						current.wrap.bind('onReset', function () {
							if ($(this).find(content).length) {
								content.hide().replaceAll( content.data(placeholder) ).data(placeholder, false);
							}
						});
					}
				break;

				case 'image':
					content = current.tpl.image.replace('{href}', href);
				break;

				case 'swf':
					content = '<object id="fancybox-swf" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%"><param name="movie" value="' + href + '"></param>';
					embed   = '';

					$.each(current.swf, function(name, val) {
						content += '<param name="' + name + '" value="' + val + '"></param>';
						embed   += ' ' + name + '="' + val + '"';
					});

					content += '<embed src="' + href + '" type="application/x-shockwave-flash" width="100%" height="100%"' + embed + '></embed></object>';
				break;
			}

			if (!(isQuery(content) && content.parent().is(current.inner))) {
				current.inner.append( content );
			}

			// Give a chance for helpers or callbacks to update elements
			F.trigger('beforeShow');

			// Set scrolling before calculating dimensions
			current.inner.css('overflow', scrolling === 'yes' ? 'scroll' : (scrolling === 'no' ? 'hidden' : scrolling));

			// Set initial dimensions and start position
			F._setDimension();

			F.reposition();

			F.isOpen = false;
			F.coming = null;

			F.bindEvents();

			if (!F.isOpened) {
				$('.fancybox-wrap').not( current.wrap ).stop(true).trigger('onReset').remove();

			} else if (previous.prevMethod) {
				F.transitions[ previous.prevMethod ]();
			}

			F.transitions[ F.isOpened ? current.nextMethod : current.openMethod ]();

			F._preloadImages();
		},

		_setDimension: function () {
			var viewport   = F.getViewport(),
				steps      = 0,
				canShrink  = false,
				canExpand  = false,
				wrap       = F.wrap,
				skin       = F.skin,
				inner      = F.inner,
				current    = F.current,
				width      = current.width,
				height     = current.height,
				minWidth   = current.minWidth,
				minHeight  = current.minHeight,
				maxWidth   = current.maxWidth,
				maxHeight  = current.maxHeight,
				scrolling  = current.scrolling,
				scrollOut  = current.scrollOutside ? current.scrollbarWidth : 0,
				margin     = current.margin,
				wMargin    = getScalar(margin[1] + margin[3]),
				hMargin    = getScalar(margin[0] + margin[2]),
				wPadding,
				hPadding,
				wSpace,
				hSpace,
				origWidth,
				origHeight,
				origMaxWidth,
				origMaxHeight,
				ratio,
				width_,
				height_,
				maxWidth_,
				maxHeight_,
				iframe,
				body;

			// Reset dimensions so we could re-check actual size
			wrap.add(skin).add(inner).width('auto').height('auto').removeClass('fancybox-tmp');

			wPadding = getScalar(skin.outerWidth(true)  - skin.width());
			hPadding = getScalar(skin.outerHeight(true) - skin.height());

			// Any space between content and viewport (margin, padding, border, title)
			wSpace = wMargin + wPadding;
			hSpace = hMargin + hPadding;

			origWidth  = isPercentage(width)  ? (viewport.w - wSpace) * getScalar(width)  / 100 : width;
			origHeight = isPercentage(height) ? (viewport.h - hSpace) * getScalar(height) / 100 : height;

			if (current.type === 'iframe') {
				iframe = current.content;

				if (current.autoHeight && iframe.data('ready') === 1) {
					try {
						if (iframe[0].contentWindow.document.location) {
							inner.width( origWidth ).height(9999);

							body = iframe.contents().find('body');

							if (scrollOut) {
								body.css('overflow-x', 'hidden');
							}

							origHeight = body.outerHeight(true);
						}

					} catch (e) {}
				}

			} else if (current.autoWidth || current.autoHeight) {
				inner.addClass( 'fancybox-tmp' );

				// Set width or height in case we need to calculate only one dimension
				if (!current.autoWidth) {
					inner.width( origWidth );
				}

				if (!current.autoHeight) {
					inner.height( origHeight );
				}

				if (current.autoWidth) {
					origWidth = inner.width();
				}

				if (current.autoHeight) {
					origHeight = inner.height();
				}

				inner.removeClass( 'fancybox-tmp' );
			}

			width  = getScalar( origWidth );
			height = getScalar( origHeight );

			ratio  = origWidth / origHeight;

			// Calculations for the content
			minWidth  = getScalar(isPercentage(minWidth) ? getScalar(minWidth, 'w') - wSpace : minWidth);
			maxWidth  = getScalar(isPercentage(maxWidth) ? getScalar(maxWidth, 'w') - wSpace : maxWidth);

			minHeight = getScalar(isPercentage(minHeight) ? getScalar(minHeight, 'h') - hSpace : minHeight);
			maxHeight = getScalar(isPercentage(maxHeight) ? getScalar(maxHeight, 'h') - hSpace : maxHeight);

			// These will be used to determine if wrap can fit in the viewport
			origMaxWidth  = maxWidth;
			origMaxHeight = maxHeight;

			if (current.fitToView) {
				maxWidth  = Math.min(viewport.w - wSpace, maxWidth);
				maxHeight = Math.min(viewport.h - hSpace, maxHeight);
			}

			maxWidth_  = viewport.w - wMargin;
			maxHeight_ = viewport.h - hMargin;

			if (current.aspectRatio) {
				if (width > maxWidth) {
					width  = maxWidth;
					height = getScalar(width / ratio);
				}

				if (height > maxHeight) {
					height = maxHeight;
					width  = getScalar(height * ratio);
				}

				if (width < minWidth) {
					width  = minWidth;
					height = getScalar(width / ratio);
				}

				if (height < minHeight) {
					height = minHeight;
					width  = getScalar(height * ratio);
				}

			} else {
				width = Math.max(minWidth, Math.min(width, maxWidth));

				if (current.autoHeight && current.type !== 'iframe') {
					inner.width( width );

					height = inner.height();
				}

				height = Math.max(minHeight, Math.min(height, maxHeight));
			}

			// Try to fit inside viewport (including the title)
			if (current.fitToView) {
				inner.width( width ).height( height );

				wrap.width( width + wPadding );

				// Real wrap dimensions
				width_  = wrap.width();
				height_ = wrap.height();

				if (current.aspectRatio) {
					while ((width_ > maxWidth_ || height_ > maxHeight_) && width > minWidth && height > minHeight) {
						if (steps++ > 19) {
							break;
						}

						height = Math.max(minHeight, Math.min(maxHeight, height - 10));
						width  = getScalar(height * ratio);

						if (width < minWidth) {
							width  = minWidth;
							height = getScalar(width / ratio);
						}

						if (width > maxWidth) {
							width  = maxWidth;
							height = getScalar(width / ratio);
						}

						inner.width( width ).height( height );

						wrap.width( width + wPadding );

						width_  = wrap.width();
						height_ = wrap.height();
					}

				} else {
					width  = Math.max(minWidth,  Math.min(width,  width  - (width_  - maxWidth_)));
					height = Math.max(minHeight, Math.min(height, height - (height_ - maxHeight_)));
				}
			}

			if (scrollOut && scrolling === 'auto' && height < origHeight && (width + wPadding + scrollOut) < maxWidth_) {
				width += scrollOut;
			}

			inner.width( width ).height( height );

			wrap.width( width + wPadding );

			width_  = wrap.width();
			height_ = wrap.height();

			canShrink = (width_ > maxWidth_ || height_ > maxHeight_) && width > minWidth && height > minHeight;
			canExpand = current.aspectRatio ? (width < origMaxWidth && height < origMaxHeight && width < origWidth && height < origHeight) : ((width < origMaxWidth || height < origMaxHeight) && (width < origWidth || height < origHeight));

			$.extend(current, {
				dim : {
					width	: getValue( width_ ),
					height	: getValue( height_ )
				},
				origWidth  : origWidth,
				origHeight : origHeight,
				canShrink  : canShrink,
				canExpand  : canExpand,
				wPadding   : wPadding,
				hPadding   : hPadding,
				wrapSpace  : height_ - skin.outerHeight(true),
				skinSpace  : skin.height() - height
			});

			if (!iframe && current.autoHeight && height > minHeight && height < maxHeight && !canExpand) {
				inner.height('auto');
			}
		},

		_getPosition: function (onlyAbsolute) {
			var current  = F.current,
				viewport = F.getViewport(),
				margin   = current.margin,
				width    = F.wrap.width()  + margin[1] + margin[3],
				height   = F.wrap.height() + margin[0] + margin[2],
				rez      = {
					position: 'absolute',
					top  : margin[0],
					left : margin[3]
				};

			if (current.autoCenter && current.fixed && !onlyAbsolute && height <= viewport.h && width <= viewport.w) {
				rez.position = 'fixed';

			} else if (!current.locked) {
				rez.top  += viewport.y;
				rez.left += viewport.x;
			}

			rez.top  = getValue(Math.max(rez.top,  rez.top  + ((viewport.h - height) * current.topRatio)));
			rez.left = getValue(Math.max(rez.left, rez.left + ((viewport.w - width)  * current.leftRatio)));

			return rez;
		},

		_afterZoomIn: function () {
			var current = F.current;

			if (!current) {
				return;
			}

			F.isOpen = F.isOpened = true;

			F.wrap.css('overflow', 'visible').addClass('fancybox-opened');

			F.update();

			// Assign a click event
			if ( current.closeClick || (current.nextClick && F.group.length > 1) ) {
				F.inner.css('cursor', 'pointer').bind('click.fb', function(e) {
					if (!$(e.target).is('a') && !$(e.target).parent().is('a')) {
						e.preventDefault();

						F[ current.closeClick ? 'close' : 'next' ]();
					}
				});
			}

			// Create a close button
			if (current.closeBtn) {
				$(current.tpl.closeBtn).appendTo(F.skin).bind('click.fb', function(e) {
					e.preventDefault();

					F.close();
				});
			}

			// Create navigation arrows
			if (current.arrows && F.group.length > 1) {
				if (current.loop || current.index > 0) {
					$(current.tpl.prev).appendTo(F.outer).bind('click.fb', F.prev);
				}

				if (current.loop || current.index < F.group.length - 1) {
					$(current.tpl.next).appendTo(F.outer).bind('click.fb', F.next);
				}
			}

			F.trigger('afterShow');

			// Stop the slideshow if this is the last item
			if (!current.loop && current.index === current.group.length - 1) {
				F.play( false );

			} else if (F.opts.autoPlay && !F.player.isActive) {
				F.opts.autoPlay = false;

				F.play();
			}
		},

		_afterZoomOut: function ( obj ) {
			obj = obj || F.current;

			$('.fancybox-wrap').trigger('onReset').remove();

			$.extend(F, {
				group  : {},
				opts   : {},
				router : false,
				current   : null,
				isActive  : false,
				isOpened  : false,
				isOpen    : false,
				isClosing : false,
				wrap   : null,
				skin   : null,
				outer  : null,
				inner  : null
			});

			F.trigger('afterClose', obj);
		}
	});

	/*
	 *	Default transitions
	 */

	F.transitions = {
		getOrigPosition: function () {
			var current  = F.current,
				element  = current.element,
				orig     = current.orig,
				pos      = {},
				width    = 50,
				height   = 50,
				hPadding = current.hPadding,
				wPadding = current.wPadding,
				viewport = F.getViewport();

			if (!orig && current.isDom && element.is(':visible')) {
				orig = element.find('img:first');

				if (!orig.length) {
					orig = element;
				}
			}

			if (isQuery(orig)) {
				pos = orig.offset();

				if (orig.is('img')) {
					width  = orig.outerWidth();
					height = orig.outerHeight();
				}

			} else {
				pos.top  = viewport.y + (viewport.h - height) * current.topRatio;
				pos.left = viewport.x + (viewport.w - width)  * current.leftRatio;
			}

			if (F.wrap.css('position') === 'fixed' || current.locked) {
				pos.top  -= viewport.y;
				pos.left -= viewport.x;
			}

			pos = {
				top     : getValue(pos.top  - hPadding * current.topRatio),
				left    : getValue(pos.left - wPadding * current.leftRatio),
				width   : getValue(width  + wPadding),
				height  : getValue(height + hPadding)
			};

			return pos;
		},

		step: function (now, fx) {
			var ratio,
				padding,
				value,
				prop       = fx.prop,
				current    = F.current,
				wrapSpace  = current.wrapSpace,
				skinSpace  = current.skinSpace;

			if (prop === 'width' || prop === 'height') {
				ratio = fx.end === fx.start ? 1 : (now - fx.start) / (fx.end - fx.start);

				if (F.isClosing) {
					ratio = 1 - ratio;
				}

				padding = prop === 'width' ? current.wPadding : current.hPadding;
				value   = now - padding;

				F.skin[ prop ](  getScalar( prop === 'width' ?  value : value - (wrapSpace * ratio) ) );
				F.inner[ prop ]( getScalar( prop === 'width' ?  value : value - (wrapSpace * ratio) - (skinSpace * ratio) ) );
			}
		},

		zoomIn: function () {
			var current  = F.current,
				startPos = current.pos,
				effect   = current.openEffect,
				elastic  = effect === 'elastic',
				endPos   = $.extend({opacity : 1}, startPos);

			// Remove "position" property that breaks older IE
			delete endPos.position;

			if (elastic) {
				startPos = this.getOrigPosition();

				if (current.openOpacity) {
					startPos.opacity = 0.1;
				}

			} else if (effect === 'fade') {
				startPos.opacity = 0.1;
			}

			F.wrap.css(startPos).animate(endPos, {
				duration : effect === 'none' ? 0 : current.openSpeed,
				easing   : current.openEasing,
				step     : elastic ? this.step : null,
				complete : F._afterZoomIn
			});
		},

		zoomOut: function () {
			var current  = F.current,
				effect   = current.closeEffect,
				elastic  = effect === 'elastic',
				endPos   = {opacity : 0.1};

			if (elastic) {
				endPos = this.getOrigPosition();

				if (current.closeOpacity) {
					endPos.opacity = 0.1;
				}
			}

			F.wrap.animate(endPos, {
				duration : effect === 'none' ? 0 : current.closeSpeed,
				easing   : current.closeEasing,
				step     : elastic ? this.step : null,
				complete : F._afterZoomOut
			});
		},

		changeIn: function () {
			var current   = F.current,
				effect    = current.nextEffect,
				startPos  = current.pos,
				endPos    = { opacity : 1 },
				direction = F.direction,
				distance  = 200,
				field;

			startPos.opacity = 0.1;

			if (effect === 'elastic') {
				field = direction === 'down' || direction === 'up' ? 'top' : 'left';

				if (direction === 'down' || direction === 'right') {
					startPos[ field ] = getValue(getScalar(startPos[ field ]) - distance);
					endPos[ field ]   = '+=' + distance + 'px';

				} else {
					startPos[ field ] = getValue(getScalar(startPos[ field ]) + distance);
					endPos[ field ]   = '-=' + distance + 'px';
				}
			}

			// Workaround for http://bugs.jquery.com/ticket/12273
			if (effect === 'none') {
				F._afterZoomIn();

			} else {
				F.wrap.css(startPos).animate(endPos, {
					duration : current.nextSpeed,
					easing   : current.nextEasing,
					complete : F._afterZoomIn
				});
			}
		},

		changeOut: function () {
			var previous  = F.previous,
				effect    = previous.prevEffect,
				endPos    = { opacity : 0.1 },
				direction = F.direction,
				distance  = 200;

			if (effect === 'elastic') {
				endPos[ direction === 'down' || direction === 'up' ? 'top' : 'left' ] = ( direction === 'up' || direction === 'left' ? '-' : '+' ) + '=' + distance + 'px';
			}

			previous.wrap.animate(endPos, {
				duration : effect === 'none' ? 0 : previous.prevSpeed,
				easing   : previous.prevEasing,
				complete : function () {
					$(this).trigger('onReset').remove();
				}
			});
		}
	};

	/*
	 *	Overlay helper
	 */

	F.helpers.overlay = {
		defaults : {
			closeClick : true,      // if true, fancyBox will be closed when user clicks on the overlay
			speedOut   : 200,       // duration of fadeOut animation
			showEarly  : true,      // indicates if should be opened immediately or wait until the content is ready
			css        : {},        // custom CSS properties
			locked     : !isTouch,  // if true, the content will be locked into overlay
			fixed      : true       // if false, the overlay CSS position property will not be set to "fixed"
		},

		overlay : null,      // current handle
		fixed   : false,     // indicates if the overlay has position "fixed"
		el      : $('html'), // element that contains "the lock"

		// Public methods
		create : function(opts) {
			opts = $.extend({}, this.defaults, opts);

			if (this.overlay) {
				this.close();
			}

			this.overlay = $('<div class="fancybox-overlay"></div>').appendTo( F.coming ? F.coming.parent : opts.parent );
			this.fixed   = false;

			if (opts.fixed && F.defaults.fixed) {
				this.overlay.addClass('fancybox-overlay-fixed');

				this.fixed = true;
			}
		},

		open : function(opts) {
			var that = this;

			opts = $.extend({}, this.defaults, opts);

			if (this.overlay) {
				this.overlay.unbind('.overlay').width('auto').height('auto');

			} else {
				this.create(opts);
			}

			if (!this.fixed) {
				W.bind('resize.overlay', $.proxy( this.update, this) );

				this.update();
			}

			if (opts.closeClick) {
				this.overlay.bind('click.overlay', function(e) {
					if ($(e.target).hasClass('fancybox-overlay')) {
						if (F.isActive) {
							F.close();
						} else {
							that.close();
						}

						return false;
					}
				});
			}

			this.overlay.css( opts.css ).show();
		},

		close : function() {
			var scrollV, scrollH;

			W.unbind('resize.overlay');

			if (this.el.hasClass('fancybox-lock')) {
				$('.fancybox-margin').removeClass('fancybox-margin');

				scrollV = W.scrollTop();
				scrollH = W.scrollLeft();

				this.el.removeClass('fancybox-lock');

				W.scrollTop( scrollV ).scrollLeft( scrollH );
			}

			$('.fancybox-overlay').remove().hide();

			$.extend(this, {
				overlay : null,
				fixed   : false
			});
		},

		// Private, callbacks

		update : function () {
			var width = '100%', offsetWidth;

			// Reset width/height so it will not mess
			this.overlay.width(width).height('100%');

			// jQuery does not return reliable result for IE
			if (IE) {
				offsetWidth = Math.max(document.documentElement.offsetWidth, document.body.offsetWidth);

				if (D.width() > offsetWidth) {
					width = D.width();
				}

			} else if (D.width() > W.width()) {
				width = D.width();
			}

			this.overlay.width(width).height(D.height());
		},

		// This is where we can manipulate DOM, because later it would cause iframes to reload
		onReady : function (opts, obj) {
			var overlay = this.overlay;

			$('.fancybox-overlay').stop(true, true);

			if (!overlay) {
				this.create(opts);
			}

			if (opts.locked && this.fixed && obj.fixed) {
				if (!overlay) {
					this.margin = D.height() > W.height() ? $('html').css('margin-right').replace("px", "") : false;
				}

				obj.locked = this.overlay.append( obj.wrap );
				obj.fixed  = false;
			}

			if (opts.showEarly === true) {
				this.beforeShow.apply(this, arguments);
			}
		},

		beforeShow : function(opts, obj) {
			var scrollV, scrollH;

			if (obj.locked) {
				if (this.margin !== false) {
					$('*').filter(function(){
						return ($(this).css('position') === 'fixed' && !$(this).hasClass("fancybox-overlay") && !$(this).hasClass("fancybox-wrap") );
					}).addClass('fancybox-margin');

					this.el.addClass('fancybox-margin');
				}

				scrollV = W.scrollTop();
				scrollH = W.scrollLeft();

				this.el.addClass('fancybox-lock');

				W.scrollTop( scrollV ).scrollLeft( scrollH );
			}

			this.open(opts);
		},

		onUpdate : function() {
			if (!this.fixed) {
				this.update();
			}
		},

		afterClose: function (opts) {
			// Remove overlay if exists and fancyBox is not opening
			// (e.g., it is not being open using afterClose callback)
			//if (this.overlay && !F.isActive) {
			if (this.overlay && !F.coming) {
				this.overlay.fadeOut(opts.speedOut, $.proxy( this.close, this ));
			}
		}
	};

	/*
	 *	Title helper
	 */

	F.helpers.title = {
		defaults : {
			type     : 'float', // 'float', 'inside', 'outside' or 'over',
			position : 'bottom' // 'top' or 'bottom'
		},

		beforeShow: function (opts) {
			var current = F.current,
				text    = current.title,
				type    = opts.type,
				title,
				target;

			if ($.isFunction(text)) {
				text = text.call(current.element, current);
			}

			if (!isString(text) || $.trim(text) === '') {
				return;
			}

			title = $('<div class="fancybox-title fancybox-title-' + type + '-wrap">' + text + '</div>');

			switch (type) {
				case 'inside':
					target = F.skin;
				break;

				case 'outside':
					target = F.wrap;
				break;

				case 'over':
					target = F.inner;
				break;

				default: // 'float'
					target = F.skin;

					title.appendTo('body');

					if (IE) {
						title.width( title.width() );
					}

					title.wrapInner('<span class="child"></span>');

					//Increase bottom margin so this title will also fit into viewport
					F.current.margin[2] += Math.abs( getScalar(title.css('margin-bottom')) );
				break;
			}

			title[ (opts.position === 'top' ? 'prependTo'  : 'appendTo') ](target);
		}
	};

	// jQuery plugin initialization
	$.fn.fancybox = function (options) {
		var index,
			that     = $(this),
			selector = this.selector || '',
			run      = function(e) {
				var what = $(this).blur(), idx = index, relType, relVal;

				if (!(e.ctrlKey || e.altKey || e.shiftKey || e.metaKey) && !what.is('.fancybox-wrap')) {
					relType = options.groupAttr || 'data-fancybox-group';
					relVal  = what.attr(relType);

					if (!relVal) {
						relType = 'rel';
						relVal  = what.get(0)[ relType ];
					}

					if (relVal && relVal !== '' && relVal !== 'nofollow') {
						what = selector.length ? $(selector) : that;
						what = what.filter('[' + relType + '="' + relVal + '"]');
						idx  = what.index(this);
					}

					options.index = idx;

					// Stop an event from bubbling if everything is fine
					if (F.open(what, options) !== false) {
						e.preventDefault();
					}
				}
			};

		options = options || {};
		index   = options.index || 0;

		if (!selector || options.live === false) {
			that.unbind('click.fb-start').bind('click.fb-start', run);

		} else {
			D.undelegate(selector, 'click.fb-start').delegate(selector + ":not('.fancybox-item, .fancybox-nav')", 'click.fb-start', run);
		}

		this.filter('[data-fancybox-start=1]').trigger('click');

		return this;
	};

	// Tests that need a body at doc ready
	D.ready(function() {
		var w1, w2;

		if ( $.scrollbarWidth === undefined ) {
			// http://benalman.com/projects/jquery-misc-plugins/#scrollbarwidth
			$.scrollbarWidth = function() {
				var parent = $('<div style="width:50px;height:50px;overflow:auto"><div/></div>').appendTo('body'),
					child  = parent.children(),
					width  = child.innerWidth() - child.height( 99 ).innerWidth();

				parent.remove();

				return width;
			};
		}

		if ( $.support.fixedPosition === undefined ) {
			$.support.fixedPosition = (function() {
				var elem  = $('<div style="position:fixed;top:20px;"></div>').appendTo('body'),
					fixed = ( elem[0].offsetTop === 20 || elem[0].offsetTop === 15 );

				elem.remove();

				return fixed;
			}());
		}

		$.extend(F.defaults, {
			scrollbarWidth : $.scrollbarWidth(),
			fixed  : $.support.fixedPosition,
			parent : $('body')
		});

		//Get real width of page scroll-bar
		w1 = $(window).width();

		H.addClass('fancybox-lock-test');

		w2 = $(window).width();

		H.removeClass('fancybox-lock-test');

		$("<style type='text/css'>.fancybox-margin{margin-right:" + (w2 - w1) + "px;}</style>").appendTo("head");
	});

}(window, document, jQuery));
/*mousewheel*/
(function(a){function d(b){var c=b||window.event,d=[].slice.call(arguments,1),e=0,f=!0,g=0,h=0;return b=a.event.fix(c),b.type="mousewheel",c.wheelDelta&&(e=c.wheelDelta/120),c.detail&&(e=-c.detail/3),h=e,c.axis!==undefined&&c.axis===c.HORIZONTAL_AXIS&&(h=0,g=-1*e),c.wheelDeltaY!==undefined&&(h=c.wheelDeltaY/120),c.wheelDeltaX!==undefined&&(g=-1*c.wheelDeltaX/120),d.unshift(b,e,g,h),(a.event.dispatch||a.event.handle).apply(this,d)}var b=["DOMMouseScroll","mousewheel"];if(a.event.fixHooks)for(var c=b.length;c;)a.event.fixHooks[b[--c]]=a.event.mouseHooks;a.event.special.mousewheel={setup:function(){if(this.addEventListener)for(var a=b.length;a;)this.addEventListener(b[--a],d,!1);else this.onmousewheel=d},teardown:function(){if(this.removeEventListener)for(var a=b.length;a;)this.removeEventListener(b[--a],d,!1);else this.onmousewheel=null}},a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})})(jQuery);
/*custom scrollbar*/
(function(c){var b={init:function(e){var f={set_width:false,set_height:false,horizontalScroll:false,scrollInertia:950,mouseWheel:true,mouseWheelPixels:"auto",autoDraggerLength:true,autoHideScrollbar:false,snapAmount:null,snapOffset:0,scrollButtons:{enable:false,scrollType:"continuous",scrollSpeed:"auto",scrollAmount:40},advanced:{updateOnBrowserResize:true,updateOnContentResize:false,autoExpandHorizontalScroll:false,autoScrollOnFocus:true,normalizeMouseWheelDelta:false},contentTouchScroll:true,callbacks:{onScrollStart:function(){},onScroll:function(){},onTotalScroll:function(){},onTotalScrollBack:function(){},onTotalScrollOffset:0,onTotalScrollBackOffset:0,whileScrolling:function(){}},theme:"light"},e=c.extend(true,f,e);return this.each(function(){var m=c(this);if(e.set_width){m.css("width",e.set_width)}if(e.set_height){m.css("height",e.set_height)}if(!c(document).data("mCustomScrollbar-index")){c(document).data("mCustomScrollbar-index","1")}else{var t=parseInt(c(document).data("mCustomScrollbar-index"));c(document).data("mCustomScrollbar-index",t+1)}m.wrapInner("<div class='mCustomScrollBox mCS-"+e.theme+"' id='mCSB_"+c(document).data("mCustomScrollbar-index")+"' style='position:relative; height:100%; overflow:hidden; max-width:100%;' />").addClass("mCustomScrollbar _mCS_"+c(document).data("mCustomScrollbar-index"));var g=m.children(".mCustomScrollBox");if(e.horizontalScroll){g.addClass("mCSB_horizontal").wrapInner("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />");var k=g.children(".mCSB_h_wrapper");k.wrapInner("<div class='mCSB_container' style='position:absolute; left:0;' />").children(".mCSB_container").css({width:k.children().outerWidth(),position:"relative"}).unwrap()}else{g.wrapInner("<div class='mCSB_container' style='position:relative; top:0;' />")}var o=g.children(".mCSB_container");if(c.support.touch){o.addClass("mCS_touch")}o.after("<div class='mCSB_scrollTools' style='position:absolute;'><div class='mCSB_draggerContainer'><div class='mCSB_dragger' style='position:absolute;' oncontextmenu='return false;'><div class='mCSB_dragger_bar' style='position:relative;'></div></div><div class='mCSB_draggerRail'></div></div></div>");var l=g.children(".mCSB_scrollTools"),h=l.children(".mCSB_draggerContainer"),q=h.children(".mCSB_dragger");if(e.horizontalScroll){q.data("minDraggerWidth",q.width())}else{q.data("minDraggerHeight",q.height())}if(e.scrollButtons.enable){if(e.horizontalScroll){l.prepend("<a class='mCSB_buttonLeft' oncontextmenu='return false;'></a>").append("<a class='mCSB_buttonRight' oncontextmenu='return false;'></a>")}else{l.prepend("<a class='mCSB_buttonUp' oncontextmenu='return false;'></a>").append("<a class='mCSB_buttonDown' oncontextmenu='return false;'></a>")}}g.bind("scroll",function(){if(!m.is(".mCS_disabled")){g.scrollTop(0).scrollLeft(0)}});m.data({mCS_Init:true,mCustomScrollbarIndex:c(document).data("mCustomScrollbar-index"),horizontalScroll:e.horizontalScroll,scrollInertia:e.scrollInertia,scrollEasing:"mcsEaseOut",mouseWheel:e.mouseWheel,mouseWheelPixels:e.mouseWheelPixels,autoDraggerLength:e.autoDraggerLength,autoHideScrollbar:e.autoHideScrollbar,snapAmount:e.snapAmount,snapOffset:e.snapOffset,scrollButtons_enable:e.scrollButtons.enable,scrollButtons_scrollType:e.scrollButtons.scrollType,scrollButtons_scrollSpeed:e.scrollButtons.scrollSpeed,scrollButtons_scrollAmount:e.scrollButtons.scrollAmount,autoExpandHorizontalScroll:e.advanced.autoExpandHorizontalScroll,autoScrollOnFocus:e.advanced.autoScrollOnFocus,normalizeMouseWheelDelta:e.advanced.normalizeMouseWheelDelta,contentTouchScroll:e.contentTouchScroll,onScrollStart_Callback:e.callbacks.onScrollStart,onScroll_Callback:e.callbacks.onScroll,onTotalScroll_Callback:e.callbacks.onTotalScroll,onTotalScrollBack_Callback:e.callbacks.onTotalScrollBack,onTotalScroll_Offset:e.callbacks.onTotalScrollOffset,onTotalScrollBack_Offset:e.callbacks.onTotalScrollBackOffset,whileScrolling_Callback:e.callbacks.whileScrolling,bindEvent_scrollbar_drag:false,bindEvent_content_touch:false,bindEvent_scrollbar_click:false,bindEvent_mousewheel:false,bindEvent_buttonsContinuous_y:false,bindEvent_buttonsContinuous_x:false,bindEvent_buttonsPixels_y:false,bindEvent_buttonsPixels_x:false,bindEvent_focusin:false,bindEvent_autoHideScrollbar:false,mCSB_buttonScrollRight:false,mCSB_buttonScrollLeft:false,mCSB_buttonScrollDown:false,mCSB_buttonScrollUp:false});if(e.horizontalScroll){if(m.css("max-width")!=="none"){if(!e.advanced.updateOnContentResize){e.advanced.updateOnContentResize=true}}}else{if(m.css("max-height")!=="none"){var s=false,r=parseInt(m.css("max-height"));if(m.css("max-height").indexOf("%")>=0){s=r,r=m.parent().height()*s/100}m.css("overflow","hidden");g.css("max-height",r)}}m.mCustomScrollbar("update");if(e.advanced.updateOnBrowserResize){var i,j=c(window).width(),u=c(window).height();c(window).bind("resize."+m.data("mCustomScrollbarIndex"),function(){if(i){clearTimeout(i)}i=setTimeout(function(){if(!m.is(".mCS_disabled")&&!m.is(".mCS_destroyed")){var w=c(window).width(),v=c(window).height();if(j!==w||u!==v){if(m.css("max-height")!=="none"&&s){g.css("max-height",m.parent().height()*s/100)}m.mCustomScrollbar("update");j=w;u=v}}},150)})}if(e.advanced.updateOnContentResize){var p;if(e.horizontalScroll){var n=o.outerWidth()}else{var n=o.outerHeight()}p=setInterval(function(){if(e.horizontalScroll){if(e.advanced.autoExpandHorizontalScroll){o.css({position:"absolute",width:"auto"}).wrap("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />").css({width:o.outerWidth(),position:"relative"}).unwrap()}var v=o.outerWidth()}else{var v=o.outerHeight()}if(v!=n){m.mCustomScrollbar("update");n=v}},300)}})},update:function(){var n=c(this),k=n.children(".mCustomScrollBox"),q=k.children(".mCSB_container");q.removeClass("mCS_no_scrollbar");n.removeClass("mCS_disabled mCS_destroyed");k.scrollTop(0).scrollLeft(0);var y=k.children(".mCSB_scrollTools"),o=y.children(".mCSB_draggerContainer"),m=o.children(".mCSB_dragger");if(n.data("horizontalScroll")){var A=y.children(".mCSB_buttonLeft"),t=y.children(".mCSB_buttonRight"),f=k.width();if(n.data("autoExpandHorizontalScroll")){q.css({position:"absolute",width:"auto"}).wrap("<div class='mCSB_h_wrapper' style='position:relative; left:0; width:999999px;' />").css({width:q.outerWidth(),position:"relative"}).unwrap()}var z=q.outerWidth()}else{var w=y.children(".mCSB_buttonUp"),g=y.children(".mCSB_buttonDown"),r=k.height(),i=q.outerHeight()}if(i>r&&!n.data("horizontalScroll")){y.css("display","block");var s=o.height();if(n.data("autoDraggerLength")){var u=Math.round(r/i*s),l=m.data("minDraggerHeight");if(u<=l){m.css({height:l})}else{if(u>=s-10){var p=s-10;m.css({height:p})}else{m.css({height:u})}}m.children(".mCSB_dragger_bar").css({"line-height":m.height()+"px"})}var B=m.height(),x=(i-r)/(s-B);n.data("scrollAmount",x).mCustomScrollbar("scrolling",k,q,o,m,w,g,A,t);var D=Math.abs(q.position().top);n.mCustomScrollbar("scrollTo",D,{scrollInertia:0,trigger:"internal"})}else{if(z>f&&n.data("horizontalScroll")){y.css("display","block");var h=o.width();if(n.data("autoDraggerLength")){var j=Math.round(f/z*h),C=m.data("minDraggerWidth");if(j<=C){m.css({width:C})}else{if(j>=h-10){var e=h-10;m.css({width:e})}else{m.css({width:j})}}}var v=m.width(),x=(z-f)/(h-v);n.data("scrollAmount",x).mCustomScrollbar("scrolling",k,q,o,m,w,g,A,t);var D=Math.abs(q.position().left);n.mCustomScrollbar("scrollTo",D,{scrollInertia:0,trigger:"internal"})}else{k.unbind("mousewheel focusin");if(n.data("horizontalScroll")){m.add(q).css("left",0)}else{m.add(q).css("top",0)}y.css("display","none");q.addClass("mCS_no_scrollbar");n.data({bindEvent_mousewheel:false,bindEvent_focusin:false})}}},scrolling:function(h,p,m,j,w,e,A,v){var k=c(this);if(!k.data("bindEvent_scrollbar_drag")){var n,o;if(c.support.msPointer){j.bind("MSPointerDown",function(H){H.preventDefault();k.data({on_drag:true});j.addClass("mCSB_dragger_onDrag");var G=c(this),J=G.offset(),F=H.originalEvent.pageX-J.left,I=H.originalEvent.pageY-J.top;if(F<G.width()&&F>0&&I<G.height()&&I>0){n=I;o=F}});c(document).bind("MSPointerMove."+k.data("mCustomScrollbarIndex"),function(H){H.preventDefault();if(k.data("on_drag")){var G=j,J=G.offset(),F=H.originalEvent.pageX-J.left,I=H.originalEvent.pageY-J.top;D(n,o,I,F)}}).bind("MSPointerUp."+k.data("mCustomScrollbarIndex"),function(x){k.data({on_drag:false});j.removeClass("mCSB_dragger_onDrag")})}else{j.bind("mousedown touchstart",function(H){H.preventDefault();H.stopImmediatePropagation();var G=c(this),K=G.offset(),F,J;if(H.type==="touchstart"){var I=H.originalEvent.touches[0]||H.originalEvent.changedTouches[0];F=I.pageX-K.left;J=I.pageY-K.top}else{k.data({on_drag:true});j.addClass("mCSB_dragger_onDrag");F=H.pageX-K.left;J=H.pageY-K.top}if(F<G.width()&&F>0&&J<G.height()&&J>0){n=J;o=F}}).bind("touchmove",function(H){H.preventDefault();H.stopImmediatePropagation();var K=H.originalEvent.touches[0]||H.originalEvent.changedTouches[0],G=c(this),J=G.offset(),F=K.pageX-J.left,I=K.pageY-J.top;D(n,o,I,F)});c(document).bind("mousemove."+k.data("mCustomScrollbarIndex"),function(H){if(k.data("on_drag")){var G=j,J=G.offset(),F=H.pageX-J.left,I=H.pageY-J.top;D(n,o,I,F)}}).bind("mouseup."+k.data("mCustomScrollbarIndex"),function(x){k.data({on_drag:false});j.removeClass("mCSB_dragger_onDrag")})}k.data({bindEvent_scrollbar_drag:true})}function D(G,H,I,F){if(k.data("horizontalScroll")){k.mCustomScrollbar("scrollTo",(j.position().left-(H))+F,{moveDragger:true,trigger:"internal"})}else{k.mCustomScrollbar("scrollTo",(j.position().top-(G))+I,{moveDragger:true,trigger:"internal"})}}if(c.support.touch&&k.data("contentTouchScroll")){if(!k.data("bindEvent_content_touch")){var l,B,r,s,u,C,E;p.bind("touchstart",function(x){x.stopImmediatePropagation();l=x.originalEvent.touches[0]||x.originalEvent.changedTouches[0];B=c(this);r=B.offset();u=l.pageX-r.left;s=l.pageY-r.top;C=s;E=u});p.bind("touchmove",function(x){x.preventDefault();x.stopImmediatePropagation();l=x.originalEvent.touches[0]||x.originalEvent.changedTouches[0];B=c(this).parent();r=B.offset();u=l.pageX-r.left;s=l.pageY-r.top;if(k.data("horizontalScroll")){k.mCustomScrollbar("scrollTo",E-u,{trigger:"internal"})}else{k.mCustomScrollbar("scrollTo",C-s,{trigger:"internal"})}})}}if(!k.data("bindEvent_scrollbar_click")){m.bind("click",function(F){var x=(F.pageY-m.offset().top)*k.data("scrollAmount"),y=c(F.target);if(k.data("horizontalScroll")){x=(F.pageX-m.offset().left)*k.data("scrollAmount")}if(y.hasClass("mCSB_draggerContainer")||y.hasClass("mCSB_draggerRail")){k.mCustomScrollbar("scrollTo",x,{trigger:"internal",scrollEasing:"draggerRailEase"})}});k.data({bindEvent_scrollbar_click:true})}if(k.data("mouseWheel")){if(!k.data("bindEvent_mousewheel")){h.bind("mousewheel",function(H,J){var G,F=k.data("mouseWheelPixels"),x=Math.abs(p.position().top),I=j.position().top,y=m.height()-j.height();if(k.data("normalizeMouseWheelDelta")){if(J<0){J=-1}else{J=1}}if(F==="auto"){F=100+Math.round(k.data("scrollAmount")/2)}if(k.data("horizontalScroll")){I=j.position().left;y=m.width()-j.width();x=Math.abs(p.position().left)}if((J>0&&I!==0)||(J<0&&I!==y)){H.preventDefault();H.stopImmediatePropagation()}G=x-(J*F);k.mCustomScrollbar("scrollTo",G,{trigger:"internal"})});k.data({bindEvent_mousewheel:true})}}if(k.data("scrollButtons_enable")){if(k.data("scrollButtons_scrollType")==="pixels"){if(k.data("horizontalScroll")){v.add(A).unbind("mousedown touchstart MSPointerDown mouseup MSPointerUp mouseout MSPointerOut touchend",i,g);k.data({bindEvent_buttonsContinuous_x:false});if(!k.data("bindEvent_buttonsPixels_x")){v.bind("click",function(x){x.preventDefault();q(Math.abs(p.position().left)+k.data("scrollButtons_scrollAmount"))});A.bind("click",function(x){x.preventDefault();q(Math.abs(p.position().left)-k.data("scrollButtons_scrollAmount"))});k.data({bindEvent_buttonsPixels_x:true})}}else{e.add(w).unbind("mousedown touchstart MSPointerDown mouseup MSPointerUp mouseout MSPointerOut touchend",i,g);k.data({bindEvent_buttonsContinuous_y:false});if(!k.data("bindEvent_buttonsPixels_y")){e.bind("click",function(x){x.preventDefault();q(Math.abs(p.position().top)+k.data("scrollButtons_scrollAmount"))});w.bind("click",function(x){x.preventDefault();q(Math.abs(p.position().top)-k.data("scrollButtons_scrollAmount"))});k.data({bindEvent_buttonsPixels_y:true})}}function q(x){if(!j.data("preventAction")){j.data("preventAction",true);k.mCustomScrollbar("scrollTo",x,{trigger:"internal"})}}}else{if(k.data("horizontalScroll")){v.add(A).unbind("click");k.data({bindEvent_buttonsPixels_x:false});if(!k.data("bindEvent_buttonsContinuous_x")){v.bind("mousedown touchstart MSPointerDown",function(y){y.preventDefault();var x=z();k.data({mCSB_buttonScrollRight:setInterval(function(){k.mCustomScrollbar("scrollTo",Math.abs(p.position().left)+x,{trigger:"internal",scrollEasing:"easeOutCirc"})},17)})});var i=function(x){x.preventDefault();clearInterval(k.data("mCSB_buttonScrollRight"))};v.bind("mouseup touchend MSPointerUp mouseout MSPointerOut",i);A.bind("mousedown touchstart MSPointerDown",function(y){y.preventDefault();var x=z();k.data({mCSB_buttonScrollLeft:setInterval(function(){k.mCustomScrollbar("scrollTo",Math.abs(p.position().left)-x,{trigger:"internal",scrollEasing:"easeOutCirc"})},17)})});var g=function(x){x.preventDefault();clearInterval(k.data("mCSB_buttonScrollLeft"))};A.bind("mouseup touchend MSPointerUp mouseout MSPointerOut",g);k.data({bindEvent_buttonsContinuous_x:true})}}else{e.add(w).unbind("click");k.data({bindEvent_buttonsPixels_y:false});if(!k.data("bindEvent_buttonsContinuous_y")){e.bind("mousedown touchstart MSPointerDown",function(y){y.preventDefault();var x=z();k.data({mCSB_buttonScrollDown:setInterval(function(){k.mCustomScrollbar("scrollTo",Math.abs(p.position().top)+x,{trigger:"internal",scrollEasing:"easeOutCirc"})},17)})});var t=function(x){x.preventDefault();clearInterval(k.data("mCSB_buttonScrollDown"))};e.bind("mouseup touchend MSPointerUp mouseout MSPointerOut",t);w.bind("mousedown touchstart MSPointerDown",function(y){y.preventDefault();var x=z();k.data({mCSB_buttonScrollUp:setInterval(function(){k.mCustomScrollbar("scrollTo",Math.abs(p.position().top)-x,{trigger:"internal",scrollEasing:"easeOutCirc"})},17)})});var f=function(x){x.preventDefault();clearInterval(k.data("mCSB_buttonScrollUp"))};w.bind("mouseup touchend MSPointerUp mouseout MSPointerOut",f);k.data({bindEvent_buttonsContinuous_y:true})}}function z(){var x=k.data("scrollButtons_scrollSpeed");if(k.data("scrollButtons_scrollSpeed")==="auto"){x=Math.round((k.data("scrollInertia")+100)/40)}return x}}}if(k.data("autoScrollOnFocus")){if(!k.data("bindEvent_focusin")){h.bind("focusin",function(){h.scrollTop(0).scrollLeft(0);var x=c(document.activeElement);if(x.is("input,textarea,select,button,a[tabindex],area,object")){var G=p.position().top,y=x.position().top,F=h.height()-x.outerHeight();if(k.data("horizontalScroll")){G=p.position().left;y=x.position().left;F=h.width()-x.outerWidth()}if(G+y<0||G+y>F){k.mCustomScrollbar("scrollTo",y,{trigger:"internal"})}}});k.data({bindEvent_focusin:true})}}if(k.data("autoHideScrollbar")){if(!k.data("bindEvent_autoHideScrollbar")){h.bind("mouseenter",function(x){h.addClass("mCS-mouse-over");d.showScrollbar.call(h.children(".mCSB_scrollTools"))}).bind("mouseleave touchend",function(x){h.removeClass("mCS-mouse-over");if(x.type==="mouseleave"){d.hideScrollbar.call(h.children(".mCSB_scrollTools"))}});k.data({bindEvent_autoHideScrollbar:true})}}},scrollTo:function(e,f){var i=c(this),o={moveDragger:false,trigger:"external",callbacks:true,scrollInertia:i.data("scrollInertia"),scrollEasing:i.data("scrollEasing")},f=c.extend(o,f),p,g=i.children(".mCustomScrollBox"),k=g.children(".mCSB_container"),r=g.children(".mCSB_scrollTools"),j=r.children(".mCSB_draggerContainer"),h=j.children(".mCSB_dragger"),t=draggerSpeed=f.scrollInertia,q,s,m,l;if(!k.hasClass("mCS_no_scrollbar")){i.data({mCS_trigger:f.trigger});if(i.data("mCS_Init")){f.callbacks=false}if(e||e===0){if(typeof(e)==="number"){if(f.moveDragger){p=e;if(i.data("horizontalScroll")){e=h.position().left*i.data("scrollAmount")}else{e=h.position().top*i.data("scrollAmount")}draggerSpeed=0}else{p=e/i.data("scrollAmount")}}else{if(typeof(e)==="string"){var v;if(e==="top"){v=0}else{if(e==="bottom"&&!i.data("horizontalScroll")){v=k.outerHeight()-g.height()}else{if(e==="left"){v=0}else{if(e==="right"&&i.data("horizontalScroll")){v=k.outerWidth()-g.width()}else{if(e==="first"){v=i.find(".mCSB_container").find(":first")}else{if(e==="last"){v=i.find(".mCSB_container").find(":last")}else{v=i.find(e)}}}}}}if(v.length===1){if(i.data("horizontalScroll")){e=v.position().left}else{e=v.position().top}p=e/i.data("scrollAmount")}else{p=e=v}}}if(i.data("horizontalScroll")){if(i.data("onTotalScrollBack_Offset")){s=-i.data("onTotalScrollBack_Offset")}if(i.data("onTotalScroll_Offset")){l=g.width()-k.outerWidth()+i.data("onTotalScroll_Offset")}if(p<0){p=e=0;clearInterval(i.data("mCSB_buttonScrollLeft"));if(!s){q=true}}else{if(p>=j.width()-h.width()){p=j.width()-h.width();e=g.width()-k.outerWidth();clearInterval(i.data("mCSB_buttonScrollRight"));if(!l){m=true}}else{e=-e}}var n=i.data("snapAmount");if(n){e=Math.round(e/n)*n-i.data("snapOffset")}d.mTweenAxis.call(this,h[0],"left",Math.round(p),draggerSpeed,f.scrollEasing);d.mTweenAxis.call(this,k[0],"left",Math.round(e),t,f.scrollEasing,{onStart:function(){if(f.callbacks&&!i.data("mCS_tweenRunning")){u("onScrollStart")}if(i.data("autoHideScrollbar")){d.showScrollbar.call(r)}},onUpdate:function(){if(f.callbacks){u("whileScrolling")}},onComplete:function(){if(f.callbacks){u("onScroll");if(q||(s&&k.position().left>=s)){u("onTotalScrollBack")}if(m||(l&&k.position().left<=l)){u("onTotalScroll")}}h.data("preventAction",false);i.data("mCS_tweenRunning",false);if(i.data("autoHideScrollbar")){if(!g.hasClass("mCS-mouse-over")){d.hideScrollbar.call(r)}}}})}else{if(i.data("onTotalScrollBack_Offset")){s=-i.data("onTotalScrollBack_Offset")}if(i.data("onTotalScroll_Offset")){l=g.height()-k.outerHeight()+i.data("onTotalScroll_Offset")}if(p<0){p=e=0;clearInterval(i.data("mCSB_buttonScrollUp"));if(!s){q=true}}else{if(p>=j.height()-h.height()){p=j.height()-h.height();e=g.height()-k.outerHeight();clearInterval(i.data("mCSB_buttonScrollDown"));if(!l){m=true}}else{e=-e}}var n=i.data("snapAmount");if(n){e=Math.round(e/n)*n-i.data("snapOffset")}d.mTweenAxis.call(this,h[0],"top",Math.round(p),draggerSpeed,f.scrollEasing);d.mTweenAxis.call(this,k[0],"top",Math.round(e),t,f.scrollEasing,{onStart:function(){if(f.callbacks&&!i.data("mCS_tweenRunning")){u("onScrollStart")}if(i.data("autoHideScrollbar")){d.showScrollbar.call(r)}},onUpdate:function(){if(f.callbacks){u("whileScrolling")}},onComplete:function(){if(f.callbacks){u("onScroll");if(q||(s&&k.position().top>=s)){u("onTotalScrollBack")}if(m||(l&&k.position().top<=l)){u("onTotalScroll")}}h.data("preventAction",false);i.data("mCS_tweenRunning",false);if(i.data("autoHideScrollbar")){if(!g.hasClass("mCS-mouse-over")){d.hideScrollbar.call(r)}}}})}if(i.data("mCS_Init")){i.data({mCS_Init:false})}}}function u(w){this.mcs={top:k.position().top,left:k.position().left,draggerTop:h.position().top,draggerLeft:h.position().left,topPct:Math.round((100*Math.abs(k.position().top))/Math.abs(k.outerHeight()-g.height())),leftPct:Math.round((100*Math.abs(k.position().left))/Math.abs(k.outerWidth()-g.width()))};switch(w){case"onScrollStart":i.data("mCS_tweenRunning",true).data("onScrollStart_Callback").call(i,this.mcs);break;case"whileScrolling":i.data("whileScrolling_Callback").call(i,this.mcs);break;case"onScroll":i.data("onScroll_Callback").call(i,this.mcs);break;case"onTotalScrollBack":i.data("onTotalScrollBack_Callback").call(i,this.mcs);break;case"onTotalScroll":i.data("onTotalScroll_Callback").call(i,this.mcs);break}}},stop:function(){var g=c(this),e=g.children().children(".mCSB_container"),f=g.children().children().children().children(".mCSB_dragger");d.mTweenAxisStop.call(this,e[0]);d.mTweenAxisStop.call(this,f[0])},disable:function(e){var j=c(this),f=j.children(".mCustomScrollBox"),h=f.children(".mCSB_container"),g=f.children(".mCSB_scrollTools"),i=g.children().children(".mCSB_dragger");f.unbind("mousewheel focusin mouseenter mouseleave touchend");h.unbind("touchstart touchmove");if(e){if(j.data("horizontalScroll")){i.add(h).css("left",0)}else{i.add(h).css("top",0)}}g.css("display","none");h.addClass("mCS_no_scrollbar");j.data({bindEvent_mousewheel:false,bindEvent_focusin:false,bindEvent_content_touch:false,bindEvent_autoHideScrollbar:false}).addClass("mCS_disabled")},destroy:function(){var e=c(this);e.removeClass("mCustomScrollbar _mCS_"+e.data("mCustomScrollbarIndex")).addClass("mCS_destroyed").children().children(".mCSB_container").unwrap().children().unwrap().siblings(".mCSB_scrollTools").remove();c(document).unbind("mousemove."+e.data("mCustomScrollbarIndex")+" mouseup."+e.data("mCustomScrollbarIndex")+" MSPointerMove."+e.data("mCustomScrollbarIndex")+" MSPointerUp."+e.data("mCustomScrollbarIndex"));c(window).unbind("resize."+e.data("mCustomScrollbarIndex"))}},d={showScrollbar:function(){this.stop().animate({opacity:1},"fast")},hideScrollbar:function(){this.stop().animate({opacity:0},"fast")},mTweenAxis:function(g,i,h,f,o,y){var y=y||{},v=y.onStart||function(){},p=y.onUpdate||function(){},w=y.onComplete||function(){};var n=t(),l,j=0,r=g.offsetTop,s=g.style;if(i==="left"){r=g.offsetLeft}var m=h-r;q();e();function t(){if(window.performance&&window.performance.now){return window.performance.now()}else{if(window.performance&&window.performance.webkitNow){return window.performance.webkitNow()}else{if(Date.now){return Date.now()}else{return new Date().getTime()}}}}function x(){if(!j){v.call()}j=t()-n;u();if(j>=g._time){g._time=(j>g._time)?j+l-(j-g._time):j+l-1;if(g._time<j+1){g._time=j+1}}if(g._time<f){g._id=_request(x)}else{w.call()}}function u(){if(f>0){g.currVal=k(g._time,r,m,f,o);s[i]=Math.round(g.currVal)+"px"}else{s[i]=h+"px"}p.call()}function e(){l=1000/60;g._time=j+l;_request=(!window.requestAnimationFrame)?function(z){u();return setTimeout(z,0.01)}:window.requestAnimationFrame;g._id=_request(x)}function q(){if(g._id==null){return}if(!window.requestAnimationFrame){clearTimeout(g._id)}else{window.cancelAnimationFrame(g._id)}g._id=null}function k(B,A,F,E,C){switch(C){case"linear":return F*B/E+A;break;case"easeOutQuad":B/=E;return -F*B*(B-2)+A;break;case"easeInOutQuad":B/=E/2;if(B<1){return F/2*B*B+A}B--;return -F/2*(B*(B-2)-1)+A;break;case"easeOutCubic":B/=E;B--;return F*(B*B*B+1)+A;break;case"easeOutQuart":B/=E;B--;return -F*(B*B*B*B-1)+A;break;case"easeOutQuint":B/=E;B--;return F*(B*B*B*B*B+1)+A;break;case"easeOutCirc":B/=E;B--;return F*Math.sqrt(1-B*B)+A;break;case"easeOutSine":return F*Math.sin(B/E*(Math.PI/2))+A;break;case"easeOutExpo":return F*(-Math.pow(2,-10*B/E)+1)+A;break;case"mcsEaseOut":var D=(B/=E)*B,z=D*B;return A+F*(0.499999999999997*z*D+-2.5*D*D+5.5*z+-6.5*D+4*B);break;case"draggerRailEase":B/=E/2;if(B<1){return F/2*B*B*B+A}B-=2;return F/2*(B*B*B+2)+A;break}}},mTweenAxisStop:function(e){if(e._id==null){return}if(!window.requestAnimationFrame){clearTimeout(e._id)}else{window.cancelAnimationFrame(e._id)}e._id=null},rafPolyfill:function(){var f=["ms","moz","webkit","o"],e=f.length;while(--e>-1&&!window.requestAnimationFrame){window.requestAnimationFrame=window[f[e]+"RequestAnimationFrame"];window.cancelAnimationFrame=window[f[e]+"CancelAnimationFrame"]||window[f[e]+"CancelRequestAnimationFrame"]}}};d.rafPolyfill.call();c.support.touch=!!("ontouchstart" in window);c.support.msPointer=window.navigator.msPointerEnabled;var a=("https:"==document.location.protocol)?"https:":"http:";c.event.special.mousewheel||document.write('<script src="'+a+'//cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.0.6/jquery.mousewheel.min.js"><\/script>');c.fn.mCustomScrollbar=function(e){if(b[e]){return b[e].apply(this,Array.prototype.slice.call(arguments,1))}else{if(typeof e==="object"||!e){return b.init.apply(this,arguments)}else{c.error("Method "+e+" does not exist")}}}})(jQuery);
(function(a){a.tiny=a.tiny||{};a.tiny.carousel={options:{start:1,display:1,axis:"x",controls:true,pager:false,interval:false,intervaltime:3000,rewind:false,animation:true,duration:1000,callback:null}};a.fn.tinycarousel_start=function(){a(this).data("tcl").start()};a.fn.tinycarousel_stop=function(){a(this).data("tcl").stop()};a.fn.tinycarousel_move=function(c){a(this).data("tcl").move(c-1,true)};function b(q,e){var i=this,h=a(".viewport:first",q),g=a(".overview:first",q),k=g.children(),f=a(".next:first",q),d=a(".prev:first",q),l=a(".pager:first",q),w=0,u=0,p=0,j=undefined,o=false,n=true,s=e.axis==="x";function m(){if(e.controls){d.toggleClass("disable",p<=0);f.toggleClass("disable",!(p+1<u))}if(e.pager){var x=a(".pagenum",l);x.removeClass("active");a(x[p]).addClass("active")}}function v(x){if(a(this).hasClass("pagenum")){i.move(parseInt(this.rel,10),true)}return false}function t(){if(e.interval&&!o){clearTimeout(j);j=setTimeout(function(){p=p+1===u?-1:p;n=p+1===u?false:p===0?true:n;i.move(n?1:-1)},e.intervaltime)}}function r(){if(e.controls&&d.length>0&&f.length>0){d.click(function(){i.move(-1);return false});f.click(function(){i.move(1);return false})}if(e.interval){q.hover(i.stop,i.start)}if(e.pager&&l.length>0){a("a",l).click(v)}}this.stop=function(){clearTimeout(j);o=true};this.start=function(){o=false;t()};this.move=function(y,z){p=z?y:p+=y;if(p>-1&&p<u){var x={};x[s?"left":"top"]=-(p*(w*e.display));g.animate(x,{queue:false,duration:e.animation?e.duration:0,complete:function(){if(typeof e.callback==="function"){e.callback.call(this,k[p],p)}}});m();t()}};function c(){w=s?a(k[0]).outerWidth(true):a(k[0]).outerHeight(true);var x=Math.ceil(((s?h.outerWidth():h.outerHeight())/(w*e.display))-1);u=Math.max(1,Math.ceil(k.length/e.display)-x);p=Math.min(u,Math.max(1,e.start))-2;g.css(s?"width":"height",(w*k.length));i.move(1);r();return i}return c()}a.fn.tinycarousel=function(d){var c=a.extend({},a.tiny.carousel.options,d);this.each(function(){a(this).data("tcl",new b(a(this),c))});return this}}(jQuery));
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

    // Change Results Text
    if (jQuery('.clubplugin.standings').length > 0) {
        jQuery('.clubplugin.standings .gespeeld').html('G');
        jQuery('.clubplugin.standings .punten').html('P');
        jQuery('.clubplugin.standings .winst').html('W');
        jQuery('.clubplugin.standings .verlies').html('V');
        jQuery('.clubplugin.standings .gelijk').html('G');
        jQuery('.clubplugin.standings .voor').html('V');
        jQuery('.clubplugin.standings .tegen').html('T');
        jQuery('.clubplugin.standings .saldo').html('S');
        jQuery('.clubplugin.standings .mindering').html('M');
    }

    // Set Youtube Widget Slider
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

    jQuery("div#match_info_program").live({
        click: function () {
            jQuery('ol.clubplugin.program li.widget').removeClass('active');
            jQuery('ul.row li.schedule div#match_info_program').html('');
            jQuery('ul.row li.schedule #match_info_program').css('display', 'none');
        }
    });

    // Get Match Results Info
    jQuery('ol.clubplugin.results li.widget').click(function () {

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

    jQuery("div#match_info_results").live({
        click: function () {
            jQuery('ol.clubplugin.results li.widget').removeClass('active');
            jQuery('ul.row li.results div#match_info_results').html('');
            jQuery('ul.row li.results #match_info_results').css('display', 'none');
        }
    });

    // Set News Dropdown on Full Width
    jQuery('.dropdown_button').click(function () {
        // Set Dropdown width Addition
        var dropdown_width_addition = 34;
        var dropdown_width = jQuery(this).width() + dropdown_width_addition;
        jQuery('#newsDropdown ul').css('width', dropdown_width + 'px');
        jQuery('#newsDropdown ul').css('max-width', dropdown_width + 'px');
        jQuery('#newsDropdown ul').css('min-width', dropdown_width + 'px');
    });

    // Login Profiles
    jQuery('.openProfiles').click(function () {
        jQuery('.l-wrapper--profiles').show();
        jQuery(this).hide();
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

    jQuery('.menu_button').click(function () {
        jQuery('#full-container').toggleClass('off-canvas');
    });

});