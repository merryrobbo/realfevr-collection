# realfevr-collection

A very simple tool to get your realfevr collection into csv format

## Description

This is very simple tool to get your realfevr collection into a csv format.

It uses a local copy of the html sourced from the realfevr collection page. You could put this script on a web server or simply run it from the command line if you have PHP installed. I developed it for my own use and as such it is not "production" ready. It is intended only as a starter script that you can modify and extend for your own purpose.

This script can probably be used to get marketplace pages into a csv format also however it is not tested and may need some modification.

## Getting Started

### Executing program

* Connect your wallet on the realfevr site.
* Browse to your collection page on the realfevr website. The default is 12 but you can change it to a maximum of 96 to get more items e.g. https://www.realfevr.com/collection?page=1&per_page=96.
* Inspect the html element then "Copy outer html".
* Save it to the collection-html folder. Any file name is accepted however for Beach Soccer prefix "bs-" to the filename.
* Repeat for each page of your collection.
```
php collection.php
```

## License

This project is licensed under the GNU General Public License - see the LICENSE file for details
