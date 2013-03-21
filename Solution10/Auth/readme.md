# Authentication System for S10 #

Simple and powerful driver-based authentication system for your apps.

Based off [Mauth for Kohana](http://github.com/alexgisby/Mauth)

## Features ##

- **Framework Agnostic**: Auth uses a driver based system to allow for any storage / session backend.
- **Create Multiple Instances**: Need a completely seperate login system for your admins and customers? On different tables? In different storage technologies? Not a problem.
- **Simple, powerful permissions**: Using Packages you can create extremely granular, powerful permissions, all through a very simple API.
- **Light.ish**: With power comes responsibility, Auth is designed to be as fast as possible whilst maintaining a great feature set.

## Security ##

Auth makes use of the excellent [phpass library](http://www.openwall.com/phpass/) to
ensure your passwords are securely stored against Rainbow Table attacks.

## Todo before inclusion:

* Add the reset_packages() function to Auth
* Docs
