Numbat v. 2.6.1
======

Numbat Academic Meta-Analysis Extraction Manager

## Overview

Numbat is free software first developed by PhD student Benjamin Carlisle in 2014 for use by the STREAM research group[^1] in the Biomedical Ethics Unit at McGill University to facilitate meta-analytic work for the *Animals, Humans and the Continuity of Evidence* grant as well as the *Signals, Safety and Success* grant. This work was funded by the Canadian Institutes of Health Research (MOP 119574), and it is released as free and open-source under the GNU GPL v 2.

It is named after the numbat, because numbats feed on termites by extracting them from their hiding places with very long and flexible tongues.

[^1]: <http://www.translationalethics.com/>

## Purpose and limitations

### What Numbat does

Numbat is a piece of software designed for managing the extraction of large volumes of data from primary sources among multiple users, and then reconciling the differences between them. It is designed for use in meta-analytic projects in an academic context.

The following are the intended uses of Numbat.

* Manage large databases of references
* Different levels of extraction (e.g. title-and-abstract vs full extraction)
* Multiple extraction forms / codebooks
* Multiple users, with an assignment manager
* Can generate reference networks among the publications in the database

### What Numbat doesn't do

* Statistical analysis of results
* Calculating Cohen's kappa
* Semantic analysis of papers to extract (you have to read the papers yourself)
* Magic

### Values for the Numbat project

* "Easy in, easy out" as a philosophy for data in Numbat—data entered in Numbat should be easily imported and exported, so that users are never trapped
* Adherence to standards
* Usability
* Expandability / modularity of software
* Ownership of one's own data
* Ease of back-up


### Why not just use a Google Form?

* Google has a bad record for keeping private data private
* No good, built-in way to reconcile multiple extractions
* Built-in blinding from other extractors' work to minimise validity threats to your meta-analytic work
* Built-in assignment manager
* Google Forms do not accommodate certain data structures, like table data and reference networks


## Installation requirements

You may be able to install Numbat on setups different from what is described below, but the following is what it was designed for.

* Apache 2.2.22-14
* PHP 5.3.27
* MySQL 5

Copy the entire file to your web server, and navigate to the Numbat directory with your browser. You will need to know your MySQL server, username and password to complete the installation.

## What's new in 2.6

* New automatic export to CSV for extractions, sub-extractions, tabled data and citations
* Fixed sub-extractions bug
* In 2.6.1: all manner of bug fixes

## Contact info

I cannot guarantee that I will be able to help you with your problems. Depending on the nature and scope of your problems, you may be better off calling 911 or admitting yourself to the nearest hospital. That said, if you have found bugs, or if you have ideas for future directions for the software, here are some reasonably reliable ways to contact me.

* Email: <benjamin.carlisle@mcgill.ca>
* Twitter: @numbatextractor
* Post: Room 304, 3647 rue Peel, Montréal QC H3A 1X1

Best,

Benjamin Carlisle  
Murph E.
