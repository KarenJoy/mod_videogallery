<?php

class DZSVideoGallery {

    public $thepath;
    public $slider_index = 0;
    public $sliders_index = 0;
    public $index_players = 0;
    public $cats_index = 0;
    public $the_shortcode = 'videogallery';
    public $capability_user = 'read';
    public $capability_admin = 'manage_options';
    public $dbitemsname = 'zsvg_items';
    public $dbvpconfigsname = 'zsvg_vpconfigs';
    public $dboptionsname = 'zsvg_options';
    public $dbdcname = 'zsvg_options_dc';
    public $dbs = array();
    public $dbdbsname = 'zsvg_dbs';
    public $currDb = '';
    public $currSlider = '';
    public $mainitems;

    private $arr_api_errors = array();

    public $mainoptions;
    public $mainoptions_dc;
    public $mainvpconfigs;
    public $mainoptions_default;
    public $pluginmode = "plugin";
    public $alwaysembed = "on";
    public $httpprotocol = 'https';
    public $adminpagename = 'dzsvg_menu';
    public $adminpagename_configs = 'dzsvg-vpc';
    public $adminpagename_designercenter = 'dzsvg-dc';
    public $adminpagename_mainoptions = 'dzsvg-mo';
    public $adminpagename_autoupdater = 'dzsvg-autoupdater';
    private $usecaching = true;
    private $addons_dzsvp_activated = false;



    function __construct() {
        if ($this->pluginmode == 'theme') {
            $this->thepath = THEME_URL.'plugins/dzs-videogallery/';
        } else {
            $this->thepath = plugins_url('',__FILE__).'/';
        }




        $currDb = '';
        if (isset($_GET['dbname'])) {
            $this->currDb = $_GET['dbname'];
            $currDb = $_GET['dbname'];
        }


        if (isset($_GET['currslider'])) {
            $this->currSlider = $_GET['currslider'];
        } else {
            $this->currSlider = 0;
        }




        $this->dbs = get_option($this->dbdbsname);
        //$this->dbs = '';
        if ($this->dbs == '') {
            $this->dbs = array('main');
            update_option($this->dbdbsname,$this->dbs);
        }
        if (is_array($this->dbs) && !in_array($currDb,$this->dbs) && $currDb != 'main' && $currDb != '') {
            array_push($this->dbs,$currDb);
            update_option($this->dbdbsname,$this->dbs);
        }
        //echo 'ceva'; print_r($this->dbs);
        if ($currDb != 'main' && $currDb != '') {
            $this->dbitemsname.='-'.$currDb;
        }

        $this->mainitems = get_option($this->dbitemsname);
        if ($this->mainitems == '') {
            $mainitems_default_ser = file_get_contents(dirname(__FILE__).'/sampledata/defaultmainitems.txt');
            $this->mainitems = unserialize($mainitems_default_ser);
            update_option($this->dbitemsname,$this->mainitems);
        }

        $this->mainvpconfigs = get_option($this->dbvpconfigsname);
        //cho 'ceva'.is_array($this->mainvpconfigs);
        if ($this->mainvpconfigs == '' || (is_array($this->mainvpconfigs) && count($this->mainvpconfigs) == 0)) {
            //echo 'ceva';
            $this->mainvpconfigs = array();
            $aux = 'a:2:{i:0;a:1:{s:8:"settings";a:13:{s:2:"id";s:17:"skinauroradefault";s:12:"skin_html5vp";s:11:"skin_aurora";s:22:"settings_video_overlay";s:3:"off";s:29:"html5design_controlsopacityon";s:1:"1";s:30:"html5design_controlsopacityout";s:1:"1";s:13:"defaultvolume";s:0:"";s:17:"youtube_sdquality";s:5:"small";s:17:"youtube_hdquality";s:5:"hd720";s:22:"youtube_defaultquality";s:2:"hd";s:13:"yt_customskin";s:2:"on";s:12:"vimeo_byline";s:1:"0";s:14:"vimeo_portrait";s:1:"0";s:11:"vimeo_color";s:0:"";}}i:1;a:1:{s:8:"settings";a:13:{s:2:"id";s:10:"skincustom";s:12:"skin_html5vp";s:11:"skin_custom";s:22:"settings_video_overlay";s:3:"off";s:29:"html5design_controlsopacityon";s:1:"1";s:30:"html5design_controlsopacityout";s:1:"1";s:13:"defaultvolume";s:0:"";s:17:"youtube_sdquality";s:5:"small";s:17:"youtube_hdquality";s:5:"hd720";s:22:"youtube_defaultquality";s:2:"hd";s:13:"yt_customskin";s:2:"on";s:12:"vimeo_byline";s:1:"0";s:14:"vimeo_portrait";s:1:"0";s:11:"vimeo_color";s:0:"";}}}';
            $this->mainvpconfigs = unserialize($aux);
            //print_r($this->mainvpconfigs);
            //$this->mainitems = array();
            update_option($this->dbvpconfigsname,$this->mainvpconfigs);
        }
        $vpconfigsstr = '';
        foreach ($this->mainvpconfigs as $vpconfig) {
            //print_r($vpconfig);
            $vpconfigsstr .='<option value="'.$vpconfig['settings']['id'].'">'.$vpconfig['settings']['id'].'</option>';
        }




        $this->mainoptions = get_option($this->dboptionsname);

        $this->mainoptions_default =  array(
            'usewordpressuploader' => 'on',
            'embed_masonry' => 'on',
            'is_safebinding' => 'on',
            'disable_api_caching' => 'off',
            'disable_fontawesome' => 'off',
            'debug_mode' => 'off',
            'youtube_api_key' => 'AIzaSyCtrnD7ll8wyyro5f1LitPggaSKvYFIvU4',
            'vimeo_api_user_id' => '',
            'vimeo_api_client_id' => '',
            'vimeo_api_client_secret' => '',
            'vimeo_api_access_token' => '',
            'vimeo_api_access_token_secret' => '',
            'always_embed' => 'off',
            'extra_css' => '',
            'use_external_uploaddir' => 'off',
            'admin_close_otheritems' => 'on',
            'admin_enable_for_users' => 'off',
            'force_file_get_contents' => 'off',
            'vimeo_thumb_quality' => 'low',
            'replace_jwplayer' => 'off',
            'replace_wpvideo' => 'off',
            'tinymce_enable_preview_shortcodes' => 'off',
            'settings_trigger_resize' => 'off',
            'settings_limit_notice_dismissed' => 'off',
            'translate_skipad' => 'Skip Ad',
            'dzsvg_purchase_code' => '',
            'dzsvg_purchase_code_binded' => 'off',
            'dzsvp_video_config' => 'default',
            'dzsvp_enable_likes' => 'on',
            'dzsvp_enable_ratings' => 'off',
            'dzsvp_enable_viewcount' => 'off',
            'dzsvp_enable_likescount' => 'off',
            'dzsvp_enable_ratingscount' => 'off',
            'dzsvp_enable_visitorupload' => 'off',
            'dzsvp_tab_share_content' => '<span class="share-icon-active"><iframe src="//www.facebook.com/plugins/like.php?href={{currurl}}&amp;width&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=21&amp;appId=569360426428348" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe></span>
<span class="share-icon-active"><div class="g-plusone" data-size="medium"></div></span>
<span class="share-icon-active"><a href="https://twitter.com/share" class="twitter-share-button" data-via="ZoomItFlash">Tweet</a></span><h5>Embed</h5><div class="dzsvp-code">{{embedcode}}</div>
<script type="text/javascript">
  (function() {
    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
    po.src = "https://apis.google.com/js/platform.js";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
  })();
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs");</script>',
            'dzsvp_enable_tab_playlist' => 'on',
            'dzsvp_enable_facebooklogin' => 'off',
            'dzsvp_facebook_loginappid' => '',
            'dzsvp_facebook_loginsecret' => '',
            'dzsvp_page_upload' => '',
            'dzsvp_categories_rewrite' => 'video_categories',
        );


        //==== default opts / inject into db
        if ($this->mainoptions == '') {
            $this->mainoptions = $this->mainoptions_default;
            update_option($this->dboptionsname,$this->mainoptions);
        }

//        print_r($defaultOpts); print_r($this->mainoptions);
        $this->mainoptions = array_merge($this->mainoptions_default,$this->mainoptions);
        //print_r($this->mainoptions);
        //===translation stuff
        load_plugin_textdomain('dzsvg',false,basename(dirname(__FILE__)).'/languages');





        $def_options_dc = array(
            'background' => '#111111',
            'controls_background' => '#333333',
            'scrub_background' => '#333333',
            'scrub_buffer' => '#555555',
            'controls_color' => '#aaaaaa',
            'controls_hover_color' => '#dddddd',
            'controls_highlight_color' => '#db4343',
            'thumbs_bg' => '#333333',
            'thumbs_active_bg' => '#777777',
            'thumbs_text_color' => '#eeeeee',
            'timetext_curr_color' => '#ffffff',
            'thumbnail_image_width' => '',
            'thumbnail_image_height' => '',
        );
        $this->mainoptions_dc = get_option($this->dbdcname);

        //==== default opts / inject into db
        if ($this->mainoptions_dc == '') {
            $this->mainoptions_dc = $def_options_dc;
            update_option($this->dbdcname,$this->mainoptions_dc);
        }


        $this->post_options();



        if (isset($_POST['deleteslider'])) {
            //print_r($this->mainitems);
            if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename) {
                unset($this->mainitems[$_POST['deleteslider']]);
                $this->mainitems = array_values($this->mainitems);
                $this->currSlider = 0;
                //print_r($this->mainitems);
                update_option($this->dbitemsname,$this->mainitems);
            }


            if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename_configs) {
                unset($this->mainvpconfigs[$_POST['deleteslider']]);
                $this->mainvpconfigs = array_values($this->mainvpconfigs);
                $this->currSlider = 0;
                //print_r($this->mainitems);
                update_option($this->dbvpconfigsname,$this->mainvpconfigs);
            }
        }

        if (isset($_POST['dzsvg_duplicateslider'])) {
            if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename) {
                $aux = ($this->mainitems[$_POST['dzsvg_duplicateslider']]);
                array_push($this->mainitems,$aux);
                $this->mainitems = array_values($this->mainitems);
                $this->currSlider = count($this->mainitems) - 1;
                update_option($this->dbitemsname,$this->mainitems);
            }
        }

        //echo get_admin_url('', 'options-general.php?page=' . $this->adminpagename) . dzs_curr_url();
        //echo $newurl;

        $uploadbtnstring = '<button class="button-secondary action upload_file">Upload</button>';



        if ($this->mainoptions['usewordpressuploader'] != 'on') {
            $uploadbtnstring = '<div class="dzs-upload">
<form name="upload" action="#" method="POST" enctype="multipart/form-data">
<input type="button" value="Upload" class="btn_upl"/>
<input type="file" name="file_field" class="file_field"/>
<input type="submit" class="btn_submit"/>
</form>
</div>
<div class="feedback"></div>';
        }

        ///==== important: settings must have the class mainsetting
        $this->sliderstructure = '<div class="slider-con" style="display:none;">
        <div class="setting type_all">
            <div class="setting-label">'.__('Select Feed Mode','dzsvg').'</div>
                <div class="main-feed-chooser select-hidden-metastyle">
                <select class="textinput mainsetting" name="0-settings-feedfrom">
                    <option value="normal">'.__('Normal','dzsvg').'</option>
                    <option value="ytuserchannel">'.__('Youtube User Channel','dzsvg').'</option>
                    <option value="ytplaylist">'.__('YouTube Playlist','dzsvg').'</option>
                    <option value="ytkeywords">'.__('YouTube Keywords','dzsvg').'</option>
                    <option value="vmuserchannel">'.__('Vimeo User Channel','dzsvg').'</option>
                    <option value="vmchannel">'.__('Vimeo Channel','dzsvg').'</option>
                    <option value="vmalbum">'.__('Vimeo Album','dzsvg').'</option>
                </select>
                <div class="option-con clearfix">
                    <div class="an-option">
                    <div class="an-title">
                    '.__('Normal','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Feed from custom items you set below.','dzsvg').'
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('Youtube User Channel','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__(' Feed videos from your YouTube User Channel.','dzsvg').'
                   
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('YouTube Playlist','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Feed videos from the YouTube Playlist you create on their site. Just input the Playlist ID below.','dzsvg').'
                    
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('YouTube Keywords','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Feed videos by searching for keywords ie. <strong>funny cat</strong>','dzsvg').'
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('Vimeo User Channel','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Feed videos from your Vimeo User channel.','dzsvg').'
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('Vimeo Channel','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Feed videos from a Vimeo Channel.','dzsvg').'
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('Vimeo Album','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Feed videos from a Vimeo Album.','dzsvg').'
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="settings-con">
        <h4>'.__('General Options','dzsvg').'</h4>
        <div class="setting type_all">
            <div class="setting-label">'.__('ID','dzsvg').'</div>
            <input type="text" class="textinput mainsetting main-id" name="0-settings-id" value="default"/>
            <div class="sidenote">'.__('Choose an unique id. Do not use spaces, do not use special characters.','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Width','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-width" value="100%"/>
            <div class="sidenote">'.__('Leave "100%" for responsive mode. ','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Height','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-height" value="300"/>
        </div>
        <div class="setting styleme">
            <div class="setting-label">'.__('Display Mode','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-displaymode">
                <option>normal</option>
                <option>wall</option>
                <option>rotator</option>
                <option>rotator3d</option>
                <option>alternatemenu</option>
                <option>alternatewall</option>
            </select>
            <div class="sidenote">'.__('<strong>alternatewall</strong> and <strong>alternatemenu</strong> are deprecated.','dzsvg').'</div>
        </div>
        <div class="setting styleme">
            <div class="setting-label">'.__('Video Gallery Skin','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-skin_html5vg">
                <option>skin_default</option>
                <option>skin_navtranparent</option>
                <option>skin_pro</option>
                <option>skin_custom</option>
            </select>
            <div class="sidenote">'.__('Skin Custom can be modified via Designer Center.','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Navigation Style','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-nav_type">
                <option>thumbs</option>
                <option>thumbsandarrows</option>
                <option>scroller</option>
                <option>outer</option>
                <option>none</option>
            </select>
            <div class="sidenote">'.__('Choose a navigation style for the normal display mode.','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Menu Position','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-menuposition">
                <option>right</option>
                <option>bottom</option>
                <option>left</option>
                <option>top</option>
                <option>none</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Autoplay','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-autoplay">
                <option value="on">'.__('on','dzsvg').'</option>
                <option value="off">'.__('off','dzsvg').'</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Autoplay Next','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-autoplaynext">
                <option value="on">'.__('on','dzsvg').'</option>
                <option value="off">'.__('off','dzsvg').'</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Cue First Video','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-cueFirstVideo">
                <option value="on">'.__('on','dzsvg').'</option>
                <option value="off">'.__('off','dzsvg').'</option>
            </select>
            <div class="sidenote">'.__('Choose if the video should load at start or it should activate on click ( if a <strong>Cover Image</strong> is set ).','dzsvg').'</div>
            
        </div>
        <div class="setting">
            <div class="setting_label">'.__('Cover Image','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-coverImage" value=""/>'.$uploadbtnstring.'
                <div class="sidenote">A image that appears while the video is cued / not played</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Randomize / Shuffle Elements','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-randomize">
                <option value="off">'.__('off','dzsvg').'</option>
                <option value="on">'.__('on','dzsvg').'</option>
            </select>
        </div>
        <div class="setting">
            <div class="setting-label">'.__('Background','dzsvg').'</div>
            <input type="text" class="textinput mainsetting with-colorpicker" name="0-settings-bgcolor" value="#111111"/><div class="picker-con"><div class="the-icon"></div><div class="picker"></div></div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Enable Shadow','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-shadow">
                <option value="off">'.__('off','dzsvg').'</option>
                <option value="on">'.__('on','dzsvg').'</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Order','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-order">
                <option value="ASC">'.__('ascending','dzsvg').'</option>
                <option value="DESC">'.__('descending','dzsvg').'</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Play Order','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-playorder">
                <option value="ASC">'.__('normal','dzsvg').'</option>
                <option value="DESC">'.__('reverse','dzsvg').'</option>
            </select>
            <div class="sidenote" style="">'.__('set to reverse for example to play the latest episode in a series first ... or for RTL configurations','dzsvg').'</div>
        </div>
        <div class="setting">
            <div class="setting_label">'.__('Logo','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-logo" value=""/>'.$uploadbtnstring.'
        </div>
        <div class="setting">
            <div class="setting_label">'.__('Logo Link','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-logoLink" value=""/>
        </div>
        
        <div class="setting type_all">
            <div class="setting-label">'.__('Video Player Configuration','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-vpconfig">
                <option value="default">'.__('default','dzsvg').'</option>
                '.$vpconfigsstr.'
            </select>
            <div class="sidenote" style="">'.__('setup these inside the <strong>Video Player Configs</strong> admin','dzsvg').'</div>
        </div>
        

        <div class="setting type_all">
            <div class="setting-label">'.__('Enable Underneath Description','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-enableunderneathdescription">
                <option value="off">'.__('off','dzsvg').'</option>
                <option value="on">'.__('on','dzsvg').'</option>
            </select>
            <div class="sidenote" style="">'.__('add a title and description holder underneath the gallery','dzsvg').'</div>
        </div>

        <div class="setting type_all">
            <div class="setting-label">'.__('Enable Search Field','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-enable_search_field">
                <option value="off">'.__('off','dzsvg').'</option>
                <option value="on">'.__('on','dzsvg').'</option>
            </select>
            <div class="sidenote" style="">'.__('enable a search field inside the gallery','dzsvg').'</div>
        </div>


        <div class="setting type_all">
            <div class="setting-label">'.__('Autoplay Ad','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-autoplay_ad">
                <option value="on">'.__('on','dzsvg').'</option>
                <option value="off">'.__('off','dzsvg').'</option>
            </select>
            <div class="sidenote" style="">'.__('autoplay the ad before a video or not - note that if the video autoplay then the ad will autoplay too before','dzsvg').'</div>
        </div>


        <hr/>
<div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">'.__('Social Options','dzsvg').'</div>
<div class="toggle-content">

        <div class="setting type_all">
            <div class="setting-label">'.__('Share Button','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-sharebutton">
                <option value="off">'.__('off','dzsvg').'</option>
                <option value="on">'.__('on','dzsvg').'</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting_label">'.__('Facebook Link','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-facebooklink" value=""/>
            <div class="sidenote" style="">'.__('input here a full link to your facebook page ie. <strong><a href="https://www.facebook.com/digitalzoomstudio">https://www.facebook.com/digitalzoomstudio</a></strong>','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting_label">'.__('Twitter Link','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-twitterlink" value=""/>
        </div>
        <div class="setting type_all">
            <div class="setting_label">'.__('Google Plus Link','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-googlepluslink" value=""/>
        </div>
        <div class="setting type_all">
            <div class="setting_label">'.__('Extra Social HTML','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-social_extracode" value=""/>
            <div class="sidenote" style="">'.__('you can have here some extra social icons','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Embed Button','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-embedbutton">
                <option value="off">'.__('off','dzsvg').'</option>
                <option value="on">'.__('on','dzsvg').'</option>
            </select>
        </div>
</div>
</div>
<div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">'.__('Design Options','dzsvg').'</div>
<div class="toggle-content">
        <div class="setting type_all">
            <div class="setting-label">'.__('Force Video Height','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-forcevideoheight" value=""/>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Design Menu Item Width','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-html5designmiw" value="275"/>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Design Menu Item Height','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-html5designmih" value="76"/>
            <div class="sidenote" style="">'.__('these also control the width and height for wall items ( for auto height leave blank here, on wall mode )','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Design Menu Item Space','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-html5designmis" value="0"/>
        </div>

        <div class="setting type_all">
            <div class="setting-label">'.__('Thumbnail Extra Classes','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-thumb_extraclass" value=""/>
            <div class="sidenote" style="">'.__('add a special class to the thumbnail like <strong>thumb-round</strong> for making the thumbnails rounded','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Navigation Space','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-nav_space" value="0"/>
            <div class="sidenote" style="">'.__('space between navigation and video container','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Disable Menu Title','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-disable_title">
                <option>off</option>
                <option>on</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Disable Video Title','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-disable_video_title">
                <option>off</option>
                <option>on</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Disable Menu Description','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-disable_menu_description">
                <option>off</option>
                <option>on</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Enable Easing on Menu','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-design_navigationuseeasing">
                <option>off</option>
                <option>on</option>
            </select>
                <div class="sidenote" style="">'.__('for navigation type <strong>thumbs</strong> - use a easing on mouse tracking ','dzsvg').'</div>
        </div>
        

        <div class="setting type_all">
            <div class="setting-label">'.__('Laptop Skin','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-laptopskin">
                <option value="off">'.__('off','dzsvg').'</option>
                <option value="on">'.__('on','dzsvg').'</option>
            </select>
                <div class="sidenote" style="">'.__('apply a laptop container to the gallery','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Layout for Mode Wall','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-mode_wall_layout">
                <option value="none">'.__('None','dzsvg').'</option>
                <option value="layout-3-cols-15-margin">'.__('3 columns','dzsvg').'</option>
                <option value="layout-4-cols-10-margin">'.__('4 columns','dzsvg').'</option>
            </select>
                <div class="sidenote" style="">'.__('the layout for the wall mode. using none will use the Design Menu Item Width and Design Menu Item Height for the item dimensions','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Transition','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-html5transition">
                <option>slideup</option>
                <option>fade</option>
            </select>
        </div>
</div>
</div>


<div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">'.__('Description Options','dzsvg').'</div>
<div class="toggle-content">
        <div class="sidenote" style="font-size:14px;">'.__('some options regarding YouTube feed mode - playlist / user channel / ','dzsvg').'</div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Max Description Length','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-maxlen_desc" value="100"/>
            <div class="sidenote" style="">'.__('youtube video descriptions will be retrieved through YouTube Data API. You can choose here the number of characters to retrieve from it. ' ,'dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Strip HTML Tags','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-striptags">
                <option value="on">'.__('on','dzsvg').'</option>
                <option value="off">'.__('off','dzsvg').'</option>
                </select>
            <div class="sidenote" style="">'.__('video descriptions will be retrieved as html rich content. you can choose to strip the html tags to leave just simple text ' ,'dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Repair HTML Markup','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-try_to_close_unclosed_tags">
                <option value="on">'.__('on','dzsvg').'</option>
                <option value="off">'.__('off','dzsvg').'</option>
                </select>
            <div class="sidenote" style="">'.__('video descriptions will be retrieved as html rich content, some may be broken after shortage. attempt to repair this by setting this to <strong>on</strong>' ,'dzsvg').'</div>
        </div>';




                                $lab = '0-settings-desc_different_settings_for_aside';
//                                echo DZSHelpers::generate_input_text($lab,array('id' => $lab, 'val' => 'off','input_type'=>'hidden'));
                                $this->sliderstructure.='<div class="setting">
                                    <h4 class="setting-label">'. __('Aside Navigation has Different Settings?','dzsapp').'</h4>
                                    <div class="dzscheckbox skin-nova">
                                        '.DZSHelpers::generate_input_checkbox($lab,array('id' => $lab,'class' => 'mainsetting', 'val' => 'on','seekval' => '')).'
                                        <label for="'.$lab.'"></label>
                                    </div>
                                    <div class="sidenote">'.__('allow creating new accounts').'</div>
                                </div>';








$this->sliderstructure.='



<div class="setting type_all appear-only-when-is-on-desc_different_settings_for_aside">
            <div class="setting-label">'.__('Max Description Length','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-desc_aside_maxlen_desc" value="100"/>
            <div class="sidenote" style="">'.__('youtube video descriptions will be retrieved through YouTube Data API. You can choose here the number of characters to retrieve from it. ' ,'dzsvg').'</div>
</div>
<div class="setting type_all appear-only-when-is-on-desc_different_settings_for_aside">
            <div class="setting-label">'.__('Strip HTML Tags','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-desc_aside_striptags">
                <option value="on">'.__('on','dzsvg').'</option>
                <option value="off">'.__('off','dzsvg').'</option>
                </select>
            <div class="sidenote" style="">'.__('video descriptions will be retrieved as html rich content. you can choose to strip the html tags to leave just simple text ' ,'dzsvg').'</div>
</div>
<div class="setting type_all appear-only-when-is-on-desc_different_settings_for_aside">
            <div class="setting-label">'.__('Repair HTML Markup','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-desc_aside_try_to_close_unclosed_tags">
                <option value="on">'.__('on','dzsvg').'</option>
                <option value="off">'.__('off','dzsvg').'</option>
                </select>
            <div class="sidenote" style="">'.__('video descriptions will be retrieved as html rich content, some may be broken after shortage. attempt to repair this by setting this to <strong>on</strong>' ,'dzsvg').'</div>
</div>





</div>
</div>

<div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">'.__('RTMP Options','dzsvg').'</div>
<div class="toggle-content">
        <div class="sidenote" style="font-size:14px;">'.__('if you have a rtmp server and want to stream, this is the solution','dzsvg').'</div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Stream Server','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-rtmp_streamserver" value=""/>
        </div>
</div>
</div>
        

<div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">'.__('Outer Parts','dzsvg').'</div>
<div class="toggle-content">
        
        <div class="setting type_all">
            <div class="setting-label">'.__('Second Con','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-enable_secondcon">
                <option value="off">'.__('off','dzsvg').'</option>
                <option value="on">'.__('on','dzsvg').'</option>
                </select>
                <div class="sidenote" style="">'.__('enable linking to a slider with titles and descriptions as seen in the demos. to insert the container in your page use this shortcode [dzsvg_secondcon id="theidofthegallery" extraclasses=""]','dzsvg').'</div>
            
        </div>
        
        <div class="setting type_all">
            <div class="setting-label">'.__('Outer Navigation','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-enable_outernav">
                <option value="off">'.__('off','dzsvg').'</option>
                <option value="on">'.__('on','dzsvg').'</option>
                </select>
                <div class="sidenote" style="">'.__('enable linking to a outside navigation [dzsvg_outernav id="theidofthegallery" skin="oasis" extraclasses=""]','dzsvg').'</div>
            
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Outer Navigation, Show Video Author','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-enable_outernav_video_author">
                <option value="off">'.__('off','dzsvg').'</option>
                <option value="on">'.__('on','dzsvg').'</option>
                </select>
                <div class="sidenote" style="">'.__('show the video author for YouTube channels and playlists','dzsvg').'</div>
            
        </div>
</div>
</div>
        
        </div><!--end settings con-->
        <div class="modes-con">
        
        <div class="setting mode_ytuserchannel">
            <div class="setting_label">'.__('YouTube User','dzsvg').'</div>
            <input type="text" class="short textinput mainsetting" name="0-settings-youtubefeed_user" value=""/>
        </div>
	<div class="setting mode_ytplaylist">
            <div class="setting_label">'.__('YouTube Playlist','dzsvg').'
                <div class="info-con">
                <div class="info-icon"></div>
                <div class="sidenote">'.__('You need to set the playlist ID there not the playlist Name. For example for this playlist http:'.'/'.''.'/'.'www.youtube.com/my_playlists?p=PL08BACDB761A0C52A the id is 08BACDB761A0C52A. Remember that if you have the characters PL at the beggining of the ID they should not be included here.','dzsvg').'</div>
                </div>
</div>
                
                <input type="text" class="short textinput mainsetting" name="0-settings-ytplaylist_source" value=""/>
        </div>
	<div class="setting mode_ytkeywords">
            <div class="setting_label">'.__('YouTube Keywords','dzsvg').'
                <div class="info-con">
                <div class="info-icon"></div>
                <div class="sidenote">'.__('','dzsvg').'</div>
                </div>
                </div>

                <input type="text" class="short textinput mainsetting" name="0-settings-ytkeywords_source" value=""/>
        </div>
        <div class="setting type_all mode_ytuserchannel mode_ytplaylist mode_ytkeywords">
            <div class="setting-label">'.__('YouTube Max Videos','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-youtubefeed_maxvideos" value="50"/>
            <div class="sidenote">'.__('input a limit of videos here ( can be a maximum of 50 ) if you have more then 50 videos in your stream, just input "<strong>all</strong>" in this field ( without quotes ) ','dzsvg').'</div>
        </div>
        <div class="setting type_all mode_vmuserchannel">
            <div class="setting_label">'.__('Vimeo User ID','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-vimeofeed_user" value=""/>
            <div class="sidenote">'.__('be sure this to be your user id . For example mine is user5137664 even if my name is DIgitalZoomStudio – https://vimeo.com/user5137664 - you get that by checking your profile link.','dzsvg').'</div>
        </div>
        
        <div class="setting type_all mode_vmchannel">
            <div class="setting_label">'.__('Vimeo Channel ID','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-vimeofeed_channel" value=""/>
            <div class="sidenote">'.__('be sure all videos are allowed to be embedded . Channel example for  – https://vimeo.com/channels/636900 - is <strong>636900</strong>.','dzsvg').'</div>
        </div>
        
        <div class="setting type_all mode_vmalbum">
            <div class="setting_label">'.__('Vimeo Album ID','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-vimeofeed_vmalbum" value=""/>
            <div class="sidenote">'.__('be sure all videos are allowed to be embedded . Channel example for  – https://vimeo.com/album/2633720 - is <strong>2633720</strong>.','dzsvg').'</div>
        </div>
        
</div>
        <div class="master-items-con mode_normal">
        <div class="items-con "></div>
        <a href="#" class="add-item"></a>
        </div><!--end master-items-con-->
        <div class="clear"></div>
        </div>';
        $this->itemstructure = '<div class="item-con">
            <div class="item-delete">x</div>
            <div class="item-duplicate"></div>
        <div class="item-preview" style="">
        </div>
        <div class="item-settings-con">
        <div class="setting type_all">
            <h4 class="non-underline"><span class="underline">'.__('Type','dzsvg').'*</span>&nbsp;&nbsp;&nbsp;<span class="sidenote">select one from below</span></h4> 
            
            <div class="main-feed-chooser select-hidden-metastyle select-hidden-foritemtype">
                <select class="textinput item-type" data-label="type" name="0-0-type">
            <option>youtube</option>
            <option>video</option>
            <option>vimeo</option>
            <option>audio</option>
            <option>image</option>
            <option>link</option>
            <option>rtmp</option>
            <option>inline</option>
                </select>
                <div class="option-con clearfix">
                    <div class="an-option">
                    <div class="an-title">
                    '.__('YouTube','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Input in the <strong>Source</strong> field below the youtube video ID. You can find the id contained in the link to 
                    the video - http://www.youtube.com/watch?v=<strong>ZdETx2j6bdQ</strong> ( for example )','dzsvg').'
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('Self-hosted Video','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Stream videos your own hosted videos. You just have to include two formats of the video you are streaming. In the <strong>Source</strong>
                    field you need to include the path to your mp4 formatted video. And in the OGG field there should be the ogg / ogv path, this is not mandatory, 
                    but recommended.','dzsvg').' <a href="'.$this->thepath.'readme/index.html#handbrake" target="_blank" class="">Documentation here</a>.
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('Vimeo Video','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Insert in the <strong>Source</strong> field the ID of the Vimeo video you want to stream. You can identify the ID easy from the link of the video,
                     for example, here see the bolded part','dzsvg').' - http://vimeo.com/<strong>55698309</strong>
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('Self-hosted Audio File','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('You need a MP3 format of your audio file and an OGG format. You put their paths in the Source and Html5 Ogg Format fields','dzsvg').'
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('Self-hosted Image File','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Just put in the <strong>Source</strong> field the path to your image.','dzsvg').'
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('A link','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Link where the visitor should go when clicking the menu item.','dzsvg').'
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('RTMP File','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('For advanced users - if you have a rtmp server - input the server in the <strong>Stream Server</strong> from the left and input here in the <strong>Source</strong> the location of the file on the server..','dzsvg').'
                    </div>
                    </div>
                    
                    <div class="an-option">
                    <div class="an-title">
                    '.__('Inline Content','dzsvg').'
                    </div>
                    <div class="an-desc">
                    '.__('Insert in the <strong>Source</strong> field custom content ( ie. embed from a custom site like dailymotion).','dzsvg').'
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Source','dzsvg').'*
                <div class="info-con">
                <div class="info-icon"></div>
                <div class="sidenote">'.__('Below you will enter your video address. If it is a video from YouTube or Vimeo you just need to enter 
                the id of the video in the "Video:" field. The ID is the bolded part http://www.youtube.com/watch?v=<strong>j_w4Bi0sq_w</strong>. 
                If it is a local video you just need to write its location there or upload it through the Upload button ( .mp4 / .flv format ).','dzsvg').'
                    </div>
                </div>
            </div>
<textarea class="textinput main-source type_all" data-label="source" name="0-0-source" style="width:320px; height:29px;">Hv7Jxi_wMq4</textarea>'.$uploadbtnstring.'
        </div>
        
        <div class="setting type_all">
            <div class="setting-label">HTML5 OGG '.__('Format','dzsvg').'</div>
            <input type="text" class="textinput upload-prev upload-type-video big-field" name="0-0-html5sourceogg" value=""/>'.$uploadbtnstring.'
            <div class="sidenote">'.__('Optional ogg / ogv file','dzsvg').' / '.__('Only for the Video or Audio type','dzsvg').'</div>
        </div>
        
<div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">'.__('Appearance Settings','dzsvg').'</div>
<div class="toggle-content">
        <div class="setting type_all floatleft220 ">
            <div class="setting-label">'.__('Thumbnail','dzsvg').'</div>
            <input type="text" class="textinput main-thumb" name="0-0-thethumb"/>'.$uploadbtnstring.'<br/>
                <button class="refresh-main-thumb button-secondary">'.__('Refresh Thumbnail','dzsvg').'</button>
                <div class="sidenote">'.__('Refresh the thumbnail if its a vimeo or youtube video','dzsvg').'</div>
        </div>
        <div class="setting type_all floatleft220 br1">
            <div class="setting-label">'.__('Menu Title','dzsvg').'</div>
            <input type="text" class="textinput" name="0-0-title"/>
        </div>
        <div class="setting type_all floatleft220">
            <div class="setting-label">'.__('Video Description','dzsvg').':</div>
            <textarea class="textinput" name="0-0-description"></textarea>
        </div>
        <div class="setting type_all floatleft220 br1">
            <div class="setting-label">'.__('Menu Description','dzsvg').'</div>
            <textarea class="textinput" name="0-0-menuDescription"></textarea>
        </div>
        <div class="clear"></div>

        <div class="setting type_all">
            <div class="setting-label">'.__('Preview Image','dzsvg').'</div>
            <input class="textinput upload-prev" name="0-0-audioimage" value=""/>'.$uploadbtnstring.'
            <div class="sidenote">'.__('will be used as the background image for audio type too','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Tags','dzsvg').'</div>
            <input class="textinput tageditor-prev" name="0-0-tags" value=""/><button class="button-secondary btn-tageditor">Tag Editor</button>
            <div class="sidenote">'.__('use the tag editor to generate tags at given times of the video','dzsvg').'</div>
        </div>
        

        <div class="setting type_all">
            <div class="setting-label">'.__('Subtitle File','dzsvg').'</div>
            <input class="textinput upload-prev" name="0-0-subtitle_file" value=""/>'.$uploadbtnstring.'
            <div class="sidenote">'.__('you can upload a srt file for optional captioning on the video - recommeded you rename the .srt file to .html format if you want to use the wordpress uploader ( security issues ) ','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Play From','dzsvg').'</div>
            <input class="textinput upload-prev" name="0-0-playfrom" value=""/>
            <div class="sidenote">'.__('you can input a number ( seconds ) for the initial play status. or just input "last" for the video to come of where it has last been left','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Responsive Ratio','dzsvg').'</div>
            <input class="textinput upload-prev" name="0-0-responsive_ratio" value=""/>
            <div class="sidenote">'.__('set a responsive ratio height/ratio 0.5 means that the player height will resize to 0.5 of the gallery width / or just set it to "detect" and it will autocalculate the ratios if it is a self hosted mp4','dzsvg').'</div>
        </div>
</div>
</div>
        
<div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">'.__('Advertising Settings','dzsvg').'</div>
<div class="toggle-content">
        <div class="setting type_all">
            <div class="setting-label">'.__('Ad  Source','dzsvg').'</div>
            <div class="sidenote">'.__('If it is a video ad, input here the mp4 / m4v path ( or upload the video ) <br/>If it is a youtube ad, input here the youtube video id<br/>If it is a image ad, input here the image path ( or upload the image ) <br/>If it is a inline ad, input here the html content ( can load iframes too )
            format in the same folder','dzsvg').'</div>
            <input class="textinput upload-prev" name="0-0-adsource" value=""/>'.$uploadbtnstring.'
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Ad  Type','dzsvg').'</div>
            <select class="textinput item-type styleme type_all" name="0-0-adtype">
            <option>video</option>
            <option>youtube</option>
            <option>image</option>
            <option>inline</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Ad  Link','dzsvg').'</div>
            <input class="textinput" name="0-0-adlink" value=""/>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Skip Ad Button Delay','dzsvg').'</div>
            <input class="textinput" name="0-0-adskip_delay" value=""/>
            <div class="sidenote">'.__('You can have a skip ad button appear after a set number of seconds. ','dzsvg').'</div>
        </div>
        <div class="clear"></div>
</div>
</div>
</div><!--end item-settings-con-->
</div>';



        $this->videoplayerconfig = '<div class="slider-con" style="display:none;">
        
        <div class="settings-con">
        <h4>'.__('General Options','dzsvg').'</h4>
        <div class="setting type_all">
            <div class="setting-label">'.__('Config ID','dzsvg').'</div>
            <input type="text" class="textinput mainsetting main-id" name="0-settings-id" value="default"/>
            <div class="sidenote">'.__('Choose an unique id.','dzsvg').'</div>
        </div>
        <div class="setting styleme">
            <div class="setting-label">'.__('Video Player Skin','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-skin_html5vp">
                <option>skin_aurora</option>
                <option>skin_default</option>
                <option>skin_white</option>
                <option>skin_pro</option>
                <option>skin_bigplay</option>
                <option>skin_reborn</option>
                <option>skin_avanti</option>
                <option>skin_custom</option>
            </select>
            <div class="sidenote">'.__('Skin Custom can be modified via Designer Center.','dzsvg').'</div>
        </div>
        <hr/>
        <div class="setting styleme">
            <div class="setting-label">'.__('Video Overlay','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-settings_video_overlay">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">'.__('an overlay over the video that you can press for pause / unpause','dzsvg').'</div>
        </div>
        

        <div class="setting styleme">
            <div class="setting-label">'.__('Disable Mouse Out Behaviour','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-settings_disable_mouse_out">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">'.__('some skins hide the controls on mouse out, you can disable this.','dzsvg').'</div>
        </div>


        <div class="setting styleme">
            <div class="setting-label">'.__('Use the Custom Skin on iOS','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-settings_ios_usecustomskin">
                <option>on</option>
                <option>off</option>
            </select>
            <div class="sidenote">'.__('overwrites the default ios ( ipad and iphone ) skin with the skin you chose in the Video Player Configuration','dzsvg').'</div>
        </div>

        <div class="setting ">
            <div class="setting-label">'.__('Send Google Analytics Event for Play','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-ga_enable_send_play">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">'.__('send the play event to google analytics to record gallery plays on your site / you need the google analytics wordpress plugin','dzsvg').'</div>
        </div>

        <div class="setting ">
            <div class="setting-label">'.__('Video End Displays the Last Frame','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-settings_video_end_reset_time">
                <option>on</option>
                <option>off</option>
            </select>
            <div class="sidenote">'.__('available for the self hosted video type','dzsvg').'</div>
        </div>
        
        <div class="setting type_all">
            <div class="setting-label">'.__('Normal Controls Opacity','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-html5design_controlsopacityon" value="1"/>
            <div class="sidenote">'.__('Choose an opacity from 0 to 1','dzsvg').'</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Roll Out Controls Opacity','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-html5design_controlsopacityout" value="1"/>
            <div class="sidenote">'.__('Choose an opacity from 0 to 1 for when the mouse is not on the video player','dzsvg').'</div>
        </div>
        
        <div class="setting type_all">
            <div class="setting-label">'.__('Default Volume','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-defaultvolume" value=""/>
        </div>
        
<div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">'.__('YouTube Options','dzsvg').'</div>
<div class="toggle-content">
        <div class="setting type_all">
            <div class="setting-label">'.__('SD Quality','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-youtube_sdquality">
                <option>small</option>
                <option>medium</option>
                <option>default</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('HD Quality','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-youtube_hdquality">
                <option>hd720</option>
                <option>hd1080</option>
                <option>default</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Default Quality','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-youtube_defaultquality">
                <option value="hd">'.__('HD','dzsvg').'</option>
                <option value="sd">'.__('SD','dzsvg').'</option>
            </select>
        </div>
        <div class="setting type_all">
            <div class="setting-label">'.__('Enable Custom Skin','dzsvg').'</div>
            <select class="textinput mainsetting styleme" name="0-settings-yt_customskin">
                <option value="on">'.__('on','dzsvg').'</option>
                <option value="off">'.__('off','dzsvg').'</option>
            </select>
            <div class="sidenote">'.__('Choose if the custom skin you set in the Video Player Skin is how YouTube videos should show ( on )
                 or if the default YouTube skin should show ( off )','dzsvg').'</div>
        </div>
</div>
</div>
        

<div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">'.__('Vimeo Options','dzsvg').'</div>
<div class="toggle-content">
        
                <div class="setting">
                    <div class="label">'.__('Vimeo Player Byline','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-vimeo_byline" value="0"/>
                    <div class="sidenote">'.__('','dzsvg').'</div>
                </div>
                <div class="setting">
                    <div class="label">'.__('Vimeo Player Portrait','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-vimeo_portrait" value="0"/>
                    <div class="sidenote">'.__('','dzsvg').'</div>
                </div>
                <div class="setting">
                    <div class="label">'.__('Vimeo Player Color','dzsvg').'</div>
            <input type="text" class="textinput mainsetting" name="0-settings-vimeo_color" value=""/>
                    <div class="sidenote">'.__('input the color of controls in this format RRGGBB, ie. <strong>ffffff</strong> for white ','dzsvg').'</div>
                </div>
</div>
</div>
        
        </div><!--end settings con-->
        </div>';
        //print_r($this->mainitems);


        // --- check posts
        if(isset($_GET['dzsvg_shortcode_builder']) && $_GET['dzsvg_shortcode_builder']=='on'){
//            dzsprx_shortcode_builder();

            include_once(dirname(__FILE__).'/tinymce/popupiframe.php');
            define('DONOTCACHEPAGE', true);
            define('DONOTMINIFY', true);

        }




        add_shortcode($this->the_shortcode,array($this,'show_shortcode'));
        add_shortcode('dzs_'.$this->the_shortcode,array($this,'show_shortcode'));
        add_shortcode('videogallerycategories',array($this,'show_shortcode_cats'));
        add_shortcode('videogallerylightbox',array($this,'show_shortcode_lightbox'));
        add_shortcode('videogallerylinks',array($this,'show_shortcode_links'));
        add_shortcode('dzsvg_secondcon',array($this,'show_shortcode_secondcon'));
        add_shortcode('dzsvg_outernav',array($this,'show_shortcode_outernav'));


        add_shortcode('vimeo',array($this,'vimeo_func'));
        add_shortcode('youtube',array($this,'youtube_func'));
        add_shortcode('dzs_youtube',array($this,'youtube_func'));
        add_shortcode('dzs_video',array($this,'video_func'));

        if ($this->mainoptions['replace_wpvideo'] == 'on') {
            add_shortcode('video',array($this,'video_func'));
        }
        if ($this->mainoptions['replace_jwplayer'] == 'on') {
            add_shortcode('jwplayer',array($this,'video_func'));
        }

        include_once dirname(__FILE__).'/extras.php';

        add_filter('attachment_fields_to_edit',array($this,'filter_attachment_fields_to_edit'),10,2);

        add_action('init',array($this,'handle_init'));
        add_action('wp_ajax_dzsvg_ajax',array($this,'post_save'));
        add_action('wp_ajax_dzsvg_import_ytplaylist',array($this,'post_importytplaylist'));
        add_action('wp_ajax_dzsvg_import_ytuser',array($this,'post_importytuser'));
        add_action('wp_ajax_dzsvg_import_vimeouser',array($this,'post_importvimeouser'));
        add_action('wp_ajax_dzsvg_get_db_gals',array($this,'post_get_db_gals'));
        add_action('wp_ajax_get_vimeothumb',array($this,'ajax_get_vimeothumb'));



        add_action('wp_ajax_dzsvg_save_vpc',array($this,'post_save_vpc'));

        add_action('wp_ajax_dzsvg_ajax_mo',array($this,'post_save_mo'));
        add_action('wp_ajax_dzsvg_ajax_options_dc',array($this,'post_save_options_dc'));

        add_action('admin_menu',array($this,'handle_admin_menu'));
        add_action('admin_head',array($this,'handle_admin_head'));


        add_action('wp_head',array($this,'handle_wp_head'));
        add_action('wp_footer',array($this,'handle_footer'));




        if ($this->mainoptions['tinymce_enable_preview_shortcodes'] == 'on') {
            add_filter('mce_external_plugins',array(&$this,'add_tcustom_tinymce_plugin'));
            add_filter('tiny_mce_before_init',array(&$this,'myformatTinyMCE'));
        }

        if ($this->pluginmode != 'theme') {
            add_action('admin_init',array($this,'admin_init'));
            add_action('save_post',array($this,'admin_meta_save'));
        }
    }

    //include the tinymce javascript plugin
    function add_tcustom_tinymce_plugin($plugin_array) {
        $plugin_array['ve_dzs_video'] = $this->thepath.'/tinymce/visualeditor/editor_plugin.js';
        return $plugin_array;
    }

    //include the css file to style the graphic that replaces the shortcode
    function myformatTinyMCE($options) {

        $ext = 'iframe[align|longdesc|name|width|height|frameborder|scrolling|marginheight|marginwidth|src|id|class|title|style],video[source],source[*]';

        if (isset($options['extended_valid_elements']))
            $options['extended_valid_elements'] .= ','.$ext;
        else
            $options['extended_valid_elements'] = $ext;


        $options['media_strict'] = 'false';

//    print_r($options);

        $options['content_css'] .= ",".$this->thepath.'/tinymce/visualeditor/editor-style.css';

        return $options;
    }

    function handle_wp_head() {
        echo '<script>';
        echo 'window.dzsvg_swfpath="'.$this->thepath.'preview.swf";';
        if(isset($this->mainoptions['translate_skipad']) && $this->mainoptions['translate_skipad']!='Skip Ad'){
            echo 'window.dzsvg_translate_skipad = "'.$this->mainoptions['translate_skipad'].'"';
        }
        echo '</script>';

        if ($this->mainoptions['extra_css']) {
            echo '<style>';
            echo $this->mainoptions['extra_css'];
            echo '</style>';
        }
    }

    function handle_admin_head() {

        //global $post; print_r($post);
        //echo 'ceva23';
        ///siteurl : "'.site_url().'", 
        $aux = remove_query_arg('deleteslider',dzs_curr_url());
        $params = array('currslider' => '_currslider_');
        $newurl = esc_url(add_query_arg($params,$aux));

        $params = array('deleteslider' => '_currslider_');
        $delurl = esc_url(add_query_arg($params,$aux));
        echo '<script>var dzsvg_settings = { thepath: "'.$this->thepath.'",the_url: "'.$this->thepath.'",version: "'.DZSVG_VERSION.'", is_safebinding: "'.$this->mainoptions['is_safebinding'].'", admin_close_otheritems:"'.$this->mainoptions['admin_close_otheritems'].'",wpurl : "'.site_url().'",translate_add_videogallery: "'.__("Add Video Gallery").'" ';

        //echo 'hmm';
        if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename && ( (isset($this->mainitems[$this->currSlider]) && $this->mainitems[$this->currSlider] == '') || isset($this->mainitems[$this->currSlider]) == false )) {
            echo ', addslider:"on"';
        }
        if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename_configs && (isset($this->mainvpconfigs[$this->currSlider])==false || $this->mainvpconfigs[$this->currSlider] == '')) {
            echo ', addslider:"on"';
        }
        echo ', urldelslider:"'.$delurl.'", urlcurrslider:"'.$newurl.'", currSlider:"'.$this->currSlider.'", currdb:"'.$this->currDb.'", zsvg_dc_poster_url_path:"'.$this->thepath.'deploy/designer/index.php"'
                . ',settings_limit_notice_dismissed: "'.$this->mainoptions['settings_limit_notice_dismissed'].'",shortcode_generator_url: "'.admin_url('admin.php?page='.$this->adminpagename_mainoptions) . '&dzsvg_shortcode_builder=on'.'"};';
        echo '  </script>';
    }

    function handle_footer() {

        global $post;
        if (!$post) {
            return;
        }
        //echo 'ceva';
        $wallid = get_post_meta($post->ID,'dzsvg_fullscreen',true);
        if ($wallid != '' && $wallid != 'none') {
            echo '<div class="wall-close">'.__('CLOSE GALLERY','dzsvg').'</div>';
            echo do_shortcode('[videogallery id="'.$wallid.'" fullscreen="on"]');
            ?>
            <script>
                var dzsvg_videofs = true;
                jQuery(document).ready(function($) {
                    //$('body').css('overflow', 'hidden');
                    jQuery(".wall-close").click(handle_wall_close);
                    function handle_wall_close() {
                        var _t = $(this);
                        if (dzsvg_videofs == true) {
                            _t.html('OPEN GALLERY');
                            jQuery(".gallery-is-fullscreen").fadeOut("slow");
                            dzsvg_videofs = false;
                        } else {
                            _t.html('CLOSE GALLERY');
                            jQuery(".gallery-is-fullscreen").fadeIn("slow");
                            dzsvg_videofs = true;
                        }
                    }
                })
            </script>
            <?php
        }
    }

    function vimeo_func($atts) {
        //[vimeo id="youtubeid"]
        $fout = '';
        $margs = array(
            'id' => '2',
            'vimeo_title' => '',
            'vimeo_byline' => '0',
            'vimeo_portrait' => '0',
            'vimeo_color' => '',
            'width' => '100%',
            'height' => '300',
            'config' => '',
            'single' => 'on',
        );

        if ($atts == false) {
            $atts = array();
        }

        $margs = array_merge($margs,$atts);

        $w = 400;
        if (isset($margs['width'])) {
            $w = $margs['width'];
        }
        $h = 300;
        if (isset($margs['height'])) {
            $h = $margs['height'];
        }

        $vpsettingsdefault = array();
        $vpsettingsdefault['settings'] = array(
            'id' => 'default',
            'skin_html5vp' => 'skin_aurora',
            'html5design_controlsopacityon' => '1',
            'html5design_controlsopacityout' => '1',
            'defaultvolume' => '',
            'sdquality' => 'small',
            'hdquality' => 'hd720',
            'defaultquality' => 'HD',
            'yt_customskin' => 'on',
        );
        $i = 0;
        $vpconfig_k = 0;
        $vpsettings = array();





        if ($margs['config'] != '') {
            $vpconfig_id = $margs['config'];

            for ($i = 0; $i < count($this->mainvpconfigs); $i++) {
                if ((isset($vpconfig_id)) && ($vpconfig_id == $vpconfig_id)) {
                    $vpconfig_k = $i;
                }
            }
            $vpsettings = $this->mainvpconfigs[$vpconfig_k];
        }


        $vpsettings = array_merge($vpsettingsdefault,$vpsettings);

        //print_r($vpsettings);

        if (isset($vpsettings['settings']) && isset($vpsettings['settings']['vimeo_byline'])) {
            $margs['vimeo_byline'] = $vpsettings['settings']['vimeo_byline'];
        }
        if (isset($vpsettings['settings']) && isset($vpsettings['settings']['vimeo_title'])) {
            $margs['vimeo_title'] = $vpsettings['settings']['vimeo_title'];
        }
        if (isset($vpsettings['settings']) && isset($vpsettings['settings']['vimeo_color'])) {
            $margs['vimeo_color'] = $vpsettings['settings']['vimeo_color'];
        }
        if (isset($vpsettings['settings']) && isset($vpsettings['settings']['vimeo_portrait'])) {
            $margs['vimeo_portrait'] = $vpsettings['settings']['vimeo_portrait'];
        }

        //print_r($margs);


        $str_title = 'title='.$margs['vimeo_title'];
        $str_byline = '&amp;byline='.$margs['vimeo_byline'];
        $str_portrait = '&amp;portrait='.$margs['vimeo_portrait'];
        $str_color = '';
        if ($margs['vimeo_color'] != '') {
            $str_color = '&amp;color='.$margs['vimeo_color'];
        }



        $fout.='<iframe src="http://player.vimeo.com/video/'.$margs['id'].'?'.$str_title.$str_byline.$str_portrait.$str_color.'" width="'.$w.'" height="'.$h.'" frameborder="0"></iframe>';
        return $fout;
    }

    function youtube_func($atts) {
        //[youtube id="youtubeid"]

        $fout = '';

        $margs = array(
            'width' => '100%',
            'config' => '',
            'height' => '300',
            'source' => '',
            'mediaid' => '',
            'config' => '',
            'player' => '',
            'mp4' => '',
            'sourceogg' => '',
            'autoplay' => 'off',
            'cuevideo' => 'on',
            'cover' => '',
            'type' => 'youtube',
            'cssid' => '',
            'single' => 'on',
        );

        $margs = array_merge($margs,$atts);

        if (isset($margs['id'])) {
            $margs['source'] = $margs['id'];
        }

        return $this->video_func($margs);
    }

    function video_func($atts) {
        //[dzs_video source="http://localhost/wordpress/wp-content/uploads/2015/03/test.m4v" config="minimalplayer" height="" type="video"]
        //[video source="pathto.mp4"]
        $this->slider_index++;

        $fout = '';


        $this->front_scripts();

        $margs = array(
            'width' => '100%',
            'config' => '',
            'height' => '300',
            'source' => '',
            'mediaid' => '',
            'player' => '',
            'mp4' => '',
            'sourceogg' => '',
            'autoplay' => 'off',
            'cuevideo' => 'on',
            'cover' => '',
            'type' => 'video',
            'cssid' => '',
            'single' => 'on',
        );

        $margs = array_merge($margs,$atts);


        if ($margs['cssid'] == '') {
            $margs['cssid'] = 'vp'.($this->index_players+1);
        }




        if ($margs['mediaid'] != '') {
            $auxpo = get_post($margs['mediaid']);
            if ($auxpo == false) {
                return '<div class="warning">Video does not exist anymore...</div>';
            }
            $margs['source'] = $auxpo->guid;
            //print_r($auxpo);
        }
        if ($margs['mp4'] != '') {
            //$auxpo = get_post($margs['mediaid']);
            $margs['source'] = $margs['mp4'];
            //print_r($auxpo);
        }
        if ($margs['player'] != '') {
            $margs['config'] = $margs['player'];
        }


        $i = 0;
        $vpconfig_k = 0;
        $vpconfig_id = '';

        $vpsettingsdefault = array(
            'id' => 'default',
            'skin_html5vp' => 'skin_aurora',
            'html5design_controlsopacityon' => '1',
            'html5design_controlsopacityout' => '1',
            'defaultvolume' => '',
            'youtube_sdquality' => 'small',
            'youtube_hdquality' => 'hd720',
            'youtube_defaultquality' => 'hd',
            'yt_customskin' => 'on',
            'vimeo_byline' => '0',
            'vimeo_portrait' => '0',
            'vimeo_color' => '',
            'settings_video_overlay' => 'off',
            'settings_disable_mouse_out' => 'off',
            'settings_ios_usecustomskin' => 'on',
        );
        $vpsettings = array();


        if ($margs['config'] != '') {
            $vpconfig_id = $margs['config'];
        }

        if ($vpconfig_id != '') {
            //print_r($this->mainvpconfigs);
            for ($i = 0; $i < count($this->mainvpconfigs); $i++) {
                if ((isset($vpconfig_id)) && ($vpconfig_id == $this->mainvpconfigs[$i]['settings']['id']))
                    $vpconfig_k = $i;
            }
            $vpsettings = $this->mainvpconfigs[$vpconfig_k];


            if (!isset($vpsettings['settings']) || $vpsettings['settings'] == '') {
                $vpsettings['settings'] = array();
            }
        }

        if (!isset($vpsettings['settings']) || (isset($vpsettings['settings']) && !is_array($vpsettings['settings']))) {
            $vpsettings['settings'] = array();
        }

        $vpsettings['settings'] = array_merge($vpsettingsdefault,$vpsettings['settings']);


        $skin_vp = 'skin_aurora';
        if ($vpsettings['settings']['skin_html5vp'] == 'skin_custom') {
            $skin_vp = 'skin_pro';
        } else {
            $skin_vp = $vpsettings['settings']['skin_html5vp'];
        }

        unset($vpsettings['settings']['id']);


        $str_sourceogg = '';

        $its = array(
            0 => $margs,
        );
        $its = array_merge($its,$vpsettings);

        if ($margs['sourceogg'] != '') {

            if (strpos($margs['sourceogg'],'.webm') === false) {
                $str_sourceogg.=' data-sourceogg="'.$margs['sourceogg'].'"';
            } else {
                $str_sourceogg.=' data-sourcewebm="'.$margs['sourceogg'].'"';
            }

            $its[0]['html5sourceogg'] = $margs['sourceogg'];
        }

        $str_cover = '';

        if ($margs['cover'] != '') {
            $str_cover = ' data-previewimg="'.$margs['cover'].'"';
        }


        //print_r($vpsettings);
//<div id="vp' . $this->slider_index . '" class="vplayer-tobe ' . $vpsettings['settings']['skin_html5vp'] . '" style="" data-sourcemp4="' . $margs['source'] . '"' . $str_sourceogg . '' . $str_cover . '>
//</div> 

        if ($vpsettings['settings']['skin_html5vp'] == 'skin_custom') {
            $fout.='<style>';
            $fout.='#vp'.$this->index_players.' { background-color:'.$this->mainoptions_dc['background'].';} ';
            $fout.='#vp'.$this->index_players.' .background{ background-color:'.$this->mainoptions_dc['controls_background'].';} ';
            $fout.='#vp'.$this->index_players.' .scrub-bg{ background-color:'.$this->mainoptions_dc['scrub_background'].';} ';
            $fout.='#vp'.$this->index_players.' .scrub-buffer{ background-color:'.$this->mainoptions_dc['scrub_buffer'].';} ';
            $fout.='#vp'.$this->index_players.' .playSimple{ border-left-color:'.$this->mainoptions_dc['controls_color'].';} #vp'.$this->index_players.' .stopSimple .pause-part-1{ background-color: '.$this->mainoptions_dc['controls_color'].'; } #vp'.$this->index_players.' .stopSimple .pause-part-2{ background-color: '.$this->mainoptions_dc['controls_color'].'; } #vp'.$this->index_players.' .volumeicon{ background: '.$this->mainoptions_dc['controls_color'].'; } #vp'.$this->index_players.' .volumeicon:before{ border-right-color: '.$this->mainoptions_dc['controls_color'].'; } #vp'.$this->index_players.' .volume_static{ background: '.$this->mainoptions_dc['controls_color'].'; } #vp'.$this->index_players.' .hdbutton-con .hdbutton-normal{ color: '.$this->mainoptions_dc['controls_color'].'; } #vp'.$this->index_players.' .total-timetext{ color: '.$this->mainoptions_dc['controls_color'].'; } ';
            $fout.='#vp'.$this->index_players.' .playSimple:hover{ border-left-color: '.$this->mainoptions_dc['controls_hover_color'].'; } #vp'.$this->index_players.' .stopSimple:hover .pause-part-1{ background-color: '.$this->mainoptions_dc['controls_hover_color'].'; } #vp'.$this->index_players.' .stopSimple:hover .pause-part-2{ background-color: '.$this->mainoptions_dc['controls_hover_color'].'; } #vp'.$this->index_players.' .volumeicon:hover{ background: '.$this->mainoptions_dc['controls_hover_color'].'; } #vp'.$this->index_players.' .volumeicon:hover:before{ border-right-color: '.$this->mainoptions_dc['controls_hover_color'].'; } ';
            $fout.='#vp'.$this->index_players.' .volume_active{ background-color: '.$this->mainoptions_dc['controls_highlight_color'].'; } #vp'.$this->index_players.' .scrub{ background-color: '.$this->mainoptions_dc['controls_highlight_color'].'; } #vp'.$this->index_players.' .hdbutton-con .hdbutton-hover{ color: '.$this->mainoptions_dc['controls_highlight_color'].'; } ';
            $fout.='#vp'.$this->index_players.' .curr-timetext{ color: '.$this->mainoptions_dc['timetext_curr_color'].'; } ';
            $fout.='</style>';
        }



        $fout.=$this->parse_items($its,$margs).' 
<script>jQuery(document).ready(function($){ var videoplayersettings = {
autoplay : "'.$margs['autoplay'].'",
cueVideo : "'.$margs['cuevideo'].'",
controls_out_opacity : "'.$vpsettings['settings']['html5design_controlsopacityon'].'",
controls_normal_opacity : "'.$vpsettings['settings']['html5design_controlsopacityout'].'"
,settings_hideControls : "off"
,settings_video_overlay : "'.$vpsettings['settings']['settings_video_overlay'].'"
,settings_disable_mouse_out : "'.$vpsettings['settings']['settings_disable_mouse_out'].'"
,settings_ios_usecustomskin : "'.$vpsettings['settings']['settings_ios_usecustomskin'].'"
,settings_swfPath : "'.$this->thepath.'preview.swf"
,design_skin: "'.$skin_vp.'"';

        if ($vpsettings['settings']['skin_html5vp'] == 'skin_custom') {
            $fout.=',controls_fscanvas_bg:"'.$this->mainoptions_dc['controls_color'].'"';
            $fout.=',controls_fscanvas_hover_bg:"'.$this->mainoptions_dc['controls_hover_color'].'"';
            $fout.=',fpc_background:"'.$this->mainoptions_dc['background'].'"';
            $fout.=',fpc_controls_background:"'.$this->mainoptions_dc['controls_background'].'"';
            $fout.=',fpc_scrub_background:"'.$this->mainoptions_dc['scrub_background'].'"';
            $fout.=',fpc_scrub_buffer:"'.$this->mainoptions_dc['scrub_buffer'].'"';
            $fout.=',fpc_controls_color:"'.$this->mainoptions_dc['controls_color'].'"';
            $fout.=',fpc_controls_hover_color:"'.$this->mainoptions_dc['controls_hover_color'].'"';
            $fout.=',fpc_controls_highlight_color:"'.$this->mainoptions_dc['controls_highlight_color'].'"';
        }

        if(isset($vpsettings['settings']['settings_video_end_reset_time']) && $vpsettings['settings']['settings_video_end_reset_time']=='off'){
            $fout.=',settings_video_end_reset_time:"off"';
        }


        $fout.='}; jQuery("#vp'.($this->index_players).'").vPlayer(videoplayersettings); });</script>';

        return $fout;
    }

    function log_event($arg) {
        $fil = dirname(__FILE__)."/log.txt";
        $fh = @fopen($fil,'a');
        @fwrite($fh,($arg."\n"));
        @fclose($fh);
    }

    function show_shortcode_cats($atts,$content = null) {
        $fout = '';
        $margs = array(
            'width' => '100',
            'height' => 400,
        );

        $margs = array_merge($margs,$atts);



        // ===== some sanitizing
        $str_tw = $margs['width'];
        $str_th = $margs['height'];





        if (strpos($str_tw,"%") === false) {
            $str_tw = $str_tw.'px';
        }
        if (strpos($str_th,"%") === false && $str_th != 'auto') {
            $str_th = $str_th.'px';
        }


//        echo 'ceva'.$content;
        $lb = array("\r\n","\n","\r","<br />");
        $content = str_replace($lb, '', $content);
//        echo $content.'alceva';


        $aux = do_shortcode($content);;

//        $aux = strip_tags($aux, '<p><br/>');

        $fout.='<div class="categories-videogallery" id="cats'.( ++$this->cats_index).'">';
        $fout.='<div class="the-categories-con"><span class="label-categories">'.__('categories','dzsvg').'</span></div>';
        $fout.=$aux;
        $fout.='</div>';
        $fout.='<script>jQuery(document).ready(function($){ vgcategories("#cats'.$this->cats_index.'"); });</script>';

        return $fout;
    }

    function show_shortcode_lightbox($atts,$content = null) {

        $fout = '';
        //$this->sliders_index++;

        $this->front_scripts();

        wp_enqueue_style('dzs.zoombox',$this->thepath.'zoombox/zoombox.css');
        wp_enqueue_script('dzs.zoombox',$this->thepath.'zoombox/zoombox.js');

        $args = array(
            'id' => 'default'
            ,'db' => ''
            ,'category' => ''
            ,'width' => ''
            ,'height' => ''
            ,'gallerywidth' => '800'
            ,'galleryheight' => '500'
        );
        $args = array_merge($args,$atts);
        $fout.='<div class="zoombox"';

        if ($args['width'] != '') {
            $fout.=' data-width="'.$args['width'].'"';
        }
        if ($args['height'] != '') {
            $fout.=' data-height="'.$args['height'].'"';
        }
        if ($args['gallerywidth'] != '') {
            $fout.=' data-bigwidth="'.$args['gallerywidth'].'"';
        }
        if ($args['galleryheight'] != '') {
            $fout.=' data-bigheight="'.$args['galleryheight'].'"';
        }

        $fout.='data-src="'.$this->thepath.'retriever.php?id='.$args['id'].'" data-type="ajax">'.$content.'</div>';
        $fout.='<script>
jQuery(document).ready(function($){
$(".zoombox").zoomBox();
});
</script>';

        return $fout;
    }
    function show_shortcode_secondcon($pargs,$content = null) {
        
        $fout = '';
        
        $margs = array(
            'id'=>'default',
            'extraclasses'=>'',
            'enable_readmore'=>'off',
        );
        if(is_array($pargs)==false){
            $pargs=array();
        }
        $margs = array_merge($margs, $pargs);






        wp_enqueue_style('dzs.advancedscroller',$this->thepath.'advancedscroller/plugin.css');
        wp_enqueue_script('dzs.advancedscroller',$this->thepath.'advancedscroller/plugin.js');

        $gallery_margs = array(
            'id' => $margs['id'],
            'return_mode' => 'items',
        );
                
        $its = $this->show_shortcode($gallery_margs);
        
        $css_classid = str_replace(' ','_',$margs['id']);
        $fout.='<div class="dzsas-second-con dzsas-second-con-for-'.$css_classid.' '.$margs['extraclasses'].'">';

        if($margs['enable_readmore']=='on'){
            $fout.='<div class="read-more-con">';
            $fout.='<div class="read-more-content">';
        }


        $fout.='<div class="dzsas-second-con--clip">';
        foreach ($its as $lab => $val){
            if ($lab==='settings') {
                continue;
            }
            
            $desc = $val['description'];



            $maxlen = 100;
            if (isset($its['settings']['maxlen_desc']) && $its['settings']['maxlen_desc']) {
                $maxlen = $its['settings']['maxlen_desc'];
            }
            if (isset($its['settings']['desc_different_settings_for_aside']) && ($its['settings']['try_to_close_unclosed_tags'])=='on'){

                if (isset($its['settings']['desc_aside_maxlen_desc']) && $its['settings']['desc_aside_maxlen_desc']) {
                    $maxlen = $its['settings']['desc_aside_maxlen_desc'];
                }
            }


//            echo 'maxlen - '.$maxlen;

            $striptags = false;

            if (isset($its['settings']['striptags']) && $its['settings']['striptags']==='on') {
                $striptags = true;
            }

            $try_to_close_unclosed_tags = true;


//                $striptags=true;
            if($striptags){
                $try_to_close_unclosed_tags = false;
            }
            if (isset($its['settings']['try_to_close_unclosed_tags']) && $its['settings']['try_to_close_unclosed_tags']==='off') {
                $try_to_close_unclosed_tags=false;
            }
            if (isset($its['settings']['desc_different_settings_for_aside']) && ($its['settings']['try_to_close_unclosed_tags'])=='on' && isset($its['settings']['desc_aside_try_to_close_unclosed_tags']) && $its['settings']['desc_aside_try_to_close_unclosed_tags']==='off') {
                $try_to_close_unclosed_tags=false;
            }


//            echo 'description - '.$che['description'];

            if (isset($val['description']) && $val['description']) {
                $desc =''.dzs_get_excerpt(0,
                        array(
                            'content' => $val['description'],
                            'maxlen' => $maxlen,
                            'try_to_close_unclosed_tags'=>$try_to_close_unclosed_tags,
                            'striptags'=>$striptags,
                        )
                    );
//                echo ' final desc -- '. $desc;
            }


            $fout.='<div class="item">
<h4>'.$val['title'].'</h4>
<p>'.$desc.'</p>
</div>';

//                print_r($val);

        }


        if($margs['enable_readmore']=='on') {
            $fout .= '</div>';
            $fout .= '</div>';
            $fout .= '<div class="read-more-label"> <i class="fa fa-angle-down"></i> <span>' . __("DETAILS") . '</span></div>';


        }else{

        }
        $fout.='</div></div>';

        
        return $fout;
        
        
//        print_r($its);
    }
    function show_shortcode_outernav($pargs,$content = null) {
        //[dzsvg_outernav id="theidofthegallery" skin="oasis" extraclasses="" thumbs_per_page="12"]
        $fout = '';
        
        $margs = array(
            'id'=>'default',
            'skin'=>'oasis',
            'extraclasses'=>'',
            'thumbs_per_page'=>'8',
        );
        if(is_array($pargs)==false){
            $pargs=array();
        }
        $margs = array_merge($margs, $pargs);
        
        
        $gallery_margs = array(
            'id' => $margs['id'],
            'return_mode' => 'items',
        );
                
        $its = $this->show_shortcode($gallery_margs);
        
        $css_classid = str_replace(' ','_',$margs['id']);
        $fout.='<div class="videogallery--navigation-outer layout-one-fourth videogallery--navigation-outer-for-'.$css_classid.' skin-'.$margs['skin'].' '.$margs['extraclasses'].'" data-vgtarget=".id_'.$css_classid.'"><div class="videogallery--navigation-outer--clip"><div class="videogallery--navigation-outer--clipmover">';
        
        $ix = 0;
        $maxblocksperrow = intval($margs['thumbs_per_page']);
        $nr_pages = 0;
        
//        print_r($its);
        
        foreach ($its as $lab => $val){
            if ($lab==='settings') {
                continue;
            }
            
//            print_r($val);
            
            if($ix%$maxblocksperrow===0){
                $fout.='<div class="videogallery--navigation-outer--bigblock';
                if($ix===0){
                    $fout.=' active';
                }
                
                $fout.='">';
            }
            


            $thumb = $val['thethumb'];
//            echo $thumb;
            if($thumb==''){
                if($val['type']=='youtube'){
                    $thumb = "http://img.youtube.com/vi/".$val['source']."/0.jpg";
                }
                if($val['type']=='vimeo'){
                    $id = $val['source'];

                    $target_file = "http://vimeo.com/api/v2/video/$id.php";
                    $cache = DZSHelpers::get_contents($target_file,array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));

                    $apiresp = $cache;
                    $imga = unserialize($apiresp);

            //        print_r($cache);

                    $thumb = $imga[0]['thumbnail_medium'];
                }
            }

            

            $fout.='<span class="videogallery--navigation-outer--block">';
            if($margs['skin']=='oasis'){
                $fout.='
<span class="block-thumb" style="background-image: url('.$thumb.');"></span>';
            }
            if($margs['skin']=='balne'){
                $fout.='
<span class="image-con"><span class="hover-rect"></span><img class="fullwidth" src="'.$thumb.'"/></span>';
            }
            $fout.='<span class="block-title">'.$val['title'].'</span>';
            
            if(isset($val['uploader']) && $val['uploader']!=''){
                $fout.='<span class="block-extra">'.__('by ', 'dzsvg').'<strong>'.$val['uploader'].'</strong>'.'</span>';
            }

            $fout.='</span>';
            
            
            if($ix%$maxblocksperrow===($maxblocksperrow-1)){
                $fout.='</div>';
                $nr_pages++;
            }
            
            
            $ix++;

//                print_r($val);
        }
        
        // -- hier
        if($ix%$maxblocksperrow<=($maxblocksperrow-1) && $ix%$maxblocksperrow>0){
            $fout.='</div>';
            $nr_pages++;
        }
        $fout.='</div></div>';
        
        if($nr_pages>1){
            $fout.='<div class="videogallery--navigation-outer--bullets-con">';
            for($i=0;$i<$nr_pages;++$i){
                $fout.='<span class="navigation-outer--bullet';
                if($i==0){
                    $fout.=' active';
                }
                $fout.='"></span>';
            }
            $fout.='</div>';
        }
                    
                        
        
        $fout.='</div>';
        
        return $fout;
    }

    function show_shortcode_links($atts,$content = null) {
        //[videogallerylinks ids="2,3" height="300" source="pathtomp4.mp4" type="normal"]
        global $post;
        //print_r($post);
        $fout = '';
        //$this->sliders_index++;

        $this->front_scripts();

        $args = array(
            'ids' => '',
            'width' => 400,
            'height' => 300,
            'source' => '',
            'sourceogg' => '',
            'type' => 'normal',
            'autoplay' => 'on',
            'design_skin' => 'skin_aurora',
            'gallery_nav_type' => 'thumbs',
            'menuitem_width' => '275',
            'menuitem_height' => '75',
            'menuitem_space' => '1',
            'settings_ajax_extradivs' => '',
        );
        $args = array_merge($args,$atts);
        //print_r($args);
        if ($args['gallery_nav_type'] == 'scroller') {
            wp_enqueue_style('dzs.scroller',$this->thepath.'dzsscroller/scroller.css');
            wp_enqueue_script('dzs.scroller',$this->thepath.'dzsscroller/scroller.js');
        }
        $its = array();
        $ind_post = 0;
        $array_ids = explode(',',$args['ids']);
        //print_r($array_ids); print_r($args);
        foreach ($array_ids as $id) {
            $po = get_post($id);
            array_push($its,$po);
        }
        //print_r($its);
        $this->sliders_index++;

        $fout.='<div class="videogallery-with-links">';
        //==start vg-con
        $fout.='<div class="videogallery-con currGallery" style="width:'.$args['menuitem_width'].'px; height:'.$args['height'].'px; float:right; padding-top: 0; padding-bottom: 0;">';
        $fout.='<div class="preloader"></div>';
        $fout.='<div id="vg'.$this->sliders_index.'" class="videogallery skin_default" >';

        $i = 0;
        foreach ($its as $it) {

            $the_src = wp_get_attachment_image_src(get_post_thumbnail_id($it->ID),'full');
            $fout.='<div class="vplayer-tobe" data-videoTitle="'.$it->post_title.'" data-type="link" data-src="'.get_permalink($it->ID).'">
<div class="menuDescription"><img src="'.$the_src[0].'" class="imgblock"/>
<div class="the-title">'.$it->post_title.'</div>'.$it->post_excerpt.'</div>
</div>';
            if ($it->ID == $post->ID) {
                $ind_post = $i;
            }
            $i++;
        }

        $fout.='</div>'; //==end vg
        $fout.='</div>'; //==end vg-con
        $fout.='';
        $fout.='<div class="history-video-element" style="overflow: hidden;">
<div id="vphistory" class="vplayer-tobe" data-videoTitle="" data-img="" data-type="'.$args['type'].'" data-src="'.$args['source'].'"';
        if ($args['sourceogg'] != '') {
            if (strpos($args['sourceogg'],'.webm') === false) {
                $fout.=' data-sourceogg="'.$args['sourceogg'].'"';
            } else {
                $fout.=' data-sourcewebm="'.$args['sourceogg'].'"';
            }
        }
        $fout.='>
</div>
<div class="nest-script">
<div class="toexecute" style="display:none">
jQuery(document).ready(function($){
    var videoplayersettings = {
        autoplay : "'.$args['autoplay'].'"
        ,controls_out_opacity : 0.9
        ,controls_normal_opacity : 0.9
        ,settings_hideControls : "off"
        ,design_skin: "skin_aurora"
	,settings_swfPath : "'.$this->thepath.'preview.swf"
    };
    $("#vphistory").vPlayer(videoplayersettings);
})
</div>
</div>
</div>';

        $fout.='<script>
jQuery(".toexecute").each(function(){
    var _t = jQuery(this);
    if(_t.hasClass("executed")==false){
        eval(_t.text());
        _t.addClass("executed");
    }
})
jQuery(document).ready(function($){
dzsvg_init("#vg'.$this->sliders_index.'", {
    totalWidth:"'.$args['menuitem_width'].'"
    ,settings_mode:"normal"
    ,menuSpace:0
    ,randomise:"off"
    ,autoplay :"'.$args['autoplay'].'"
    ,cueFirstVideo: "off"
    ,autoplayNext : "on"
    ,nav_type: "'.$args['gallery_nav_type'].'"
    ,menuitem_width:"'.$args['menuitem_width'].'"
    ,menuitem_height:"'.$args['menuitem_height'].'"
    ,menuitem_space:"'.$args['menuitem_space'].'"
    ,menu_position:"right"
    ,transition_type:"fade"
    ,design_skin: "skin_navtransparent"
    ,embedCode:""
    ,shareCode:""
    ,logo: ""
    ,design_shadow:"off"
    ,settings_disableVideo:"on"
    ,startItem: "'.$ind_post.'"
    ,settings_enableHistory: "on"
        ,settings_ajax_extraDivs : "'.$args['settings_ajax_extradivs'].'"
});
});
</script>';
        $fout.='</div>';

        return $fout;
    }

    function show_shortcode($atts) {
        global $post;
        $fout = '';
        $iout = ''; //items parse

//        echo 'show_shortcode()';

        if ($this->mainoptions['debug_mode'] == 'on') {
            echo 'memory usage - ' . memory_get_usage() . "\n";
        }

        $margs = array(
            'id' => 'default'
            ,'db' => ''
            ,'category' => ''
            ,'fullscreen' => 'off'
            ,'settings_separation_mode' => 'normal'  // === normal ( no pagination ) or pages or scroll or button
            ,'settings_separation_pages_number' => '5'//=== the number of items per 'page'
            ,'settings_separation_paged' => '0'//=== the page number
            ,'return_mode' => 'normal' // -- "normal" returns the whole gallery, "items" returns the items array, "parsed items" returns the parsed items ( for pagination for example ) 
        );

        if ($atts == '') {
            $atts = array();
        }

        $margs = array_merge($margs,$atts);

        if (isset($_GET['dzsvg_settings_separation_paged'])) {
            $margs['settings_separation_paged'] = $_GET['dzsvg_settings_separation_paged'];
        }
        
        $extra_galleries = array();

        //===setting up the db
        $currDb = '';
        if (isset($margs['db']) && $margs['db'] != '') {
            $this->currDb = $margs['db'];
            $currDb = $this->currDb;
        }
        $this->dbs = get_option($this->dbdbsname);

        //echo 'ceva'; print_r($this->dbs);
        if ($currDb != 'main' && $currDb != '') {
            $dbitemsname = $this->dbitemsname.'-'.$currDb;
            $this->mainitems = get_option($dbitemsname);
        }
        //===setting up the db END
//        print_r($margs) ; echo $this->dbitemsname; print_r($this->mainitems);


        if ($this->mainitems == '') {
            return;
        }

        $this->front_scripts();


        if($margs['return_mode']=='normal'){ $this->sliders_index++; }
        


        $i = 0;
        $k = 0;
        $id = 'default';
        if (isset($margs['id'])) {
            $id = $margs['id'];
        }



        //---- extra galleries code
        
        if(strpos($id,',')!==false){
            $auxa = explode(",", $id);
            $id = $auxa[0];
            
            unset($auxa[0]);
            $extra_galleries = $auxa;
//            print_r($auxa);
        }

        //echo 'ceva' . $id;
        for ($i = 0; $i < count($this->mainitems); $i++) {
            if ((isset($id)) && ($id == $this->mainitems[$i]['settings']['id']))
                $k = $i;
        }

        $its = $this->mainitems[$k];
//        print_r($this->mainitems);


        $vpsettingsdefault = array(
            'id' => 'default',
            'skin_html5vp' => 'skin_aurora',
            'html5design_controlsopacityon' => '1',
            'html5design_controlsopacityout' => '1',
            'defaultvolume' => '',
            'youtube_sdquality' => 'small',
            'youtube_hdquality' => 'hd720',
            'youtube_defaultquality' => 'hd',
            'yt_customskin' => 'on',
            'vimeo_byline' => '0',
            'vimeo_portrait' => '0',
            'vimeo_color' => '',
            'settings_video_overlay' => 'off',
        );

        $vpsettings = array();


        $i = 0;
        $vpconfig_k = 0;
        $vpconfig_id = $its['settings']['vpconfig'];
        for ($i = 0; $i < count($this->mainvpconfigs); $i++) {
            if ((isset($vpconfig_id)) && ($vpconfig_id == $this->mainvpconfigs[$i]['settings']['id'])) {
                $vpconfig_k = $i;
            }
        }
        $vpsettings = $this->mainvpconfigs[$vpconfig_k];

        if (!isset($vpsettings['settings']) || $vpsettings['settings'] == '') {
            $vpsettings['settings'] = array();
        }

        $vpsettings['settings'] = array_merge($vpsettingsdefault,$vpsettings['settings']);

        unset($vpsettings['settings']['id']);
        //print_r($vpsettings);
        if (is_array($its['settings']) == false) {
            $its['settings'] = array();
        }
        $its['settings'] = array_merge($its['settings'],$vpsettings['settings']);
        //print_r($its);



        if ($post && $this->sliders_index == 1) {
            if (get_post_meta($post->ID,'dzsvg_preview',true) == 'on') {
                wp_enqueue_script('preseter',$this->thepath.'preseter/preseter.js');
                wp_enqueue_style('preseter',$this->thepath.'preseter/preseter.css');
                echo '<div class="preseter"><div class="the-icon"></div>
<div class="the-content"><h3>Quick Config</h3>
<form method="GET">
<div class="setting">
<div class="alabel">Menu Position:</div>
<div class="select-wrapper"><span>right</span><select name="opt3" class="textinput short"><option>right</option><option>down</option><option>up</option><option>left</option><option>none</option></select></div>
</div>
<div class="setting">
<div class="alabel">Autoplay:</div>
<div class="select-wrapper"><span>on</span><select name="opt4" class="textinput short"><option value="on">'.__('on','dzsvg').'</option><option value="off">'.__('off','dzsvg').'</option></select></div>
</div>
<div class="setting type_all">
    <div class="setting-label">'.__('Feed From','dzsvg').'</div>
    <div class="select-wrapper"><span>normal</span><select class="textinput styleme" name="feedfrom">
        <option>ytuserchannel</option>
        <option>ytkeywords</option>
        <option>ytplaylist</option>
        <option>vmuserchannel</option>
        <option>vmchannel</option>
    </select></div>
</div>
<div class="setting">
    <div class="alabel">Target Feed User</div>
    <div class="sidenote">Or playlist ID if you have selected playlist in the dropdown</div>
    <input type="text" name="opt6" value="digitalzoomstudio"/>
</div>
<div class="setting">
    <input type="submit" class="button-primary" name="submiter" value="Submit"/>
</div>
</form>
</div><!--end the-content-->
</div>';
                if (isset($_GET['opt3'])) {
                    $its['settings']['nav_type'] = 'none';
                    $its['settings']['menuposition'] = $_GET['opt3'];
                    $its['settings']['autoplay'] = $_GET['opt4'];
                    $its['settings']['feedfrom'] = $_GET['feedfrom'];
                    $its['settings']['youtubefeed_user'] = $_GET['opt6'];
                    $its['settings']['ytkeywords_source'] = $_GET['opt6'];
                    $its['settings']['ytplaylist_source'] = $_GET['opt6'];
                    $its['settings']['vimeofeed_user'] = $_GET['opt6'];
                    $its['settings']['vimeofeed_channel'] = $_GET['opt6'];
                }
            }
        }//----dzsvg preview END


        if ($its['settings']['nav_type'] == 'scroller') {
            wp_enqueue_style('dzs.scroller',$this->thepath.'dzsscroller/scroller.css');
            wp_enqueue_script('dzs.scroller',$this->thepath.'dzsscroller/scroller.js');
        }

        $w = $its['settings']['width'].'px';
        $h = $its['settings']['height'].'px';
        $fullscreenclass = '';
        $theclass = 'videogallery';
        //echo $id;
        //$fout.='<div class="videogallery-con" style="width:'.$w.'; height:'.$h.'; opacity:0;">';
        if ($margs['category'] != '') {
            $its['settings']['autoplay'] = 'off';
        }


        $user_feed = '';
        $yt_playlist_feed = '';




        $skin_html5vg = 'skin_pro';
        if (isset($vpsettings['settings']['skin_html5vg']) == false || $vpsettings['settings']['skin_html5vg'] == 'skin_custom') {
            $skin_html5vg = 'skin_pro';
        } else {
            $skin_html5vg = $vpsettings['settings']['skin_html5vg'];
        }

        $skin_html5vp = 'skin_aurora';
        if (isset($vpsettings['settings']['skin_html5vp']) == false || $vpsettings['settings']['skin_html5vp'] == 'skin_custom') {
            $skin_html5vp = 'skin_pro';
        } else {
            $skin_html5vp = $vpsettings['settings']['skin_html5vp'];
        };

        $wmode = 'opaque';
        if (isset($its['settings']['windowmode'])) {
            $wmode = $its['settings']['windowmode'];
        }


        $targetfeed = '';
        $target_file = '';
        if (($its['settings']['feedfrom'] == 'ytuserchannel') && $its['settings']['youtubefeed_user'] != '') {
            $user_feed = $its['settings']['youtubefeed_user'];
            $targetfeed = $its['settings']['youtubefeed_user'];
        }
        if (($its['settings']['feedfrom'] == 'ytplaylist') && $its['settings']['ytplaylist_source'] != '') {
            $yt_playlist_feed = $its['settings']['ytplaylist_source'];
            $targetfeed = $its['settings']['ytplaylist_source'];

            if (substr($yt_playlist_feed,0,2) == "PL") {
                $yt_playlist_feed = substr($yt_playlist_feed,2);
            }
            $user_feed = '';
        }



//        echo 'feedfrom - '.$its['settings']['feedfrom'];
        // -- ---- ---- YouTube user feed ==========
        if (($its['settings']['feedfrom'] == 'ytuserchannel') && $its['settings']['youtubefeed_user'] != '') {

            // -- deleting all items
            $len = count($its) - 1;
            for ($i = 0; $i < $len; $i++) {
                unset($its[$i]);
            }
            //echo $target_file;


            $cacher = get_option('dzsvg_cache_ytuserchannel');

            $cached = false;


            if ($cacher == false || is_array($cacher) == false || $this->mainoptions['disable_api_caching'] == 'on') {
                $cached = false;
            } else {

//                print_r($cacher);


                $ik = -1;
                $i = 0;
                for ($i = 0; $i < count($cacher); $i++) {
                    if ($cacher[$i]['id'] == $targetfeed) {
                        if ($_SERVER['REQUEST_TIME'] - $cacher[$i]['time'] < 3600) {
                            $ik = $i;

//                                echo 'yabebe';
                            $cached = true;
                            break;
                        }
                    }
                }


                if($cached) {
                    foreach ($cacher[$ik]['items'] as $lab => $item) {
                        if ($lab === 'settings') {
                            continue;
                        }

                        $its[$lab] = $item;
                    }
                }

            }
            $i = 0;



//            echo 'iscached - ' . $cached;


            if (!$cached) {


                // -- if not cached

                $target_file = 'https://www.googleapis.com/youtube/v3/search?q=' . $targetfeed . '&key=' . $this->mainoptions['youtube_api_key'] . '&type=channel&part=snippet';


                $ida = DZSHelpers::get_contents($target_file, array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));


                if ($this->mainoptions['debug_mode'] == 'on') {
                    echo 'debug mode:  ida is is...<br>';
                    print_r($ida);
                    echo '<br/>';
                }

//            echo 'ceva'.$ida;

                if (isset($its['settings']['youtubefeed_maxvideos']) == false || $its['settings']['youtubefeed_maxvideos'] == '') {
                    $its['settings']['youtubefeed_maxvideos'] = 50;
                }
                $yf_maxi = $its['settings']['youtubefeed_maxvideos'];

                if ($its['settings']['youtubefeed_maxvideos'] == 'all') {
                    $yf_maxi = 50;
                }


                if (!(isset($its['settings']['is_not_nicename']) && $its['settings']['is_not_nicename']=='on')) {


                    if ($ida) {

                        $obj = json_decode($ida);


                        if ($this->mainoptions['debug_mode'] == 'on') {
                            echo 'debug mode: is not nicename is ON, obj is is...<br>';
                            print_r($obj);
                            echo '<br/>';
                        }


                        if ($obj && is_object($obj)) {


                            if (isset($obj->items[0]->id->channelId)) {

//                        array_push($this->arr_api_errors, '<div class="dzsvg-error">'.__('This is dirty').'</div>');

                                $channel_id = $obj->items[0]->id->channelId;


                                $breaker = 0;
                                $nextPageToken = 'none';

                                while ($breaker < 10 || $nextPageToken !== '') {


                                    $str_nextPageToken = '';

                                    if ($nextPageToken && $nextPageToken != 'none') {
                                        $str_nextPageToken = '&pageToken=' . $nextPageToken;
                                    }


                                    if ($this->mainoptions['youtube_api_key'] == '') {
                                        $this->mainoptions['youtube_api_key'] = 'AIzaSyCtrnD7ll8wyyro5f1LitPggaSKvYFIvU4';
                                    }

                                    $target_file = 'https://www.googleapis.com/youtube/v3/search?key=' . $this->mainoptions['youtube_api_key'] . '&channelId=' . $channel_id . '&part=snippet&order=date&type=video' . $str_nextPageToken . '&maxResults=' . $yf_maxi;

//                        echo $target_file;

                                    $ida = DZSHelpers::get_contents($target_file, array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));


                                    if ($ida) {

                                        $obj = json_decode($ida);

//                                        print_r($obj);


                                        if ($obj && is_object($obj)) {

//                                        print_r($obj);

                                            if (isset($obj->items[0]->id->videoId)) {


                                                foreach ($obj->items as $ytitem) {
//                    print_r($ytitem); echo $ytitem->id->videoId;


                                                    if (isset($ytitem->id->videoId) == false) {
                                                        echo 'this does not have id ? ';
                                                        continue;
                                                    }
                                                    $its[$i]['source'] = $ytitem->id->videoId;
                                                    $its[$i]['thethumb'] = $ytitem->snippet->thumbnails->medium->url;
                                                    $its[$i]['type'] = "youtube";

                                                    $aux = $ytitem->snippet->title;
                                                    $lb = array('"', "\r\n", "\n", "\r", "&", "-", "`", '???', "'", '-');
                                                    $aux = str_replace($lb, ' ', $aux);
                                                    $its[$i]['title'] = $aux;

                                                    $aux = $ytitem->snippet->description;
                                                    $lb = array("\r\n","\n","\r");
                                                    $aux = str_replace($lb,'<br>',$aux);
                                                    $lb = array('"');
                                                    $aux = str_replace($lb,'&quot;',$aux);
                                                    $lb = array("'");
                                                    $aux = str_replace($lb,'&#39;',$aux);


                                                    $auxcontent = '<p>' . str_replace(array("\r\n", "\n", "\r"), '</p><p>', $aux) . '</p>';

                                                    $its[$i]['description'] = $auxcontent;
                                                    $its[$i]['menuDescription'] = $auxcontent;

//                    print_r($its['settings']);
                                                    if ($its['settings']['enable_outernav_video_author'] == 'on') {
//                        echo 'ceva';
                                                        $its[$i]['uploader'] = $ytitem->snippet->channelTitle;
                                                    }

                                                    $i++;


//                                            if ($i > $yf_maxi + 1){ break; }

                                                }


                                            } else {

                                                array_push($this->arr_api_errors, '<div class="dzsvg-error">' . __('No videos to be found') . '</div>');
                                            }
//                                print_r($obj);
                                        } else {

                                            array_push($this->arr_api_errors, '<div class="dzsvg-error">' . __('Object channel is not JSON...') . '</div>');
                                        }
                                    } else {

                                        array_push($this->arr_api_errors, '<div class="dzsvg-error">' . __('Cannot get info from YouTube API about channel') . '</div>');
                                    }


                                    if ($its['settings']['youtubefeed_maxvideos'] === 'all') {

                                        if (isset($obj->nextPageToken) && $obj->nextPageToken) {
                                            $nextPageToken = $obj->nextPageToken;
                                        } else {

                                            $nextPageToken = '';
                                            break;
                                        }

                                    } else {
                                        $nextPageToken = '';
                                        break;
                                    }

                                    $breaker++;
                                }


                                $sw34 = false;
                                $auxa34 = array(
                                    'id' => $targetfeed
                                , 'items' => $its
                                , 'time' => $_SERVER['REQUEST_TIME']

                                );

                                if (!is_array($cacher)) {
                                    $cacher = array();
                                } else {


                                    foreach ($cacher as $lab => $cach) {
                                        if ($cach['id'] == $targetfeed) {
                                            $sw34 = true;

                                            $cacher[$lab] = $auxa34;

                                            update_option('dzsvg_cache_ytuserchannel', $cacher);

//                                        print_r($cacher);
                                            break;
                                        }
                                    }


                                }

                                if ($sw34 == false) {

                                    array_push($cacher, $auxa34);

//                                            print_r($cacher);

                                    update_option('dzsvg_cache_ytuserchannel', $cacher);
                                }


                            } else {

                                array_push($this->arr_api_errors, '<div class="dzsvg-error">' . __('Cannot access channel ID, this is feed - ') . $target_file . '</div>');
                            }
                        } else {

                            array_push($this->arr_api_errors, '<div class="dzsvg-error">' . __('Object is not JSON...') . '</div>');
                        }
                    }
                } else {
                    {

//                        array_push($this->arr_api_errors, '<div class="dzsvg-error">'.__('This is dirty').'</div>');

                        $channel_id = $targetfeed;


                        $breaker = 0;
                        $nextPageToken = 'none';

                        while ($breaker < 10 || $nextPageToken !== '') {


                            $str_nextPageToken = '';

                            if ($nextPageToken && $nextPageToken != 'none') {
                                $str_nextPageToken = '&pageToken=' . $nextPageToken;
                            }


                            if ($this->mainoptions['youtube_api_key'] == '') {
                                $this->mainoptions['youtube_api_key'] = 'AIzaSyCtrnD7ll8wyyro5f1LitPggaSKvYFIvU4';
                            }

                            $target_file = 'https://www.googleapis.com/youtube/v3/search?key=' . $this->mainoptions['youtube_api_key'] . '&channelId=' . $channel_id . '&part=snippet&order=date&type=video' . $str_nextPageToken . '&maxResults=' . $yf_maxi;

//                        echo $target_file;

                            $ida = DZSHelpers::get_contents($target_file, array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));


                            if ($ida) {


                                $obj = json_decode($ida);


                                if ($obj && is_object($obj)) {

//                                        print_r($obj);

                                    if (isset($obj->items[0]->id->videoId)) {


                                        foreach ($obj->items as $ytitem) {
                    print_r($ytitem); echo $ytitem->id->videoId;


                                            if (isset($ytitem->id->videoId) == false) {
                                                echo 'this does not have id ? ';
                                                continue;
                                            }
                                            $its[$i]['source'] = $ytitem->id->videoId;
                                            $its[$i]['thethumb'] = $ytitem->snippet->thumbnails->medium->url;
                                            $its[$i]['type'] = "youtube";

                                            $aux = $ytitem->snippet->title;
                                            $lb = array('"', "\r\n", "\n", "\r", "&", "-", "`", '???', "'", '-');
                                            $aux = str_replace($lb, ' ', $aux);
                                            $its[$i]['title'] = $aux;

                                            $aux = $ytitem->snippet->description;
                                            $lb = array("\r\n","\n","\r");
                                            $aux = str_replace($lb,'<br>',$aux);
                                            $lb = array('"');
                                            $aux = str_replace($lb,'&quot;',$aux);
                                            $lb = array("'");
                                            $aux = str_replace($lb,'&#39;',$aux);


                                            $auxcontent = '<p>' . str_replace(array("\r\n", "\n", "\r"), '</p><p>', $aux) . '</p>';

                                            $its[$i]['description'] = $auxcontent;
                                            $its[$i]['menuDescription'] = $auxcontent;

//                    print_r($its['settings']);
                                            if ($its['settings']['enable_outernav_video_author'] == 'on') {
//                        echo 'ceva';
                                                $its[$i]['uploader'] = $ytitem->snippet->channelTitle;
                                            }

                                            $i++;


//                                            if ($i > $yf_maxi + 1){ break; }

                                        }


                                    } else {

                                        array_push($this->arr_api_errors, '<div class="dzsvg-error">' . __('No videos to be found') . '</div>');
                                    }
//                                print_r($obj);
                                } else {

                                    array_push($this->arr_api_errors, '<div class="dzsvg-error">' . __('Object channel is not JSON...') . '</div>');
                                }
                            } else {

                                array_push($this->arr_api_errors, '<div class="dzsvg-error">' . __('Cannot get info from YouTube API about channel') . '</div>');
                            }


                            if ($its['settings']['youtubefeed_maxvideos'] === 'all') {

                                if (isset($obj->nextPageToken) && $obj->nextPageToken) {
                                    $nextPageToken = $obj->nextPageToken;
                                } else {

                                    $nextPageToken = '';
                                    break;
                                }

                            } else {
                                $nextPageToken = '';
                                break;
                            }

                            $breaker++;
                        }


                        $sw34 = false;
                        $auxa34 = array(
                            'id' => $targetfeed
                        , 'items' => $its
                        , 'time' => $_SERVER['REQUEST_TIME']
                        , 'maxlen' => $its['settings']['youtubefeed_maxvideos']

                        );

                        if (!is_array($cacher)) {
                            $cacher = array();
                        } else {


                            foreach ($cacher as $lab => $cach) {
                                if ($cach['id'] == $targetfeed) {
                                    $sw34 = true;

                                    $cacher[$lab] = $auxa34;

                                    update_option('dzsvg_cache_ytuserchannel', $cacher);

//                                        print_r($cacher);
                                    break;
                                }
                            }


                        }

                        if ($sw34 == false) {

                            array_push($cacher, $auxa34);

//                                            print_r($cacher);

                            update_option('dzsvg_cache_ytuserchannel', $cacher);
                        }


                    }
                }


            }




        }
        // -- END YT USER CHANNEL





        //==============START youtube playlist
        if (($its['settings']['feedfrom'] == 'ytplaylist') && $its['settings']['ytplaylist_source'] != '') {



            $len = count($its) - 1;
            for ($i = 0; $i < $len; $i++) {
                unset($its[$i]);
            }



            $targetfeed = $its['settings']['ytplaylist_source'];





            $cacher = get_option('dzsvg_cache_ytplaylist');

            $cached = false;
            $found_for_cache = false;


            if ($cacher == false || is_array($cacher) == false || $this->mainoptions['disable_api_caching'] == 'on') {
                $cached = false;
            } else {

//                print_r($cacher);


                $ik = -1;
                $i = 0;
                for ($i = 0; $i < count($cacher); $i++) {
                    if ($cacher[$i]['id'] == $targetfeed) {
                        if(isset($cacher[$i]['maxlen']) && $cacher[$i]['maxlen'] == $its['settings']['youtubefeed_maxvideos']){
                            if ($_SERVER['REQUEST_TIME'] - $cacher[$i]['time'] < 3600) {
                                $ik = $i;

//                                echo 'yabebe';
                                $cached = true;
                                break;
                            }
                        }

                    }
                }


                if($cached){

                    foreach ($cacher[$ik]['items'] as $lab => $item) {
                        if ($lab === 'settings') {
                            continue;
                        }

                        $its[$lab] = $item;

//                        print_r($item);
//                        echo 'from cache';
                    }

                }
            }



            if ($this->mainoptions['debug_mode'] == 'on') {
                echo 'is cached - '.$cached.' | ';
            }



            if(!$cached){
                if (isset($its['settings']['youtubefeed_maxvideos']) == false || $its['settings']['youtubefeed_maxvideos'] == '') {
                    $its['settings']['youtubefeed_maxvideos'] = 50;
                }
                $yf_maxi = $its['settings']['youtubefeed_maxvideos'];

                if ($its['settings']['youtubefeed_maxvideos'] == 'all') {
                    $yf_maxi = 50;
                }



                $breaker = 0;

                $i_for_its = 0;
                $nextPageToken = 'none';

                while ($breaker < 10 || $nextPageToken !== '') {


                    $str_nextPageToken = '';

                    if ($nextPageToken && $nextPageToken != 'none') {
                        $str_nextPageToken = '&pageToken=' . $nextPageToken;
                    }

//                echo '$breaker is '.$breaker;

                    if($this->mainoptions['youtube_api_key']==''){
                        $this->mainoptions['youtube_api_key'] = 'AIzaSyCtrnD7ll8wyyro5f1LitPggaSKvYFIvU4';
                    }


                    $target_file='https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=' . $targetfeed . '&key=' . $this->mainoptions['youtube_api_key'] . '' . $str_nextPageToken . '&maxResults='.$yf_maxi;

//                    echo $target_file;


                    if ($this->mainoptions['debug_mode'] == 'on') {
                        echo 'target file - '.$target_file;
                    }


                    $ida = DZSHelpers::get_contents($target_file, array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));

//            echo 'ceva'.$ida;

                    if ($ida) {

                        $obj = json_decode($ida);


                        if ($obj && is_object($obj)) {
//                            print_r($obj);


                            if ($obj && is_object($obj)) {

//                                        print_r($obj);

                                if (isset($obj->items[0]->snippet->resourceId->videoId)) {


                                    foreach ($obj->items as $ytitem) {
//                                print_r($ytitem);


                                        if (isset($ytitem->snippet->resourceId->videoId) == false) {
                                            echo 'this does not have id ? ';
                                            continue;
                                        }
                                        $its[$i_for_its]['source'] = $ytitem->snippet->resourceId->videoId;
                                        $its[$i_for_its]['thethumb'] = $ytitem->snippet->thumbnails->medium->url;
                                        $its[$i_for_its]['type'] = "youtube";

                                        $aux = $ytitem->snippet->title;
                                        $lb = array('"', "\r\n", "\n", "\r", "&", "-", "`", '???', "'", '-');
                                        $aux = str_replace($lb, ' ', $aux);
                                        $its[$i_for_its]['title'] = $aux;

                                        $aux = $ytitem->snippet->description;
                                        $lb = array("\r\n","\n","\r");
                                        $aux = str_replace($lb,'<br>',$aux);
                                        $lb = array('"');
                                        $aux = str_replace($lb,'&quot;',$aux);
                                        $lb = array("'");
                                        $aux = str_replace($lb,'&#39;',$aux);


                                        $auxcontent = '<p>' . str_replace(array("\r\n", "\n", "\r"), '</p><p>', $aux) . '</p>';

                                        $its[$i_for_its]['description'] = $auxcontent;
                                        $its[$i_for_its]['menuDescription'] = $auxcontent;

//                    print_r($its['settings']);
                                        if ($its['settings']['enable_outernav_video_author'] == 'on') {
//                        echo 'ceva';
                                            $its[$i_for_its]['uploader'] = $ytitem->snippet->channelTitle;
                                        }

                                        $i_for_its++;


                                    }

                                    $found_for_cache=true;


                                } else {

                                    array_push($this->arr_api_errors, '<div class="dzsvg-error">' . __('No youtube playlist videos to be found - maybe API key not set ? This is the feed - '.$target_file) . '</div>');
                                }
                            }
                        }






                        if ($its['settings']['youtubefeed_maxvideos'] === 'all') {

                            if (isset($obj->nextPageToken) && $obj->nextPageToken) {
                                $nextPageToken = $obj->nextPageToken;
                            } else {

                                $nextPageToken = '';
                                break;
                            }

                        } else {
                            $nextPageToken = '';
                            break;
                        }


                    }
                    $breaker++;
                }





                if($found_for_cache){

                    $sw34 = false;
                    $auxa34 = array(
                        'id' => $targetfeed
                    , 'items' => $its
                    , 'time' => $_SERVER['REQUEST_TIME']
                    , 'maxlen' => $its['settings']['youtubefeed_maxvideos']

                    );

                    if (!is_array($cacher)) {
                        $cacher = array();
                    } else {


                        foreach ($cacher as $lab => $cach) {
                            if ($cach['id'] == $targetfeed) {
                                $sw34 = true;

                                $cacher[$lab] = $auxa34;

                                update_option('dzsvg_cache_ytplaylist', $cacher);

//                                        print_r($cacher);
                                break;
                            }
                        }


                    }

                    if ($sw34 == false) {

                        array_push($cacher, $auxa34);

//                                            print_r($cacher);

                        update_option('dzsvg_cache_ytplaylist', $cacher);
                    }
                }
            }


        }
        //=======END youtube playlist
        //
        //




        //==============START youtube keywords
        if (($its['settings']['feedfrom'] == 'ytkeywords') && $its['settings']['ytkeywords_source'] != '') {



            $len = count($its) - 1;
            for ($i = 0; $i < $len; $i++) {
                unset($its[$i]);
            }



            $targetfeed = $its['settings']['ytkeywords_source'];




            $cacher = get_option('dzsvg_cache_ytkeywords');

            $cached = false;
            $found_for_cache = false;


            if ($cacher == false || is_array($cacher) == false || $this->mainoptions['disable_api_caching'] == 'on') {
                $cached = false;
            } else {

//                print_r($cacher);


                $ik = -1;
                $i = 0;
                for ($i = 0; $i < count($cacher); $i++) {
                    if ($cacher[$i]['id'] == $targetfeed) {
                        if ($_SERVER['REQUEST_TIME'] - $cacher[$i]['time'] < 3600) {
                            $ik = $i;

//                                echo 'yabebe';
                            $cached = true;
                            break;
                        }
                    }
                }


                if($cached){

                    foreach ($cacher[$ik]['items'] as $lab => $item) {
                        if ($lab === 'settings') {
                            continue;
                        }

                        $its[$lab] = $item;

//                        print_r($item);
//                        echo 'from cache';
                    }

                }
            }




            if(!$cached){
                if (isset($its['settings']['youtubefeed_maxvideos']) == false || $its['settings']['youtubefeed_maxvideos'] == '') {
                    $its['settings']['youtubefeed_maxvideos'] = 50;
                }
                $yf_maxi = $its['settings']['youtubefeed_maxvideos'];

                if ($its['settings']['youtubefeed_maxvideos'] == 'all') {
                    $yf_maxi = 50;
                }



                $breaker = 0;

                $i_for_its = 0;
                $nextPageToken = 'none';

                while ($breaker < 5 || $nextPageToken !== '') {


                    $str_nextPageToken = '';

                    if ($nextPageToken && $nextPageToken != 'none') {
                        $str_nextPageToken = '&pageToken=' . $nextPageToken;
                    }

//                echo '$breaker is '.$breaker;


                    $targetfeed = str_replace(' ','+',$targetfeed);


                    if($this->mainoptions['youtube_api_key']==''){
                        $this->mainoptions['youtube_api_key'] = 'AIzaSyCtrnD7ll8wyyro5f1LitPggaSKvYFIvU4';
                    }

                    $target_file='https://www.googleapis.com/youtube/v3/search?part=snippet&q=' . $targetfeed . '&type=video&key=' . $this->mainoptions['youtube_api_key'] . $str_nextPageToken.'&maxResults='.$yf_maxi;


                    $ida = DZSHelpers::get_contents($target_file, array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));

//            echo 'ceva'.$ida;

                    if ($ida) {

                        $obj = json_decode($ida);


                        if ($obj && is_object($obj)) {
//                                print_r($obj);



                                if (isset($obj->items[0]->id->videoId)) {


                                    foreach ($obj->items as $ytitem) {
//                                print_r($ytitem);


                                        if (isset($ytitem->id->videoId) == false) {
                                            echo 'this does not have id ? ';
                                            continue;
                                        }
                                        $its[$i_for_its]['source'] = $ytitem->id->videoId;
                                        $its[$i_for_its]['thethumb'] = $ytitem->snippet->thumbnails->medium->url;
                                        $its[$i_for_its]['type'] = "youtube";

                                        $aux = $ytitem->snippet->title;
                                        $lb = array('"', "\r\n", "\n", "\r", "&", "-", "`", '???', "'", '-');
                                        $aux = str_replace($lb, ' ', $aux);
                                        $its[$i_for_its]['title'] = $aux;

                                        $aux = $ytitem->snippet->description;
                                        $lb = array("\r\n","\n","\r");
                                        $aux = str_replace($lb,'<br>',$aux);
                                        $lb = array('"');
                                        $aux = str_replace($lb,'&quot;',$aux);
                                        $lb = array("'");
                                        $aux = str_replace($lb,'&#39;',$aux);


                                        $auxcontent = '<p>' . str_replace(array("\r\n", "\n", "\r"), '</p><p>', $aux) . '</p>';

                                        $its[$i_for_its]['description'] = $auxcontent;
                                        $its[$i_for_its]['menuDescription'] = $auxcontent;

//                    print_r($its['settings']);
                                        if ($its['settings']['enable_outernav_video_author'] == 'on') {
//                        echo 'ceva';
                                            $its[$i_for_its]['uploader'] = $ytitem->snippet->channelTitle;
                                        }

                                        $i_for_its++;

                                        $found_for_cache = true;

                                    }


                                } else {

                                    array_push($this->arr_api_errors, '<div class="dzsvg-error">' . __('No youtube keyboard videos to be found') . '</div>');
                                }

                        }






                        if ($its['settings']['youtubefeed_maxvideos'] === 'all') {

                            if (isset($obj->nextPageToken) && $obj->nextPageToken) {
                                $nextPageToken = $obj->nextPageToken;
                            } else {

                                $nextPageToken = '';
                                break;
                            }

                        } else {
                            $nextPageToken = '';
                            break;
                        }


                    }else{

                        array_push($this->arr_api_errors, '<div class="dzsvg-error">' . __('No youtube keyboards ida found '.$target_file) . '</div>');
                    }
                    $breaker++;
                }



                if($found_for_cache){

                    $sw34 = false;
                    $auxa34 = array(
                        'id' => $targetfeed
                    , 'items' => $its
                    , 'time' => $_SERVER['REQUEST_TIME']
                    , 'maxlen' => $its['settings']['youtubefeed_maxvideos']

                    );

                    if (!is_array($cacher)) {
                        $cacher = array();
                    } else {


                        foreach ($cacher as $lab => $cach) {
                            if ($cach['id'] == $targetfeed) {
                                $sw34 = true;

                                $cacher[$lab] = $auxa34;

                                update_option('dzsvg_cache_ytkeywords', $cacher);

//                                        print_r($cacher);
                                break;
                            }
                        }


                    }


                    if ($sw34 == false) {

                        array_push($cacher, $auxa34);

//                                            print_r($cacher);

                        update_option('dzsvg_cache_ytkeywords', $cacher);
                    }
                }



            }


        }
        //=======END youtube keywords
        //
        //














        //------start vimeo user channel //http://vimeo.com/api/v2/blakewhitman/videos.json
        if (($its['settings']['feedfrom'] == 'vmuserchannel') && $its['settings']['vimeofeed_user'] != '') {






            $len = count($its) - 1;
            for ($i = 0; $i < $len; $i++) {
                unset($its[$i]);
            }
            $target_file = "http://vimeo.com/api/v2/".$its['settings']['vimeofeed_user']."/videos.json";

            $cacher = get_option('cache_dzsvg_vmuser');

            if ($cacher == '') {
                $cacher = array();
            }
            if (count($cacher) == 0 || $this->mainoptions['disable_api_caching'] == 'on') {


                $ida = '';
                if ($this->mainoptions['vimeo_api_user_id'] != '' && $this->mainoptions['vimeo_api_client_id'] != '' && $this->mainoptions['vimeo_api_client_secret'] != '' && $this->mainoptions['vimeo_api_access_token'] != '') {



                    if (!class_exists('VimeoAPIException')) {
                        require_once(dirname(__FILE__).'/vimeoapi/vimeo.php');
                    }

                    
                    $vimeo_id = $this->mainoptions['vimeo_api_user_id']; // Get from https://vimeo.com/settings, must be in the form of user123456
                    $consumer_key = $this->mainoptions['vimeo_api_client_id'];
                    $consumer_secret = $this->mainoptions['vimeo_api_client_secret'];
                    $token = $this->mainoptions['vimeo_api_access_token'];

                    // Do an authentication call        
                    $vimeo = new Vimeo($consumer_key,$consumer_secret);
                    $vimeo->setToken($token); //,$token_secret
//                    $vimeo->user_id = $vimeo_id;
//                        echo $this->mainoptions['disable_api_caching'].'hmmdada/channels/' . $its['settings']['vimeofeed_channel'];

                            $vimeo_response = $vimeo->request('/users/'.$its['settings']['vimeofeed_user'].'/videos');
//                            print_r($vimeo_response);
                            
                    if ($vimeo_response['status'] != 200) {
//                        throw new Exception($channel_videos['body']['message']);
                        echo 'vimeo error';
                    }

                            if (isset($vimeo_response['body']['data'])) {
                                $ida = $vimeo_response['body']['data'];
                            }
                    
                    
                } else {
                    $ida = DZSHelpers::get_contents($target_file,array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));
                }

                $jida = $ida;
                if (is_array($ida)) {
                    $jida = json_encode($ida);
                }

                $cache_mainaux = array();
                $cache_aux = array(
                    'output' => $jida
                    ,'username' => $its['settings']['vimeofeed_user']
                    ,'time' => $_SERVER['REQUEST_TIME']
                );
                array_push($cache_mainaux,$cache_aux);
                update_option('cache_dzsvg_vmuser',$cache_mainaux);
            } else {
                if (is_array($cacher)) {
                    $ik = -1;
                    for ($i = 0; $i < count($cacher); $i++) {
                        if ($cacher[$i]['username'] == $its['settings']['vimeofeed_user']) {
                            if ($_SERVER['REQUEST_TIME'] - $cacher[$i]['time'] < 3600) {
                                $ik = $i;
                                break;
                            }
                        }
                    }
                    if ($ik > -1) {
                        $ida = $cacher[$ik]['output'];
                    } else {
                        $ida = '';
                        if ($this->mainoptions['vimeo_api_user_id'] != '' && $this->mainoptions['vimeo_api_client_id'] != '' && $this->mainoptions['vimeo_api_client_secret'] != '' && $this->mainoptions['vimeo_api_access_token'] != '') {



                            if (!class_exists('VimeoAPIException')) {
                                require_once(dirname(__FILE__).'/vimeoapi/vimeo.php');
                            }

                            
                            $vimeo_id = $this->mainoptions['vimeo_api_user_id']; // Get from https://vimeo.com/settings, must be in the form of user123456
                            $consumer_key = $this->mainoptions['vimeo_api_client_id'];
                            $consumer_secret = $this->mainoptions['vimeo_api_client_secret'];
                            $token = $this->mainoptions['vimeo_api_access_token'];

                            // Do an authentication call        
                            $vimeo = new Vimeo($consumer_key,$consumer_secret);
                            $vimeo->setToken($token); //,$token_secret
        //                    $vimeo->user_id = $vimeo_id;
                            $vimeo_response = $vimeo->request('/users/'.$its['settings']['vimeofeed_user'].'/videos');
//                            print_r($vimeo_response);
                            if ($vimeo_response['status'] != 200) {
                                throw new Exception($vimeo_response['body']['message']);
                            }
                            if (isset($vimeo_response['body']['data'])) {
                                $ida = $vimeo_response['body']['data'];
                            }
                    
                    
                    
                        } else {
                            $ida = DZSHelpers::get_contents($target_file,array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));
                        }


                        $jida = $ida;
                        if (is_array($ida)) {
                            $jida = json_encode($ida);
                        }

                        //=== we test if we already have the username - but old call - to replace that old call
                        $ik = -1;
                        for ($i = 0; $i < count($cacher); $i++) {
                            if ($cacher[$i]['username'] == $its['settings']['vimeofeed_user']) {
                                $ik = $i;
                                break;
                            }
                        }
                        $cache_aux = array(
                            'output' => $jida
                            ,'username' => $its['settings']['vimeofeed_user']
                            ,'time' => $_SERVER['REQUEST_TIME']
                        );
                        if ($ik > -1) {
                            $cacher[$ik] = $cache_aux;
                        } else {
                            array_push($cacher,$cache_aux);
                        }
                        update_option('cache_dzsvg_vmuser',$cacher);
                    }
                }
            }
            $idar = array();

            if ($this->mainoptions['debug_mode'] == 'on') {
                echo 'debug mode: ida is...<br>';
                print_r($ida);
                echo '<br/>';
                echo 'ida is object ';
                echo is_object($ida);
            }
            

//            print_r($ida);
            if (!is_object($ida) && !is_array($ida)) {
                $idar = json_decode($ida); // === vmuser
            } else {
                $idar = $ida;
            }

            $i = 0;

            if ($this->mainoptions['debug_mode'] == 'on') {
                echo 'debug mode: idar is...<br>';
                print_r($idar);
                echo '<br/>';
            }
            if (is_array($idar)) {
//                echo 'idararray'; print_r($idar); echo 'idaarrayend';
                foreach ($idar as $item) {
                    
                    if(is_object($item)){
//                        echo 'cev23a';
                        $item = (array) $item;
                    }

                    if(isset($item['uri'])){
                        $auxa = explode('/',$item['uri']);
                    }
                    if(isset($item['url'])){
                        $auxa = explode('/',$item['url']);
                    }
                    $its[$i]['source'] = $auxa[count($auxa) - 1];


//                    print_r($item);


                    $vimeo_quality_ind = 2;

                    if($this->mainoptions['vimeo_thumb_quality']=='medium'){

                        $vimeo_quality_ind = 3;
                    }

                    if($this->mainoptions['vimeo_thumb_quality']=='high'){

                        $vimeo_quality_ind = 4;
                    }

                    if(is_object($item['pictures'])){
                        $item['pictures'] = (array) $item['pictures'];
                        if(is_object($item['pictures']['sizes'])){
                            $item['pictures']['sizes'] = (array) $item['pictures']['sizes'];
                        }

                        if(is_object($item['pictures']['sizes'][$vimeo_quality_ind])){
                            $item['pictures']['sizes'][$vimeo_quality_ind] = (array) $item['pictures']['sizes'][$vimeo_quality_ind];
                        }
                        $its[$i]['thethumb'] = $item['pictures']['sizes'][$vimeo_quality_ind]['link'];
                    }else{

                        if(isset($item['thumbnail_medium'])){

                            $its[$i]['thethumb'] = $item['thumbnail_medium'];
                        }
                        if(isset($item['pictures']['sizes'][$vimeo_quality_ind]['link'])){

                            $its[$i]['thethumb'] = $item['pictures']['sizes'][$vimeo_quality_ind]['link'];
                        }
                    }
                    $its[$i]['type'] = "vimeo";

                    $aux = 'title';
                    if(isset($item['name'])){
                        $aux = $item['name'];

                    }
                    if(isset($item['title'])){
                        $aux = $item['title'];
                    }
                    $lb = array('"',"\r\n","\n","\r","&","-","`",'???',"'",'-');
                    $aux = str_replace($lb,' ',$aux);
                    $its[$i]['title'] = $aux;


                    $aux = $item['description'];

                    if($its['settings']['striptags']=='on'){
                        $aux = strip_tags($aux);
                    }

                    $lb = array("\r\n","\n","\r");
                    $aux = str_replace($lb,'<br>',$aux);
                    $lb = array('"');
                    $aux = str_replace($lb,'&quot;',$aux);
                    $lb = array("'");
                    $aux = str_replace($lb,'&#39;',$aux);
                    $its[$i]['description'] = $aux;
                    $its[$i]['menuDescription'] = $aux;
                    $i++;
                }
            } else {
                if (is_object($idar)) {
                    if (isset($idar->videos->video)) {
                        foreach ($idar->videos->video as $item) {

                            if(is_object($item)){
//                        echo 'cev23a';
                                $item = (array) $item;
                            }

                            if(isset($item['uri'])){
                                $auxa = explode('/',$item['uri']);
                            }
                            if(isset($item['url'])){
                                $auxa = explode('/',$item['url']);
                            }
                            $its[$i]['source'] = $auxa[count($auxa) - 1];




                            $vimeo_quality_ind = 2;

                            if($this->mainoptions['vimeo_thumb_quality']=='medium'){

                                $vimeo_quality_ind = 3;
                            }

                            if($this->mainoptions['vimeo_thumb_quality']=='high'){

                                $vimeo_quality_ind = 4;
                            }

//                            print_r($item);
                            if(is_object($item['pictures'])){
                                $item['pictures'] = (array) $item['pictures'];
                                if(is_object($item['pictures']['sizes'])){
                                    $item['pictures']['sizes'] = (array) $item['pictures']['sizes'];
                                }

                                if(is_object($item['pictures']['sizes'][$vimeo_quality_ind])){
                                    $item['pictures']['sizes'][$vimeo_quality_ind] = (array) $item['pictures']['sizes'][$vimeo_quality_ind];
                                }
//                        print_r($item);
                                $its[$i]['thethumb'] = $item['pictures']['sizes'][$vimeo_quality_ind]['link'];
                            }else{

                                $its[$i]['thethumb'] = $item['thumbnail_medium'];
                            }
                            $its[$i]['type'] = "vimeo";

                            $aux = 'title';
                            if(isset($item['name'])){
                                $aux = $item['name'];

                            }
                            if(isset($item['title'])){
                                $aux = $item['title'];
                            }
                            $lb = array('"',"\r\n","\n","\r","&","-","`",'???',"'",'-');
                            $aux = str_replace($lb,' ',$aux);
                            $its[$i]['title'] = $aux;


                            $aux = $item['description'];
                            if($its['settings']['striptags']=='on'){
                                $aux = strip_tags($aux);
                            }
                            $lb = array("\r\n","\n","\r");
                            $aux = str_replace($lb,'<br>',$aux);
                            $lb = array('"');
                            $aux = str_replace($lb,'&quot;',$aux);
                            $lb = array("'");
                            $aux = str_replace($lb,'&#39;',$aux);
                            $its[$i]['description'] = $aux;
                            $its[$i]['menuDescription'] = $aux;
                            $i++;
                        }
                    } else {

                        echo '<div class="error">error: vimeo api, no videos...</div>';
                    }
                } else {
                    echo '<div class="error">error: <a href="'.$target_file.'">this</a> returned nothing useful</div>';
                }
            }
        }

        // END vmchanneluser



//        print_r($its);



        //------start vmchannel //http://vimeo.com/api/v2/blakewhitman/videos.json
        // -- VIMEO CHANNEL
        if (($its['settings']['feedfrom'] == 'vmchannel') && $its['settings']['vimeofeed_channel'] != '') {
            $len = count($its) - 1;
            for ($i = 0; $i < $len; $i++) {
                unset($its[$i]);
            }
            $target_file = "http://vimeo.com/api/v2/channel/".$its['settings']['vimeofeed_channel']."/videos.json";


            $cacher = get_option('cache_dzsvg_vmchannel');

            if ($cacher == '') {
                $cacher = array();
            }

            if (count($cacher) == 0 || $this->mainoptions['disable_api_caching'] == 'on') {
                $ida = '';
                if ($this->mainoptions['vimeo_api_client_id'] != '' && $this->mainoptions['vimeo_api_client_secret'] != '' && $this->mainoptions['vimeo_api_access_token'] != '' ) {



                    if (!class_exists('Vimeo')) {
                        require_once(dirname(__FILE__).'/vimeoapi/vimeo.php');
                    }

                    $vimeo_id = $this->mainoptions['vimeo_api_user_id']; // Get from https://vimeo.com/settings, must be in the form of user123456
                    $consumer_key = $this->mainoptions['vimeo_api_client_id'];
                    $consumer_secret = $this->mainoptions['vimeo_api_client_secret'];
                    $token = $this->mainoptions['vimeo_api_access_token'];

                    // Do an authentication call        
                    $vimeo = new Vimeo($consumer_key,$consumer_secret);
                    $vimeo->setToken($token); //,$token_secret
//                    $vimeo->user_id = $vimeo_id;
                    $vimeo_response = $vimeo->request('/channels/'.$its['settings']['vimeofeed_channel'].'/videos');
                    if ($vimeo_response['status'] != 200) {
                        throw new Exception($vimeo_response['body']['message']);
                    }
                    if (isset($vimeo_response['body']['data'])) {
                        $ida = $vimeo_response['body']['data'];
                    }
                } else {
                    $ida = DZSHelpers::get_contents($target_file,array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));
                }



                if ($this->mainoptions['debug_mode'] == 'on') {
                    echo 'debug mode: mode vimeo channell target file - '.$target_file
                    .'<br>ida is:';
                    print_r($ida);
                }


                $jida = $ida;
                if (is_array($ida)) {
                    $jida = json_encode($ida);
                }
                if ($this->mainoptions['disable_api_caching'] != 'on') {
                    $cache_mainaux = array();
                    $cache_aux = array(
                        'output' => $jida
                        ,'username' => $its['settings']['vimeofeed_channel']
                        ,'time' => $_SERVER['REQUEST_TIME']
                    );
                    array_push($cache_mainaux,$cache_aux);
                    update_option('cache_dzsvg_vmchannel',$cache_mainaux);
                }
            } else {

                // -- channel
                if (is_array($cacher)) {
                    $ik = -1;
                    for ($i = 0; $i < count($cacher); $i++) {
                        if ($cacher[$i]['username'] == $its['settings']['vimeofeed_channel']) {
                            if ($_SERVER['REQUEST_TIME'] - $cacher[$i]['time'] < 3600) {
                                $ik = $i;
//                                print_r($cacher[$i]['username']);
                                break;
                            }
                        }
                    }
                    if ($ik > -1) {
                        $ida = $cacher[$ik]['output'];
                    } else {
                        $ida = '';
                        if ($this->mainoptions['vimeo_api_user_id'] != '' && $this->mainoptions['vimeo_api_client_id'] != '' && $this->mainoptions['vimeo_api_client_secret'] != '' && $this->mainoptions['vimeo_api_access_token'] != '') {



                            if (!class_exists('VimeoAPIException')) {
                                require_once(dirname(__FILE__).'/vimeoapi/vimeo.php');
                            }

                            $vimeo_id = $this->mainoptions['vimeo_api_user_id']; // Get from https://vimeo.com/settings, must be in the form of user123456
                            $consumer_key = $this->mainoptions['vimeo_api_client_id'];
                            $consumer_secret = $this->mainoptions['vimeo_api_client_secret'];
                            $token = $this->mainoptions['vimeo_api_access_token'];

                            // Do an authentication call 
                            $vimeo = new Vimeo($consumer_key,$consumer_secret);
                            $vimeo->setToken($token); //,$token_secret


                            $vimeo_response = $vimeo->request('/channels/'.$its['settings']['vimeofeed_channel'].'/videos');
                            if ($vimeo_response['status'] != 200) {
                                throw new Exception($vimeo_response['body']['message']);
                            }
                            if (isset($vimeo_response['body']['data'])) {
                                $ida = $vimeo_response['body']['data'];
                            }



                        } else {
                            $ida = DZSHelpers::get_contents($target_file,array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));
                        }



                        $ik = -1;
                        for ($i = 0; $i < count($cacher); $i++) {
                            if ($cacher[$i]['username'] == $its['settings']['vimeofeed_channel']) {
                                $ik = $i;
                                break;
                            }
                        }


                        $jida = $ida;
                        if (is_array($ida)) {
                            $jida = json_encode($ida);
                        }
                        $cache_aux = array(
                            'output' => $jida
                            ,'username' => $its['settings']['vimeofeed_channel']
                            ,'time' => $_SERVER['REQUEST_TIME']
                        );
                        if ($ik > -1) {
                            $cacher[$ik] = $cache_aux;
                        } else {
                            array_push($cacher,$cache_aux);
                        }
                        update_option('cache_dzsvg_vmchannel',$cacher);
                    }
                }
            }


            if (!is_object($ida) && !is_array($ida)) {
                $idar = json_decode($ida); // === vmuser
            } else {
                $idar = $ida;
            }


            $i = 0;


            if ($this->mainoptions['debug_mode'] == 'on') {
                echo 'debug mode: idar is...';
                print_r($idar);
            }


//            print_r($idar);
            if (is_array($idar)) {
                foreach ($idar as $item) {
                    if(is_object($item)){
//                        echo 'cev23a';
                        $item = (array) $item;
                    }
//                    print_r($item);

                    if(isset($item['uri'])){
                        $auxa = explode('/',$item['uri']);
                    }
                    if(isset($item['url'])){
                        $auxa = explode('/',$item['url']);
                    }
                    $its[$i]['source'] = $auxa[count($auxa) - 1];

//                    print_r($item['pictures']);





                    $vimeo_quality_ind = 2;

                    if($this->mainoptions['vimeo_thumb_quality']=='medium'){

                        $vimeo_quality_ind = 3;
                    }

                    if($this->mainoptions['vimeo_thumb_quality']=='high'){

                        $vimeo_quality_ind = 4;
                    }

                    if(is_object($item['pictures'])){
                        $item['pictures'] = (array) $item['pictures'];
                        if(is_object($item['pictures']['sizes'])){
                            $item['pictures']['sizes'] = (array) $item['pictures']['sizes'];
                        }

                        if(is_object($item['pictures']['sizes'][$vimeo_quality_ind])){
                            $item['pictures']['sizes'][$vimeo_quality_ind] = (array) $item['pictures']['sizes'][$vimeo_quality_ind];
                        }
                        $its[$i]['thethumb'] = $item['pictures']['sizes'][$vimeo_quality_ind]['link'];
                    }else{

//                        if(isset($item['thumbnail_medium'])){
//
//                            $its[$i]['thethumb'] = $item['thumbnail_medium'];
//                        }
                        if(isset($item['thumbnail_large'])){

                            $its[$i]['thethumb'] = $item['thumbnail_large'];
                        }
                        if(isset($item['pictures']['sizes'][$vimeo_quality_ind]['link'])){

                            $its[$i]['thethumb'] = $item['pictures']['sizes'][$vimeo_quality_ind]['link'];
                        }


//                        echo $its[$i]['thethumb'];
                    }
                    $its[$i]['type'] = "vimeo";


                    if(isset($item['name'])){
                        $aux = $item['name'];

                    }
                    if(isset($item['title'])){
                        $aux = $item['title'];
                    }




                    $lb = array('"',"\r\n","\n","\r","&","-","`",'???',"'",'-');
                    $aux = str_replace($lb,' ',$aux);
                    $its[$i]['title'] = $aux;


                    $aux = $item['description'];
                    if($its['settings']['striptags']=='on'){
                        $aux = strip_tags($aux);
                    }
                    $lb = array("\r\n","\n","\r");
                    $aux = str_replace($lb,'<br>',$aux);
                    $lb = array('"');
                    $aux = str_replace($lb,'&quot;',$aux);
                    $lb = array("'");
                    $aux = str_replace($lb,'&#39;',$aux);
                    $its[$i]['description'] = $aux;
                    $its[$i]['menuDescription'] = $aux;
                    $i++;
                }
            } else {
                if (is_object($idar)) {
                    print_r($idar);
                    if (isset($idar->videos->video)) {
                        foreach ($idar->videos->video as $item) {
                            $its[$i]['source'] = $item->id;
                            $its[$i]['thethumb'] = $item->thumbnails->thumbnail[0]->_content;
                            $its[$i]['type'] = "vimeo";

                            $aux = $item->title;
                            $lb = array('"',"\r\n","\n","\r","&","-","`",'???',"'",'-');
                            $aux = str_replace($lb,' ',$aux);
                            $its[$i]['title'] = $aux;

                            $aux = $item->description;
                            $lb = array("\r\n","\n","\r","&",'???');
                            $aux = str_replace($lb,' ',$aux);
                            $lb = array('"');
                            $aux = str_replace($lb,'&quot;',$aux);
                            $lb = array("'");
                            $aux = str_replace($lb,'&#39;',$aux);
                            $its[$i]['menuDescription'] = $aux;
                            $i++;
                        }
                    } else {

                        echo '<div class="error">error: vimeo api, no videos...</div>';
                    }
                } else {
                    echo '<div class="error">error: <a href="'.$target_file.'">this</a> returned nothing useful</div>';
                }
            }
//            print_r($its);
        }
        // -- end vmchannel
        
        
        
        
        
        //------start vmalbum //http://vimeo.com/api/v2/blakewhitman/videos.json
        if (($its['settings']['feedfrom'] == 'vmalbum') && $its['settings']['vimeofeed_vmalbum'] != '') {
            $len = count($its) - 1;
            for ($i = 0; $i < $len; $i++) {
                unset($its[$i]);
            }
            $target_file = "http://vimeo.com/api/v2/album/".$its['settings']['vimeofeed_vmalbum']."/videos.json";


            $cacher = get_option('cache_dzsvg_vmalbum');

            if ($cacher == '') {
                $cacher = array();
            }

            if (count($cacher) == 0 || $this->mainoptions['disable_api_caching'] == 'on') {
                $ida = '';
                if ($this->mainoptions['vimeo_api_client_id'] != '' && $this->mainoptions['vimeo_api_client_secret'] != '' && $this->mainoptions['vimeo_api_access_token'] != '' ) {



                    if (!class_exists('Vimeo')) {
                        require_once(dirname(__FILE__).'/vimeoapi/vimeo.php');
                    }

                    $vimeo_id = $this->mainoptions['vimeo_api_user_id']; // Get from https://vimeo.com/settings, must be in the form of user123456
                    $consumer_key = $this->mainoptions['vimeo_api_client_id'];
                    $consumer_secret = $this->mainoptions['vimeo_api_client_secret'];
                    $token = $this->mainoptions['vimeo_api_access_token'];

                    // Do an authentication call        
                    $vimeo = new Vimeo($consumer_key,$consumer_secret);
                    $vimeo->setToken($token); //,$token_secret
//                    $vimeo->user_id = $vimeo_id;
//                        echo $this->mainoptions['disable_api_caching'].'hmmdada/channels/' . $its['settings']['vimeofeed_vmalbum'];
                    $vimeo_response = $vimeo->request('/albums/'.$its['settings']['vimeofeed_vmalbum'].'/videos');
                    if ($vimeo_response['status'] != 200) {
                        error_log('dzsvg.php line 4023: '.$vimeo_response['body']['message']);
                    }
                    if (isset($vimeo_response['body']['data'])) {
                        $ida = $vimeo_response['body']['data'];
                    }
                } else {
                    $ida = DZSHelpers::get_contents($target_file,array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));
                }



                if ($this->mainoptions['debug_mode'] == 'on') {
                    echo 'debug mode: mode vimeo album target file - '.$target_file
                    .'<br>ida is:';
                    print_r($ida);
                }


                $jida = $ida;
                if (is_array($ida)) {
                    $jida = json_encode($ida);
                }
                if ($this->mainoptions['disable_api_caching'] != 'on') {
                    $cache_mainaux = array();
                    $cache_aux = array(
                        'output' => $jida
                        ,'username' => $its['settings']['vimeofeed_vmalbum']
                        ,'time' => $_SERVER['REQUEST_TIME']
                    );
                    array_push($cache_mainaux,$cache_aux);
                    update_option('cache_dzsvg_vmalbum',$cache_mainaux);
                }
            } else {
                if (is_array($cacher)) {
                    $ik = -1;
                    for ($i = 0; $i < count($cacher); $i++) {
                        if ($cacher[$i]['username'] == $its['settings']['vimeofeed_vmalbum']) {
                            if ($_SERVER['REQUEST_TIME'] - $cacher[$i]['time'] < 3600) {
                                $ik = $i;
//                                print_r($cacher[$i]['username']);
                                break;
                            }
                        }
                    }
                    if ($ik > -1) {
                        $ida = $cacher[$ik]['output'];
                    } else {
                        $ida = '';
                        if ($this->mainoptions['vimeo_api_user_id'] != '' && $this->mainoptions['vimeo_api_client_id'] != '' && $this->mainoptions['vimeo_api_client_secret'] != '' && $this->mainoptions['vimeo_api_access_token'] != '') {



                            if (!class_exists('VimeoAPIException')) {
                                require_once(dirname(__FILE__).'/vimeoapi/vimeo.php');
                            }

                            $vimeo_id = $this->mainoptions['vimeo_api_user_id']; // Get from https://vimeo.com/settings, must be in the form of user123456
                            $consumer_key = $this->mainoptions['vimeo_api_client_id'];
                            $consumer_secret = $this->mainoptions['vimeo_api_client_secret'];
                            $token = $this->mainoptions['vimeo_api_access_token'];

                            // Do an authentication call 
                            $vimeo = new Vimeo($consumer_key,$consumer_secret);
                            $vimeo->setToken($token); //,$token_secret


                            $vimeo_response = $vimeo->request('/albums/'.$its['settings']['vimeofeed_vmalbum'].'/videos');
                            if ($vimeo_response['status'] != 200) {
                                throw new Exception($vimeo_response['body']['message']);
                            }
                            if (isset($vimeo_response['body']['data'])) {
                                $ida = $vimeo_response['body']['data'];
                            }



                        } else {
                            $ida = DZSHelpers::get_contents($target_file,array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));
                        }



                        $ik = -1;
                        for ($i = 0; $i < count($cacher); $i++) {
                            if ($cacher[$i]['username'] == $its['settings']['vimeofeed_vmalbum']) {
                                $ik = $i;
                                break;
                            }
                        }


                        $jida = $ida;
                        if (is_array($ida)) {
                            $jida = json_encode($ida);
                        }
                        $cache_aux = array(
                            'output' => $jida
                            ,'username' => $its['settings']['vimeofeed_vmalbum']
                            ,'time' => $_SERVER['REQUEST_TIME']
                        );
                        if ($ik > -1) {
                            $cacher[$ik] = $cache_aux;
                        } else {
                            array_push($cacher,$cache_aux);
                        }
                        update_option('cache_dzsvg_vmalbum',$cacher);
                    }
                }
            }


            if (!is_object($ida) && !is_array($ida)) {
                $idar = json_decode($ida); // === vmuser
            } else {
                $idar = $ida;
            }


            $i = 0;


            if ($this->mainoptions['debug_mode'] == 'on') {
                echo 'debug mode: idar is...';
                print_r($idar);
            }


//            print_r($idar);
            if (is_array($idar)) {
                foreach ($idar as $item) {
                    if(is_object($item)){
//                        echo 'cev23a';
                        $item = (array) $item;
                    }
//                    print_r($item);


                    if(isset($item['uri'])){
                        $auxa = explode('/',$item['uri']);
                    }
                    if(isset($item['url'])){
                        $auxa = explode('/',$item['url']);
                    }





                    $vimeo_quality_ind = 2;

                    if($this->mainoptions['vimeo_thumb_quality']=='medium'){

                        $vimeo_quality_ind = 3;
                    }

                    if($this->mainoptions['vimeo_thumb_quality']=='high'){

                        $vimeo_quality_ind = 4;
                    }

                    $its[$i]['source'] = $auxa[count($auxa) - 1];
                    if(is_object($item['pictures'])){
                        $item['pictures'] = (array) $item['pictures'];
                        if(is_object($item['pictures']['sizes'])){
                            $item['pictures']['sizes'] = (array) $item['pictures']['sizes'];
                        }

                        if(is_object($item['pictures']['sizes'][$vimeo_quality_ind])){
                            $item['pictures']['sizes'][$vimeo_quality_ind] = (array) $item['pictures']['sizes'][$vimeo_quality_ind];
                        }
                        $its[$i]['thethumb'] = $item['pictures']['sizes'][$vimeo_quality_ind]['link'];
                    }else{

                        $its[$i]['thethumb'] = $item['thumbnail_medium'];
                    }

                    $its[$i]['type'] = "vimeo";


                    if(isset($item['name'])){
                        $aux = $item['name'];

                    }
                    if(isset($item['title'])){
                        $aux = $item['title'];
                    }


                    $lb = array('"',"\r\n","\n","\r","&","-","`",'???',"'",'-');
                    $aux = str_replace($lb,' ',$aux);
                    $its[$i]['title'] = $aux;

                    $aux = $item['description'];
                    if($its['settings']['striptags']=='on'){
                        $aux = strip_tags($aux);
                    }
                    $lb = array("\r\n","\n");
                    $aux = str_replace($lb,'<br>',$aux);
                    $lb = array('"');
                    $aux = str_replace($lb,'&quot;',$aux);
                    $lb = array("'");
                    $aux = str_replace($lb,'&#39;',$aux);
                    $its[$i]['description'] = $aux;
                    $its[$i]['menuDescription'] = $aux;
                    $i++;
                }
            } else {
                if (is_object($idar)) {
                    if (isset($idar->videos->video)) {
                        foreach ($idar->videos->video as $item) {
                            $its[$i]['source'] = $item->id;
                            $its[$i]['thethumb'] = $item->thumbnails->thumbnail[0]->_content;
                            $its[$i]['type'] = "vimeo";

                            $aux = $item->title;
                            $lb = array('"',"\r\n","\n","\r","&","-","`",'???',"'",'-');
                            $aux = str_replace($lb,' ',$aux);
                            $its[$i]['title'] = $aux;

                            $aux = $item->description;
                            $lb = array("\r\n","\n","\r");
                            $aux = str_replace($lb,'<br>',$aux);
                            $lb = array('"');
                            $aux = str_replace($lb,'&quot;',$aux);
                            $lb = array("'");
                            $aux = str_replace($lb,'&#39;',$aux);
                            $its[$i]['menuDescription'] = $aux;
                            $i++;
                        }
                    } else {

                        echo '<div class="error">error: vimeo api, no videos...</div>';
                    }
                } else {
                    echo '<div class="error">error: <a href="'.$target_file.'">this</a> returned nothing useful</div>';
                }
            }
//            print_r($its);
        }

        
        
        
        



        if ($its['settings']['randomize'] == 'on' && is_array($its)) {

            $backup_its = $its;
//print_r($its); $rand_keys = array_rand($its, count($its)); print_r($rand_keys);
            shuffle($its);
//print_r($its);print_r($backup_its);

            for ($i = 0; $i < count($its); $i++) {
                if (isset($its[$i]['feedfrom'])) {
                    //print_r($it);

                    unset($its[$i]);
                }
            }
            $its['settings'] = $backup_its['settings'];
            $its = array_reverse($its);
//print_r($its);
        }

        if (isset($its['settings']['order']) && $its['settings']['order'] == 'DESC') {
            $its = array_reverse($its);
        }
        
        // --- items settled
        
        if($margs['return_mode']=='items'){
            return $its;
        }


        foreach($extra_galleries as $extragal){
            $args = array(
                'id' => $extragal,
                'return_mode' => 'items',

            );

//            print_r($this->show_shortcode($args));


            foreach($this->show_shortcode($args) as $lab=>$it3){
                if($lab==='settings'){
                    continue;
                }
                array_push($its,$it3);
            }
//            $fout.=$this->show_shortcode($args);
//            print_r($its);
        }


        // --- if display mode is wall, it cannot be shown on a laptop, and height needs to be set to auto
        if ($its['settings']['displaymode'] == 'wall') {
            $its['settings']['laptopskin'] = 'off';
            $its['settings']['height'] = 'auto';
        }

        // ------- some sanitizing
        $tw = $its['settings']['width'];
        $th = $its['settings']['height'];




        $etw = $tw;
        $eth = $th;



        if (strpos($tw,"%") === false) {
            $tw = $tw.'px';
        }
        if (strpos($th,"%") === false && $th != 'auto') {
            $th = $th.'px';
        }

        if (strpos($its['settings']['facebooklink'],"{currurl}") !== false) {
            $its['settings']['facebooklink'] = str_replace('{currurl}',urlencode(dzs_curr_url()),$its['settings']['facebooklink']);
        }



        if ($margs['fullscreen'] == 'on') {
            $tw = '100%';
            $th = '100%';
        }



//        echo 'ceva'; echo $its['settings']['skin_html5vg'];
        if ($its['settings']['skin_html5vg'] == 'skin_custom') {
            $fout.='<style>';
            $fout.='#vg'.$this->sliders_index.'.videogallery { background:'.$this->mainoptions_dc['background'].';} ';
            $fout.='#vg'.$this->sliders_index.'.videogallery .navigationThumb{ background: '.$this->mainoptions_dc['thumbs_bg'].'; } ';
            $fout.='#vg'.$this->sliders_index.'.videogallery .navigationThumb.active{ background-color: '.$this->mainoptions_dc['thumbs_active_bg'].'; } ';
            $fout.='#vg'.$this->sliders_index.'.videogallery .navigationThumb{ color: '.$this->mainoptions_dc['thumbs_text_color'].'; } #vg'.$this->sliders_index.'.videogallery .navigationThumb .the-title{ color: '.$this->mainoptions_dc['thumbs_text_color'].'; } ';

            if ($this->mainoptions_dc['thumbnail_image_width'] != '') {
                $fout.='#vg'.$this->sliders_index.'.videogallery .imgblock{ width: '.$this->mainoptions_dc['thumbnail_image_width'].'px; } ';
            }

            if ($this->mainoptions_dc['thumbnail_image_height'] != '') {
                $fout.='#vg'.$this->sliders_index.'.videogallery .imgblock{ height: '.$this->mainoptions_dc['thumbnail_image_height'].'px; } ';
            }


            $fout.='</style>';
        }



        if ($vpsettings['settings']['skin_html5vp'] == 'skin_custom') {
            $fout.='<style>';
            $fout.='#vg'.$this->sliders_index.'.videogallery .background{ background-color:'.$this->mainoptions_dc['controls_background'].';} ';
            $fout.='#vg'.$this->sliders_index.'.videogallery .scrub-bg{ background-color:'.$this->mainoptions_dc['scrub_background'].';} ';
            $fout.='#vg'.$this->sliders_index.'.videogallery .scrub-buffer{ background-color:'.$this->mainoptions_dc['scrub_buffer'].';} ';
            $fout.='#vg'.$this->sliders_index.'.videogallery .playSimple{ border-left-color:'.$this->mainoptions_dc['controls_color'].';} #vg'.$this->sliders_index.'.videogallery .stopSimple .pause-part-1{ background-color: '.$this->mainoptions_dc['controls_color'].'; } #vg'.$this->sliders_index.'.videogallery .stopSimple .pause-part-2{ background-color: '.$this->mainoptions_dc['controls_color'].'; } #vg'.$this->sliders_index.'.videogallery .volumeicon{ background: '.$this->mainoptions_dc['controls_color'].'; } #vg'.$this->sliders_index.'.videogallery .volumeicon:before{ border-right-color: '.$this->mainoptions_dc['controls_color'].'; } #vg'.$this->sliders_index.'.videogallery .volume_static{ background: '.$this->mainoptions_dc['controls_color'].'; } #vg'.$this->sliders_index.'.videogallery .hdbutton-con .hdbutton-normal{ color: '.$this->mainoptions_dc['controls_color'].'; } #vg'.$this->sliders_index.'.videogallery .total-timetext{ color: '.$this->mainoptions_dc['controls_color'].'; } ';
            $fout.='#vg'.$this->sliders_index.'.videogallery .playSimple:hover{ border-left-color: '.$this->mainoptions_dc['controls_hover_color'].'; } #vg'.$this->sliders_index.'.videogallery .stopSimple:hover .pause-part-1{ background-color: '.$this->mainoptions_dc['controls_hover_color'].'; } #vg'.$this->sliders_index.'.videogallery .stopSimple:hover .pause-part-2{ background-color: '.$this->mainoptions_dc['controls_hover_color'].'; } #vg'.$this->sliders_index.'.videogallery .volumeicon:hover{ background: '.$this->mainoptions_dc['controls_hover_color'].'; } #vg'.$this->sliders_index.'.videogallery .volumeicon:hover:before{ border-right-color: '.$this->mainoptions_dc['controls_hover_color'].'; } ';
            $fout.='#vg'.$this->sliders_index.'.videogallery .volume_active{ background-color: '.$this->mainoptions_dc['controls_highlight_color'].'; } #vg'.$this->sliders_index.'.videogallery .scrub{ background-color: '.$this->mainoptions_dc['controls_highlight_color'].'; } #vg'.$this->sliders_index.'.videogallery .hdbutton-con .hdbutton-hover{ color: '.$this->mainoptions_dc['controls_highlight_color'].'; } ';
            $fout.='#vg'.$this->sliders_index.'.videogallery .curr-timetext{ color: '.$this->mainoptions_dc['timetext_curr_color'].'; } ';
            $fout.='</style>';
        }





        $fout.='<div class="gallery-precon gp'.$this->sliders_index.'';
        if ($margs['fullscreen'] == 'on') {
            $fout.=' gallery-is-fullscreen';
        }


        $str_h = 'auto';
        if ($margs['fullscreen'] == 'on') {
            $str_h='100%';
        }

        $fout.='" style="width:'.$tw.';height:'.$str_h.';';

        if ($margs['fullscreen'] == 'on') {
            $fout.=' position:'.'fixed'.'; z-index:50005; top:0; left:0;';
        }
        if ($margs['category'] != '') {
//            $fout.=' display:none;"';
            $fout.='"';
            $fout.=' data-category="'.$margs['category'].'';
        }
        /*
         * 
         */
        $fout.='"';
        $fout.='>';


        $menuitem_w = $its['settings']['html5designmiw'];
        $menuitem_h = $its['settings']['html5designmih'];
        $menuposition = ($its['settings']['menuposition']);
//        echo $menuposition;
        $html5mp = $menuposition;

        $jreadycall = 'jQuery(document).ready(function($)';
        if ($menuposition == 'right' || $menuposition == 'left') {
            //$tw -= $menuitem_w;
        }
        if ($menuposition == 'down' || $menuposition == 'up') {
            //$th -= $menuitem_h;
        }
        if ($menuposition == 'down') {
            $html5mp = 'bottom';
        }
        if ($menuposition == 'up') {
            $html5mp = 'top';
        }



        $skin_vp = 'skin_aurora';
        if ($vpsettings['settings']['skin_html5vp'] == 'skin_custom') {
            $skin_vp = 'skin_pro';
        } else {
            $skin_vp = $vpsettings['settings']['skin_html5vp'];
        }
        //echo $its['settings']['skin_html5vg'];

        if (!isset($its['settings']['fullscreen']) || $margs['fullscreen'] != 'on') {
            $fout.='<div class="videogallery-con';

            if (isset($its['settings']['laptopskin']) && $its['settings']['laptopskin'] == 'on') {
                $fout.=' skin-laptop';
                $its['settings']['totalheight'] = '';
                $th = '';
                $its['settings']['bgcolor'] = 'transparent';
            }

//            echo 'hmmdada';


            $str_th = '';


            if ($margs['fullscreen'] == 'on') {
                $str_th=' height: 100%;';
            }

            $fout.='" style="width:'.$tw.';'.$str_th.'">';

            if (isset($its['settings']['laptopskin']) && $its['settings']['laptopskin'] == 'on') {
                $fout.='<img class="thelaptopbg" src="'.$this->thepath.'videogallery/img/mb-body.png"/>';
            }
            $fout.='<div class="preloader"></div>';
        }
        
        $css_classid = str_replace(' ','_',$its['settings']['id']);


        foreach($this->arr_api_errors as $dzsvg_error){
            echo $dzsvg_error;
        }

        if (isset($its['settings']['enable_search_field']) && $its['settings']['enable_search_field']=='on' ) {
            if( !(  $its['settings']['displaymode'] == 'normal' && $its['settings']['nav_type']=='thumbs' && ( $html5mp=='left' || $html5mp=='right' ) )){

                $fout.='<div id="vg'.$this->sliders_index.'-search-field" class="dzsvg-search-field outer"><input type="text" placeholder="'.__('Search').'..."/><svg class="search-icon" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="15px" height="15px" viewBox="230.042 230.042 15 15" enable-background="new 230.042 230.042 15 15" xml:space="preserve"> <g> <path fill="#898383" d="M244.708,243.077l-3.092-3.092c0.746-1.076,1.118-2.275,1.118-3.597c0-0.859-0.167-1.681-0.501-2.465 c-0.333-0.784-0.783-1.46-1.352-2.028s-1.244-1.019-2.027-1.352c-0.785-0.333-1.607-0.5-2.466-0.5s-1.681,0.167-2.465,0.5 s-1.46,0.784-2.028,1.352s-1.019,1.244-1.352,2.028s-0.5,1.606-0.5,2.465s0.167,1.681,0.5,2.465s0.784,1.46,1.352,2.028 s1.244,1.019,2.028,1.352c0.784,0.334,1.606,0.501,2.465,0.501c1.322,0,2.521-0.373,3.597-1.118l3.092,3.083 c0.217,0.229,0.486,0.343,0.811,0.343c0.312,0,0.584-0.114,0.812-0.343c0.228-0.228,0.342-0.499,0.342-0.812 C245.042,243.569,244.931,243.3,244.708,243.077z M239.241,239.241c-0.79,0.79-1.741,1.186-2.853,1.186s-2.062-0.396-2.853-1.186 c-0.79-0.791-1.186-1.741-1.186-2.853s0.396-2.063,1.186-2.853c0.79-0.791,1.741-1.186,2.853-1.186s2.062,0.396,2.853,1.186 s1.186,1.741,1.186,2.853S240.032,238.45,239.241,239.241z"/> </g> </svg> </div>';

            }
        }


        $fout.='<div id="vg'.$this->sliders_index.'" class="videogallery id_'.$css_classid.' '.$skin_html5vg.'" style="background-color:'.$its['settings']['bgcolor'].'; width:'.$tw.'; height:'.$th.';">';
//<div class="vplayer-tobe" data-videoTitle="Pages"  data-description="<img src=thumbs/pages1.jpg class='imgblock'/><div class='the-title'>Pages</div>AE Project by Generator" data-sourcemp4="video/pages.mp4" data-sourceogg="video/pages.ogv" ><div class="videoDescription">You can have a description here if you choose to.</div></div>



        $fout.=$this->parse_items($its,$margs);
        $iout.=$this->parse_items($its,$margs);
        
//        foreach($extra_galleries as $extragal){
//            $args = array(
//                'id' => $extragal,
//                'return_mode' => 'items',
//
//            );
//
//            array_push($its,$this->show_shortcode($args));
////            $fout.=$this->show_shortcode($args);
//        }

        $html5vgautoplay = 'off';
        if ($its['settings']['autoplay'] == 'on') {
            $html5vgautoplay = 'on';
        }

        if (!isset($its['settings']['fullscreen']) || $its['settings']['fullscreen'] != 'on') {
            $fout.= '</div>';
        }
        $fout.='</div>
<script>
var videoplayersettings = {
autoplay : "off",
controls_out_opacity : 0.9,
controls_normal_opacity : 0.9
,settings_swfPath : "'.$this->thepath.'preview.swf"';

        $fout.='}
';
        if ($its['settings']['displaymode'] == 'wall') {
            $fout.='window.zoombox_videoplayersettings = videoplayersettings;';
        }

        $fout.=$jreadycall.'{
videoplayersettings.design_skin = "'.$skin_vp.'";
videoplayersettings.settings_youtube_usecustomskin = "'.$its['settings']['yt_customskin'].'";
videoplayersettings.controls_normal_opacity = "'.$its['settings']['html5design_controlsopacityon'].'";
videoplayersettings.controls_out_opacity = "'.$its['settings']['html5design_controlsopacityout'].'";
videoplayersettings.settings_video_overlay = "'.$its['settings']['settings_video_overlay'].'";';

        if (isset($its['settings']['youtube_sdquality'])) {
            $fout.='videoplayersettings.youtube_sdQuality = "'.$its['settings']['youtube_sdquality'].'";';
        }if (isset($its['settings']['youtube_hdquality'])) {
            $fout.='videoplayersettings.youtube_hdQuality = "'.$its['settings']['youtube_hdquality'].'";';
        }if (isset($its['settings']['youtube_defaultquality'])) {
            $fout.='videoplayersettings.youtube_defaultQuality = "'.$its['settings']['youtube_defaultquality'].'";';
        }



        if(isset($its['settings']['settings_video_end_reset_time']) && $its['settings']['settings_video_end_reset_time']=='off'){
            $fout.='videoplayersettings.settings_video_end_reset_time="off";';
        }

        if (isset($its['settings']['rtmp_streamserver'])) {
            $fout.='videoplayersettings.rtmp_streamServer = "'.$its['settings']['rtmp_streamserver'].'";';
        }

        if(isset($vpsettings['settings']['settings_ios_usecustomskin'])){
            $fout.='videoplayersettings.settings_ios_usecustomskin = "'.$its['settings']['settings_ios_usecustomskin'].'";';

        }
        if(isset($vpsettings['settings']['ga_enable_send_play'])){
            $fout.='videoplayersettings.ga_enable_send_play = "'.$its['settings']['ga_enable_send_play'].'";';

        }



        $fout.='dzsvg_init("#vg'.$this->sliders_index.'",{
menuSpace:0
,randomise:"off"
,settings_menu_overlay:"on"
,totalWidth : "'.$tw.'"';
        if (isset($its['settings']['totalheight']) && $its['settings']['totalheight'] != '') {
            $fout.=',totalHeight : "'.$th.'"';
        }

        if (isset($its['settings']['forcevideoheight']) && $its['settings']['forcevideoheight'] != '') {
            $fout.=',forceVideoHeight : "'.$its['settings']['forcevideoheight'].'"';
        }


        if ($this->mainoptions['settings_trigger_resize'] == 'on') {
            $fout.=',settings_trigger_resize:"1000"';
        };

        $fout.=',autoplay :"'.$html5vgautoplay.'"
,autoplayNext : "'.$its['settings']['autoplaynext'].'"
,nav_type : "'.$its['settings']['nav_type'].'"
,menuitem_width:"'.$menuitem_w.'"
,menuitem_space:"'.$its['settings']['html5designmis'].'"
,menuitem_height:"'.$menuitem_h.'"
,modewall_bigwidth:"800"
,modewall_bigheight:"500"
';
        if (isset($its['settings']['nav_space'])) {
            $fout.=',nav_space: "'.$its['settings']['nav_space'].'"';
        }
        if ($margs['settings_separation_mode'] == 'scroll' || $margs['settings_separation_mode'] == 'button') {
            $fout.=',settings_separation_mode: "'.$margs['settings_separation_mode'].'"';
            $fout.=',settings_separation_pages: [';
            for ($i = 1; $i < (ceil(count($its) - 1) / intval($margs['settings_separation_pages_number']) ); $i++) {

                if ($i > 1) {
                    $fout.=',';
                }
                $aux_args = $margs;
                $fout.='"'.$this->thepath.'ajaxreceiver.php?args='.urlencode(json_encode($aux_args)).'&dzsvg_settings_separation_paged='.($i + 1).'"';
            }
            $fout.=']';
        }
        if (isset($its['settings']['cueFirstVideo'])) {
            $fout.=',cueFirstVideo:"'.$its['settings']['cueFirstVideo'].'"';
        }
        if (isset($its['settings']['displaymode']) && ($its['settings']['displaymode'] == 'wall' || $its['settings']['displaymode'] == 'normal') || $its['settings']['displaymode'] == 'rotator' || $its['settings']['displaymode'] == 'rotator3d') {
            $fout.=',settings_mode:"'.$its['settings']['displaymode'].'"';
        }

        if(isset($its['settings']['mode_wall_layout']) && $its['settings']['mode_wall_layout'] && $its['settings']['mode_wall_layout']!='none'){

            $fout.=',extra_class_slider_con:"'.$its['settings']['mode_wall_layout'].'"';
        }

        if (isset($its['settings']['logoLink']) && $its['settings']['logoLink'] != '') {
            $fout.=',logoLink:"'.$its['settings']['logoLink'].'"';
        }
        $fout.=',menu_position:"'.$html5mp.'"
,transition_type:"'.$its['settings']['html5transition'].'"
,design_skin: "'.$skin_html5vg.'"';

        if (isset($its['settings']['logo']) && $its['settings']['logo'] != '') {
            $fout.=',logo : "'.$its['settings']['logo'].'" ';
        }


        if (isset($its['settings']['playorder'])) {
            $fout.=',playorder :"'.$its['settings']['playorder'].'"';
        }
        if (isset($its['settings']['design_navigationuseeasing'])) {
            $fout.=',design_navigationUseEasing :"'.$its['settings']['design_navigationuseeasing'].'"';
        }
        if (isset($its['settings']['enable_search_field']) && $its['settings']['enable_search_field']=='on') {
            $fout.=',search_field :"on"';
        }
//        print_r($its['settings']);
        if (isset($its['settings']['autoplay_ad']) ) {
            $fout.=',autoplay_ad :"'.$its['settings']['autoplay_ad'].'"';
        }



        if (isset($its['settings']['enable_search_field']) && $its['settings']['enable_search_field']=='on' ) {
            if( !(  $its['settings']['displaymode'] == 'normal' && $its['settings']['nav_type']=='thumbs' && ( $html5mp=='left' || $html5mp=='right' ) )){

                $fout.=',search_field_con: $("#vg'.$this->sliders_index.'-search-field > input")';

            }
        }

        if($its['settings']['enableunderneathdescription']=='on'){
            $its['settings']['enable_secondcon']='off';
            $fout.=',settings_secondCon: "#as'.$this->sliders_index.'-secondcon"';
        }


        if ($its['settings']['sharebutton'] == 'on') {
            $auxout = '';
            if ($its['settings']['facebooklink'] != 'on') {
                $auxout .= '<a class="icon" target="_blank" href="'.stripslashes($its['settings']['facebooklink']).'"><img src="'.$this->thepath.'img/facebook.png"/></a>';
            }
            if ($its['settings']['twitterlink'] != 'on') {
                $auxout .= '<a class="icon" target="_blank"  href="'.stripslashes($its['settings']['twitterlink']).'"><img src="'.$this->thepath.'img/twitter.png"/></a>';
            }
            if ($its['settings']['googlepluslink'] != 'on') {
                $auxout .= '<a class="icon" target="_blank"  href="'.stripslashes($its['settings']['googlepluslink']).'"><img src="'.$this->thepath.'img/google.png"/></a>';
            }
            if (isset($its['settings']['social_extracode']) && $its['settings']['social_extracode'] != '') {
                $auxout.=$its['settings']['social_extracode'];
            }

            $auxout = str_replace("'", "\\'", $auxout);
            $auxout = str_replace("\\\'", "\\'", $auxout);
            $fout.=',shareCode : '."'".$auxout."'".' ';
        }
        
        if($its['settings']['enable_secondcon']=='on'){
            $fout.=',settings_secondCon:".dzsas-second-con-for-'.$css_classid.'"';
        }
        if($its['settings']['enable_outernav']=='on'){
            $fout.=',settings_outerNav:$(".videogallery--navigation-outer-for-'.$css_classid.'")';
        }

        if ($its['settings']['embedbutton'] == 'on') {
            $auxout = '<iframe src="'.$this->thepath.'bridge.php?action=view&id='.$its['settings']['id'].'&db='.$this->currDb.'" width="'.$its['settings']['width'].'" height="'.$its['settings']['height'].'" style="overflow:hidden;" scrolling="no" frameborder="0"></iframe>';
            $fout.=',embedCode : \''.$auxout.'\' ';
        }

        $fout.=',videoplayersettings : videoplayersettings
})
})
</script>';
        if ($its['settings']['shadow'] == 'on') {
            $fout.='<div class="all-shadow" style="width:'.$tw.';"></div>';
        }

        $fout.='<div class="clear"></div>';

        if ($margs['settings_separation_mode'] == 'pages') {
            $fout.='<div class="con-dzsvg-pagination">';
            //echo ceil((count($its) - 1) / intval($margs['settings_separation_pages_number']));
            for ($i = 0; $i < (ceil(count($its) - 1) / intval($margs['settings_separation_pages_number']) ); $i++){
                $str_active = '';
                if (($i + 1) == $margs['settings_separation_paged']) {
                    $str_active = ' active';
                };

                $auxurl = add_query_arg(array('dzsvg_settings_separation_paged' => ($i + 1)),dzs_curr_url());

                $fout.='<a class="pagination-number '.$str_active.'" href="'.esc_url($auxurl).'">'.($i + 1).'</a>';
            }
            $fout.='</div>';
        }

        $fout.='</div>'; //END gallery-precon
        
        if($its['settings']['enableunderneathdescription']=='on'){
            
            $fout.='<div id="as'.$this->sliders_index.'-secondcon" class="dzsas-second-con"><div class="dzsas-second-con--clip">';
            foreach ($its as $lab => $val){
                if ($lab==='settings') {
                    continue;
                }

//                print_r($val);

                $fout.='<div class="item">';
                if(isset($val['title'])){
                    $fout.='<h4>'.$val['title'].'</h4>';
                }



                if(isset($val['menuDescription'])) {


                    $maxlen = 100;
                    if (isset($its['settings']['maxlen_desc']) && $its['settings']['maxlen_desc']) {
                        $maxlen = $its['settings']['maxlen_desc'];
                    }


//            echo 'maxlen - '.$maxlen;

                    $striptags = false;

                    $try_to_close_unclosed_tags = true;


                    if (isset($its['settings']['striptags']) && $its['settings']['striptags'] === 'on') {
//                $striptags=true;
                        $try_to_close_unclosed_tags = false;
                    }
                    if (isset($its['settings']['try_to_close_unclosed_tags']) && $its['settings']['try_to_close_unclosed_tags'] === 'off') {
                        $try_to_close_unclosed_tags = false;
                    }


//            echo 'description - '.$val['menuDescription'];

                    if (isset($val['menuDescription']) && $val['menuDescription']) {
                        $fout.= '<p>' . dzs_get_excerpt(0,
                                array(
                                    'content' => $val['menuDescription'],
                                    'maxlen' => $maxlen,
                                    'try_to_close_unclosed_tags' => $try_to_close_unclosed_tags,
                                    'striptags' => $striptags,
                                )
                            ) . '</p>';
                    }
                }

                $fout.='</div>';
                
//                print_r($val);
                
            }
            $fout.='</div></div>';
        }


        if ($its['settings']['displaymode'] == 'wall') {
            wp_enqueue_script('jquery.masonry',$this->thepath."masonry/jquery.masonry.min.js");

            wp_enqueue_style('dzs.zoombox',$this->thepath.'zoombox/zoombox.css');
            wp_enqueue_script('dzs.zoombox',$this->thepath.'zoombox/zoombox.js');
        }






        //=======alternatewall
        //----mode alternatewall
        if ($its['settings']['displaymode'] == 'alternatewall') {
            $fout = '';
            $iout = '';
            $fout.='<style>
            .dzs-gallery-container .item{ width:23%; margin-right:1%; float:left; position:relative; display:block; margin-bottom:10px; }
            .dzs-gallery-container .item-image{ width:100%; }
            .dzs-gallery-container h4{  color:#D26; }
            .dzs-gallery-container h4:hover{ background: #D26; color:#fff; }
            .last { margin-right:0!important; }
            .clear { clear:both; }
            </style>';
            $fout.='<div class="dzs-gallery-container">';


            $fout.=$this->parse_items($its,$margs);
            $iout.=$this->parse_items($its,$margs);



            $fout.='<div class="clear"></div>';
            $fout.='</div>';


            if ($margs['settings_separation_mode'] == 'pages') {
                $fout.='<div class="con-dzsvg-pagination">';
                //echo ceil((count($its) - 1) / intval($margs['settings_separation_pages_number']));
                for ($i = 0; $i < (ceil(count($its) - 1) / intval($margs['settings_separation_pages_number']) ); $i++) {
                    $str_active = '';
                    if (($i + 1) == $margs['settings_separation_paged']) {
                        $str_active = ' active';
                    }
                    $fout.='<a class="pagination-number '.$str_active.'" href="'.esc_url(add_query_arg(array('dzsvg_settings_separation_paged' => ($i + 1)),dzs_curr_url())).'">'.($i + 1).'</a>';
                }
                $fout.='</div>';
            }

            $fout.='<div class="clear"></div>';
            $fout.='<script>jQuery(document).ready(function($){ jQuery(".zoombox").zoomBox(); });</script>';

            wp_enqueue_style('dzs.zoombox',$this->thepath.'zoombox/zoombox.css');
            wp_enqueue_script('dzs.zoombox',$this->thepath.'zoombox/zoombox.js');

            return $fout;
        }


        //=======alternate menu
        /////---mode alternatemenu
        if ($its['settings']['displaymode'] == 'alternatemenu') {
            $i = 0;
            $k = 0;


            $current_urla = explode("?",dzs_curr_url());
            $current_url = $current_urla[0];

            $fout = '';
            $fout .= '
<style type="text/css">
.submenu{
margin:0;
padding:0;
list-style-type:none;
list-style-position:outside;
position:relative;
z-index:32;
}

.submenu a{
display:block;
padding:5px 15px;
background-color: #28211b;
color:#fff;
text-decoration:none;
}

.submenu li ul a{
display:block;
width:200px;
height:auto;
}

.submenu li{
float:left;
position:static;
width: auto;
position:relative;
}

.submenu ul, .submenu ul ul{
position:absolute;
width:200px;
top:auto;
display:none;
list-style-type:none;
list-style-position:outside;
}
.submenu > li > ul{
position:absolute;
top:auto;
left:0;
margin:0;
}

.submenu a:hover{
background-color:#555;
color:#eee;
}

.submenu li:hover ul, .submenu li li:hover ul{
display:block;
}
</style>';

            $fout .= '<ul class="submenu">';
            if (isset($this->mainitems)) {
                for ($k = 0; $k < count($this->mainitems); $k++) {
                    if (count($this->mainitems[$k]) < 2) {
                        continue;
                    }
                    $fout.='<li><a href="#">'.$this->mainitems[$k]["settings"]["id"].'</a>';

                    if (isset($this->mainitems[$k]) && count($this->mainitems[$k]) > 1) {

                        $fout.='<ul>';
                        for ($i = 0; $i < count($this->mainitems[$k]); $i++) {
                            if (isset($this->mainitems[$k][$i]["thethumb"]))
                                $fout.='<li><a href="'.$current_url.'?the_source='.$this->mainitems[$k][$i]["source"].'&the_thumb='.$this->mainitems[$k][$i]["thethumb"].'&the_type='.$this->mainitems[$k][$i]["type"].'&the_title='.$this->mainitems[$k][$i]["title"].'">'.$this->mainitems[$k][$i]["title"].'</a>';
                        }
                        $fout.='</ul>';
                    }
                    $fout.='</li>';
                }
            }

            $k = 0;
            $i = 0;
            $fout .= '</ul>
<div class="clearfix"></div>
<br>';

            if (isset($_REQUEST['the_source'])) {
                $fout.='<a class="zoombox" data-type="video" data-videotype="'.$_REQUEST['the_type'].'" data-src="'.$_REQUEST['the_source'].'"><img class="item-image" src="';
                if ($its[$i]['thethumb'] != '')
                    $fout.=$_REQUEST['the_thumb'];
                else {
                    if ($its[$i]['type'] == "youtube") {
                        $fout.='https://img.youtube.com/vi/'.$_REQUEST['the_source'].'/0.jpg';
                        $its[$i]['thethumb'] = 'https://img.youtube.com/vi/'.$_REQUEST['the_source'].'/0.jpg';
                    }
                }
                $fout.='"/></a>';
            }


            $fout.='<script>jQuery(document).ready(function($){ jQuery(".zoombox").zoomBox(); });</script>';

            wp_enqueue_style('dzs.zoombox',$this->thepath.'zoombox/zoombox.css');
            wp_enqueue_script('dzs.zoombox',$this->thepath.'zoombox/zoombox.js');

            return $fout;
        }

        if ($this->mainoptions['debug_mode'] == 'on') {
            echo 'memory usage - ' . memory_get_usage() . "\n <br>memory limit - " . ini_get('memory_limit');;

        }


        if ($margs['return_mode'] != 'parsed items') {
            return $fout;
        } else {
            return $iout;
        }




        //echo $k;
    }

    function parse_items($its,$pargs) {
        //====returns only the html5 gallery items



        $margs = array(
            'settings_separation_mode' => 'normal',
            'settings_separation_paged' => '0',
            'settings_separation_pages_number' => '5',
            'single' => 'off',
        );

        if (is_array($pargs) == false) {
            $pargs = $margs;
        }

        $margs = array_merge($margs,$pargs);

        $fout = '';
        $start_nr = 0; // === the i start nr 
        $end_nr = count($its); // === the i start nr 
        $nr_per_page = 5;
        $nr_items = count($its) - 1;
        $nr_page = intval($margs['settings_separation_paged']);



        if ($nr_page == 0) {
            $nr_page = 1;
        }
//        print_r($its); print_r($margs); echo $margs['settings_separation_mode']; echo $margs['settings_separation_mode']!='normal';
        if ($margs['settings_separation_mode'] != 'normal') {
            $nr_per_page = intval($margs['settings_separation_pages_number']);

            if ($nr_per_page * $nr_page <= $nr_items) {
                $start_nr = $nr_per_page * ($nr_page - 1);
                $end_nr = $start_nr + $nr_per_page;
            } else {
                $start_nr = $nr_items - $nr_per_page - 1;
                $end_nr = $nr_items;
            }
        }
//        echo 'ceva '.$nr_per_page . ' || ' . ($nr_per_page * $nr_page) . ' ||||| ' . $start_nr . ' ' . $end_nr;

        if (isset($its['settings']['displaymode']) && $its['settings']['displaymode'] == 'alternatewall') {
            for ($i = $start_nr; $i < $end_nr; $i++) {
                if (!isset($its[$i]['type'])) {
                    continue;
                }
                $islastonrow = false;
                if ($i % 4 == 3) {
                    $islastonrow = true;
                }
                $itemclass = 'item';
                if ($islastonrow == true) {
                    $itemclass.=' last';
                }
                $fout.='<div class="'.$itemclass.'">';
                //$fout.='<a href="' . $this->thepath . 'ajax.php?ajax=true&height=' . $its['settings']['height'] . '&width=' . $its['settings']['width'] . '&type=' . $its[$i]['type'] . '&source=' . $its[$i]['source'] . '" title="' . $its[$i]['type'] . '" rel=""><img class="item-image" src="';
                $fout.='<a class="zoombox" data-type="video" data-videotype="'.$its[$i]['type'].'" data-src="'.$its[$i]['source'].'"><img class="item-image" src="';
                if ($its[$i]['thethumb'] != '')
                    $fout.=$its[$i]['thethumb'];
                else {
                    if ($its[$i]['type'] == "youtube") {
                        $fout.='https://img.youtube.com/vi/'.$its[$i]['source'].'/0.jpg';
                        $its[$i]['thethumb'] = 'https://img.youtube.com/vi/'.$its[$i]['source'].'/0.jpg';
                    }
                }
                $fout.='"/></a>';
                $fout.='<h4>'.$its[$i]['title'].'</h4>';
                $fout.='</div>';
                if ($islastonrow) {
                    $fout.='<div class="clear"></div>';
                }
            }
            return $fout;
        }


        //print_r($its); print_r($margs); echo ' start nr : '.$start_nr; echo ' end nr : '. $end_nr;

        for ($i = $start_nr; $i < $end_nr; $i++) {
            if (isset($its[$i]) == false) {
                continue;
            }


            $che = $its[$i];
            $this->index_players++;

            if ($che['source'] == '' || $che['source'] == ' ') {
                continue;
            }



            $fout.='<div id="vp'.$this->index_players.'" class="vplayer-tobe"';
            if (isset($its['settings']['coverImage']) && $its['settings']['coverImage']) {
                $fout.=' data-img="'.$its['settings']['coverImage'].'"';
            }

            if (isset($che['cssid']) && $che['cssid'] != '') {
                $fout.=' id="'.$che['cssid'].'"';
            }

            if ((isset($its['settings']['disable_video_title']) && $its['settings']['disable_video_title'] != 'on') && isset($che['title']) && $che['title']) {
                $che['title'] = str_replace(array("\r","\r\n","\n",'\\',"\\"),'',$che['title']);
                $che['title'] = str_replace(array('"'),"&#8221;",$che['title']);
                $fout.=' data-videoTitle="'.$che['title'].'"';
            }
            if (isset($che['type']) && $che['type'] == 'video') {
                $fout.=' data-sourcemp4="'.$che['source'].'"';


                if (isset($che['html5sourceogg']) && $che['html5sourceogg'] != '') {

                    if (strpos($che['html5sourceogg'],'.webm') === false) {
                        $fout.=' data-sourceogg="'.$che['html5sourceogg'].'"';
                    } else {
                        $fout.=' data-sourcewebm="'.$che['html5sourceogg'].'"';
                    }
                }
            }
            if (isset($che['audioimage']) && $che['audioimage'] != '') {
                $fout.=' data-previewimg="'.$che['audioimage'].'"';
                $fout.=' data-img="'.$che['audioimage'].'"';
            } else {

                if (isset($its['settings']['displaymode']) && $its['settings']['displaymode'] == 'wall' && isset($che['thethumb']) && $che['thethumb'] != '') {
                    $fout.=' data-previewimg="'.$che['thethumb'].'"';
                }
            }
            if (isset($che['type']) && $che['type'] == 'audio') {
                $fout.=' data-sourcemp3="'.$che['source'].'"';
                if (isset($che['html5sourceogg']) && $che['html5sourceogg'] != '') {
                    $fout.=' data-sourceogg="'.$che['html5sourceogg'].'"';
                }
                if (isset($che['audioimage']) && $che['audioimage'] != '') {
                    $fout.=' data-audioimg="'.$che['audioimage'].'"';
                }
                $fout.=' data-type="audio"';
            }
            if (isset($che['type']) && $che['type'] == 'youtube') {
                $fout.=' data-type="youtube"';
                $fout.=' data-src="'.$che['source'].'"';
            }
            if (isset($che['type']) && $che['type'] == 'vimeo') {
                $fout.=' data-type="vimeo"';
                $fout.=' data-src="'.$che['source'].'"';
            }
            if (isset($che['type']) && $che['type'] == 'image') {
                $fout.=' data-type="image"';
                $fout.=' data-img="'.$che['source'].'"';
            }
            if (isset($che['type']) && $che['type'] == 'link') {
                $fout.=' data-type="link"';
                $fout.=' data-source="'.$che['source'].'"';
            }
            if (isset($che['type']) && $che['type'] == 'inline') {
                $fout.=' data-type="inline"';
            }
            if (isset($che['type']) && $che['type'] == 'rtmp') {
                $fout.=' data-type="rtmp"';
                $fout.=' data-source="'.$che['source'].'"';
            }
            $aux = 'adsource';
            if (isset($che[$aux]) && $che[$aux] != '') {
                if (isset($che['adtype']) && $che['adtype'] != 'inline') {
                    $fout.=' data-'.$aux.'="'.$che[$aux].'"';
                }
            }
            $aux = 'adtype';
            if (isset($che[$aux]) && $che[$aux] != '') {
                $fout.=' data-'.$aux.'="'.$che[$aux].'"';
            }
            $aux = 'adlink';
            if (isset($che[$aux]) && $che[$aux] != '') {
                $fout.=' data-'.$aux.'="'.$che[$aux].'"';
            }
            $aux = 'adskip_delay';
            if (isset($che[$aux]) && $che[$aux] != '') {
                $fout.=' data-'.$aux.'="'.$che[$aux].'"';
            }
            $aux = 'playfrom';
            if (isset($che[$aux]) && $che[$aux] != '') {
                $fout.=' data-'.$aux.'="'.$che[$aux].'"';
            }
            
            $aux='responsive_ratio';
            if (isset($che[$aux]) && $che[$aux] != '') {
                $fout.=' data-'.$aux.'="'.$che[$aux].'"';
            }

            // -- if the video player is single shortcode then we can alter width height
            if ($margs['single'] == 'on') {
//                print_r($margs);
                // ===== some sanitizing
                $tw = $margs['width'];
                $th = $margs['height'];
                $str_tw = '';
                $str_th = '';



                if ($tw != '') {
                    if (strpos($tw,"%") === false && $tw != 'auto') {
                        $str_tw = ' width: '.$tw.'px;';
                    } else {
                        $str_tw = ' width: '.$tw.';';
                    }
                }


                if ($th != '') {
                    if (strpos($th,"%") === false && $th != 'auto') {
                        $str_th = ' height: '.$th.'px;';
                    } else {
                        $str_th = ' height: '.$th.';';
                    }
                }


                $fout.=' style="'.$str_tw.$str_th.'"';
            }




            $fout.='>';



            $maxlen = 100;
            if (isset($its['settings']['maxlen_desc']) && $its['settings']['maxlen_desc']) {
                $maxlen = $its['settings']['maxlen_desc'];
            }


            $striptags = false;

            $try_to_close_unclosed_tags = true;


            if (isset($its['settings']['striptags']) && $its['settings']['striptags']==='on') {
//                $striptags=true;
                $try_to_close_unclosed_tags = false;
            }
            if (isset($its['settings']['try_to_close_unclosed_tags']) && $its['settings']['try_to_close_unclosed_tags']==='off') {
                $try_to_close_unclosed_tags=false;
            }

//            echo 'description - '.$che['description'];

            if (isset($che['description']) && $che['description']) {
                $fout.='<div class="videoDescription">'.dzs_get_excerpt(0,
                        array(
                            'content' => $che['description'],
                            'maxlen' => $maxlen,
                            'try_to_close_unclosed_tags'=>$try_to_close_unclosed_tags,
                            'striptags'=>$striptags,
                        )
                    ).'</div>';
            }


            $aux = 'subtitle_file';
            if (isset($che[$aux]) && $che[$aux] != '') {
                $fil = DZSHelpers::get_contents($che[$aux]);
                $fout.='<div class="subtitles-con-input">'.$fil.'</div>';
            }

            $fout.='<div class="menuDescription">';
//            if($i==1){
//                print_r($che);
//            }
            //==== imgblock or imgfull
            $thumbclass = 'imgblock';


            if (isset($its['settings']['thumb_extraclass']) && $its['settings']['thumb_extraclass'] != '') {
                $thumbclass .= ' '.$its['settings']['thumb_extraclass'];
            }

            if (isset($its['settings']['nav_type']) && $its['settings']['nav_type'] == 'outer') {
                $thumbclass = 'imgfull';
            }

            if (isset($che['thethumb']) && $che['thethumb'] != '') {
//                echo 'hmmdada'; print_r($che['thethumb']);
                $fout.='<img src="'.$che['thethumb'].'" class="'.$thumbclass.'"/>';
            } else {
                if ($che['type'] == 'youtube') {
                    $fout.='{ytthumb}';
                }
            }
            if ((isset($its['settings']['disable_title']) && $its['settings']['disable_title'] != 'on') && isset($che['title']) && $che['title']) {
                $fout.='<div class="the-title">'.stripslashes($che['title']).'</div>';
            }
//            echo 'hmmtest'.!isset($its['settings']['disable_menu_description']).' '.isset($its['settings']['disable_menu_description']).' '.$its['settings']['disable_menu_description'];


            if (((isset($its['settings']['disable_menu_description'])) && $its['settings']['disable_menu_description'] != 'on') && isset($che['menuDescription']) && $che['menuDescription']) {

                $fout.=''.dzs_get_excerpt(0,
                        array(
                            'content' => stripslashes($che['menuDescription']),
                            'maxlen' => $maxlen,
                            'try_to_close_unclosed_tags'=>$try_to_close_unclosed_tags,
                            'striptags'=>$striptags,
                        )
                    ).'';

            }
            $fout.='</div>'; //---menuDescription END
            if (isset($che['tags']) && $che['tags']) {
                $arr_septag = explode('$$;',$che['tags']);
                foreach ($arr_septag as $septag) {
                    //print_r($septag);
                    if ($septag != '') {
                        $arr_septagprop = explode('$$',$septag);
                        //print_r($arr_septagprop);
                        $fout.='<div class="dzstag-tobe" data-starttime="'.$arr_septagprop[0].'" data-endtime="'.$arr_septagprop[1].'" data-left="'.$arr_septagprop[2].'" data-top="'.$arr_septagprop[3].'" data-width="'.$arr_septagprop[4].'" data-height="'.$arr_septagprop[5].'" data-link="'.$arr_septagprop[6].'">'.$arr_septagprop[7].'</div>';
                    }
                }
                //print_r($arr_septag);
            }

            if (isset($che['type']) && $che['type'] == 'inline') {
                $fout.=stripslashes($che['source']);
            }


            if (isset($che['adtype']) && $che['adtype'] == 'inline') {
                $fout.='<div class="adSource">'.$che['adsource'].'</div>';
            }

            $fout.='</div>';
        }
        return $fout;
    }

    function admin_init() {



        add_meta_box('dzsvg_meta_options',__('DZS Video Gallery Settings'),array($this,'admin_meta_options'),'post','normal','high');
        add_meta_box('dzsvg_meta_options',__('DZS Video Gallery Settings'),array($this,'admin_meta_options'),'page','normal','high');
    }

    function admin_meta_options() {
        global $post;
        ?>
        <input type="hidden" name="dzs_nonce" value="<?php echo wp_create_nonce('dzs_nonce'); ?>" />
        <h4><?php _e("Fullscreen Gallery",'dzsvg'); ?></h4>
        <select class="textinput styleme" name="dzsvg_fullscreen">
            <option>none</option>
        <?php
        foreach ($this->mainitems as $it) {
            echo '<option ';
            dzs_checked(get_post_meta($post->ID,'dzsvg_fullscreen',true),$it['settings']['id'],'selected');
            echo '>'.$it['settings']['id'].'</option>';
        }
        ?>
        </select>
        <div class="clear"></div>

        <div class="sidenote">
        <?php echo __('Get a fullscreen gallery in your post / page with a close button.','dzsvg'); ?><br/>
        </div>
            <?php
        }

        function admin_meta_save($post_id) {
            global $post;
            if (!$post) {
                return;
            }
            if (isset($post->post_type) && !($post->post_type == 'post' || $post->post_type == 'page')) {
                return $post_id;
            }
            /* Check autosave */
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }
            if (isset($_REQUEST['dzs_nonce'])) {
                $nonce = $_REQUEST['dzs_nonce'];
                if (!wp_verify_nonce($nonce,'dzs_nonce'))
                    wp_die('Security check');
            }
            if (isset($_POST['dzsvg_fullscreen'])) {
                dzs_savemeta($post->ID,'dzsvg_fullscreen',$_POST['dzsvg_fullscreen']);
            }
            if (isset($_POST['dzsvg_extras_featured'])) {
                dzs_savemeta($post->ID,'dzsvg_extras_featured',$_POST['dzsvg_extras_featured']);
            }
        }

        function handle_init() {

            wp_enqueue_script('jquery');
            if (is_admin()) {
                wp_enqueue_style('dzsvg_admin_global',$this->thepath.'admin/admin_global.css');
                wp_enqueue_script('dzsvg_admin_global',$this->thepath.'admin/admin_global.js');
                wp_enqueue_style('dzs.zoombox',$this->thepath.'zoombox/zoombox.css');
                wp_enqueue_script('dzs.zoombox',$this->thepath.'zoombox/zoombox.js');
                if (isset($_GET['page']) && ($_GET['page'] == $this->adminpagename || $_GET['page'] == $this->adminpagename_configs)) {
                    if (current_user_can($this->capability_admin) && function_exists('wp_enqueue_media')) {
                        wp_enqueue_media();
                    }

                    $this->admin_scripts();
                }
                if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename_designercenter) {
                    wp_enqueue_style('dzsvg-dc.style',$this->thepath.'deploy/designer/style/style.css');
                    wp_enqueue_script('dzs.farbtastic',$this->thepath."admin/colorpicker/farbtastic.js");
                    wp_enqueue_style('dzs.farbtastic',$this->thepath.'admin/colorpicker/farbtastic.css');
                    wp_enqueue_script('dzsvg-dc.admin',$this->thepath.'admin/admin-dc.js');
                    wp_enqueue_style('dzs.vplayer',$this->thepath.'videogallery/vplayer.css');
                    wp_enqueue_script('dzs.vplayer',$this->thepath."videogallery/vplayer.js");


                }
                if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename_mainoptions) {
                    wp_enqueue_style('dzsvg_admin',$this->thepath.'admin/admin.css');
                    wp_enqueue_script('dzsvg_admin',$this->thepath."admin/admin-mo.js");
                    wp_enqueue_script('jquery-ui-core');
                    wp_enqueue_script('jquery-ui-sortable');
                    wp_enqueue_style('iphone.checkbox',$this->thepath.'admin/checkbox/checkbox.css');
                    wp_enqueue_script('iphone.checkbox',$this->thepath."admin/checkbox/checkbox.dev.js");


                    if(isset($_GET['dzsvg_shortcode_builder']) && $_GET['dzsvg_shortcode_builder']=='on'){

                        wp_enqueue_style('dzsvg_shortcode_builder_style', $this->thepath . 'tinymce/popup.css');
                        wp_enqueue_script('dzsvg_shortcode_builder', $this->thepath . 'tinymce/popup.js');


                        wp_enqueue_media();
                    }else{

                    }



                }

                if (current_user_can('edit_posts') || current_user_can('edit_pages')) {
                    wp_enqueue_script('thickbox');
                    wp_enqueue_style('thickbox');
                    wp_enqueue_script('dzsvg_htmleditor',$this->thepath.'tinymce/plugin-htmleditor.js');
                    wp_enqueue_script('dzsvg_configreceiver',$this->thepath.'tinymce/receiver.js');
                }
            } else {
                if (isset($this->mainoptions['always_embed']) && $this->mainoptions['always_embed'] == 'on') {
                    $this->front_scripts();
                }
                wp_enqueue_style('dzs.vplayer',$this->thepath.'videogallery/vplayer.css');
            }
        }

        function handle_admin_menu() {





            global $current_user;

            $the_plugins = get_plugins();
            $pluginname = 'DZS Video Portal';

            foreach ($the_plugins as $plugin) {
                if ($plugin['Name'] == $pluginname) {
                    if (defined('DZSVP_VERSION')) {
                        $this->addons_dzsvp_activated = true;
                    }
                }
            }



            $admin_cap = $this->capability_admin;

//        echo 'ceva'.$this->addons_dzsvp_activated;
            if ($this->mainoptions['admin_enable_for_users'] == 'on') {
                $this->capability_user = 'read';



                //if current user is not an admin then it is a user and should have it's own database
                if (current_user_can($this->capability_admin) == false) {
                    //print_r($current_user);

                    $currDb = 'user'.$current_user->data->ID;
                    //echo 'ceva'; print_r($this->dbs);
                    if ($currDb != 'main' && $currDb != '') {
                        $this->dbitemsname.='-'.$currDb;
                    }
                    $this->currDb = $currDb;

                    if (is_array($this->dbs) && !in_array($currDb,$this->dbs) && $currDb != 'main' && $currDb != '') {
                        array_push($this->dbs,$currDb);
                        update_option($this->dbdbsname,$this->dbs);
                    }

                    $this->mainitems = get_option($this->dbitemsname);
                    if ($this->mainitems == '') {

                        $mainitems_default_ser = file_get_contents(dirname(__FILE__).'/sampledata/defaultmainitems.txt');
                        $this->mainitems = unserialize($mainitems_default_ser);

                        update_option($this->dbitemsname,$this->mainitems);
                    }
                }
                $admin_cap = $this->capability_user;
            }




            $dzsvg_page = add_menu_page(__('Video Gallery','dzsvg'),__('Video Gallery','dzsvg'),$admin_cap,$this->adminpagename,array($this,'admin_page'),'div');
            $dzsvg_subpage = add_submenu_page($this->adminpagename,__('Video Player Configs','dzsvg'),__('Video Player Configs','dzsvg'),$this->capability_admin,$this->adminpagename_configs,array($this,'admin_page_vpc'));
            $dzsvg_subpage = add_submenu_page($this->adminpagename,__('Designer Center','dzsvg'),__('Designer Center','dzsvg'),$this->capability_admin,$this->adminpagename_designercenter,array($this,'admin_page_dc'));
            $dzsvg_subpage = add_submenu_page($this->adminpagename,__('Video Gallery Settings','dzsvg'),__('Settings','dzsvg'),$this->capability_admin,$this->adminpagename_mainoptions,array($this,'admin_page_mainoptions'));
            $dzsvg_subpage = add_submenu_page($this->adminpagename,__('Autoupdater','dzsvg'),__('Autoupdater','dzsvg'),$this->capability_admin,$this->adminpagename_autoupdater,array($this,'admin_page_autoupdater'));

        }

        function admin_scripts() {
            wp_enqueue_script('media-upload');
            wp_enqueue_script('tiny_mce');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
            wp_enqueue_script('dzsvg_admin',$this->thepath."admin/admin.js");
            wp_enqueue_style('dzsvg_admin',$this->thepath.'admin/admin.css');
            wp_enqueue_script('dzs.farbtastic',$this->thepath."admin/colorpicker/farbtastic.js");
            wp_enqueue_style('dzs.farbtastic',$this->thepath.'admin/colorpicker/farbtastic.css');
            wp_enqueue_style('dzsvg_dzsuploader',$this->thepath.'admin/dzsuploader/upload.css');
            wp_enqueue_script('dzsvg_dzsuploader',$this->thepath.'admin/dzsuploader/upload.js');
            wp_enqueue_style('dzs.scroller',$this->thepath.'dzsscroller/scroller.css');
            wp_enqueue_script('dzs.scroller',$this->thepath.'dzsscroller/scroller.js');
            wp_enqueue_style('dzs.dzscheckbox',$this->thepath.'assets/dzscheckbox/dzscheckbox.css');
            wp_enqueue_style('dzs.dzstoggle',$this->thepath.'dzstoggle/dzstoggle.css');
            wp_enqueue_script('dzs.dzstoggle',$this->thepath.'dzstoggle/dzstoggle.js');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-sortable');
        }

        function front_scripts() {
            //print_r($this->mainoptions);
            $videogalleryscripts = array('jquery');
            wp_enqueue_script('dzs.vplayer',$this->thepath."videogallery/vplayer.js");


//        wp_enqueue_script('dzs.flashhtml5main', $this->thepath . "videogallery/flashhtml5main.js");
            wp_enqueue_style('dzs.vgallery.skin.custom',$this->thepath.'customs/skin_custom.css');


            if($this->mainoptions['disable_fontawesome']!='on'){

                wp_enqueue_style('fontawesome','https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
            }

            //if($this->mainoptions['embed_masonry']=='on'){
            //wp_enqueue_script('jquery.masonry', $this->thepath . "masonry/jquery.masonry.min.js");
            //}
        }

        function add_simple_field($pname,$otherargs = array()) {
            global $data;
            $fout = '';
            $val = '';

            $args = array(
                'val' => ''
            );
            $args = array_merge($args,$otherargs);

            $val = $args['val'];

            //====check if the data from database txt corresponds
            if (isset($data[$pname])) {
                $val = $data[$pname];
            }
            $fout.='<div class="setting"><input type="text" class="textinput short" name="'.$pname.'" value="'.$val.'"></div>';
            echo $fout;
        }

        function add_cb_field($pname) {
            global $data;
            $fout = '';
            $val = '';
            if (isset($data[$pname]))
                $val = $data[$pname];
            $checked = '';
            if ($val == 'on')
                $checked = ' checked';

            $fout.='<div class="setting"><input type="checkbox" class="textinput" name="'.$pname.'" value="on" '.$checked.'/> on</div>';
            echo $fout;
        }

        function add_cp_field($pname,$otherargs = array()) {
            global $data;
            $fout = '';
            $val = '';


            $args = array(
                'val' => '',
                'class' => '',
            );

            $args = array_merge($args,$otherargs);



            //print_r($args);
            $val = $args['val'];


            $fout.='
<div class="setting-input"><input type="text" class="textinput with-colorpicker '.$args['class'].'" name="'.$pname.'" value="'.$val.'">
<div class="picker-con"><div class="the-icon"></div><div class="picker"></div></div>
</div>';
            return $fout;
        }

        function admin_page_dc() {
            $dc_config = array(
                'ispreview' => 'off'
            );
            ?>
        <div class="wrap">
            <h1><?php echo __('Video Gallery Designer Center','dzsvg'); ?></h1>
        <?php if ($dc_config['ispreview'] == 'on') { ?>
                <div class="comment"><?php echo __('Hello and welcome to DZS Video / YouTube / Vimeo Gallery Designer Center. As this is only a preview, it will not save the changes in the primary database, but it will create temp files so you can preview the full power of this 
                    tool ( click <strong>Preview</strong> from the right ). You may notice that you would not find here all the options that you may need for fully customising the gallery. That is because here are only the options that are stricly related to the controls
                 of the gallery. The others like menu position, video list etc. are found in the main xml file ( gallery.xml ) you can find a full list of those options at the bottom.','dzsvg'); ?>
                </div>
                <?php } ?>
            <hr>
            <form class="settings-html5vg">
                <div class="settings_block">
                    <h2><?php echo __('HTML5 Gallery Settings','dzsvg'); ?></h2>

                    <div class="setting">
                        <div class="setting-label">Background</div>
        <?php
        $sname = 'background';
        $val = $this->mainoptions_dc[$sname];
        echo $this->add_cp_field($sname,array('class' => 'dc-input','val' => $val));
        ?>
                    </div>
                    <div class="setting">
                        <div class="setting-label">Controls Background</div>
        <?php
        $sname = 'controls_background';
        $val = $this->mainoptions_dc[$sname];
        echo $this->add_cp_field($sname,array('class' => 'dc-input','val' => $val));
        ?>
                    </div>
                    <div class="setting">
                        <div class="setting-label">Scrubbar Background</div>
        <?php
        $sname = 'scrub_background';
        $val = $this->mainoptions_dc[$sname];
        echo $this->add_cp_field($sname,array('class' => 'dc-input','val' => $val));
        ?>
                    </div>
                    <div class="setting">
                        <div class="setting-label">Scrubbar Buffer</div>
        <?php
        $sname = 'scrub_buffer';
        $val = $this->mainoptions_dc[$sname];
        echo $this->add_cp_field($sname,array('class' => 'dc-input','val' => $val));
        ?>
                    </div>
                    <div class="setting">
                        <div class="setting-label">Controls Main Color</div>
        <?php
        $sname = 'controls_color';
        $val = $this->mainoptions_dc[$sname];
        echo $this->add_cp_field($sname,array('class' => 'dc-input','val' => $val));
        ?>
                    </div>
                    <div class="setting">
                        <div class="setting-label">Controls Hover Color</div>
        <?php
        $sname = 'controls_hover_color';
        $val = $this->mainoptions_dc[$sname];
        echo $this->add_cp_field($sname,array('class' => 'dc-input','val' => $val));
        ?>
                    </div>
                    <div class="setting">
                        <div class="setting-label">Controls Highlight Color</div>
        <?php
        $sname = 'controls_highlight_color';
        $val = $this->mainoptions_dc[$sname];
        echo $this->add_cp_field($sname,array('class' => 'dc-input','val' => $val));
        ?>
                    </div>
                    <div class="setting">
                        <div class="setting-label">Current Time Color</div>
        <?php
        $sname = 'timetext_curr_color';
        $val = $this->mainoptions_dc[$sname];
        echo $this->add_cp_field($sname,array('class' => 'dc-input','val' => $val));
        ?>
                    </div>
                    <br/>
                    <div class="toggle">
                        <div class="toggle-title"><h3><?php echo __('Gallery Thumbs Design','dzsvg'); ?></h3><div class="arrow-down"></div></div>

                        <div class="toggle-content" style="display:none">

                            <div class="setting">
                                <div class="setting-label">Background Color</div>
        <?php
        $sname = 'thumbs_bg';
        $val = $this->mainoptions_dc[$sname];
        echo $this->add_cp_field($sname,array('class' => 'dc-input','val' => $val));
        ?>
                            </div>
                            <div class="setting">
                                <div class="setting-label">Active Background Color</div>
        <?php
        $sname = 'thumbs_active_bg';
        $val = $this->mainoptions_dc[$sname];
        echo $this->add_cp_field($sname,array('class' => 'dc-input','val' => $val));
        ?>
                            </div>
                            <div class="setting">
                                <div class="setting-label">Text Color</div>
        <?php
        $sname = 'thumbs_text_color';
        $val = $this->mainoptions_dc[$sname];
        echo $this->add_cp_field($sname,array('class' => 'dc-input','val' => $val));
        ?>
                            </div>
                            <div class="setting">
                                <div class="setting-label">Thumbnail Image Width</div>
        <?php
        $sname = 'thumbnail_image_width';
        $val = '';
        if (isset($this->mainoptions_dc[$sname])) {
            $val = $this->mainoptions_dc[$sname];
        }
        echo DZSHelpers::generate_input_text($sname,array('class' => 'dc-input','seekval' => $val));
        ?>
                            </div>
                            <div class="setting">
                                <div class="setting-label">Thumbnail Image Height</div>
        <?php
        $sname = 'thumbnail_image_height';
        $val = '';
        if (isset($this->mainoptions_dc[$sname])) {
            $val = $this->mainoptions_dc[$sname];
        }
        echo DZSHelpers::generate_input_text($sname,array('class' => 'dc-input','seekval' => $val));
        ?>
                            </div>

                        </div>
                    </div>




                </div>
                <div class="preview_block">
                    <div>
                        <h2><?php echo __('Preview','dzsvg'); ?></h2>
                        <style id="html5vg-preview-style"></style>
                        <div id="html5vg-preview" class="videogallery skin_pro no-mouse-out" style="width:100%; height:300px;">

                            <div class="vplayer-tobe" data-videoTitle="YouTube Video" data-type="youtube" data-src="AaD0o9q5HXs"><div class="menuDescription">{ytthumb}<div class="the-title">This is an YouTube video</div>The thumbnail can autogenerate... </div>
                            </div>
                            <div class="vplayer-tobe" data-videoTitle="YouTube Video" data-type="youtube" data-src="O2-hiHUh4UQ"><div class="menuDescription">{ytthumb}<div class="the-title">This is an YouTube video</div>The thumbnail can autogenerate... </div>
                            </div>
                            <div class="vplayer-tobe" data-videoTitle="YouTube Video" data-type="youtube" data-src="J-F2q77fhkM"><div class="menuDescription">{ytthumb}<div class="the-title">This is an YouTube video</div>The thumbnail can autogenerate... </div>
                            </div>
                        </div>
                        <!--END VIDEO GALLERY-->
                        <script>

                            var videoplayersettings = {
                                autoplay: "off",
                                constrols_out_opacity: 0.9,
                                constrols_normal_opacity: 0.9
                                , settings_video_overlay: 'on'
                                , settings_hideControls: "off"
                                , design_skin: "sameasgallery"
                                , youtube_defaultQuality: 'hd'
        <?php
        $sname = 'controls_color';
        $val = $this->mainoptions_dc[$sname];
        if ($val != '') {
            echo ',controls_fscanvas_bg:"'.$val.'"';
        }
        $sname = 'controls_hover_color';
        $val = $this->mainoptions_dc[$sname];
        if ($val != '') {
            echo ',controls_fscanvas_hover_bg:"'.$val.'"';
        }
        ?>
                            };
                            jQuery(document).ready(function($) {
                                videoplayersettings.design_skin = "skin_pro";
                                videoplayersettings.settings_youtube_usecustomskin = "on";
                                dzsvg_init("#html5vg-preview", {
                                    totalWidth: '100%',
                                    settings_mode: "normal",
                                    menuSpace: 0,
                                    randomise: "off",
                                    autoplay: "on",
                                    cueFirstVideo: "off",
                                    autoplayNext: "off",
                                    menuitem_width: 275,
                                    menuitem_height: 75,
                                    menuitem_space: 1,
                                    nav_space: '0',
                                    menu_position: "right",
                                    transition_type: "slideup",
                                    design_skin: "skin_navtransparent",
                                    videoplayersettings: videoplayersettings
                                    , design_shadow: "on"
                                    , settings_menu_overlay: 'on'
                                    , settings_disableOutBehaviour: 'on'
                                });
                            });
                        </script>
                    </div>



                </div>
            </form>
            <div class="clear"></div><p>&nbsp;</p>
            <div class="sidenote">
        <?php echo __('Other design options can be found in the main admin under Html5 Gallery Options.','dzsvg'); ?><br/>
                <img src="<?php echo $this->thepath; ?>admin/img/design_main.png"/>
            </div>
            <p><?php echo __('Remember that in order to use the colors set up here, you must set the Video Galley Skin to <strong>skin_custom</strong>. And use with it a video player configuration with <strong>skin_custom</strong> set to it..','dzsvg'); ?></p>
        </div>

        <div class="clear"></div>

        <br/>
        <?php
        if ($dc_config['ispreview'] == 'on') {
            echo '<div>Because preview mode is enabled, saving is disabled. You can still preview your configuration from the Preview button in the right half.</div>';
        }
        ?>
        <a class="<?php
        if ($dc_config['ispreview'] != 'on') {
            echo 'save-button ';
        }
        ?> button-primary" href="#"><?php echo __('Save','dzsvg'); ?></a><div id="save-ajax-loading" class="preloader"></div>
        <div class="clear"></div><br/>
        <?php
       }

       function misc_input_text($argname,$pargs=array()) {
           $fout = '';

           $margs = array(
               'type' => 'text',
               'class' => '',
               'seekval' => '',
               'extra_attr' => '',
           );


           $margs = array_merge($margs,$pargs);

           $type = 'text';
           if (isset($margs['type'])) {
               $type =$margs['type'];
           }
           $fout.='<input type="'.$type.'"';
           if (isset($margs['class'])) {
               $fout.=' class="'.$margs['class'].'"';
           }
           $fout.=' name="'.$argname.'"';
           if (isset($margs['seekval'])) {
               $fout.=' value="'.$margs['seekval'].'"';
           }

           $fout.=$margs['extra_attr'];

           $fout.='/>';
           return $fout;
       }

       function misc_input_textarea($argname,$otherargs = array()) {
           $fout = '';
           $fout.='<textarea';
           $fout.=' name="'.$argname.'"';

           $margs = array(
               'class' => '',
               'val' => '',// === default value
               'seekval' => '',// ===the value to be seeked
               'type' => '',
           );
           $margs = array_merge($margs,$otherargs);



           if ($margs['class'] != '') {
               $fout.=' class="'.$margs['class'].'"';
           }
           $fout.='>';
           if (isset($margs['seekval']) && $margs['seekval'] != '') {
               $fout.=''.$margs['seekval'].'';
           } else {
               $fout.=''.$margs['val'].'';
           }
           $fout.='</textarea>';

           return $fout;
       }

       function misc_input_checkbox($argname,$argopts) {
           $fout = '';
           $auxtype = 'checkbox';

           if (isset($argopts['type'])) {
               if ($argopts['type'] == 'radio') {
                   $auxtype = 'radio';
               }
           }
           $fout.='<input type="'.$auxtype.'"';
           $fout.=' name="'.$argname.'"';
           if (isset($argopts['class'])) {
               $fout.=' class="'.$argopts['class'].'"';
           }
           $theval = 'on';
           if (isset($argopts['val'])) {
               $fout.=' value="'.$argopts['val'].'"';
               $theval = $argopts['val'];
           } else {
               $fout.=' value="on"';
           }
           //print_r($this->mainoptions); print_r($argopts['seekval']);
           if (isset($argopts['seekval'])) {
               $auxsw = false;
               if (is_array($argopts['seekval'])) {
                   //echo 'ceva'; print_r($argopts['seekval']);
                   foreach ($argopts['seekval'] as $opt) {
                       //echo 'ceva'; echo $opt; echo 
                       if ($opt == $argopts['val']) {
                           $auxsw = true;
                       }
                   }
               } else {
                   //echo $argopts['seekval']; echo $theval;
                   if ($argopts['seekval'] == $theval) {
                       //echo $argval;
                       $auxsw = true;
                   }
               }
               if ($auxsw == true) {
                   $fout.=' checked="checked"';
               }
           }
           $fout.='/>';
           return $fout;
       }

       function admin_page_mainoptions() {
           //print_r($this->mainoptions);





           if(isset($_GET['dzsvg_shortcode_builder']) && $_GET['dzsvg_shortcode_builder']=='on'){
               dzsvg_shortcode_builder();
           }else {

               if (isset($_POST['dzsvg_delete_cache']) && $_POST['dzsvg_delete_cache'] == 'on') {
                   delete_option('dzsvg_cache_ytuserchannel');
                   delete_option('dzsvg_cache_ytplaylist');
                   delete_option('dzsvg_cache_ytkeywords');
                   delete_option('cache_dzsvg_vmuser');
                   delete_option('cache_dzsvg_vmchannel');
                   delete_option('cache_dzsvg_vmalbum');
               }
//        print_r($this->mainoptions);
               ?>

               <div class="wrap">
                   <h2><?php echo __('Video Gallery Main Settings', 'dzsvg'); ?></h2>
                   <br/>

                   <form class="mainsettings">

                       <h3>Admin Options</h3>

                       <div class="setting">
                           <div class="label"><?php echo __('do not use wordpres uploader', 'dzsvg'); ?></div>
                           <?php echo $this->misc_input_checkbox('usewordpressuploader', array('val' => 'off', 'seekval' => $this->mainoptions['usewordpressuploader'])); ?>
                       </div>

                       <div class="setting">
                           <div
                               class="label"><?php echo __('Use External wp-content Upload Directory ?', 'dzsvg'); ?></div>
                           <?php echo $this->misc_input_checkbox('use_external_uploaddir', array('val' => 'on', 'seekval' => $this->mainoptions['use_external_uploaddir'])); ?>
                           <div
                               class="sidenote"><?php echo __('use an outside directory for uploading files', 'dzsvg'); ?></div>
                       </div>

                       <div class="setting">
                           <div class="label"><?php echo __('Always Embed Scripts?', 'dzsvg'); ?></div>
                           <?php echo $this->misc_input_checkbox('always_embed', array('val' => 'on', 'seekval' => $this->mainoptions['always_embed'])); ?>
                           <div
                               class="sidenote"><?php echo __('by default scripts and styles from this gallery are included only when needed for optimizations reasons, but you can choose to always use them ( useful for when you are using a ajax theme that does not reload the whole page on url change )', 'dzsvg'); ?></div>
                       </div>

                       <div class="setting">
                           <div class="label"><?php echo __('Fast binding?', 'dzsvg'); ?></div>
                           <?php echo $this->misc_input_checkbox('is_safebinding', array('val' => 'off', 'seekval' => $this->mainoptions['is_safebinding'])); ?>
                           <div
                               class="sidenote"><?php echo __('the galleries admin can use a complex ajax backend to ensure fast editing, but this can cause limitation issues on php servers. Turn this to on if you want a faster editing experience ( and if you have less then 20 videos accross galleries ) ', 'dzsvg'); ?></div>
                       </div>
                       <div class="setting">
                           <div class="label"><?php echo __('Do Not Use Caching', 'dzsvg'); ?></div>
                           <?php
                           //        print_r($this->mainoptions);
                           echo $this->misc_input_checkbox('disable_api_caching', array('val' => 'on', 'seekval' => $this->mainoptions['disable_api_caching'])); ?>
                           <div
                               class="sidenote"><?php echo __('use caching for vimeo / youtube api ( recommended - on )', 'dzsvg'); ?></div>
                       </div>

                       <div class="setting">
                           <div class="label"><?php echo __('Disable FontAwesome', 'dzsvg'); ?></div>
                           <?php
                           //        print_r($this->mainoptions);
                           echo $this->misc_input_checkbox('disable_fontawesome', array('val' => 'on', 'seekval' => $this->mainoptions['disable_fontawesome'])); ?>
                           <div
                               class="sidenote"><?php echo __('include the fontawesome library', 'dzsvg'); ?></div>
                       </div>
                       <div class="setting">
                           <div class="label"><?php echo __('Enable Visitors Gallery Access', 'dzsvg'); ?></div>
                           <?php
                           $lab = 'admin_enable_for_users';
                           echo $this->misc_input_checkbox($lab, array('val' => 'on', 'seekval' => $this->mainoptions[$lab]));
                           ?>
                           <div
                               class="sidenote"><?php echo __('your logged in users will be able to have their own galleries', 'dzsvg'); ?></div>
                       </div>

                       <div class="setting">
                           <div class="label"><?php echo __('Force File Get Contents', 'dzsvg'); ?></div>
                           <?php echo $this->misc_input_checkbox('force_file_get_contents', array('val' => 'on', 'seekval' => $this->mainoptions['force_file_get_contents'])); ?>
                           <div
                               class="sidenote"><?php echo __('sometimes curl will not work for retrieving youtube user name / playlist - try enabling this option if so...', 'dzsvg'); ?></div>
                       </div>


                       <div class="setting">
                           <div class="label"><?php echo __('Force Refresh Size Every 1000ms', 'dzsvg'); ?></div>
                           <?php
                           $lab = 'settings_trigger_resize';
                           echo $this->misc_input_checkbox($lab, array('val' => 'on', 'seekval' => $this->mainoptions[$lab]));
                           ?>
                           <div
                               class="sidenote"><?php echo __('sometimes sizes need to be recalculated ( for example if you use the gallery in tabs )', 'dzsvg'); ?></div>
                       </div>

                       <div class="setting">
                           <div class="label"><?php echo __('Replace JWPlayer', 'dzsvg'); ?></div>
                           <?php echo $this->misc_input_checkbox('replace_jwplayer', array('val' => 'on', 'seekval' => $this->mainoptions['replace_jwplayer'])); ?>
                           <div
                               class="sidenote"><?php echo __('render jw player shortcodes with DZS Video Gallery', 'dzsvg'); ?></div>
                       </div>
                       <div class="setting">
                           <div
                               class="label"><?php echo __('Replace [video] Shortcode for Simple Videos', 'dzsvg'); ?></div>
                           <?php echo $this->misc_input_checkbox('replace_wpvideo', array('val' => 'on', 'seekval' => $this->mainoptions['replace_wpvideo'])); ?>
                           <div
                               class="sidenote"><?php echo __('render simple wp videos with DZS Video Gallery', 'dzsvg'); ?></div>
                       </div>
                       <div class="setting">
                           <div
                               class="label"><?php echo __('Enable Preview Shortcodes in TinyMce Editor', 'dzsvg'); ?></div>
                           <?php echo $this->misc_input_checkbox('tinymce_enable_preview_shortcodes', array('val' => 'on', 'seekval' => $this->mainoptions['tinymce_enable_preview_shortcodes'])); ?>
                           <div
                               class="sidenote"><?php echo __('add a box with the shortcode in the tinymce Visual Editor', 'dzsvg'); ?></div>
                       </div>


                       <div class="setting">
                           <div class="label"><?php echo __('Debug Mode', 'dzsvg'); ?></div>
                           <?php echo $this->misc_input_checkbox('debug_mode', array('val' => 'on', 'seekval' => $this->mainoptions['debug_mode'])); ?>
                           <div
                               class="sidenote"><?php echo __('activate debug mode ( advanced mode )', 'dzsvg'); ?></div>
                       </div>
                       <div class="setting">
                           <div class="label"><?php echo __('Extra CSS', 'dzsp'); ?></div>
                           <?php echo $this->misc_input_textarea('extra_css', array('val' => '', 'seekval' => $this->mainoptions['extra_css'])); ?>
                           <div class="sidenote"><?php echo __('', 'dzsp'); ?></div>
                       </div>


                       <div class="setting">
                           <div class="label"><?php echo __('Vimeo Thumbnail Quality', 'dzsvp'); ?></div>
                           <?php
                           $arr_opts = array(
                               array(
                                 'lab'=>__('Low Quality'),
                                 'val'=>'low',
                               ),
                               array(
                                 'lab'=>__('Medium Quality'),
                                 'val'=>'medium',
                               ),
                               array(
                                 'lab'=>__('High Quality'),
                                 'val'=>'high',
                               ),
                           );

                           $lab = 'vimeo_thumb_quality';
                           $val = $this->mainoptions[$lab];
                           echo DZSHelpers::generate_select($lab, array('options' => $arr_opts, 'class' => 'styleme', 'seekval' => $val));
                           ?>

                       <?php
                       echo '
                <h3>' . __('YouTube Options', 'dzsvg') . '</h3>
                <div class="setting">
                    <div class="label">' . __('YouTube API Key', 'dzsvg') . '</div>
                    ' . $this->misc_input_text('youtube_api_key', array('val' => '', 'seekval' => $this->mainoptions['youtube_api_key'])) . '
                    <div class="sidenote">' . __('get a api key <a href="https://console.developers.google.com">here</a>, create a new project, access API > <strong>APIs</strong> and enabled YouTube Data API, then create your Public API Access from API > Credentials', 'dzsvg') . '</div>
                    <div class="sidenote">' . __('remember, do not enter anything in referers field, unless you know what you are doing, leave it clear like so - <a href="https://lh3.googleusercontent.com/5eps7rIYzxwpO5ftxy4D6GiMdimShMRWM7XE0-pQ5lI=w1221-h950-no">here</a>', 'dzsvg') . '</div>
                </div>';

                       $lab = 'vimeo_api_user_id';
                       echo '<h3>' . __('Vimeo Options', 'dzsvg') . '</h3>
                <div class="setting">
                    <div class="label">' . __('Your User ID', 'dzsvg') . '</div>
                    ' . $this->misc_input_text($lab, array('val' => '', 'seekval' => $this->mainoptions[$lab])) . '
                    <div class="sidenote">' . __('get it from https://vimeo.com/settings, must be in the form of user123456 ', 'dzsvg') . '</div>
                </div>';

                       $lab = 'vimeo_api_client_id';
                       echo '
                <div class="setting">
                    <div class="label">' . __('Client ID', 'dzsvg') . '</div>
                    ' . $this->misc_input_text($lab, array('val' => '', 'seekval' => $this->mainoptions[$lab])) . '
                    <div class="sidenote">' . __('you can get an api key from <a href="https://developer.vimeo.com/apps">here</a> - section <strong>oAuth2</strong> from the app ', 'dzsvg') . '</div>
                </div>';


                       $lab = 'vimeo_api_client_secret';
                       echo '
                <div class="setting">
                    <div class="label">' . __('Client Secret', 'dzsvg') . '</div>
                    ' . $this->misc_input_text($lab, array('val' => '', 'seekval' => $this->mainoptions[$lab])) . '
                </div>';


                       $lab = 'vimeo_api_access_token';
                       echo '
                <div class="setting">
                    <div class="label">' . __('Access Token', 'dzsvg') . '</div>
                    ' . $this->misc_input_text($lab, array('val' => '', 'seekval' => $this->mainoptions[$lab])) . '
                </div>';

                       echo '<h3>' . __('Translate Options', 'dzsvg') . '</h3>';


                       $lab = 'translate_skipad';
                       echo '
                <div class="setting">
                    <div class="label">' . __('Translate Skip Ad', 'dzsvg') . '</div>
                    ' . $this->misc_input_text($lab, array('val' => '', 'seekval' => $this->mainoptions[$lab])) . '
                </div>';


                       do_action('dzsvg_mainoptions_extra');
                       ?>
                       <br/>
                       <a href='#'
                          class="button-primary dzsvg-mo-save-mainoptions"><?php echo __('Save Options', 'dzsvg'); ?></a>
                   </form>
                   <br/><br/>

                   <form class="mainsettings" method="POST">
                       <button name="dzsvg_delete_cache" value="on"
                               class="button-secondary"><?php echo __('Delete All Caches', 'dzsvg'); ?></button>
                   </form>
                   <div class="sidenote">Delete all YouTube and Vimeo channel feeds caches</div>
                   <br/>

                   <div class="saveconfirmer" style=""><img alt="" style="" id="save-ajax-loading2"
                                                            src="<?php echo site_url(); ?>/wp-admin/images/wpspin_light.gif"/>
                   </div>
                   <script>
                       jQuery(document).ready(function ($) {
                           $('input:checkbox').checkbox();
                       })
                   </script>
               </div>
               <div class="clear"></div><br/>
           <?php
           }
    }

    function admin_page_autoupdater(){

    ?>
    <div class="wrap">



        <?php

        $auxarray = array();


        if(isset($_GET['dzsvg_purchase_remove_binded']) && $_GET['dzsvg_purchase_remove_binded']=='on'){

            $this->mainoptions['dzsvg_purchase_code_binded']='off';

            update_option($this->dboptionsname,$this->mainoptions);

        }

        if(isset($_POST['action']) && $_POST['action']==='dzsvg_update_request'){





            if(isset($_POST['dzsvg_purchase_code'])){
                $auxarray= array('dzsvg_purchase_code' => $_POST['dzsvg_purchase_code']);
                $auxarray = array_merge($this->mainoptions, $auxarray);

                $this->mainoptions= $auxarray;


                update_option($this->dboptionsname,$auxarray);
            }



        }

        $extra_class = '';
        $extra_attr = '';
        $form_method = "POST";
        $form_action = "";
        $disable_button = '';

        $lab = 'dzsvg_purchase_code';

        if($this->mainoptions['dzsvg_purchase_code_binded']=='on'){
            $extra_attr = ' disabled';
            $disable_button = ' <input type="hidden" name="purchase_code" value="'.$this->mainoptions[$lab].'"/><input type="hidden" name="site_url" value="'.site_url().'"/><input type="hidden" name="redirect_url" value="'.esc_url(add_query_arg('dzsvg_purchase_remove_binded','on',dzs_curr_url())).'"/><button class="button-secondary" name="action" value="dzsvg_purchase_code_disable">'.__("Disable Key").'</button>';
            $form_action=' action="http://zoomthe.me/updater_dzsvg/servezip.php"';
        }





        echo '<form'.$form_action.' class="mainsettings" method="'.$form_method.'">';

        echo '
                <div class="setting">
                    <div class="label">'.__('Purchase Code','dzsvg').'</div>
                    '.$this->misc_input_text($lab,array('val' => '','seekval' => $this->mainoptions[$lab],'class' => $extra_class,'extra_attr' => $extra_attr)).$disable_button.'
                    <div class="sidenote">'.__('You can <a href="https://lh5.googleusercontent.com/-o4WL83UU4RY/Unpayq3yUvI/AAAAAAAAJ_w/HJmso_FFLNQ/w786-h1179-no/puchase.jpg" target="“_blank”">find it here</a> ','dzsvg').'</div>
                </div>';


        if($this->mainoptions['dzsvg_purchase_code_binded']=='on'){
            echo '</form><form class="mainsettings" method="post">';
        }

        echo '<p><button class="button-primary" name="action" value="dzsvg_update_request">'.__("Update").'</button></p>';

        if(isset($_POST['action']) && $_POST['action']==='dzsvg_update_request'){



//            echo 'ceva';


//            die();



            $aux = 'http://zoomthe.me/updater_dzsvg/servezip.php?purchase_code='.$this->mainoptions['dzsvg_purchase_code'].'&site_url='.site_url();
            $res = DZSHelpers::get_contents($aux);

//            echo 'hmm'; echo strpos($res,'<div class="error">'); echo 'dada'; echo $res;
            if($res===false){
                echo 'server offline';
            }else{
                if(strpos($res,'<div class="error">')===0){
                    echo $res;


                    if(strpos($res,'<div class="error">error: in progress')===0){

                        $this->mainoptions['dzsvg_purchase_code_binded']='on';
                        update_option($this->dboptionsname,$this->mainoptions);
                    }
                }else{

                    file_put_contents(dirname(__FILE__).'/update.zip', $res);
                    if(class_exists('ZipArchive')){
                        $zip = new ZipArchive;
                        $res = $zip->open(dirname(__FILE__).'/update.zip');
                        //test
                        if ($res === TRUE) {
//                echo 'ok';
                            $zip->extractTo(dirname(__FILE__));
                            $zip->close();


                            $this->mainoptions['dzsvg_purchase_code_binded']='on';
                            update_option($this->dboptionsname,$this->mainoptions);


                        } else {
                            echo 'failed, code:' . $res;
                        }
                        echo __('Update done.');
                    }else{

                        echo __('ZipArchive class not found.');
                    }

                }
            }
        }





        ?>
            </form>
        </div>
        <?php
    }

    function admin_page() {
        ?>
        <div class="wrap">
            <div class="import-export-db-con">
                <div class="the-toggle"></div>
                <div class="the-content-mask" style="">

                    <div class="the-content">
                        <form enctype="multipart/form-data" action="" method="POST">
                            <div class="one_half">
                                <h3>Import Database</h3>
                                <input name="dzsvg_importdbupload" type="file" size="10"/><br />
                            </div>
                            <div class="one_half last alignright">
                                <input class="button-secondary" type="submit" name="dzsvg_importdb" value="Import" />
                            </div>
                            <div class="clear"></div>
                        </form>


                        <form enctype="multipart/form-data" action="" method="POST">
                            <div class="one_half">
                                <h3>Import Slider</h3>
                                <input name="importsliderupload" type="file" size="10"/><br />
                            </div>
                            <div class="one_half last alignright">
                                <input class="button-secondary" type="submit" name="dzsvg_importslider" value="Import" />
                            </div>
                            <div class="clear"></div>
                        </form>

                        <div class="one_half">
                            <h3>Export Database</h3>
                        </div>
                        <div class="one_half last alignright">
                            <form action="" method="POST"><input class="button-secondary" type="submit" name="dzsvg_exportdb" value="Export"/></form>
                        </div>
                        <div class="clear"></div>

                    </div>
                </div>
            </div>
            <h2>DZS <?php _e('Video Gallery Admin','dzsvg'); ?>&nbsp; <span class="version-number" style="font-size:13px; font-weight: 100;">version <span class="now-version"><?php echo DZSVG_VERSION; ?></span></span> <img alt="" style="visibility: visible;" id="main-ajax-loading" src="<?php bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif"/></h2>
            <noscript><?php _e('You need javascript for this.','dzsvg'); ?></noscript>
        <?php
        if (current_user_can($this->capability_admin)) {
            ?><div class="top-buttons">
                    <a href="<?php echo $this->thepath; ?>readme/index.html" class="button-secondary action"><?php _e('Documentation','dzsvg'); ?></a>
                    <a href="<?php echo admin_url('admin.php?page=dzsvg-dc'); ?>" target="_blank" class="button-secondary action"><?php _e('Go to Designer Center','dzsvg'); ?></a>
                    <div class="super-select db-select dzsvg"><button class="button-secondary btn-show-dbs">Current Database - <span class="strong currdb"><?php
            if ($this->currDb == '') {
                echo 'main';
            } else {
                echo $this->currDb;
            }
            ?></span></button>
                        <select class="main-select hidden"><?php
                                //print_r($this->dbs);

                                if (is_array($this->dbs)) {
                                    foreach ($this->dbs as $adb) {
                                        $params = array('dbname' => $adb);
                                        $newurl = esc_url(add_query_arg($params,dzs_curr_url()));
                                        echo '<option'.' data-newurl="'.$newurl.'"'.'>'.$adb.'</option>';
                                    }
                                } else {
                                    $params = array('dbname' => 'main');
                                    $newurl = esc_url(add_query_arg($params,dzs_curr_url()));
                                    echo '<option'.' data-newurl="'.$newurl.'"'.' selected="selected"'.'>'.$adb.'</option>';
                                }
                                ?></select><div class="hidden replaceurlhelper"><?php
                            $params = array('dbname' => 'replaceurlhere');
                            $newurl = esc_url(add_query_arg($params,dzs_curr_url()));
                            echo $newurl;
                            ?></div>
                    </div>
                </div><?php
                            }
                            ?><table cellspacing="0" class="wp-list-table widefat dzs_admin_table main_sliders">
                <thead> 
                    <tr> 
                        <th style="" class="manage-column column-name" id="name" scope="col"><?php echo __('ID','dzsvg'); ?></th>
                        <th class="column-edit"><?php echo __('Edit','dzsvg'); ?></th>
                        <th class="column-edit"><?php echo __('Embed','dzsvg'); ?></th>
                        <th class="column-edit"><?php echo __('Export','dzsvg'); ?></th>
                        <th class="column-edit"><?php echo __('Duplicate','dzsvg'); ?></th>
                        <th class="column-edit"><?php echo __('Delete','dzsvg'); ?></th> 
                    </tr>
                </thead> 
                <tbody>
                </tbody>
            </table>
        <?php
        $url_add = '';
        $items = $this->mainitems;
        //echo count($items);

        $aux =  esc_url(remove_query_arg('deleteslider',dzs_curr_url()));

        $nextslidernr = count($items);
        if ($nextslidernr < 1) {
            $nextslidernr = 1;
        }
        $params = array('currslider' => $nextslidernr);
        $url_add = esc_url(add_query_arg($params,$aux));
        ?>
            <a class="button-secondary add-slider" href="<?php echo $url_add; ?>"><?php _e('Add Slider','dzsvg'); ?></a>
            <form class="master-settings">
            </form>
            <div class="block">
                <div class="extra-options">
                    <h3><?php echo __('Import','dzsvg'); ?></h3>
                    <!-- demo/ playlist: ADC18FE37410D250, user: digitalzoomstudio, vimeo: 5137664 -->
                    <input type="text" name="import_inputtext" id="import_inputtext" value="digitalzoomstudio"/>
                    <div class="sidenote"><?php _e('Import here feed from a YT Playlist, YT User Channel or Vimeo User Channel - you just have to enter the 
                        id of the playlist / user id in the box below and select the correct type from below','dzsvg').'. Remember to set the <strong>Feed From</strong> field to <strong>Normal</strong> after your videos have been imported.'; ?></div>
                    <a href="#" id="importytplaylist" class="button-secondary">YouTube Playlist</a>
                    <a href="#" id="importytuser" class="button-secondary">YouTube User Channel</a>
                    <a href="#" id="importvimeouser" class="button-secondary">Vimeo User Channel</a>
                    <br/>
                    <span class="import-error" style="display:none;"></span>
                </div>
            </div>
            <div class="dzs-multi-upload">
                <h3>Choose file(s)</h3>
                <div>
                    <input class="files-upload multi-uploader" name="file_field" type="file" multiple>
                </div>
                <div class="droparea">
                    <div class="instructions">drag & drop files here</div>
                </div>
                <div class="upload-list-title">The Preupload List</div>
                <ul class="upload-list">
                    <li class="dummy">add files here from the button or drag them above</li>
                </ul>
                <button class="primary-button upload-button">Upload All</button>
            </div>
            <div class="notes">
                <div class="curl">Curl: <?php echo function_exists('curl_version') ? 'Enabled' : 'Disabled'.'<br />'; ?>
                </div>
                <div class="fgc">File Get Contents: <?php echo ini_get('allow_url_fopen') ? "Enabled" : "Disabled"; ?>
                </div>
                <div class="sidenote"><?php _e('If neither of these are enabled, only normal feed will work. 
                    Contact your host provider on how to enable these services to use the YouTube User Channel 
                    or YouTube Playlist feed.','dzsvg'); ?>
                </div>
            </div>
            <div class="saveconfirmer"><?php _e('Loading...','dzsvg'); ?></div>
            <a href="#" class="button-primary master-save"></a> <img alt="" style="position:fixed; bottom:18px; right:125px; visibility: hidden;" id="save-ajax-loading" src="<?php bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif"/>

            <a href="#" class="button-primary master-save"><?php echo __('Save All Galleries','dzsvg'); ?></a>
            <a href="#" class="button-primary slider-save"><?php echo __('Save Gallery','dzsvg'); ?></a>
        </div>
        <script>
        <?php
//$jsnewline = '\\' + "\n";
        if (isset($this->mainoptions['use_external_uploaddir']) && $this->mainoptions['use_external_uploaddir'] == 'on') {
            echo "window.dzs_upload_path = '".site_url('wp-content')."/upload/';
";
            echo "window.dzs_phpfile_path = '".site_url('wp-content')."/upload.php';
";
        } else {
            echo "window.dzs_upload_path = '".$this->thepath."admin/upload/';
";
            echo "window.dzs_phpfile_path = '".$this->thepath."admin/upload.php';
";
        }

//        print_r($items);

        $aux = str_replace(array("\r","\r\n","\n"),'',$this->sliderstructure);
        if(isset($_GET['currslider']) && isset($items[$_GET['currslider']]) && $items[$_GET['currslider']]['settings']){

        $aux = str_replace(array("theidofthegallery"),$items[$_GET['currslider']]['settings']['id'],$aux);
        }
        echo "var sliderstructure = '".$aux."';
";
        $aux = str_replace(array("\r","\r\n","\n"),'',$this->itemstructure);
        echo "var itemstructure = '".$aux."';
";
        $aux = str_replace(array("\r","\r\n","\n"),'',$this->videoplayerconfig);
        echo "var videoplayerconfig = '".$aux."';
";
        ?>
            jQuery(document).ready(function($) {
                sliders_ready();
                if (jQuery.fn.multiUploader) {
                    jQuery('.dzs-multi-upload').multiUploader();
                }
        <?php
        $items = $this->mainitems;
        for ($i = 0; $i < count($items); $i++) {
//print_r($items[$i]);
            $aux = '';
            if (isset($items[$i]) && isset($items[$i]['settings']) && isset($items[$i]['settings']['id'])) {
                //echo $items[$i]['settings']['id'];
                $aux2= $items[$i]['settings']['id'];
                $aux2 = str_replace(array("\r","\r\n","\n",'\\',"\\"),'',$aux2);
                $aux2 = str_replace(array('"'),"'",$aux2);
                $aux = '{ name: "'.$aux2.'"}';
            }
            echo "sliders_addslider(".$aux.");";
        }
        if (count($items) > 0){
            echo 'sliders_showslider(0);';
        }
        
        
        for ($i = 0; $i < count($items); $i++) {
//echo $i . $this->currSlider . 'cevava';
            if (($this->mainoptions['is_safebinding'] != 'on' || $i == $this->currSlider) && is_array($items[$i])) {

                //==== jsi is the javascript I, if safebinding is on then the jsi is always 0 ( only one gallery ) 
                $jsi = $i;
                if ($this->mainoptions['is_safebinding'] == 'on') {
                    $jsi = 0;
                }

                for ($j = 0; $j < count($items[$i]) - 1; $j++) {
                    echo "sliders_additem(".$jsi.");";
                }

                foreach ($items[$i] as $label => $value) {
                    if ($label === 'settings') {
                        if (is_array($items[$i][$label])) {
                            foreach ($items[$i][$label] as $sublabel => $subvalue) {
                                $subvalue = (string)$subvalue;
                                $subvalue = stripslashes($subvalue);
                                $subvalue = str_replace(array("\r","\r\n","\n",'\\',"\\"),'',$subvalue);
                                $subvalue = str_replace(array("'"),'"',$subvalue);

                                //--- compatibility with older versions
                                if($sublabel=='feedfrom'){
                                    if($subvalue=='youtube playlist'){
                                        $subvalue='ytplaylist';
                                    }
                                }
                                if($sublabel=='youtubefeed_playlist'){
                                    $sublabel='ytplaylist_source';
                                }
                                echo 'sliders_change('.$jsi.', "settings", "'.$sublabel.'", '."'".$subvalue."'".');';
                            }
                        }
                    } else {

                        if (is_array($items[$i][$label])) {
                            foreach ($items[$i][$label] as $sublabel => $subvalue) {
                                $subvalue = (string)$subvalue;
                                $subvalue = stripslashes($subvalue);
                                $subvalue = str_replace(array("\r","\r\n","\n",'\\',"\\"),'',$subvalue);
                                $subvalue = str_replace(array("'"),'"',$subvalue);
                                if ($label == '') {
                                    $label = '0';
                                }
                                echo 'sliders_change('.$jsi.', '.$label.', "'.$sublabel.'", '."'".$subvalue."'".');';
                            }
                        }
                    }
                }
                if ($this->mainoptions['is_safebinding'] == 'on') {
                    break;
                }
            }
        }
        ?>
                jQuery('#main-ajax-loading').css('visibility', 'hidden');
                if (dzsvg_settings.is_safebinding == "on") {
                    jQuery('.master-save').remove();
                    if (dzsvg_settings.addslider == "on") {
                        sliders_addslider();
                        window.currSlider_nr = -1
                        sliders_showslider(0);
                    }
                }
                check_global_items();
                sliders_allready();
            });
        </script>
        <?php
    }

    function admin_page_vpc() {
        ?>
        <div class="wrap">
            <div class="import-export-db-con">
                <div class="the-toggle"></div>
                <div class="the-content-mask" style="">

                    <div class="the-content">
                        <form enctype="multipart/form-data" action="" method="POST">
                            <div class="one_half">
                                <h3>Import Database</h3>
                                <input name="dzsvg_importdbupload" type="file" size="10"/><br />
                            </div>
                            <div class="one_half last alignright">
                                <input class="button-secondary" type="submit" name="dzsvg_importdb" value="Import" />
                            </div>
                            <div class="clear"></div>
                        </form>


                        <form enctype="multipart/form-data" action="" method="POST">
                            <div class="one_half">
                                <h3>Import Slider</h3>
                                <input name="importsliderupload" type="file" size="10"/><br />
                            </div>
                            <div class="one_half last alignright">
                                <input class="button-secondary" type="submit" name="dzsvg_importslider" value="Import" />
                            </div>
                            <div class="clear"></div>
                        </form>

                        <div class="one_half">
                            <h3>Export Database</h3>
                        </div>
                        <div class="one_half last alignright">
                            <form action="" method="POST"><input class="button-secondary" type="submit" name="dzsvg_exportdb" value="Export"/></form>
                        </div>
                        <div class="clear"></div>

                    </div>
                </div>
            </div>
            <h2>DZS <?php _e('Video Gallery Admin','dzsvg'); ?> <img alt="" style="visibility: visible;" id="main-ajax-loading" src="<?php bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif"/></h2>
            <noscript><?php _e('You need javascript for this.','dzsvg'); ?></noscript>
            <div class="top-buttons">
                <a href="<?php echo $this->thepath; ?>readme/index.html" class="button-secondary action"><?php _e('Documentation','dzsvg'); ?></a>
                <a href="<?php echo $this->thepath; ?>deploy/designer/index.php" target="_blank" class="button-secondary action"><?php _e('Go to Designer Center','dzsvg'); ?></a>

            </div>
            <table cellspacing="0" class="wp-list-table widefat dzs_admin_table main_sliders">
                <thead> 
                    <tr> 
                        <th style="" class="manage-column column-name" id="name" scope="col"><?php _e('ID','dzsvg'); ?></th>
                        <th class="column-edit">Edit</th>
                        <th class="column-edit">Embed</th>
                        <th class="column-edit">Export</th>
        <?php
        if ($this->mainoptions['is_safebinding'] != 'on') {
            ?>
                            <th class="column-edit">Duplicate</th> 
                            <?php
                        }
                        ?>
                        <th class="column-edit">Delete</th> 
                    </tr> 
                </thead> 
                <tbody>
                </tbody>
            </table>
        <?php
        $url_add = '';
        $url_add = '';
        $items = $this->mainvpconfigs;
        //echo count($items);
        //print_r($items);

        $aux = remove_query_arg('deleteslider',dzs_curr_url());
        $params = array('currslider' => count($items));
        $url_add = esc_url(add_query_arg($params,$aux));
        ?>
            <a class="button-secondary add-slider" href="<?php echo $url_add; ?>"><?php _e('Add Slider','dzsvg'); ?></a>
            <form class="master-settings only-settings-con mode_vpconfigs">
            </form>
            <div class="saveconfirmer"><?php _e('Loading...','dzsvg'); ?></div>
            <a href="#" class="button-primary master-save-vpc"></a> <img alt="" style="position:fixed; bottom:18px; right:125px; visibility: hidden;" id="save-ajax-loading" src="<?php bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif"/>

            <a href="#" class="button-primary master-save-vpc"><?php _e('Save All Configs','dzsvg'); ?></a>
            <a href="#" class="button-secondary slider-save-vpc"><?php _e('Save Config','dzsvg'); ?></a>
        </div>
        <script>
        <?php
//$jsnewline = '\\' + "\n";
        if (isset($this->mainoptions['use_external_uploaddir']) && $this->mainoptions['use_external_uploaddir'] == 'on') {
            echo "window.dzs_upload_path = '".site_url('wp-content')."/upload/';
";
            echo "window.dzs_phpfile_path = '".site_url('wp-content')."/upload.php';
";
        } else {
            echo "window.dzs_upload_path = '".$this->thepath."admin/upload/';
";
            echo "window.dzs_phpfile_path = '".$this->thepath."admin/upload.php';
";
        }
        $aux = str_replace(array("\r","\r\n","\n"),'',$this->sliderstructure);
        echo "var sliderstructure = '".$aux."';
";
        $aux = str_replace(array("\r","\r\n","\n"),'',$this->itemstructure);
        echo "var itemstructure = '".$aux."';
";
        $aux = str_replace(array("\r","\r\n","\n"),'',$this->videoplayerconfig);
        echo "var videoplayerconfig = '".$aux."';
";
        ?>
            jQuery(document).ready(function($) {
                sliders_ready();
                if (jQuery.fn.multiUploader) {
                    jQuery('.dzs-multi-upload').multiUploader();
                }
        <?php
        $items = $this->mainvpconfigs;
        for ($i = 0; $i < count($items); $i++) {
//print_r($items[$i]);
            $aux = '';
            if (isset($items[$i]) && isset($items[$i]['settings']) && isset($items[$i]['settings']['id'])) {
                //echo $items[$i]['settings']['id'];
                $aux2 = $items[$i]['settings']['id'];
                
                $aux2 = str_replace(array("\r","\r\n","\n",'\\',"\\"),'',$aux2);
                $aux2 = str_replace(array("'"),'"',$aux2);
                $aux = '{ name: \''.$aux2.'\'}';
            }
            echo "sliders_addslider(".$aux.");";
        }
        if (count($items) > 0)
            echo 'sliders_showslider(0);';
        for ($i = 0; $i < count($items); $i++) {
//echo $i . $this->currSlider . 'cevava';
            if (($this->mainoptions['is_safebinding'] != 'on' || $i == $this->currSlider) && is_array($items[$i])) {

                //==== jsi is the javascript I, if safebinding is on then the jsi is always 0 ( only one gallery ) 
                $jsi = $i;
                if ($this->mainoptions['is_safebinding'] == 'on') {
                    $jsi = 0;
                }

                for ($j = 0; $j < count($items[$i]) - 1; $j++) {
                    echo "sliders_additem(".$jsi.");";
                }

                foreach ($items[$i] as $label => $value) {
                    if ($label === 'settings') {
                        if (is_array($items[$i][$label])) {
                            foreach ($items[$i][$label] as $sublabel => $subvalue) {
                                $subvalue = (string)$subvalue;
                                $subvalue = stripslashes($subvalue);
                                $subvalue = str_replace(array("\r","\r\n","\n",'\\',"\\"),'',$subvalue);
                                $subvalue = str_replace(array("'"),'"',$subvalue);
                                echo 'sliders_change('.$jsi.', "settings", "'.$sublabel.'", '."'".$subvalue."'".');';
                            }
                        }
                    } else {

                        if (is_array($items[$i][$label])) {
                            foreach ($items[$i][$label] as $sublabel => $subvalue) {
                                $subvalue = (string)$subvalue;
                                $subvalue = stripslashes($subvalue);
                                $subvalue = str_replace(array("\r","\r\n","\n",'\\',"\\"),'',$subvalue);
                                $subvalue = str_replace(array("'"),'"',$subvalue);
                                if ($label == '') {
                                    $label = '0';
                                }
                                echo 'sliders_change('.$jsi.', '.$label.', "'.$sublabel.'", '."'".$subvalue."'".');';
                            }
                        }
                    }
                }
                if ($this->mainoptions['is_safebinding'] == 'on') {
                    break;
                }
            }
        }
        ?>
                jQuery('#main-ajax-loading').css('visibility', 'hidden');
                if (dzsvg_settings.is_safebinding == "on") {
                    jQuery('.master-save-vpc').remove();
                    if (dzsvg_settings.addslider == "on") {
                        //console.log(dzsvg_settings.addslider)
                        sliders_addslider();
                        window.currSlider_nr = -1
                        sliders_showslider(0);
                    }
                }
                check_global_items();
                sliders_allready();
            });
        </script>
        <?php
    }

    function post_options() {
        //// POST OPTIONS ///

        if (isset($_POST['dzsvg_exportdb'])) {


            //===setting up the db
            $currDb = '';
            if (isset($_POST['currdb']) && $_POST['currdb'] != '') {
                $this->currDb = $_POST['currdb'];
                $currDb = $this->currDb;
            }

            //echo 'ceva'; print_r($this->dbs);
            if ($currDb != 'main' && $currDb != '') {
                $this->dbitemsname.='-'.$currDb;
                $this->mainitems = get_option($this->dbitemsname);
            }
            //===setting up the db END

            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="'."dzsvg_backup.txt".'"');
            echo serialize($this->mainitems);
            die();
        }
        if (isset($_POST['dzsvg_dismiss_limit_notice']) && $_POST['dzsvg_dismiss_limit_notice']=='dismiss') {
            $this->mainoptions['settings_limit_notice_dismissed'] = 'on';
            
//            print_r($this->mainoptions);
            
            update_option($this->dboptionsname, $this->mainoptions);
        }

        if (isset($_POST['dzsvg_exportslider'])) {


            //===setting up the db
            $currDb = '';
            if (isset($_POST['currdb']) && $_POST['currdb'] != '') {
                $this->currDb = $_POST['currdb'];
                $currDb = $this->currDb;
            }

            //echo 'ceva'; print_r($this->dbs);
            if ($currDb != 'main' && $currDb != '') {
                $this->dbitemsname.='-'.$currDb;
                $this->mainitems = get_option($this->dbitemsname);
            }
            //===setting up the db END
            //print_r($currDb);

            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="'."dzsvg-slider-".$_POST['slidername'].".txt".'"');
            //print_r($_POST);
            echo serialize($this->mainitems[$_POST['slidernr']]);
            die();
        }


        if (isset($_POST['dzsvg_importdb'])) {
            $file_data = file_get_contents($_FILES['dzsvg_importdbupload']['tmp_name']);
            $this->mainitems = unserialize($file_data);
            update_option($this->dbitemsname,$this->mainitems);
        }

        if (isset($_POST['dzsvg_importslider'])) {
            //print_r( $_FILES);
            $file_data = file_get_contents($_FILES['importsliderupload']['tmp_name']);
            $auxslider = unserialize($file_data);
            //dzs_replace_in_matrix('http://localhost/wpmu/eos/wp-content/themes/eos/', THEME_URL, $this->mainitems);
            //dzs_replace_in_matrix('http://eos.digitalzoomstudio.net/wp-content/themes/eos/', THEME_URL, $this->mainitems);
            //echo 'ceva';
            //print_r($auxslider);
            $this->mainitems = get_option($this->dbitemsname);
            //print_r($this->mainitems);
            $this->mainitems[] = $auxslider;

            update_option($this->dbitemsname,$this->mainitems);
        }

        if (isset($_POST['dzsvg_saveoptions'])) {
            if ($_POST['use_external_uploaddir'] == 'on') {
                copy(dirname(__FILE__).'/admin/upload.php',dirname(dirname(dirname(__FILE__))).'/upload.php');
                $mypath = dirname(dirname(dirname(__FILE__))).'/upload';
                if (is_dir($mypath) === false && file_exists($mypath) === false) {
                    mkdir($mypath,0755);
                }
            }


            //$this->mainoptions['embed_masonry'] = $_POST['embed_masonry'];
            update_option($this->dboptionsname,$this->mainoptions);
        }
    }

    function post_save_mo() {

        $auxarray_defs = array(
            'disable_api_caching' => 'off',
            'disable_fontawesome' => 'off',
            'tinymce_enable_preview_shortcodes' => 'off',
            'force_file_get_contents' => 'off',
            'debug_mode' => 'off',
            'settings_trigger_resize' => 'off',
            'replace_wpvideo' => 'off',
            'usewordpressuploader' => 'on',
            'dzsvp_enable_visitorupload' => 'off',
        );
        $auxarray = array();
        //parsing post data
        parse_str($_POST['postdata'],$auxarray);

        $auxarray = array_merge($auxarray_defs,$auxarray);

        if (isset($auxarray['use_external_uploaddir']) && $auxarray['use_external_uploaddir'] == 'on') {

            $path_uploadfile = dirname(dirname(dirname(__FILE__))).'/upload.php';
            if (file_exists($path_uploadfile) === false) {
                copy(dirname(__FILE__).'/admin/upload.php',$path_uploadfile);
            }
            $path_uploaddir = dirname(dirname(dirname(__FILE__))).'/upload';
            if (is_dir($path_uploaddir) === false) {
                mkdir($path_uploaddir,0777);
            }
        }

        print_r($this->mainoptions);
        $auxarray = array_merge($this->mainoptions, $auxarray);

        update_option($this->dboptionsname,$auxarray);
        die();
    }

    function post_save_options_dc() {
        $auxarray = array();
        //parsing post data
        parse_str($_POST['postdata'],$auxarray);
        print_r($auxarray);


        update_option($this->dbdcname,$auxarray);
        die();
    }

    function post_save() {
        //---this is the main save function which saves gallery
        $auxarray = array();
        $mainarray = array();

        //print_r($this->mainitems);
        //parsing post data
        parse_str($_POST['postdata'],$auxarray);


        if (isset($_POST['currdb'])) {
            $this->currDb = $_POST['currdb'];
        }
        //echo 'ceva'; print_r($this->dbs);
        if ($this->currDb != 'main' && $this->currDb != '') {
            $this->dbitemsname.='-'.$this->currDb;
        }
        
        //echo $this->dbitemsname;
        if (isset($_POST['sliderid'])) {
            //print_r($auxarray);
            $mainarray = get_option($this->dbitemsname);
            foreach ($auxarray as $label => $value) {
                $aux = explode('-',$label);
                $tempmainarray[$aux[1]][$aux[2]] = $auxarray[$label];
            }
            $mainarray[$_POST['sliderid']] = $tempmainarray;
        } else {
            foreach ($auxarray as $label => $value) {
                //echo $auxarray[$label];
                $aux = explode('-',$label);
                $mainarray[$aux[0]][$aux[1]][$aux[2]] = $auxarray[$label];
            }
        }
//        echo $this->dbitemsname; print_r($_POST); print_r($this->currDb); echo isset($_POST['currdb']);
        update_option($this->dbitemsname,$mainarray);
        echo 'success';
        die();
    }

    function post_get_db_gals() {

        if (isset($_POST['postdata'])) {
            $this->currDb = $_POST['postdata'];
        }



        if ($this->currDb != 'main' && $this->currDb != '') {
            $this->dbitemsname.='-'.$this->currDb;
        }


        $mainarray = get_option($this->dbitemsname);

        $i = 0;
        foreach ($mainarray as $gal) {
            if ($i > 0) {
                echo ';';
            }

            echo $gal['settings']['id'];

            $i++;
        }


        //echo 'success';
        die();
    }

    function post_save_vpc() {
        //---this is the main save function which saves item
        $auxarray = array();
        $mainarray = array();

        //print_r($this->mainitems);
        //parsing post data
        parse_str($_POST['postdata'],$auxarray);


        if (isset($_POST['currdb'])) {
            $this->currDb = $_POST['currdb'];
        }
        //echo 'ceva'; print_r($this->dbs);
        if ($this->currDb != 'main' && $this->currDb != '') {
            $this->dbvpconfigsname.='-'.$this->currDb;
        }
        //echo $this->dbitemsname;
        if (isset($_POST['sliderid'])) {
            //print_r($auxarray);
            $mainarray = get_option($this->dbvpconfigsname);
            foreach ($auxarray as $label => $value) {
                $aux = explode('-',$label);
                $tempmainarray[$aux[1]][$aux[2]] = $auxarray[$label];
            }
            $mainarray[$_POST['sliderid']] = $tempmainarray;
        } else {
            foreach ($auxarray as $label => $value) {
                //echo $auxarray[$label];
                $aux = explode('-',$label);
                $mainarray[$aux[0]][$aux[1]][$aux[2]] = $auxarray[$label];
            }
        }
        //echo $this->dbitemsname; print_r($_POST); print_r($this->currDb); echo isset($_POST['currdb']);
        update_option($this->dbvpconfigsname,$mainarray);
        echo 'success';
        die();
    }

    function post_importytplaylist() {
        //echo 'ceva';
        $pd = $_POST['postdata'];
        //echo $aux;
        $yf_maxi = 100;
        $i = 0;
        $its = array();

        $str_apikey = '';

        if ($this->mainoptions['youtube_api_key'] != '') {
            $str_apikey = '&key='.$this->mainoptions['youtube_api_key'];
        }

        $target_file = $this->httpprotocol."://gdata.youtube.com/feeds/api/playlists/".$pd."?alt=json&start-index=1&max-results=40".$str_apikey;
        $ida = DZSHelpers::get_contents($target_file,array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));
        $idar = json_decode($ida);
        //print_r($idar);
        if ($idar == false) {
            echo 'error: '.'check the id';
        } else {
            foreach ($idar->feed->entry as $ytitem) {
                $cache = $ytitem;
                $aux = array();
                $auxtitle = '';
                $auxcontent = '';
                //print_r($cache);
                //print_r(get_object_vars($cache->title));
                foreach ($cache->title as $hmm) {
                    $auxtitle = $hmm;
                    break;
                }
                foreach ($cache->content as $hmm) {
                    $auxcontent = $hmm;
                    break;
                }
                //print_r($aux2);
                //print_r(parse_str($cache->title));
                parse_str($ytitem->link[0]->href,$aux);
                //print_r($aux);

                $its[$i]['source'] = $aux[$this->httpprotocol.'://www_youtube_com/watch?v'];
                $its[$i]['thethumb'] = "";
                $its[$i]['type'] = "youtube";
                $its[$i]['title'] = $auxtitle;
                $its[$i]['menuDescription'] = $auxcontent;
                $its[$i]['description'] = $auxcontent;

                //print_r($ytitem);
                $aux2 = get_object_vars($ytitem->title);
                $aux = ($aux2['$t']);
                $lb = array("\r\n","\n","\r","&","-","`",'???',"'",'-');
                $aux = str_replace($lb,' ',$aux);

                /*
                  $aux = $ytitem->description;
                  $lb   = array("\r\n", "\n", "\r", "&" ,"-", "`", '???', "'", '-');
                  $aux = str_replace($lb, ' ', $aux);
                  $its['settings']['description'] = $aux;
                 */
                $i++;
                if ($i > $yf_maxi)
                    break;
            }
        }

        if (count($its) == 0) {
            echo 'error: '.'<a href="'.$target_file.'">this</a> is what the feed returned '.$ida;
            die();
        }
        for ($i = 0; $i < count($its); $i++) {
            
        }
        $sits = json_encode($its);
        echo $sits;



        die();
    }

    function post_importytuser() {
        //echo 'ceva';
        $pd = $_POST['postdata'];
        $yf_maxi = 100;
        $i = 0;
        $its = array();
        //echo $aux;
        //echo 'ceva';


        $sw = false;
        //print_r($idar);
        //print_r($idar);
        //print_r(count($idar->data->items));
        $i = 0;
        $yf_maxi = 100;

        //echo $ida;



        $target_file = $this->httpprotocol."://gdata.youtube.com/feeds/api/users/".$pd."/uploads?v=2&alt=jsonc";
        $ida = DZSHelpers::get_contents($target_file,array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));
        $idar = json_decode($ida);

        if ($ida == 'yt:quotatoo_many_recent_calls') {
            echo 'error: too many recent calls - YouTube rejected the call';
            $sw = true;
        }
        //print_r($idar);

        if ($idar == false) {
            echo 'error: '.'check the id ';
            print_r($ida);
            die();
        } else {

            foreach ($idar->data->items as $ytitem) {
                //print_r($ytitem);
                $its[$i]['source'] = $ytitem->id;
                $its[$i]['thethumb'] = "";
                $its[$i]['type'] = "youtube";

                $aux = $ytitem->title;
                $lb = array('"',"\r\n","\n","\r","&","-","`",'???',"'",'-');
                $aux = str_replace($lb,' ',$aux);
                $its[$i]['title'] = $aux;

                $aux = $ytitem->description;
                $lb = array("\r\n","\n","\r","&",'???');
                $aux = str_replace($lb,' ',$aux);
                $lb = array('"');
                $aux = str_replace($lb,'&quot;',$aux);
                $lb = array("'");
                $aux = str_replace($lb,'&#39;',$aux);
                $its[$i]['description'] = $aux;

                $i++;
                if ($i > $yf_maxi + 1)
                    break;
            }
        }
        if (count($its) == 0) {
            echo 'error: '.'this is what the feed returned '.$ida;
            die();
        }
        $sits = json_encode($its);
        echo $sits;



        die();
    }

    function ajax_get_vimeothumb() {
        $id = $_POST['postdata'];

//        $ch = curl_init("http://vimeo.com/api/v2/video/$the_id.php");
//        $target_file = "https://vimeo.com/api/v2/video/$id.php";
//        vimeo.com/api/v2/video/6518700.php
//        http://vimeo.com/api/v2/video/9696328.xml
//        $target_file = "http://vimeo.com/api/v2/video/$id.php";
//
//        echo $target_file;
//        $cache = DZSHelpers::get_contents($target_file,array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));
//
////        print_r($cache);
//        $apiresp = $cache;
//        $imga = unserialize($apiresp);
//
//        print_r($imga);
//
//
//        echo $imga[0]['thumbnail_medium'];





//        $target_file = "https://api.vimeo.com/videos/2/pictures";


        if ( $this->mainoptions['vimeo_api_client_id'] != '' && $this->mainoptions['vimeo_api_client_secret'] != '' && $this->mainoptions['vimeo_api_access_token'] != '') {


            if (!class_exists('VimeoAPIException')) {
                require_once(dirname(__FILE__) . '/vimeoapi/vimeo.php');
            }


            $vimeo_id = $this->mainoptions['vimeo_api_user_id']; // Get from https://vimeo.com/settings, must be in the form of user123456
            $consumer_key = $this->mainoptions['vimeo_api_client_id'];
            $consumer_secret = $this->mainoptions['vimeo_api_client_secret'];
            $token = $this->mainoptions['vimeo_api_access_token'];

            // Do an authentication call
            $vimeo = new Vimeo($consumer_key, $consumer_secret);
            $vimeo->setToken($token); //,$token_secret





            $vimeo_response = $vimeo->request('/videos/' . $id . '/pictures');

            if ($vimeo_response['status'] != 200) {
//                        throw new Exception($channel_videos['body']['message']);
                echo 'vimeo error';

                print_r($vimeo_response);
            }

            $ida = '';
            if (isset($vimeo_response['body']['data'])) {
                $ida = $vimeo_response['body']['data'];
            }


//            print_r($ida);

            if($ida && $ida[0]){

                $vimeo_quality_ind = 2;

                if($this->mainoptions['vimeo_thumb_quality']=='medium'){

                    $vimeo_quality_ind = 3;
                }

                if($this->mainoptions['vimeo_thumb_quality']=='high'){

                    $vimeo_quality_ind = 4;
                }

                echo $ida[0]['sizes'][$vimeo_quality_ind]['link'];

            }

        }




                die();
    }

    function post_importvimeouser() {
        //echo 'ceva';
        $pd = $_POST['postdata'];
        $yf_maxi = 100;
        $i = 0;
        $its = array();
        //echo $aux;
        $target_file = "http://vimeo.com/api/v2/".$pd."/videos.json";
        $ida = DZSHelpers::get_contents($target_file,array('force_file_get_contents' => $this->mainoptions['force_file_get_contents']));
        $idar = json_decode($ida);
        $i = 0;
        if ($idar == false) {
            echo 'error: '.'check the id ';
            print_r($ida);
            die();
        } else {
            foreach ($idar as $item) {
                $its[$i]['source'] = $item->id;
                $its[$i]['thethumb'] = $item->thumbnail_small;


                $its[$i]['type'] = "vimeo";

                $aux = $item->title;
                $lb = array('"',"\r\n","\n","\r","&","-","`",'???',"'",'-');
                $aux = str_replace($lb,' ',$aux);
                $its[$i]['title'] = $aux;

                $aux = $item->description;
                $lb = array("\r\n","\n","\r","&",'???');
                $aux = str_replace($lb,' ',$aux);
                $lb = array('"');
                $aux = str_replace($lb,'&quot;',$aux);
                $lb = array("'");
                $aux = str_replace($lb,'&#39;',$aux);
                $its[$i]['description'] = $aux;
                $i++;
            }
        }
        if (count($its) == 0) {
            echo 'error: '.'this is what the feed returned '.$ida;
            die();
        }

        $sits = json_encode($its);
        echo $sits;


        die();
    }

    function filter_attachment_fields_to_edit($form_fields,$post) {


        $vpconfigsstr = '';
        $the_id = $post->ID;
        $post_type = get_post_mime_type($the_id);
        //print_r($this->mainvpconfigs);

        if (strpos($post_type,"video") === false) {
            return $form_fields;
        }


        foreach ($this->mainvpconfigs as $vpconfig) {
            //print_r($vpconfig);
            $vpconfigsstr .='<option value="'.$vpconfig['settings']['id'].'">'.$vpconfig['settings']['id'].'</option>';
        }

        $html_sel = '<select class="styleme" id="attachments-'.$post->ID.'-video-player-config" name="attachments['.$post->ID.'][video-player-config]">';
        $html_sel.=$vpconfigsstr;
        $html_sel .='</select>';
        $form_fields['video-player-config'] = array(
            'label' => 'Video Player Config',
            'input' => 'html',
            'html' => $html_sel,
            'helps' => 'choose a configuration for the player / edit in Video Gallery > Player Configs',
        );

        $form_fields['video-player-height'] = array(
            'label' => 'Force Height',
            'input' => 'html',
            'html' => '<input type="text" id="attachments-'.$post->ID.'-video-player-height" name="attachments['.$post->ID.'][video-player-height]"/>',
            'helps' => 'force a height',
        );





        return $form_fields;
    }

    function show_generator_export_slider() {
        ?>Please note that this feature uses the last saved data. Unsaved changes will not be exported.
        <form action="<?php echo site_url().'/wp-admin/options-general.php?page=dzsvg_menu'; ?>" method="POST">
            <input type="hidden" class="hidden" name="slidernr" value="<?php echo $_GET['slidernr']; ?>"/> 
            <input type="hidden" class="hidden" name="slidername" value="<?php echo $_GET['slidername']; ?>"/> 
            <input type="hidden" class="hidden" name="currdb" value="<?php echo $_GET['currdb']; ?>"/> 
            <input class="button-secondary" type="submit" name="dzsvg_exportslider" value="Export"/>
        </form>
        <?php
    }

}

//add_filter( 'script_loader_attrs', 'my_function' );
//
//function my_function( $attrs ) {
//    $attrs = array('async' => 'async', 'charset' => 'utf8'); // whatever attributes you want
//
//   // alternatively, eliminate type='text/javascript' by emptying $attrs:
//   // $attrs = '';
//
//   return $attrs;
//}