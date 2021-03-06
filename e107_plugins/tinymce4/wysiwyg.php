<?php
/*
+ ----------------------------------------------------------------------------+
|     e107 website system - Tiny MCE controller file.
|
|     $URL$
|     $Id$
+----------------------------------------------------------------------------+
*/
$_E107['no_online'] = true;
require_once("../../class2.php");

/*
echo '


tinymce.init({
	"selector": ".e-wysiwyg",
	"theme": "modern",
	"plugins": "advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen        insertdatetime media nonbreaking save table contextmenu directionality emoticons template paste textcolor",
	"language": "en",
	"menubar": "edit view format insert table tools",
	"toolbar1": "undo redo | styleselect | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | e107-image e107-video e107-glyph | preview",
	"external_plugins": {"e107":"/e107_plugins/tinymce4/plugins/e107/plugin.js","example":"/e107_plugins/tinymce4/plugins/example/plugin.js"},
	"image_advtab": true,
	"extended_valid_elements": "i[*], object[*],embed[*],bbcode[*]",
	"convert_fonts_to_spans": false,
	"content_css": "http://netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css",
	"relative_urls": false,
	"preformatted": true,
//	"document_base_url": "http://eternal.technology/"
});
';
exit;
*/



/*
echo 'tinymce.init({
	 selector: ".e-wysiwyg",
    theme: "modern",
    plugins: "template",
    toolbar: "template",
 //   template_cdate_classes: "cdate creationdate",
 //   template_mdate_classes: "mdate modifieddate",
 //   template_selected_content_classes: "selcontent",
 //   template_cdate_format: "%m/%d/%Y : %H:%M:%S",
 //   template_mdate_format: "%m/%d/%Y : %H:%M:%S",
 //   template_replace_values: {
//        username : "Jack Black",
//        staffid : "991234"
 //   },
    templates : [
        {
            title: "Editor Details",
            url: "editor_details.htm",
            description: "Adds Editor Name and Staff ID"
        },
        {
            title: "Timestamp",
            content: "Some Content goes here. ",
            description: "Adds an editing timestamp."
        }
    ]
});';
*/

// exit;



/*
$text = <<<TMPL


tinymce.init({
    selector: ".e-wysiwyg",
    theme: "modern",
    plugins: [
        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
        "searchreplace wordcount visualblocks visualchars code fullscreen",
        "insertdatetime media nonbreaking save table contextmenu directionality",
        "emoticons template paste textcolor "
    ],
    external_plugins: {
        "example": "{e_PLUGIN_ABS}tinymce4/plugins/example/plugin.min.js",
        "e107" : "{e_PLUGIN_ABS}tinymce4/plugins/e107/plugin.js"
    },
    menubar: "edit view format insert table tools",
    
    toolbar1: "undo redo | styleselect | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | e107-image e107-video e107-glyph | preview",

    image_advtab: true,
    extended_valid_elements: 'span[*],i[*],iframe[*]',
    trim_span_elements: false,
    content_css: 'http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css',
    templates: [
        {title: 'Test template 1', content: 'Test 1'},
        {title: 'Test template 2', content: 'Test 2'}
    ]
});


TMPL;

$output = str_replace("{e_PLUGIN_ABS}", e_PLUGIN_ABS, $text);
*/


$wy = new wysiwyg();

$gen = $wy->renderConfig();


if(ADMIN && e_QUERY == 'debug')
{
	define('e_IFRAME', true); 
	require_once(HEADERF);

	echo "<table class='table'><tr><td>";

	print_a($output);

	echo "</td>
	<td>
	".print_a($gen,true)."
	</td>
	</tr></table>";
	
	require_once(FOOTERF);

}
else
{
	//ob_start();
	//ob_implicit_flush(0);
	//header("last-modified: " . gmdate("D, d M Y H:i:s",mktime(0,0,0,15,2,2004)) . " GMT");
	header('Content-type: text/javascript', TRUE);
	header('Content-Encoding: gzip');

	$minified = e107::minify($gen);
	$gzipoutput = gzencode($minified,6);

	header('Content-Length: '.strlen($gzipoutput));
	echo $gzipoutput;
}
		
	
exit;





// echo_gzipped_page();

class wysiwyg
{
	var $js;
	var $config = array();
	var $configName;

	function renderConfig($config='')
	{
		$this->getConfig($config);	
		$text = "\n /* TinyMce Config: ".$this->configName." */\n\n";
		$text .= "tinymce.init({\n";
		$text .= $this->config; // Moc: temporary fix for BC with PHP 5.3: https://github.com/e107inc/e107/issues/614
		$text .= "\n});";



		return stripslashes($text);
	}
	


	function __construct($config=FALSE)
	{

	}

	function tinymce_lang()
	{
		$lang = e_LANGUAGE;
		$tinylang = array(
			"Arabic" 	=> "ar",
			"Bulgarian"	=> "bg",
			"Danish" 	=> "da",
			"Dutch" 	=> "nl",
			"English" 	=> "en",
			"Persian" 	=> "fa",
			"French" 	=> "fr",
			"German"	=> "de",
			"Greek" 	=> "el",
			"Hebrew" 	=> " ",
			"Hungarian" => "hu",
			"Italian" 	=> "it",
			"Japanese" 	=> "ja",
			"Korean" 	=> "ko",
			"Norwegian" => "nb",
			"Polish" 	=> "pl",
			"Russian" 	=> "ru",
			"Slovak" 	=> "sk",
			"Spanish" 	=> "es",
			"Swedish" 	=> "sv"
		);

		if(!$tinylang[$lang])
		{
		 	$tinylang[$lang] = "en";
		}

		return $tinylang[$lang];
	}



	function getExternalPlugins($data)
	{
		if(empty($data))
		{
			return;
		}

		$tmp = explode(" ",$data);

		if(e107::pref('core','smiley_activate',false))
		{
			$tmp[] = "smileys";
		}

		$ext = array();

		foreach($tmp as $val)
		{
			$ext[$val] = e_PLUGIN_ABS."tinymce4/plugins/".$val."/plugin.js";
		}


			
			
		return json_encode($ext);
	}
		
		
				
	function convertBoolean($string)
	{

		if(substr($string,0,1) == '{' || substr($string,0,1) == '[' || substr($string,0,9) == 'function(')
		{
			return $string;
		}

		if(is_numeric($string))
		{
			return $string;
		}
	
		if(is_string($string))
		{
			$string = trim($string); 
			$string = str_replace("\n","",$string);
		}
		
		if($string === true)
		{
			return 'true';	
		}
		
		if($string === false)
		{
			return 'false';	
		}
		
		if($string === 'true' || $string === 'false' || $string[0] == '[')
		{
			return $string; 
		}
						
		return '"'.$string.'"';
	}			
		


	function getConfig($config=false)
	{
		$tp = e107::getParser();	
		$fl = e107::getFile();

				
		if(getperms('0'))
		{
			$template = "mainadmin.xml";		
		}
		elseif(ADMIN)
		{
			$template = "admin.xml";			
		}
		elseif(USER)
		{
			$template = "member.xml";			
		}
		else
		{
			$template = "public.xml";			
		}
		
		$configPath = (is_readable(THEME."templates/tinymce/".$template)) ? THEME."templates/tinymce/".$template : e_PLUGIN."tinymce4/templates/".$template;
		$config 	= e107::getXml()->loadXMLfile($configPath, true); 

		//TODO Cache!

		$this->configName = $config['@attributes']['name'];

		unset($config['@attributes']);

		$ret = array(
			'selector' 			=> '.e-wysiwyg',
			'theme'				=> 'modern',
			'plugins'			=> $this->filter_plugins($config['tinymce_plugins']),
			'language'			=> $this->tinymce_lang()
			
		);

		
		// Loop thru XML parms. 
		foreach($config as $k=>$xml)
		{
			$ret[$k] = $xml; 			
		}

		$tPref = e107::pref('tinymce4');

		if(!empty($tPref['paste_as_text']))
		{
			$ret['paste_as_text']	= true;
		}


		if(!empty($tPref['browser_spellcheck']))
		{
			$ret['browser_spellcheck']	= true;
		}

		$formats = array(
			'hilitecolor' => array('inline'=> 'span', 'classes'=> 'hilitecolor', 'styles'=> array('backgroundColor'=> '%value'))
			//	block : 'h1', attributes : {title : "Header"}, styles : {color : red}
		);

		//@see http://www.tinymce.com/wiki.php/Configuration:formats

		$formats = "[
                {title: 'Headers', items: [
                    {title: 'Heading 1', block: 'h1'},
                    {title: 'Heading 2', block: 'h2'},
                    {title: 'Heading 3', block: 'h3'},
                    {title: 'Heading 4', block: 'h4'},
                    {title: 'Heading 5', block: 'h5'},
                    {title: 'Heading 6', block: 'h6'}
                ]},

                {title: 'Inline', items: [
                    {title: 'Bold', inline: 'b', icon: 'bold'},
                    {title: 'Italic', inline: 'em', icon: 'italic'},
                    {title: 'Underline', inline: 'span', styles : {textDecoration : 'underline'}, icon: 'underline'},
                    {title: 'Strikethrough', inline: 'span', styles : {textDecoration : 'line-through'}, icon: 'strikethrough'},
                    {title: 'Superscript', inline: 'sup', icon: 'superscript'},
                    {title: 'Subscript', inline: 'sub', icon: 'subscript'},
                    {title: 'Code', inline: 'code', icon: 'code'},
                    {title: 'Small', inline: 'small', icon: ''},
                ]},

                {title: 'Blocks', items: [
                    {title: 'Paragraph', block: 'p'},
                    {title: 'Blockquote', block: 'blockquote'},
                    {title: 'Div', block: 'div'},
                    {title: 'Pre', block: 'pre'},
                    {title: 'Code Highlighted', block: 'pre', classes: 'prettyprint linenums' }
                ]},

                {title: 'Alignment', items: [
                    {title: 'Left', block: 'div', classes: 'text-left',  icon: 'alignleft'},
                    {title: 'Center', block: 'div',classes: 'text-center', icon: 'aligncenter'},
                    {title: 'Right', block: 'div', classes: 'text-right',  icon: 'alignright'},
                    {title: 'Justify', block: 'div', classes: 'text-justify', icon: 'alignjustify'},
                    {title: 'No-Wrap', block: 'div', classes: 'text-nowrap', icon: ''},
                    {title: 'Image Left', selector: 'img', classes: 'pull-left', styles: {'margin': '0 10px 5px 0'  },  icon: 'alignleft'},
                    {title: 'Image Right', selector: 'img', classes: 'pull-right', styles: { 'margin': '0 0 5px 10px'}, icon: 'alignright'}

                ]},

                {title: 'Bootstrap Inline', items: [
				 {title: 'Label (Default)', inline: 'span', classes: 'label label-default'},
				 {title: 'Label (Primary)', inline: 'span', classes: 'label label-primary'},
                 {title: 'Label (Success)', inline: 'span', classes: 'label label-success'},
                 {title: 'Label (Info)', inline: 'span', classes: 'label label-info'},
                 {title: 'Label (Warning)', inline: 'span', classes: 'label label-warning'},
                 {title: 'Label (Danger)', inline: 'span', classes: 'label label-danger'},
                 {title: 'Muted', inline: 'span', classes: 'text-muted'},
                ]},

                 {title: 'Bootstrap Blocks', items: [
                 {title: 'Alert (Success)', block: 'div', classes: 'alert alert-success'},
                 {title: 'Alert (Info)', block: 'div', classes: 'alert alert-info'},
                 {title: 'Alert (Warning)', block: 'div', classes: 'alert alert-warning'},
                 {title: 'Alert (Danger)', block: 'div', classes: 'alert alert-block alert-danger'},
                 {title: 'Float Clear', block: 'div', classes: 'clearfix'},
                 {title: 'Lead', block: 'p', classes: 'lead'},
                 {title: 'Well', block: 'div', classes: 'well'},
                 {title: '1/4 Width Block', block: 'div', classes: 'col-md-3 col-sm-12'},
                 {title: '3/4 Width Block', block: 'div', classes: 'col-md-9 col-sm-12'},
                 {title: '1/3 Width Block', block: 'div', classes: 'col-md-4 col-sm-12'},
                 {title: '2/3 Width Block', block: 'div', classes: 'col-md-8 col-sm-12'},
                 {title: '1/2 Width Block', block: 'div', classes: 'col-md-6 col-sm-12'},
                ]},

                 {title: 'Bootstrap Buttons', items: [
                 {title: 'Button (Default)', selector: 'a', classes: 'btn btn-default'},
				 {title: 'Button (Primary)', selector: 'a', classes: 'btn btn-primary'},
                 {title: 'Button (Success)', selector: 'a', classes: 'btn btn-success'},
                 {title: 'Button (Info)', selector: 'a', classes: 'btn btn-info'},
                 {title: 'Button (Warning)', selector: 'a', classes: 'btn-warning'},
                 {title: 'Button (Danger)', selector: 'a', classes: 'btn-danger'},
                ]},

				 {title: 'Bootstrap Images', items: [
				 {title: 'Responsive (recommended)',  selector: 'img', classes: 'img-responsive'},
				 {title: 'Rounded',  selector: 'img', classes: 'img-rounded'},
				 {title: 'Circle', selector: 'img', classes: 'img-circle'},
                 {title: 'Thumbnail', selector: 'img', classes: 'img-thumbnail'},
                ]},

				 {title: 'Bootstrap Tables', items: [
				 {title: 'Bordered',  selector: 'table', classes: 'table-bordered'},
				 {title: 'Condensed', selector: 'table', classes: 'table-condensed'},
				 {title: 'Hover', selector: 'table', classes: 'table-hover'},
                 {title: 'Striped', selector: 'table', classes: 'table-striped'},
                ]},


            ]";




	//	$ret['style_formats_merge'] = true;

	//	$ret['visualblocks_default_state'] = true; //pref
		$ret['style_formats']  = $formats; // json_encode($formats);
		$ret['link_class_list'] = "[
        {title: 'None', value: ''},
        {title: 'Link', value: 'btn btn-link'},
        {title: 'Alert Link', value: 'alert-link'},
        {title: 'Button (Default)', value: 'btn btn-default'},
        {title: 'Button (Primary)', value: 'btn btn-primary'},
        {title: 'Button (Success)', value: 'btn btn-success'},
        {title: 'Button (Info)', value: 'btn btn-info'},
        {title: 'Button (Warning)', value: 'btn btn-warning'},
        {title: 'Button (Danger)', value: 'btn btn-danger'}
    ]";


// https://github.com/valtlfelipe/TinyMCE-LocalAutoSave


	/*
		$ret['setup'] = "function(ed) {
      ed.addMenuItem('test', {
         text: 'Clear Floats',
         context: 'insert',
         icon: false,
         onclick: function() {
            ed.insertContent('<br class=\"clearfix\" />');
         }
      });
      }";




	*/

	// e107 Bbcodes.
	/*

		$ret['setup'] = "function(ed) {
			ed.addButton('e107-bbcode', {
				text: 'bbcode',
				icon: 'emoticons',
				onclick: function() {
		// Open window

			ed.windowManager.open({
						title: 'Example plugin',
						body: [
							{type: 'listbox', name: 'code', label: 'BbCode', values: [
								{text: 'Left', value: 'left'},
						        {text: 'Right', value: 'right'},
						        {text: 'Center', value: 'center'}
						    ]},
                            {type: 'textbox', name: 'parm', label: 'Parameters'}
						],
						onsubmit: function(e) {

							var selected = ed.selection.getContent({format : 'text'});

						//	alert(selected);
							// Insert content when the window form is submitted
							ed.insertContent('[' + e.data.code + ']' + selected + '[/' + e.data.code + ']');
						}
					});
				}
			});
	}";
*/


		// Emoticon Support @see //https://github.com/nhammadi/Smileys
		if(e107::pref('core','smiley_activate',false))
		{

			$emo = e107::getConfig("emote")->getPref();
			$pack = e107::pref('core','emotepack');

			$emotes = array();
			$i = 0;
			$c = 0;
			foreach($emo as $path=>$co)
			{
				$codes = explode(" ",$co);
				$url = SITEURLBASE.e_IMAGE_ABS."emotes/" . $pack . "/" . str_replace("!",".",$path);
				$emotes[$i][] = array('shortcut'=>$codes, 'url'=>$url, 'title'=>ucfirst($path));

				if($c == 6)
				{
					$i++;
					$c = 0;
				}
				else
				{
					$c++;
				}
			}

		//	print_r($emotes);

			$ret['extended_smileys'] = json_encode($emotes);
		}





		$ret['convert_fonts_to_spans']	= false;
		$ret['content_css']				= e_PLUGIN_ABS.'tinymce4/editor.css,https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css,http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css';
		
		$ret['relative_urls']			= false;  //Media Manager prefers it like this. 
		$ret['preformatted']			= true;
		$ret['document_base_url']		= SITEURL;


	//	$ret['table_default_attributes'] = json_encode(array('class'=>'table table-striped' ));

		
		if(!empty($ret['templates']))
		{
			$ret['templates']				 = $tp->replaceConstants($ret['templates'],'abs'); // $this->getTemplates(); 
		}
		//	$this->config['verify_css_classes']	= 'false';
		
		$text = array();
		foreach($ret as $k=>$v)
		{
			if($k == 'external_plugins')
			{
				$text[] = 'external_plugins: '. $this->getExternalPlugins($v); 
				continue;		
			}
			$text[] = $k.': '.$this->convertBoolean($v);	
		}


		
		$this->config = implode(",\n",$text); 
		
		
		return; 

		// -------------------------------------------------------------------------------------
		
		
	
		$cssFiles = $fl->get_files(THEME,"\.css",'',2);
		
		
		foreach($cssFiles as $val)
		{
			$css[] = str_replace(THEME,THEME_ABS,$val['path'].$val['fname']);	
		}
		$css[] = "{e_WEB_ABS}js/bootstrap/css/bootstrap.min.css";
		$content_css = vartrue($config['content_css'], implode(",",$css)); 
		
		$content_styles = array('Bootstrap Button' => 'btn btn-primary', 'Bootstrap Table' => 'table');



		$this->config += array(

	//		'theme_advanced_buttons1'			=> $config['tinymce_buttons1'],
	//		'theme_advanced_buttons2'			=> vartrue($config['tinymce_buttons2']),
	//		'theme_advanced_buttons3'			=> vartrue($config['tinymce_buttons3']),
	//		'theme_advanced_buttons4'			=> vartrue($config['tinymce_buttons4']),
	//		'theme_advanced_toolbar_location'	=> vartrue($config['theme_advanced_toolbar_location'],'top'),
	//		'theme_advanced_toolbar_align'		=> 'left',
	//		'theme_advanced_blockformats' 		=> 'p,h2,h3,h4,h5,h6,blockquote,pre,code',
	//		'theme_advanced_styles'				=> str_replace(array("+")," ",http_build_query($content_styles)),  //'Bootstrap Button=btn btn-primary;Bootstrap Table=table;border=border;fborder=fborder;tbox=tbox;caption=caption;fcaption=fcaption;forumheader=forumheader;forumheader3=forumheader3',
		
			// 'theme_advanced_resize_vertical' 		=> 'true',
			'dialog_type' 						=> "modal",		
		//	'theme_advanced_source_editor_height' => '400',
            
            // ------------- html5 Stuff. 
		
		    //  'visualblocks_default_state'   => 'true',

                // Schema is HTML5 instead of default HTML4
           //     'schema'     => "html5",
        
                // End container block element when pressing enter inside an empty block
           //     'end_container_on_empty_block' => true,
        
                // HTML5 formats
                /*
                'style_formats' => "[
                        {title : 'h1', block : 'h1'},
                        {title : 'h2', block : 'h2'},
                        {title : 'h3', block : 'h3'},
                        {title : 'h4', block : 'h4'},
                        {title : 'h5', block : 'h5'},
                        {title : 'h6', block : 'h6'},
                        {title : 'p', block : 'p'},
                        {title : 'div', block : 'div'},
                        {title : 'pre', block : 'pre'},
                        {title : 'section', block : 'section', wrapper: true, merge_siblings: false},
                        {title : 'article', block : 'article', wrapper: true, merge_siblings: false},
                        {title : 'blockquote', block : 'blockquote', wrapper: true},
                        {title : 'hgroup', block : 'hgroup', wrapper: true},
                        {title : 'aside', block : 'aside', wrapper: true},
                        {title : 'figure', block : 'figure', wrapper: true}
                ]",
        		*/
	       // --------------------------------
		
			
	//		'theme_advanced_statusbar_location'	=> 'bottom',
			'theme_advanced_resizing'			=> 'true',
			'remove_linebreaks'					=> 'false',
			'extended_valid_elements'			=> vartrue($config['extended_valid_elements']), 
	//		'pagebreak_separator'				=> "[newpage]", 
			'apply_source_formatting'			=> 'true',
			'invalid_elements'					=> 'font,align,script,applet',
			'auto_cleanup_word'					=> 'true',
			'cleanup'							=> 'true',
			'convert_fonts_to_spans'			=> 'true',
	//		'content_css'						=> $tp->replaceConstants($content_css),
			'popup_css'							=> 'false', 
			
			'trim_span_elements'				=> 'true',
			'inline_styles'						=> 'true',
			'auto_resize'						=> 'false',
			'debug'								=> 'true',
			'force_br_newlines'					=> 'true',
			'media_strict'						=> 'false',
			'width'								=> vartrue($config['width'],'100%'),
		//	'height'							=> '90%', // higher causes padding at the top?
			'forced_root_block'					=> 'false', //remain as false or it will mess up some theme layouts. 
		
			'convert_newlines_to_brs'			=> 'true', // will break [list] if set to true
		//	'force_p_newlines'					=> 'false',
			'entity_encoding'					=> 'raw',
			'convert_fonts_to_styles'			=> 'true',
			'remove_script_host'				=> 'true',
			'relative_urls'						=> 'false', //Media Manager prefers it like this. 
			'preformatted'						=> 'true',
			'document_base_url'					=> SITEURL,
			'verify_css_classes'				=> 'false'

		);

	//	if(!in_array('e107bbcode',$plug_array))
		{
	//		$this->config['cleanup_callback'] = 'tinymce_e107Paths';										
		}

		$paste_plugin = false; // (strpos($config['tinymce_plugins'],'paste')!==FALSE) ? TRUE : FALSE;

		if($paste_plugin)
		{
			$this->config += array(

				'paste_text_sticky'						=> 'true',
				'paste_text_sticky_default'				=> 'true',
				'paste_text_linebreaktype'				=> 'br',
		
				'remove_linebreaks'						=> 'false', // remove line break stripping by tinyMCE so that we can read the HTML
 				'paste_create_paragraphs'				=> 'false',	// for paste plugin - double linefeeds are converted to paragraph elements
 				'paste_create_linebreaks'				=> 'true',	// for paste plugin - single linefeeds are converted to hard line break elements
 				'paste_use_dialog'						=> 'true',	// for paste plugin - Mozilla and MSIE will present a paste dialog if true
 				'paste_auto_cleanup_on_paste'			=> 'true',	// for paste plugin - word paste will be executed when the user copy/paste content
 				'paste_convert_middot_lists'			=> 'false',	// for paste plugin - middot lists are converted into UL lists
 				'paste_unindented_list_class'			=> 'unindentedList', // for paste plugin - specify what class to assign to the UL list of middot cl's
 				'paste_convert_headers_to_strong'		=> 'true',	// for paste plugin - converts H1-6 elements to strong elements on paste
 				'paste_insert_word_content_callback'	=> 'convertWord', // for paste plugin - This callback is executed when the user pastes word content
				'auto_cleanup_word'						=> 'true'	// auto clean pastes from Word
			);
		}

		if(ADMIN)
		{
	//		$this->config['external_link_list_url'] = e_PLUGIN_ABS."tiny_mce/filelist.php";
		}
	}


	function getTemplates()
	{
		$templatePath = (is_readable(THEME."templates/tinymce/".$template)) ? THEME."templates/tinymce/".$template : e_PLUGIN."tinymce4/templates/".$template;
		
		
		
		
	}


	function filter_plugins($plugs)
	{

		$smile_pref = e107::getConfig()->getPref('smiley_activate');

		$admin_only = array("ibrowser");

		$plug_array = explode(",",$plugs);

		foreach($plug_array as $val)
		{
			if(in_array($val,$admin_only) && !ADMIN)
			{
		    	continue;
			}

			if(!$smile_pref && ($val=="emoticons"))
			{
		    	continue;
			}

			$tinymce_plugins[] = $val;
		}

		return $tinymce_plugins;
	}



}

?>