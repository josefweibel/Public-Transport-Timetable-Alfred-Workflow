# Timetable as a workflow for Alfred.app

A simple workflow which returns the next connections by typing the start station and the destination for the public transport in Switzerland, Germany, Austria, France, Netherlands and Belgium.


## Installation
Download the Fahrplan.alfredworkflow file and open it in Alfred v2. Requires the Powerpack of Alfred.


## Features
### current connections
Get the next connections by typing the start and destination. The worklow will give you some suggestions for the possible stations.

    von Zürich HB nach Bern ...

### set home station
When you set a start station by typing the following command ...

    fahrplan start Basel SBB
	
... you can only type the destination into Alfred and get your connections.

    nach Biel ...

### delays and track changes
If your connection is late or your train leaves from a different track, the Workflow will show you the changes.

### capacity
The workflow will show you how heavily loaded your connection is. This requires that the transport provider provides this data. You can change the train classes (first and second) by typing into Alfred.

	fahrplan klasse

### updates
This workflow supports the [Alleyoop update workflow](http://www.alfredforum.com/topic/1582-alleyoop-update-alfred-workflows/) for updates.


## planned features
* You will be able to add a departure or arrival date and time.
* There will be a take-me-home function.

## data source
This worklow uses the great [Opendata Transport API](http://transport.opendata.ch).

## More information
You can find additional information on the [offical workflow website](http://www.josefweibel.ch/alfred/fahrplan/). Have fun!
