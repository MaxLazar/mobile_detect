#MX Mobile Detect

It uses the User-Agent string combined with specific HTTP headers to detect the mobile environment.

##Installation

* Place the **mobile_detect** folder inside your **user/addons** folder
* Go to **cp/addons** and install *MX Mobile Detect*.

##Template Tags

### Check if any mobile device
	{exp:mobile_detect:ismobile}
	
_Detects all mobile devices, both phones and tablets_

### Check if tablet
	{exp:mobile_detect:istablet}

### Check if phone

	{exp:mobile_detect:isphone}

### Check if not a mobile device

	{exp:mobile_detect:isnotmobile}

### Redirect any mobile device including tablets
	
	{exp:mobile_detect:redirect location="http://site.com/mobile.html" redirect="mobile"}

### Redirect not mobile devices

	{exp:mobile_detect:redirect location="http://site.com/mobile.html" redirect="not_mobile"}

### Redirect based on HTTP_USER_AGENT

{exp:mobile_detect:redirect location="http://site.com/please-update-your-ie.html" client="MSIE 6."}

_redirect all users with Internet Explorer 6.0 to specific page._

### exp:mobile_detect:pair

	{exp:mobile_detect:pair}
		{if device == "mobile"}
			Hello Steve
		{/if}
		{if device == "tablet" }
			Hello Bender
		{/if}
		{if not_mobile}
			Hello ...
		{/if}
	{/exp:mobile_detect:pair}
	

### custom_agents

**NOTE:** Custom agents must be in lowercase. Replace spaces in names with underscores.

	{exp:mobile_detect custom_agents="mobile/8a293"}
	    {if device == "ipad"}
	        Hello Steve
	    {/if}
	    {if device == "android" }
	        Hello Bender
	    {/if}
	    {if device == "mobile/8a293" }
	        Hello iPhone4
	    {/if}
	    {if not_mobile}
	        Hello ...
	    {/if}
	{/exp:mobile_detect}



###Conditional variables

	
		{if "{exp:mobile_detect:device}" != "not_mobile"}
		    ...
		{/if}d
		
		
		{if "{exp:mobile_detect:device}" == "not_mobile"}
		  	...
		{/if}
		
		{if "{exp:mobile_detect:isphone}"}
		  	is phone
		{/if}
		
		
	   {if "{exp:mobile_detect:ismobile}"}
		  	 is mobile
		{/if}
	
	
		{if "{exp:mobile_detect:istablet}"}
		  	is tablet
		{/if}
		
	   {if "{exp:mobile_detect:isnotmobile}"}
		  	 is not mobile
		{/if}
	
	{exp:mobile_detect:redirect location="http://m.site.com/"}
	
	{exp:mobile_detect:redirect location="http://site.com/" redirect="not_mobile"}
	
	{exp:mobile_detect:redirect location="http://site.com/" ipad="http://site.com/ipad/" android="http://site.com/android/"}
	
	{exp:mobile_detect:pair custom_agents="mobile/8a293"}
		{if device == "ipad"}
			Hello Steve
		{/if}
		{if device == "android" }
			Hello Bender
		{/if}
		{if not_mobile}
			Hello ...
		{/if}
	
	   {if device == "mobile/8a293" }
	        Hello iPhone4
	    {/if}
	{/exp:mobile_detect:pair}

### Extra parameters
**enable on / off**

Turned off mobile redirection.

	{exp:mobile_detect:redirect enable="off"  location="http://site.com/"}

**cookie_name = "mobile_redirect"**

**cookie_value = "on"**


This allows you to set a cookie on the mobile site to notify a plugin that the mobile visitor should view the full site.



**ignore_cookies = "yes"**

Can be useful for menu


	{exp:mobile_detect:pair ignore_cookies="yes"}
	   {if mobile}
	      <a href="#" onClick="Set_Cookie( 'exp'+'_'+'mobile_redirect', 'off', '', '/', '', '','' );">Show desktop version</a>
	   {/if}
	   {if not_mobile}
	      <a href="#" onClick="Set_Cookie( 'exp'+'_'+'mobile_redirect', 'on', '', '/', '', '','' );">Show mobile version</a>
	   {/if}
	{/exp:mobile_detect:pair}

**refresh = "yes"**

Refresh mobile detect result


##Support Policy

This is Communite Edition add-on.

##Contributing To MX Mobile Detect

Your participation to MX Mobile Detect development is very welcome!

You may participate in the following ways:

* [Report issues](https://github.com/MaxLazar/mobile_detect/issues)
* Fix issues, develop features, write/polish documentation
Before you start, please adopt an existing issue (labelled with "ready for adoption") or start a new one to avoid duplicated efforts.
Please submit a merge request after you finish development.

###License

The MX Mobile Detect is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
