<?php

namespace Anastaszor\Incapsula;

/**
 * IncapsulaDomFactory class file.
 *
 * This factory abstracts the base simple html dom factory with automatic
 * resolution of the Incapsula's challenge. This factory does nothing on
 * websites that are not giving Incapsula's challenge page.
 *
 * @author Anastaszor
 */
class IncapsulaDomFactory
{
	/**
	 *
	 * @var CookieJar
	 */
	private $_cookiejar = null;
	
	/**
	 *
	 * @var Browser
	 */
	private $_navigator = null;
	
	/**
	 *
	 * @return CookieJar
	 */
	public function getCookiejar()
	{
		if($this->_cookiejar === null)
			$this->_cookiejar = new CookieJar();
		return $this->_cookiejar;
	}
	
	/**
	 *
	 * @return Browser
	 */
	public function getBrowser()
	{
		if($this->_navigator === null)
			$this->_navigator = new Browser('firefox');
		return $this->_navigator;
	}
	
	/**
	 *
	 * @param string $url
	 * @param array $contextarr
	 * @return \simple_html_dom
	 */
	public function getDomFromUrl($url, $contextarr = array())
	{
		$contextBuilder = new ContextBuilder();
		
		$factory = new \SimpleHtmlDomFactory();
		
// 		do
// 		{
			$context = $contextBuilder->build($this->getCookiejar(), $this->getBrowser(), $contextarr);
			$dom = $factory->getDomFromUrl($url, $context);
			
			$meta_robot = $dom->find('meta[name=robots]', 0);
// 			if($meta_robot !== null && $meta_robot->content === 'noindex,nofollow')
// 			{
				$this->resolveIncapsulaChallenge($dom, $url);
// 				$dom->clear();
// 				unset($dom);
// 			}
// 			else break;
// 		}
// 		while(true);
		
		return $dom;
	}
	
	/**
	 * Resolves the challenge of Incapsula. This will allow to fetch the real
	 * page that was hidden in the first place.
	 *
	 * @param \simple_html_dom $dom
	 * @param string $url
	 */
	protected function resolveIncapsulaChallenge(\simple_html_dom $dom, $url)
	{
		$icp1 = new FirstChallengePage($this->_navigator, $this->_cookiejar, $dom);
		$icp1->runJs();
		$schwanedl = $icp1->getSchwanedl();
		
		var_dump($schwanedl);
	}
	
}
