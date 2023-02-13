## Simple Crawler

Crawler written with Laravel framework. Project has simple web interface with input field and submit button. To start crawling a web site insert URL and press submit. After this server will check provided URL, make request to a web site, parse the response, find all internal links. Then it will repeat the proccess for 5 more links from a web site. Some scrapped data will be displayed below the input field.


### Local setup

Dependecies: docker, docker compose.
If docker is present project can be started by running `./vendor/bin/sail up` command.