## What is this?

Showing a list of .ics files (iCalendar) in one calendar


## Install
1. git clone into a virtual root that can run PHP
2. Maybe set some permissions so the proxy php can write to /cache
3. Access site

## Directory Structure
```
/app-source     - vue app source
/cache          - empty folder, will contain cached files
/dist           - compiled vue.js app

/proxy.php      - simple proxy
/calendars.json - contains all calendars
```

## How it works
For each entry in calendars.json there is a "name" field, this is the ID for each calendar. It needs to be unique and slug-friendly (no space, or weird characters, etc). The value of 'name' is passed to the proxy like. The proxy only accepts a 'name' listed in calendars.json
```
proxy.php?name=xxxx
```

## Configure
You can change the urls of calendars.json and proxy.php as attributes to the app in index.html

## Todo
- "proxy.php" is now hardcoded
- Error handling from the proxy. If the proxy throws an error now, nothing happens.
- Lift out all CSS to an external file to make it easier to style
- How data is passed around in the vue app could be structured better
- the calendar id (name) is sometimes called 'domain' in the vue code
- Better date formatting in event view (full day events, in particular)

## Roadmap
- Make a nice cardview. A grid with images that shows what's up 'today', 'tomorrow', 'upcoming' and 'past' etc. This is a better way to show events than the traditional calendar
