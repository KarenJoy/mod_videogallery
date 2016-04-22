var hap_player;  
			jQuery(document).ready(function($) {
				
				var vplp_settings = {
					/* mediaId: unique string for player identification (if multiple player instances were used, then strings need to be different!) */
					mediaId:'player1',
					/* useDeeplink: true, false */
					useDeeplink:false,
					/*activePlaylist: Active playlist to start with. If no deeplink is used, enter element 'id' attribute, or if deeplink is used enter element data-address attribute. */
					activePlaylist:'playlist1',
					/*activeItem: Active video to start with. Enter number, -1 = no video loaded, 0 = first video, 1 = second video etc */
					activeItem:0,
					
					/*autoHideControls: auto hide player controls on mouse out: true/false. Defaults to false on mobile. */
					autoHideControls:false,
					/*controlsTimeout: time after which controls and playlist hides in fullscreen if screen is inactive, in miliseconds. */
					controlsTimeout:3000,
					/*defaultVolume: 0-1 */
					defaultVolume:0.5,
					/*autoPlay: true/false (defaults to false on mobile)*/
					autoPlay:true,
					/*randomPlay: true/false */
					randomPlay:false,
					/* loopingOn: on playlist end rewind to beginning (last item in playlist) */
					loopingOn: true,
					/*autoAdvanceToNextVideo: true/false (use this to loop video) */
					autoAdvanceToNextVideo:true,
					/*autoOpenDescription: true/false  */
					autoOpenDescription:false,
					/*useLivePreview: true/false (if true, you need small videos for preview for local videos, otherwise thumbnails). Defaults to false on mobile. */
					useLivePreview:true,
					
					/* showControlsInAdvert: true/false (show controls while video advert plays)  */
					showControlsInAdvert:true,
					/* disableSeekbarInAdvert: true/false (disable seekbar while video advert plays)  */
					disableSeekbarInAdvert:true,
					/* showSkipButtonInAdvert: true/false (show skip button while video advert plays)  */
					showSkipButtonInAdvert:true,
					advertSkipBtnText:'SKIP AD >',
					advertSkipVideoText:'You can skip to video in',
					
					/* contextMenuType: disabled, custom, default */
					contextMenuType:'custom',
					/* contextMenuText: Custom text link in context menu. Leave empty for none.  */
					contextMenuText:'@Your Company Title',
					/* contextMenuLink: url link, leave empty for none  */
					contextMenuLink:'http://codecanyon.net/user/Tean',
					/* contextMenuTarget: _blank/_parent (opens in new/same window)  */
					contextMenuTarget:'_blank',
					
					logoPath: 'http://www.interactivepixel.net/images/jqueryPreviews/helper/apvplp_logo.png',
				    logoPosition: 'tl',/* tl, tr, bl, br */
				    logoXOffset: 5,
				    logoYOffset: 5,
				    logoTooltipText: '@Your Company',
				    logoUrl: 'http://www.google.com',
				    logoTarget: '_blank',
					
					/*aspectRatio: video aspect ratio (0 = original, 1 = fit inside, 2 = fit outside). Defaults to 1 on mobile! */
					aspectRatio: 2,
					/*playlistOrientation: vertical/horizontal  */
					playlistOrientation:'vertical',
					/*playlistType: list/wall/wall_popup */
					playlistType:'list',
					showPlaylist:true,
					/*scrollType: scroll/buttons  */
					scrollType:'buttons',
					/*wallPath: folder replacement path for the wall data */
					wallPath:'/wall/',
					ytAppId:'AIzaSyDeqvaVCC5GEldPL1uOpI04h9sFoeH7WlY',
					useShare: true,
					/*fsAppId: facebook application id (if you use facebook share, https://developers.facebook.com/apps) */
					fsAppId:'644413448983338',
					/*dropdownId: id attribute of the element with holds the dropdown */
					dropdownId:'#hap_drop',
					/*playlistList: dom element which holds list of playlists */
					playlistList:'#playlist_list',
					buttonsUrl: {thumbnailPreloaderUrl: 'data/loading.gif'},
					/* autoReuseMailForDownload: true/false. download backup for ios, save email after client first enters email address and auto send all emails to the same address */
					autoReuseMailForDownload: true,
					useTooltips:true,
				};
				
				hap_player = $('#mainWrapper').vplp(vplp_settings);
				//initDemo($);
			});
			
			var api_panel, api_panel_inited, api_panel_inner, toggle_api_panel, api_panel_opened, tgtime = 300;
			function initDemo($){
				
				if(isMobile) {
					$('#api_panel').remove();
					$('.toggle_panel').remove();
					return;	
				}
				
				api_panel = $('#api_panel').css('display', 'block');
				api_panel_inner = $('#api_panel_inner');
				api_panel.css('right', -api_panel_inner.outerWidth(true)-20+'px');
				
				toggle_api_panel = $('.toggle_panel').css({cursor:'pointer', display:'block'}).bind('click', function(){
					if(api_panel_opened){
						api_panel.stop().animate({ 'right': -api_panel_inner.outerWidth(true)-20+'px'},  {duration: tgtime, complete: function(){
							api_panel.css('display','none');
						}});
						toggle_api_panel.removeClass().addClass('toggle_panel');
						api_panel_opened=false;	
					}else{
						api_panel.css('display','block').stop().animate({ 'right': 41+'px'},  {duration: tgtime});
						toggle_api_panel.removeClass().addClass('toggle_panel_close');
						api_panel_opened=true;
					}
					return false;	
				});
				api_panel_inited=true;
			}