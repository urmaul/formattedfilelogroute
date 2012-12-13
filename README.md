# Formatted FileLogRoute for Yii Framework

This log route extends CFileLogRoute and gives you ability to specify log format.

## How to attach

Add route config to log routes. It will look like this:

```php
array(
	'class'   => 'ext.formattedfilelogroute.FormattedFileLogRoute',
	'format'  => '{time}  {msg}',
	'logFile' => 'formatted.log',
),
```

## Log variables

You can use these variables in your log format:

* {ip} - client ip
* {uri} - request URI
* {time} - current time
* {level} - log message level
* {category} - log message category
* {message} - full log message text
* {msg} - log message text without stack trace
* {trace} - stack trace
