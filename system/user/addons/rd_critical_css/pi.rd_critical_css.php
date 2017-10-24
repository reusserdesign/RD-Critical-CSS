<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * RD Critical CSS
 *
 * @package		ExpressionEngine
 * @category	Plugin
 * @author		Jarrod D Nix, Reusser Design
 * @license		https://opensource.org/licenses/MIT The MIT License (MIT)
 */

class Rd_critical_css
{
	public $loadCSS = '<script>!function(w){"use strict";var loadCSS=function(href,before,media){function ready(cb){return doc.body?cb():void setTimeout(function(){ready(cb)})}function loadCB(){ss.addEventListener&&ss.removeEventListener("load",loadCB),ss.media=media||"all"}var ref,doc=w.document,ss=doc.createElement("link");if(before)ref=before;else{var refs=(doc.body||doc.getElementsByTagName("head")[0]).childNodes;ref=refs[refs.length-1]}var sheets=doc.styleSheets;ss.rel="stylesheet",ss.href=href,ss.media="only x",ready(function(){ref.parentNode.insertBefore(ss,before?ref:ref.nextSibling)});var onloadcssdefined=function(cb){for(var resolvedHref=ss.href,i=sheets.length;i--;)if(sheets[i].href===resolvedHref)return cb();setTimeout(function(){onloadcssdefined(cb)})};return ss.addEventListener&&ss.addEventListener("load",loadCB),ss.onloadcssdefined=onloadcssdefined,onloadcssdefined(loadCB),ss};"undefined"!=typeof exports?exports.loadCSS=loadCSS:w.loadCSS=loadCSS}("undefined"!=typeof global?global:this),function(w){if(w.loadCSS){var rp=loadCSS.relpreload={};if(rp.support=function(){try{return w.document.createElement("link").relList.supports("preload")}catch(e){return!1}},rp.poly=function(){for(var links=w.document.getElementsByTagName("link"),i=0;i<links.length;i++){var link=links[i];"preload"===link.rel&&"style"===link.getAttribute("as")&&(w.loadCSS(link.href,link),link.rel=null)}},!rp.support()){rp.poly();var run=w.setInterval(rp.poly,300);w.addEventListener&&w.addEventListener("load",function(){w.clearInterval(run)}),w.attachEvent&&w.attachEvent("onload",function(){w.clearInterval(run)})}}}(this);</script>';

	public $return_data  = "";

	public function __construct()
	{

		if (version_compare(APP_VER, '3', '>='))
		{

			// Get critical css file
			$critical = ee()->TMPL->fetch_param('critical') ? ee()->TMPL->fetch_param('critical') : FALSE;

			// Get Google Fonts file
			$google = ee()->TMPL->fetch_param('google-fonts') ? ee()->TMPL->fetch_param('google-fonts') : FALSE;

			// Create array of stylesheets
			$styles = ee()->TMPL->fetch_param('styles') ? ee()->TMPL->fetch_param('styles') : FALSE;
			if (stristr($styles, "|") !== FALSE)
			{
				$styles = explode("|", $styles);
			}else
			{
				$styles = array($styles);
			}

			if ($critical && file_exists($_SERVER['DOCUMENT_ROOT'].$critical) && ($criticalTime = filemtime($_SERVER['DOCUMENT_ROOT'].$critical)) !== FALSE && (!isset($_COOKIE["cssEmbedded"]) || $_COOKIE["cssEmbedded"] < $criticalTime))
			{

				// Set cookie to use cached stylesheets
				setcookie("cssEmbedded", time(), time()+60*60*24*365, "/");

				// Get contents of critical css file
				$criticalContents = file_get_contents($_SERVER['DOCUMENT_ROOT'].$critical);

				// Remove any source map comments, i.e. /*# sourceMappingURL=critical.css.map */
				$criticalContents = preg_replace("(\n\/\*\#.*\*\/)", "", $criticalContents);

				$this->return_data = "<style>";

				// Embed Google Fonts if available
				if($google)
				{
					$this->return_data .= file_get_contents($google);
				}

				// Embed contents of critical css file
				$this->return_data .= $criticalContents;

				$this->return_data .= "</style>";

				// Create preload `<link>` elements
				$this->return_data .= '<link href="'.$google.'" as="style" onload="this.rel=\'stylesheet\'" rel="preload" />';
				foreach($styles as $stylesheet) {
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$stylesheet) && ($styleTime = filemtime($_SERVER['DOCUMENT_ROOT'].$stylesheet)) !== FALSE)
					{
						$this->return_data .= '<link href="'.$stylesheet.'?'.$styleTime.'" as="style" onload="this.rel=\'stylesheet\'" rel="preload" />';
					}
				}

				// Create `<noscript>` fallbacks
				$this->return_data .= '<noscript>';
				$this->return_data .= '<link href="'.$google.'" rel="stylesheet" />';
				foreach($styles as $stylesheet) {
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$stylesheet) && ($styleTime = filemtime($_SERVER['DOCUMENT_ROOT'].$stylesheet)) !== FALSE)
					{
						$this->return_data .= '<link href="'.$stylesheet.'?'.$styleTime.'" rel="stylesheet" />';
					}
				}
				$this->return_data .= '</noscript>';

				// Add loadCSS function as preload fallback
				$this->return_data .= $this->loadCSS;

			}else
			{

				$this->return_data = "";

				if($google)
				{
					$this->return_data .= "<link href='".$google."' rel='stylesheet' />";
				}

				// Create standard `<link>` elements since stylesheets are cached
				foreach($styles as $stylesheet) {
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$stylesheet) && ($styleTime = filemtime($_SERVER['DOCUMENT_ROOT'].$stylesheet)) !== FALSE)
					{
						$this->return_data .= '<link href="'.$stylesheet.'?'.$styleTime.'" rel="stylesheet" />';
					}
				}

			}

		}

	}

}

/* End of file pi.rd_critical_css.php */
/* Location: ./system/user/addons/rd_critical_css/pi.rd_critical_css.php */