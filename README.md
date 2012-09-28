MXRequestManager [for Qt]
=========================

Required
--------
- Don't care about this one, just to tell you that I haven't built **MXRequestManager** on _Qt >= 5.0_, so as you should know, there is no JSON parser below 5.0. So I'm using another project called [**QtJson**](https://github.com/XXX), which is built and linked with my lib.

How to download
---------------
There are several ways to download **MxRequestManager** for Qt:

- Clone the github repository: `$ git clone git://github.com/QtMXRequestManager`
- Download the zip file on github directly
- Try to find another one by yourself :/

How to install
-----------
The installation process is really really really simple, and before I forget to write it, it's simple.

- On MacOSX:
	1. Open a terminal
	2. Know your `qmake` directory (usually `~/QtSDK/Desktop/Qt/%QtVersion%/gcc/bin`, with **QtVersion** replaced by your version of Qt)
	3. `quake`, `make` and `make install`, everything should be ok. Here is an example, let's say you're using Qt 4.8.0:
	
```
$ cd ~/Desktop/QtMXRequestManager
$ ~/QtSDK/Desktop/Qt/4.8.0/gcc/bin/qmake
$ make
[Compiling...]
$ make install
[Copying files to Qt standard lib directory]
```
AAAAAAAAAAAAANNND it's done !

How to use
----------
Captain Obvious is so Obvious, that this is truly the most interesting part of the README.

You can find a Doxygen doc somewhere, you'll just see how to basically use the library.

First of all, let's take a simple example. You have your APIs (http**s**://api.awsome-guy.com) and you want to retrieve a member list from your **users** resource, with a **GET**.

JSON string would be:

    users:[
    	{
    		"id": 4,
    		"username": "foo"
    	}, {
    		"id": 12,
    		"username": "bar"
    	}
    ]

```
AwsomeApp::AwsomeApp(void)
{
	// The MXRequestManager object must be an internal member of the class using it.
	this->req = new MXRequestManager(QUrl("https://api.awsome-guy.com/"));
	QPushButton	*button = new QPushButton("Button");

	QObject::connect(button, SIGNAL(clicked()), SLOT(memberlistStartRequest()));

	button->show();
}

void	AwsomeApp::memberlistStartRequest(void)
{
	QObject::connect(this->req, SIGNAL(finished()), SLOT(memberlistRequestFinished()));

	this->req->request("users.json", "GET");
}

void	AwsomeApp::memberlistRequestFinised(void)
{
	QObject::disconnect(this->req, SIGNAL(finished()), this, SLOT(memberlistRequestFinished()));

	MXRequestManager::MXVList const& receivedData = this->req->data().value("users").toList();

	for (int i=0;i<receivedData.size();i++))
		qDebug() << i+1 << ") Id: " << receivedData.at(i).toMap().value("id")
				 << ", username: " << receivedData.at(i).toMap().value("id");
}
```
-> Note that you'll be working with `QVariant`s, it's quite confusing at the beginning, the code is quite long on each line, but it's still powerful.  
-> `qDebug` should output this to the console:

```
1) Id: 4, username: foo
2) Id: 12, username: bar
```

That's it, you're 

How to Master the library
-------------------------
`RTFM bitch`, which is coming soon.