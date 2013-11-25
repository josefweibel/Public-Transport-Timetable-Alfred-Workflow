# Timetable as a workflow for Alfred.app

A simple workflow which returns the next connections by typing the start station and the destination for the public transport in Switzerland, Germany, Austria, France, Netherlands and Belgium.

## Installation
Download the Fahrplan.alfredworkflow file and open it in Alfred v2. Requires the Powerpack of Alfred.

### Updates
This workflow supports the [Alleyoop update workflow](http://www.alfredforum.com/topic/1582-alleyoop-update-alfred-workflows/) for updates.

## Features
### Current Connections
Get the next connections by typing the start and destination. The worklow will give you some suggestions for the possible stations.

    von Zürich HB nach Bern jetzt

### Home Station
When you set a start station by typing the following command ...

    fahrplan start Basel SBB

... you can only type the destination into Alfred and get your connections ...

    nach Biel jetzt
	
... or you if you want to go back home, you can do one of the following query types:
	
	von Aarau nach Hause jetzt
	zurück Aarau jetzt

### Departure Times
You can add on every query a departure time. The following patterns works currently:

* jetzt
* um hh:mm
* morgen hh:mm
* übermorgen hh:mm
* Montag-Sonntag hh:mm
* dd.mm.yyyy hh:mm

### Delays and Track Changes
If your connection is late or your train leaves from a different track, the Workflow will show you the changes.

### Capacity
The workflow will show you how heavily loaded your connection is. This requires that the transport provider provides this data. You can change the train classes (first and second) by typing into Alfred.

	fahrplan klasse

## Planned Features
* queries with relative departure times
* queries with arrival time
* internationalization

You have a great idea? Contact me!

## Data Source
This worklow uses the great [Opendata Transport API](http://transport.opendata.ch).

## More information
In the readme section of the workflow in Alfred or on the [workflow website](http://www.josefweibel.ch/alfred/fahrplan/). Have fun!
