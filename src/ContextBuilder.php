<?php

namespace Anastaszor\Incapsula;

/**
 * ContextBuilder class file.
 *
 * This class builds context php objects to be given to the factory that will
 * effectively get the html contents.
 *
 * @author Anastaszor
 */
class ContextBuilder
{
	
	/**
	 * Builds a context object using the stream_context_create function using
	 * given data from the cookie, the browser and any additional data that
	 * may be provided (for proxy authentication, etc.)
	 *
	 * @param CookieJar $cj
	 * @param Browser $browser
	 * @param array $additionalData
	 */
	public function build(CookieJar $cj, Browser $browser, array $additionalData = array())
	{
		if(isset($additionalData['cookie']))
		{
			foreach($additionalData['cookie'] as $key => $value)
				$cj->add($key, $value);
			unset($additionalData['cookie']);
		}
		
		$cs = $cj->getFullCookieString();
		
		$acceptLanguageHeader = 'Accept-language: '.implode($browser->languages);
		
		$headers = array(
			$acceptLanguageHeader,
			$cs,
		);
		
		if(isset($additionalData['headers']))
		{
			$headers = array_merge($headers, $additionalData['headers']);
			unset($additionalData['headers']);
		}
		
		$http_headers = array(
			'method' => 'GET',
			'header' => implode("\r\n", $headers),
		);
		
		$full_ops = array_merge(array(
			'http' => $http_headers,
		), $additionalData);
		
		return stream_context_create($full_ops);
	}
	
}
