# YOURLS-Expiry
[YOURLS](https://github.com/YOURLS/YOURLS) plugin to define conditions under which links will expire - time and click limited links

#### Installation
Works with YOURLS 1.7.3 (current from YOURLS master git) or YOURLS 1.7.2 with the following additional commit [dd78d7f](https://github.com/YOURLS/YOURLS/commit/dd78d7f226017b8dbba4f2e9ee4baafe759a7dee) from YOURLS master. 
 
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
   - seperate page to list all expiry links with details (time left, etc)
   - one-click management of expiry data, bulk or individual
   - admin page action link button
   - expiry data on admin page under share box
-  robust api interface (documentation provided)
   - add and update individual links with precise detail
   - manage database with a cron call (pre-formatted example provided)
      - prune expired links that haven't been visited in a while, etc.
   - get precise individual url expiry info
- add expiry data in various ways
   - via public interface (new url)
   - via regular admin new url form (new url)
   - via expiry page (old url)
      - directly or by way of admin area action link button

###### Note: 
 Uses code adapted from the [Change Error Msgs](https://github.com/adigitalife/yourls-change-error-messages) plugin.
