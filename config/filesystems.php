<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Default Filesystem Disk
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default filesystem disk that should be used
	| by the framework. A "local" driver, as well as a variety of cloud
	| based drivers are available for your choosing. Just store away!
	|
	| Supported: "local", "s3", "rackspace"
	|
	*/

	'default' => 'local',

	/*
	|--------------------------------------------------------------------------
	| Default Cloud Filesystem Disk
	|--------------------------------------------------------------------------
	|
	| Many applications store files both locally and in the cloud. For this
	| reason, you may specify a default "cloud" driver here. This driver
	| will be bound as the Cloud disk implementation in the container.
	|
	*/

	'cloud' => 's3',

	/*
	|--------------------------------------------------------------------------
	| Filesystem Disks
	|--------------------------------------------------------------------------
	|
	| Here you may configure as many filesystem "disks" as you wish, and you
	| may even configure multiple disks of the same driver. Defaults have
	| been setup for each driver as an example of the required options.
	|
	| Sendfile allows you to process file download requests in PHP, while
	| still having your web server handle the actual task of serving the file
	| itself. For more information, just Google "<<Your Web Server>> XSendfile"
	|
	*/

	'disks' => [

		'local' => [
			'driver' => 'local',
			'root'   => env('FS_LOCAL_ROOT', storage_path().'/app'),
            'sendfile' => [
                'enabled' => env('FS_LOCAL_SENDFILE', false),
                'server'  => env('FS_LOCAL_SENDFILE-SERVER', 'nginx'), // accepts nginx, apache or lighttpd
                'path'    => env('FS_LOCAL_SENDFILE-PATH', '/internal')
            ],
		],

		's3' => [
			'driver' => 's3',
			'key'    => env('FS_S3_KEY', 'your-key'),
			'secret' => env('FS_S3_SECRET', 'your-secret'),
			'bucket' => env('FS_S3_BUCKET', 'your-bucket'),
		],

		'rackspace' => [
			'driver'    => 'rackspace',
			'username'  => 'your-username',
			'key'       => 'your-key',
			'container' => 'your-container',
			'endpoint'  => 'https://identity.api.rackspacecloud.com/v2.0/',
			'region'    => 'IAD',
		],

	],

];
