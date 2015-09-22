#MX Mobile Detect
MX Mobile Detect is a small and simple plugin to detect if the template was requested by Mobile Device or not…


##Compatibility
* Updater
* Multi Site Manager

##Installation
* Download the latest version of MX Mobile Detect and extract the .zip to your desktop.
* Copy *system/expressionengine/third_party/mobile_detect* to *system/expressionengine/third_party/*

-or-
you can install MX Select Plus in minutes using DevDemon Updater.



##Activation
*NOTE:: You don't need to activate extension if you don't plan to use Screen Resolution functions*

* Log into your control panel
* Browse to Addons > Extension
* Enable all the MX Mobile Detect  components


##Templates Variables


	{exp:mobile_detect:pair}
		{if device == "mobile"}
			Hello Steve
		{/if}
		{if device == "table" }
			Hello Bender
		{/if}
		{if not_mobile}
			Hello ...
		{/if}

	{/exp:mobile_detect:pair}

- or -


	{if "{exp:mobile_detect:device}" != "not_mobile"}
	    ...
	{/if}


	{if "{exp:mobile_detect:device}" == "not_mobile"}
	  	...
	{/if}

##Support Policy

This is Communite Edition add-on.

##Contributing To MX Mobile Detect

Your participation toMX Mobile Detect development is very welcome!

You may participate in the following ways:

* [Report issues](https://github.com/MaxLazar/mobile_detect/issues)
* Fix issues, develop features, write/polish documentation
Before you start, please adopt an existing issue (labelled with "ready for adoption") or start a new one to avoid duplicated efforts.
Please submit a merge request after you finish development.
* [Donate] (https://www.paypal.me/maxlazar)

###License

The MX Mobile Detect is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
