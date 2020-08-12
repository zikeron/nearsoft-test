# Nearsoft Test

Logger Parser Test
## Description

Create a log parser that can:  
* Read an access log file
* Resolve Country and State from IP address (IE MaxMind GeoLite2 Free)
* Translate useragent to device type (Mobile, Desktop, Tablet) and Browser
(Safari, Chrome, etc)
* Combine new Geo & Device fields with existing fields on access log file and
output/export a CSV

## Dependencies

* Docker

## Getting Started

First of all consider that you need to have Docker installed in your machine. Also, you need to add the following host in your hosts  
````
127.0.0.1  nearsoft.tst
````
### Consider
 You might need to make some request to fill the nginx access.log to get some data to generate the CSV.
 
## Installing
* Clone the repository
```
git clone git@github.com:zikeron/nearsoft-test.git
```

## Executing program

* Initialize the docker
````
docker-compose up
````

* In other terminal session run the command to enter the container 
````
docker exec -it «name_container» bash
````

* Go to the following path
````
cd /var/www/nearsoft
````

* Run the following path
```
php artisan logger-parser
```

## Authors

Alejandro Cortez  [@ziker_on](https://twitter.com/ziker_on)

## Version History

* 1.0.0
    * Initial Release

## License

This project is licensed under the MIT License - see the LICENSE file for details
