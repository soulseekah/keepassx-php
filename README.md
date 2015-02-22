# KeepAssX PHP

This is a simple PHP file which implements the tiniest subset of the WebDAV (GET, PUT, MOVE, DELETE) to manage kdbx database files remotely and share them across devices easily.
Compatible with http://www.keepassx.org/ and the keepass2android Android client among others probably.

## Installation

1. Upload to you server, enable HTTPS, set HTTP BasicAuth, create database directory which is writable by PHP.
2. Upload your database file, make sure it's writable by PHP.
3. Point your KeepAss client to https://remote/path/to/vault.php?db=database.kdbx
