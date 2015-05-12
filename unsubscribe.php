<?php

if (!defined('e107_INIT'))
{
	require_once("class2.php");
}

define('e_IFRAME', true);
require_once(HEADERF);

class unsubscribe
{
	
	function __construct()
	{
		$mes = e107::getMessage();
		$frm = e107::getForm();
		
	//	$this->simulation();
		
		$mailoutPlugins = e107::getConfig()->get('e_mailout_list');
		
		if(!vartrue($_GET['id']))
		{
			return;	
		}
		
		$tmp = base64_decode($_GET['id']);
		
		parse_str($tmp,$data);
		
		e107::getMessage()->addDebug(print_a($data,true));
		
		$plugin = vartrue($data['plugin'],false);

		
		if(empty($data) || !e107::isInstalled($plugin) || !in_array($plugin, $mailoutPlugins))
		{
			$this->invalidURL();
			return;
		}	

		$ml = e107::getAddon($plugin,'e_mailout');
		
		if(vartrue($_POST['remove']) && !empty($data))
		{
			if($ml->unsubscribe('process',$data)!=false)
			{
				$text = "<p><b>".$data['email']."</b> has been removed from ".$ml->mailerName.".</p>";
				$mes->addSuccess($text);
			}
			else
			{
				$text = "<p>There was a problem when attempting to remove <b>".$data['email']."</b> from ".$ml->mailerName.".</p>";
				$mes->addError($text);	
			}
			
			echo "<div class='container'>".$mes->render()."</div>";
			return;			
		}
		
		if($ml->unsubscribe('check',$data) != false)
		{
			$text = "<p>We are very sorry for the inconvenience. <br />Please click the button below to remove <b>".$data['email']."</b> from the ".$ml->mailerName.".</p>";
			$text .= $frm->open('unsub','post',e_REQUEST_URI);
			$text .= $frm->button('remove','Remove ','submit');
			$text .= $frm->close();
			
			$mes->setTitle('Unsubscribe',E_MESSAGE_INFO)->addInfo($text);
			 
			echo "<div class='container'>".$mes->render()."</div>";
			return;
			
		}
		else
		{
			$this->invalidURL();
			return;	
		}
	}	
	
	
	
	
	function simulation()
	{
		$row = array();
		$row['datestamp'] = time();
		$row['email'] = "test@test.com";
		$row['id']		= 23;
		
		$unsubscribe = array('date'=>$row['datestamp'],'email'=>$row['email'],'id'=>$row['id'],'plugin'=>'user');
				
		$urlQuery = http_build_query($unsubscribe,null,'&');
		
		$_GET['id'] = base64_encode($urlQuery);	
		
		e107::getMessage()->addDebug("urlQuery = ".$urlQuery);
		//echo "urlQuery = ".$urlQuery."<br/>";
		
		e107::getMessage()->addDebug(e_SELF."?id=".$_GET['id']);
		
	}
	
	
	
	
	function invalidURL()
	{
		$mes = e107::getMessage();
		$mes->addWarning("Invalid URL");
		echo "<div class='container'>".$mes->render()."</div>";
		return;				
		
	}
	
	
}


new unsubscribe;





require_once(FOOTERF);
exit;


?>