# tweetDelete
This project came from a need that I had to delete some past tweets that I didn't quite want still floating around.  
It uses [twitter-api-php](https://github.com/J7mbo/twitter-api-php.git) for authentication/calls to the API, and
[Clusterize.js](https://clusterize.js.org/) to display large sets of tweets without jamming up the browser.

If you want to use it, you'll need to grab a copy of your Tweet Archive in csv form and load that into a mysql database.
In my case, I grabbed the tweet ID, timestamp, and text content of each tweet and imported it into a table simply named tweets.
