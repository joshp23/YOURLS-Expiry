# YOURLS-Expiry
[YOURLS](https://github.com/YOURLS/YOURLS) plugin to define conditions under which links will expire - time and click limited links

#### Installation
As any YOURLS plugin:
-  Extract the `expiry` folder from this repo, and place it at `user/plugins/expiry`
-  Enable in admin area

The databse ought to create itself, if not, there is an sql file in the `expiry/assets` folder
##### What's Working:
-  add per-link and/or global time based or click count limited links
-  update links already in database to have expiration conditions
-  upon shorturl expiration:
    - delete from YOURLS 
    - update short url's with an optional per link or global secondary URL
-  robust admin area features
   - optionally highlight expiry links in admin interface
   - provide page to list all expiry links with details (time left, etc)
   - one-click management of expiry data, bulk or individual
-  robust api interface (documentation provided)
   - add and update individual links with precise detail
   - manage database with a cron call (pre-formatted example provided)
      - prune expired links that haven't been visited in a while, etc.
- public interface post requests and admin page requests

##### What's Planned:
-  Integrate expiry data to stats page OR stand alone expiry stats page.
-  more clarity of info in list page (human readable time left, # of clicks left)
-  simple and robust options for updating expiry data via the list page (add X clicks or days to expiry, etc...)
-  api to call for expiry data

###### Note: 
 Uses code from the [Change Error Msgs](https://github.com/adigitalife/yourls-change-error-messages) plugin.

 Currently, the following needs to be added to `inclides/functions-html.php` at line 179 in order for the admin page form to work. A [pull request](https://github.com/YOURLS/YOURLS/pull/2345/commits/1546416dbefee8f21030f28d165eb14a5ba7eae6) with this filter has been submitted to YOURLS/YOURLS.
```
	$pre = yourls_apply_filter( 'shunt_html_addnew', false );
		if ( false !== $pre )
			return $pre;
```
