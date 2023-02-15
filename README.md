## Simple Crawler

Crawler written with Laravel framework. Project has simple web interface with input field and submit button. To start crawling a web site insert URL and press submit. After this server will check provided URL, make request to a web site, parse the response, find all internal links. Then it will repeat the proccess for 5 more links from a web site. Some scrapped data will be displayed below the input field.
Total links to be crawled can be configured(check `/config/crawler.php`)


### Local setup

Dependecies: project requires `docker` and `docker compose` to be installed.
If docker is present run composer like this
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```
after composer has installed all dependencies we can start local server by running
`./vendor/bin/sail up`

After that application is running and available at `http://localhost` 