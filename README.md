Numbat
======

Numbat Meta-Analysis Extraction Manager

![image](https://github.com/bgcarlisle/Numbat/blob/master/images/numbat.gif)

## Overview

Numbat is free software first developed by PhD student Benjamin Carlisle in 2014 for use by the STREAM research group[^1] in the Biomedical Ethics Unit at McGill University to facilitate meta-analytic work for the *Animals, Humans and the Continuity of Evidence* grant as well as the *Signals, Safety and Success* grant. This work was funded by the Canadian Institutes of Health Research (MOP 119574), and it is released as free and open-source under the GNU GPL v 2.

It is named after the numbat, because numbats feed on termites by extracting them from their hiding places with very long and flexible tongues.

[^1]: <http://www.translationalethics.com/>

## Purpose and limitations

### What Numbat does

Numbat is a piece of software designed for managing the extraction of large volumes of data from primary sources among multiple users, and then reconciling the differences between them.

* Manage large databases of references
* Different levels of extraction (e.g. title-and-abstract vs full extraction)
* Multiple extraction forms / codebooks
* Multiple users, with an assignment manager
* Can generate reference networks among the publications in the database

### What Numbat doesn't do

* Statistical analysis
* Automatic semantic analysis (you have to read the papers yourself)
* Magic

## Installation requirements

You may be able to install Numbat on setups different from what is described below, but the following is what it was designed for.

* Apache 2.2.22-14
* PHP 5.3.27
* MySQL 5

Copy the entire file to your web server, and navigate to the Numbat directory with your browser. You will need to know your MySQL server, username and password to complete the installation.


## Contact info

I cannot guarantee that I will be able to help you with your problems. Depending on the nature and scope of your problems, I may not even try. That said, if you have found bugs, or if you have ideas for future directions for the software, here are some reasonably reliable ways to contact me.

* Email: <benjamin.carlisle@mcgill.ca>
* Twitter: @numbatmanager
* Bitmessage: BM-2DBF2CD9Dq25NnESpf4gZ69r5UqoCAbVZV
* Post: Room 303, 3647 rue Peel, Montréal QC H1M 2N9