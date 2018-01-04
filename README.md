# YOURLS-Expiry
YOURLS plugin to define conditions under which links will expire - time and click limited links

##### What's Working:
-  add per-link and/or global time limited links
-  add per-link and/or global click count limited links
-  update links already in database to have expiration conditions
-  upon shorturl expiration:
    - delete from YOURLS 
    - update short url's with an optional per link or global secondary URL
-  robust admin area features
   - optionally highlight expiry links in admin interface
   - provide page to list all expiry links with details (time left, etc)
   - one-click management of expiry data, bulk or individual
-  robust api interface
   - add and update individual links with precise detail
   - manage database with a cron call
      - prune expired links that haven't been visited in a while, etc.
- web interface post requests

##### What's Planned:
-  Integrate expiry data to stats page OR stand alone expiry stats page.
-  more clarity of info in list page (human readable time left, # of clicks left)
-  simple and robust options for updating expiry data via the list page (add X clicks or days to expiry, etc...)
-  api to call for expiry data

##### What's not working
-  admin interface to add a link with expiry data (some js included... WIP)
###### Note: 
 Uses code from the [Change Error Msgs](https://github.com/adigitalife/yourls-change-error-messages) plugin.

 Currently, this needs to be added to `inclides/functions-html.php` at line 179 in order for the admin page form to work. A pull request with this filter has been submitted to YOURLS/YOURLS.
```
	$pre = yourls_apply_filter( 'shunt_html_addnew', false );
		if ( false !== $pre )
			return $pre;
```
