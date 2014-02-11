# Formatted FileLogRoute for Yii Framework

This log route extends [CFileLogRoute](http://www.yiiframework.com/doc/api/1.1/CFileLogRoute) and gives you ability to specify log format.

## How to attach

Add route config to log routes. It will look like this:

```php
array(
	'class'      => 'ext.formattedfilelogroute.FormattedFileLogRoute',
	'format'     => "{time}\t{ip}\t{msg}",
	'categories' => 'application',
	'logFile'    => 'formatted.log',
),
```

## Log variables

You can use these variables in your log format:

* {ip} - client ip
* {uri} - request URI
* {ref} - request referer
* {sref} - short request referer (domain name is removed if referer domain is current domain)
* {time} - current time
* {level} - log message level
* {category} - log message category
* {message} - full log message text
* {msg} - log message text without stack trace
* {trace} - stack trace

## Suggested routes

```php
array(
	'class'   => 'ext.formattedfilelogroute.FormattedFileLogRoute',
	'format'  => "{time}\t{ip}\t{category}\t{uri}\t{message}",
	'except'  => 'exception.CHttpException.404',
	'levels'  => 'error',
	'logFile' => 'error.log',
),
array(
	'class'      => 'ext.formattedfilelogroute.FormattedFileLogRoute',
	'format'     => "{time}\t{ip}\t{uri}\t{sref}",
	'categories' => 'exception.CHttpException.404',
	'logFile'    => 'error404.log',
),
```
