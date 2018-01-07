# YOURLS-Expiry
[YOURLS](https://github.com/YOURLS/YOURLS) plugin to define conditions under which links will expire - time and click limited links

#### Installation
Requires YOURLS 1.7.3, commit [dd78d7f](https://github.com/YOURLS/YOURLS/commit/dd78d7f226017b8dbba4f2e9ee4baafe759a7dee) + from YOURLS master.  
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
 Uses code adapted from the [Change Error Msgs](https://github.com/adigitalife/yourls-change-error-messages) plugin.
