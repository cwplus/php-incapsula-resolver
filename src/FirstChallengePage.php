<?php

namespace Anastaszor\Incapsula;

/**
 * FirstChallengePage class file.
 *
 * This class represents the first page of Incapsula's Challenge. This page is
 * likely to be like the following :
 *
 * <html>
 * <head>
 * <META NAME="robots" CONTENT="noindex,nofollow">
 * <script>
 * (function(){function getSessionCookies(){ ... }})();
 * </script>
 * <script>
 * (function() {
 * var z="";var b=" ... ";for (var i=0;i<b.length;i+=2){z=z+parseInt(b.substring(i, i+2), 16)+",";}z = z.substring(0,z.length-1); eval(eval('String.fromCharCode('+z+')'));})();
 * </script></head>
 * <body>
 * <iframe style="display:none;visibility:hidden;" src="//content.incapsula.com/jsTest.html" id="gaIframe"></iframe>
 * </body></html>
 *
 * We don't care about the real iframe that just serves to make a request to
 * google Analytics, but the two parts of javascript formed the challenge.
 *
 * The first part sets up two specific cookies for the next requests, and the
 * second part is obfuscated javascript which leads to a specific url to reach
 * in order to get other response cookies.
 *
 * @author Anastaszor
 */
class FirstChallengePage
{
	
	/**
	 *
	 * @var Browser
	 */
	private $_browser = null;
	
	/**
	 *
	 * @var CookieJar
	 */
	private $_cookie = null;
	
	/**
	 *
	 * @var \simple_html_dom
	 */
	private $_dom = null;
	
	/**
	 *
	 * @var string
	 */
	private $_swhanedl = null;
	
	/**
	 * Builds a new FirstChallengePage
	 *
	 * @param \simple_html_dom $dom
	 */
	public function __construct(Browser $browser, CookieJar $cj, \simple_html_dom $dom)
	{
		$this->_browser = $browser;
		$this->_cookie = $cj;
		$this->_dom = $dom;
	}
	
	/**
	 * Get the incapsula schwanedl token to target page.
	 *
	 * @return string
	 */
	public function getSchwanedl()
	{
		return $this->_swhanedl;
	}
	
	/*
	 * The first function can be displayed as follows :
	 *
	 *	(function(){
	 *		function getSessionCookies(){
	 *			var cookieArray=new Array();
	 *			var cName=/^\s?incap_ses_/;
	 *			var c=document.cookie.split(";");
	 *			for(var i=0;i<c.length;i++){
	 *				var key=c[i].substr(0,c[i].indexOf("="));
	 *				var value=c[i].substr(c[i].indexOf("=")+1,c[i].length);
	 *				if(cName.test(key)){
	 *					cookieArray[cookieArray.length]=value
	 *				}
	 *			}
	 *			return cookieArray
	 *		}
	 *
	 *		function setIncapCookie(vArray){
	 *			var res;
	 *			try{
	 *				var cookies=getSessionCookies();
	 *				var digests=new Array(cookies.length);
	 *				for(var i=0;i<cookies.length;i++){
	 *					digests[i]=simpleDigest((vArray)+cookies[i])
	 *				}
	 *				res=vArray+",digest="+(digests.join())
	 *			}catch(e){
	 *				res=vArray+",digest="+(encodeURIComponent(e.toString()))
	 *			}
	 *			createCookie("___utmvc",res,20)
	 *		}
	 *
	 *		function simpleDigest(mystr){
	 *			var res=0;
	 *			for(var i=0;i<mystr.length;i++){
	 *				res+=mystr.charCodeAt(i)
	 *			}
	 *			return res
	 *		}
	 *
	 *		function createCookie(name,value,seconds){
	 *			var expires="";
	 *			if(seconds){
	 *				var date=new Date();date.setTime(date.getTime()+(seconds*1000));
	 *				var expires=";
	 *				expires="+date.toGMTString()
	 *			}
	 *			document.cookie=name+"="+value+expires+"; path=/"
	 *		}
	 *
	 *		function test(o){
	 *			var res="";
	 *			var vArray=new Array();
	 *			for(var j=0;j<o.length;j++){
	 *				var test=o[j][0];
	 *				switch(o[j][1]){
	 *					case"exists":
	 *						try{
	 *							if(typeof(eval(test))!="undefined"){
	 *								vArray[vArray.length]=encodeURIComponent(test+"=true")
	 *							}else{
	 *								vArray[vArray.length]=encodeURIComponent(test+"=false")
	 *							}
	 *						}catch(e){
	 *							vArray[vArray.length]=encodeURIComponent(test+"=false")
	 *						}
	 *						break;
	 *					case"value":
	 *						try{
	 *							try{
	 *								res=eval(test);
	 *								if(typeof(res)==="undefined"){
	 *									vArray[vArray.length]=encodeURIComponent(test+"=undefined")
	 *								}else if(res===null){
	 *									vArray[vArray.length]=encodeURIComponent(test+"=null")
	 *								}else{
	 *									vArray[vArray.length]=encodeURIComponent(test+"="+res.toString())
	 *								}
	 *							}catch(e){
	 *								vArray[vArray.length]=encodeURIComponent(test+"=cannot evaluate");
	 *								break
	 *							}
	 *							break
	 *						}catch(e){
	 *							vArray[vArray.length]=encodeURIComponent(test+"="+e)
	 *						}
	 *					case"plugin_extentions":
	 *						try{
	 *							var extentions=[];
	 *							try{
	 *								i=extentions.indexOf("i")
	 *							}catch(e){
	 *								vArray[vArray.length]=encodeURIComponent("plugin_ext=indexOf is not a function");
	 *								break
	 *							}
	 *							try{
	 *								var num=navigator.plugins.length
	 *								if(num==0||num==null){
	 *									vArray[vArray.length]=encodeURIComponent("plugin_ext=no plugins");
	 *									break
	 *								}
	 *							}catch(e){
	 *								vArray[vArray.length]=encodeURIComponent("plugin_ext=cannot evaluate");
	 *								break
	 *							}
	 *							for(var i=0;i<navigator.plugins.length;i++){
	 *								if(typeof(navigator.plugins[i])=="undefined"){
	 *									vArray[vArray.length]=encodeURIComponent("plugin_ext=plugins[i] is undefined");
	 *									break
	 *								}
	 *								var filename=navigator.plugins[i].filename
	 *								var ext="no extention";
	 *								if(typeof(filename)=="undefined"){
	 *									ext="filename is undefined"
	 *								}else if(filename.split(".").length>1){
	 *									ext=filename.split('.').pop()
	 *								}
	 *								if(extentions.indexOf(ext)<0){
	 *									extentions.push(ext)
	 *								}
	 *							}
	 *							for(i=0;i<extentions.length;i++){
	 *								vArray[vArray.length]=encodeURIComponent("plugin_ext="+extentions[i])
	 *							}
	 *						}catch(e){
	 *							vArray[vArray.length]=encodeURIComponent("plugin_ext="+e)
	 *						}
	 *						break;
	 *					case"plugins":
	 *						try{
	 *							p=navigator.plugins pres=""
	 *							for(a in p){
	 *								pres+=(p[a]['description']+" ").substring(0,20)
	 *							}
	 *							vArray[vArray.length]=encodeURIComponent("plugins="+pres)
	 *						}catch(e){
	 *							vArray[vArray.length]=encodeURIComponent("plugins="+e)
	 *						}
	 *						break;
	 *					case"plugin":
	 *						try{
	 *							var a=navigator.plugins;
	 *							for(i in a){
	 *								var f=a[i]["filename"].split(".");
	 *								if(f.length==2){
	 *									vArray[vArray.length]=encodeURIComponent("plugin="+f[1]);
	 *									break
	 *								}
	 *							}
	 *						}catch(e){
	 *							vArray[vArray.length]=encodeURIComponent("plugin="+e)
	 *						}
	 *						break
	 *				}
	 *			}
	 *			vArray=vArray.join();return vArray
	 *		}
	 *
	 *		var o=[
	 *			["navigator","exists"],
	 *			["navigator.vendor","value"],
	 *			["navigator.appName","value"],
	 *			["navigator.plugins.length==0","value"],
	 *			["navigator.platform","value"],
	 *			["navigator.webdriver","value"],
	 *			["platform","plugin"],
	 *			["platform","plugin_extentions"],
	 *			["ActiveXObject","exists"],
	 *			["webkitURL","exists"],
	 *			["_phantom","exists"],
	 *			["callPhantom","exists"],
	 *			["chrome","exists"],
	 *			["yandex","exists"],
	 *			["opera","exists"],
	 *			["opr","exists"],
	 *			["safari","exists"],
	 *			["awesomium","exists"],
	 *			["puffinDevice","exists"],
	 *			["navigator.cpuClass","exists"],
	 *			["navigator.oscpu","exists"],
	 *			["navigator.connection","exists"],
	 *			["window.outerWidth==0","value"],
	 *			["window.outerHeight==0","value"],
	 *			["window.WebGLRenderingContext","exists"],
	 *			["document.documentMode","value"],
	 *			["eval.toString().length","value"]
	 *		];
	 *
	 *		try{
	 *			setIncapCookie(test(o));
	 *			document.createElement("img").src="/_Incapsula_Resource?SWKMTFSR=1&e="+Math.random()
	 *		}catch(e){
	 *			img=document.createElement("img");
	 *			img.src="/_Incapsula_Resource?SWKMTFSR=1&e="+e
	 *		}
	 *	})();
	 *
	 */
	public function runJs()
	{
		$o = array(
			'navigator' => 'exists',
			'navigator.vendor' => 'value',
			'navigator.appName' => 'value',
			'navigator.plugins.length' => 'isZero',
			'navigator.platform' => 'value',
			'navigator.webdriver' => 'value',
			'platform' => 'plugin',
			'platform' => 'plugin_extensions',
			'ActiveXObject' => 'exists',
			'webkitURL' => 'exists',
			'_phantom' => 'exists',
			'callPhantom' => 'exists',
			'chrome' => 'exists',
			'yandex' => 'exists',
			'opera' => 'exists',
			'opr' => 'exists',
			'safari' => 'exists',
			'awesomium' => 'exists',
			'puffinDevice' => 'exists',
			'navigator.cpuClass' => 'exists',
			'navigator.oscpu' => 'exists',
			'navigator.connection' => 'exists',
			'window.outerWidth' => 'isZero',
			'window.outerHeight' => 'isZero',
			'window.WebGLRenderingContext' => 'exists',
			'document.documentMode' => 'value',
			'eval.length' => 'value'
		);
		
		$testo = $this->test($o);
// 		var_dump($testo);die();
		$this->setIncapCookie($testo);
		
		$this->processObfuscatedJs();
	}
	
	/**
	 * Executes the js part (php-translation)
	 *
	 * @param array $data
	 * @return string
	 */
	public function test(array $o)
	{
		$res = '';
		$vArray = array();
		foreach($o as $test => $testval)
		{
			switch($testval)
			{
				case 'exists':
					try
					{
						$val = $this->__get($test);
						if($val === null)
							$vArray[] = rawurlencode($test.'=false');
						else
							$vArray[] = rawurlencode($test.'=true');
					}
					catch(\Exception $e)
					{
						$vArray[] = rawurlencode($test.'=false');
					}
					break;
				case 'value':
					try
					{
						try
						{
							$res = $this->__get($test);
							if($test === 'eval.length') $test = 'eval.toString().length';
							if($res === 'undefined')
								$vArray[] = rawurlencode($test."=undefined");
							elseif($res === null)
								$vArray[] = rawurlencode($test."=null");
							else
								$vArray[] = rawurlencode($test."=$res");
						}
						catch(\Exception $e)
						{
							$vArray[] = rawurlencode($test."=cannot evaluate");
						}
					}
					catch(\Exception $e)
					{
						$vArray[] = encodeURIComponent($test."=".$e);
					}
					break;
				case 'plugin_extensions':
					try
					{
						$extensions = array();
						try
						{
							$num = count($this->__get('navigator.plugins'));
							if($num === 0)
							{
								$vArray[] = rawurlencode('plugin_ext=no plugins');
							}
						}
						catch(\Exception $e)
						{
							$vArray[] = rawurlencode('plugin_ext=cannot evaluate');
							break;
						}
						for($i = 0; $i < $num; $i++)
						{
							$ithn = $this->__get('navigator.plugins.'.$i);
							if($ithn === null)
							{
								$vArray[] = rawurlencode('plugin_ext=plugins[i] is undefined');
								break;
							}
							$filename = @$ithn['filename'];
							$ext = 'no extension';
							if(!isset($filename))
								$ext = 'filename is undefined';
							elseif(count(explode('.', $filename)) > 1)
								$ext = explode('.', $filename)[0];
							if(!in_array($ext, $extensions))
								$extensions[] = $ext;
						}
						for($i = 0; $i < count($extensions); $i++)
						{
							$vArray[] = rawurlencode('plugin_ext='.$extensions[$i]);
						}
					}
					catch(\Exception $e)
					{
						$vArray[] = rawurlencode('plugin_ext='.$e);
					}
					break;
				case 'plugins':
					try
					{
						$p = $this->__get('navigator.plugins');
						$pres = '';
						foreach($p as $a)
						{
							$pres .= substr($p[$a]['description'].' ', 0, 20);
						}
						$vArray[] = rawurlencode('plugins='.$pres);
					}
					catch(\Exception $e)
					{
						$vArray[] = rawurlencode('plugins='.$e);
					}
					break;
				case 'plugin':
					try
					{
						$a = $this->__get('navigator.plugins');
						foreach($a as $i)
						{
							$f = explode('.', $i['filename']);
							if(count($f) == 2)
							{
								$vArray[] = rawurlencode('plugin='.$f[1]);
							}
						}
					}
					catch(\Exception $e)
					{
						$vArray[] = rawurlencode('plugin='.$e);
					}
					break;
				case 'isZero':
					try
					{
						$val = $this->__get($test);
						if(empty($val))
							$vArray[] = rawurlencode($test.'==0=true');
						else
							$vArray[] = rawurlencode($test.'==0=false');
					}
					catch(\Exception $e)
					{
						$vArray[] = rawurlencode($test.'==0=undefined');
					}
					break;
			}
		}
		$vstr = implode(',', $vArray);
		return $vstr;
	}
	
	/**
	 * Generates the ___utmvc cookie which is specific to incapsula's challenge
	 *
	 * @param string $vArray
	 */
	public function setIncapCookie($vArray)
	{
		$res = '';
		try
		{
			$digests = array();
			foreach($this->_cookie->getAllCookies() as $cookie)
			{
				$digests[] = $this->simpleDigest($vArray.$cookie);
			}
			$res = $vArray.',digest='.implode($digests);
		}
		catch(\Exception $e)
		{
			$res = $vArray.',digests='.rawurlencode($e);
		}
		$this->_cookie->set('___utmvc', $res);
	}
	
	public function __get($name)
	{
// 		var_dump('__get: '.$name);
		
		if($name === 'navigator') return $this->_browser;
		
		if(strpos($name, 'navigator') === 0)
		{
			$newname = substr($name, strlen('navigator.'));
// 			var_dump('__get_forward: '.$newname);
			$getted = $this->_browser->__get($newname);
// 			if(!is_array($getted))
// 				var_dump($getted);
// 			else
// 				var_dump('array:'.count($getted));
			return $getted;
		}
		if(($posp = strpos($name, '.')) !== false)
			$shname = substr($name, 0, $posp);
		else
			$shname = $name;
		$getter = 'get'.ucfirst($shname);
		if(method_exists($this, $getter))
		{
// 			var_dump('__get_redirect:'. $getter);
			$getted = $this->$getter();
			if(strlen($shname) < strlen($name))
			{
				$new2name = substr($name, $posp+1);
				if(isset($getted[$new2name]))
					$getted = $getted[substr($name, $posp+1)];
				elseif($new2name === 'length')
				{
					if(is_array($getted))
						$getted = count($getted);
					else
						$getted = strlen("$getted");
				}
				else
					$getted = null;
			}
// 			var_dump($getted);
			return $getted;
		}
		
// 		var_dump('__get_forward: '.$name);
		$getted = $this->_browser->__get($name);
// 		var_dump($getted);
		return $getted;
	}
	
	/**
	 * Gets the value of the eval.toString() function like a navigator does
	 * This one is like chromium.
	 *
	 * @return string
	 */
	public function getEval()
	{
		return "function eval() {\n    [native code]\n}";
	}
	
	/**
	 * Gets the properties of a windows for some screen. Here it is 1920x1080.
	 *
	 * @return string[]
	 */
	public function getWindow()
	{
		return array(
			'outerWidth' => '1920',
			'outerHeight' => '1080',
		);
	}
	
	/**
	 * Generates a simple digest by converting all characters of a string into
	 * their numeric ordinal number in the ascii table.
	 *
	 * @param string $data
	 * @return string ord encoded
	 */
	public function simpleDigest($data)
	{
		$res = '';
		for($i = 0; $i < strlen($data); $i++)
		{
			$res .= ord($data[$i]);
		}
		return $res;
	}
	
	/**
	 * Generates the right code that was digested by simple digest, by converting
	 * all sequences of 2-digit ascii chars into their utf8 equivalent character.
	 *
	 * @param string $data
	 * @return string chr decoded
	 */
	public function simpleUndigest($data)
	{
		$res = '';
		for($i = 0; $i < strlen($data); $i+=2)
		{
			$res .= chr(substr($data, $i, 2));
		}
		return $res;
	}
	
	/**
	 * Resolves the incapsula challenge for this request. This will allow the
	 * incapsula CDN to authorize our ip for some time.
	 *
	 * The incapsula challenge is a javascript function which looks like :
	 *
	 *
	 * (function() {
	 * var z="";var b="7472797B766172207868723B76617220743D6E6577204461746528292
	 * E67657454696D6528293B766172207374617475733D227374617274223B7661722074696D
	 * 696E673D6E65772041727261792833293B77696E646F772E6F6E756E6C6F61643D66756E6
	 * 374696F6E28297B74696D696E675B325D3D22723A222B286E6577204461746528292E6765
	 * 7454696D6528292D74293B646F63756D656E742E637265617465456C656D656E742822696
	 * D6722292E7372633D222F5F496E63617073756C615F5265736F757263653F4553324C5552
	 * 43543D363726743D373826643D222B656E636F6465555249436F6D706F6E656E742873746
	 * 17475732B222028222B74696D696E672E6A6F696E28292B222922297D3B69662877696E64
	 * 6F772E584D4C4874747052657175657374297B7868723D6E657720584D4C4874747052657
	 * 1756573747D656C73657B7868723D6E657720416374697665584F626A65637428224D6963
	 * 726F736F66742E584D4C4854545022297D7868722E6F6E726561647973746174656368616
	 * E67653D66756E6374696F6E28297B737769746368287868722E7265616479537461746529
	 * 7B6361736520303A7374617475733D6E6577204461746528292E67657454696D6528292D7
	 * 42B223A2072657175657374206E6F7420696E697469616C697A656420223B627265616B3B
	 * 6361736520313A7374617475733D6E6577204461746528292E67657454696D6528292D742
	 * B223A2073657276657220636F6E6E656374696F6E2065737461626C6973686564223B6272
	 * 65616B3B6361736520323A7374617475733D6E6577204461746528292E67657454696D652
	 * 8292D742B223A2072657175657374207265636569766564223B627265616B3B6361736520
	 * 333A7374617475733D6E6577204461746528292E67657454696D6528292D742B223A20707
	 * 26F63657373696E672072657175657374223B627265616B3B6361736520343A7374617475
	 * 733D22636F6D706C657465223B74696D696E675B315D3D22633A222B286E6577204461746
	 * 528292E67657454696D6528292D74293B6966287868722E7374617475733D3D323030297B
	 * 706172656E742E6C6F636174696F6E2E72656C6F616428297D627265616B7D7D3B74696D6
	 * 96E675B305D3D22733A222B286E6577204461746528292E67657454696D6528292D74293B
	 * 7868722E6F70656E2822474554222C222F5F496E63617073756C615F5265736F757263653
	 * F535748414E45444C3D383831343337353030363930353037393930382C34383238393834
	 * 3339383636323531303532362C31333334333637373036383638393136383936362C31333
	 * 0333536222C66616C7365293B7868722E73656E64286E756C6C297D63617463682863297B
	 * 7374617475732B3D6E6577204461746528292E67657454696D6528292D742B2220696E636
	 * 1705F6578633A20222B633B646F63756D656E742E637265617465456C656D656E74282269
	 * 6D6722292E7372633D222F5F496E63617073756C615F5265736F757263653F4553324C555
	 * 243543D363726743D373826643D222B656E636F6465555249436F6D706F6E656E74287374
	 * 617475732B222028222B74696D696E672E6A6F696E28292B222922297D3B";
	 * for (var i=0;i<b.length;i+=2){z=z+parseInt(b.substring(i, i+2), 16)+",";}
	 * z = z.substring(0,z.length-1); eval(eval('String.fromCharCode('+z+')'));})();
	 *
	 *
	 * This challenge can be broken down to a javascript into the final eval
	 * statement. This javascript is as follows:
	 *
	 * try{
	 * 	var xhr;
	 * 	var t=new Date().getTime();
	 * 	var status="start";
	 * 	var timing=new Array(3);
	 *
	 * 	window.onunload=function(){
	 * 		timing[2]="r:"+(new Date().getTime()-t);
	 * 		document.createElement("img").src="/_Incapsula_Resource?ES2LURCT=67&t=78&d="+encodeURIComponent(status+" ("+timing.join()+")")
	 * 	};
	 *
	 * 	if(window.XMLHttpRequest){
	 * 		xhr=new XMLHttpRequest
	 * 	}else{
	 * 		xhr=new ActiveXObject("Microsoft.XMLHTTP")
	 * 	}
	 *
	 * 	xhr.onreadystatechange=function(){
	 * 		switch(xhr.readyState){
	 * 			case 0:
	 * 				status=new Date().getTime()-t+": request not initialized ";
	 * 				break;
	 * 			case 1:
	 * 				status=new Date().getTime()-t+": server connection established";
	 * 				break;
	 * 			case 2:
	 * 				status=new Date().getTime()-t+": request received";
	 * 				break;
	 * 			case 3:
	 * 				status=new Date().getTime()-t+": processing request";
	 * 				break;
	 * 			case 4:
	 * 				status="complete";
	 * 				timing[1]="c:"+(new Date().getTime()-t);
	 * 				if(xhr.status==200){
	 * 					parent.location.reload()
	 * 				}
	 * 				break
	 * 		}
	 * 	};
	 *
	 * 	timing[0]="s:"+(new Date().getTime()-t);
	 * 	xhr.open("GET","/_Incapsula_Resource?SWHANEDL=3941557330473056554,6558706525315003056,13732182475966873540,129048",false);
	 * 	xhr.send(null)
	 * }catch(c){
	 * 	status+=new Date().getTime()-t+" incap_exc: "+c;document.createElement("img").src="/_Incapsula_Resource?ES2LURCT=67&t=78&d="+encodeURIComponent(status+" ("+timing.join()+")")
	 * };
	 *
	 * (In this js, spaces and indentation is not provided, given here for readability)
	 * This piece of javascript then executes an XHR request to /_Incapsula_Resource?SWHANEDL
	 * url, given a list of moving numbers, and then logs for each status of that
	 * request the time at which it was executed.
	 *
	 *
	 * @throws \Exception
	 */
	public function processObfuscatedJs()
	{
		/* @var $scr2 simple_html_dom_node */
		$scr2 = $this->_dom->find('script', 1);
		if($scr2 === null)
			throw new \Exception("Impossible to find the second script element with obfuscated js.");
		
		$fulljs = $scr2->innertext();
// 		var_dump('full');
// 		var_dump($fulljs);
		
		// first part, transform binary into executable js
		$pos1 = strpos($fulljs, 'b="');
		if($pos1 === false)
			throw new \Exception('Impossible to find beginning of message for incapsula challenge in js : '.$fulljs);
		
		$pos2 = strpos($fulljs, '";', $pos1 + 1);
		if($pos2 === false)
			throw new \Exception('Impossible to find ending of message for incapsula challenge in js : '.$fulljs);
		
		$binaryjs = substr($fulljs, $pos1 + 3, $pos2 - $pos1 - 3);
		
// 		var_dump('binary');
// 		var_dump($binaryjs);
		
		// decoding...
		$execjs = '';
		for($i = 0; $i < strlen($binaryjs); $i+=2)
			$execjs .= chr(hexdec(substr($binaryjs, $i, 2)));
		
		$matches = array();
		if(preg_match('#SWHANEDL=\d+,\d+,\d+,\d+#', $execjs, $matches))
		{
			$this->_swhanedl = $matches[0];
		}
		else
			throw new \Exception('Impossible to find target url in executable js : '.$execjs);
	}
	
}

