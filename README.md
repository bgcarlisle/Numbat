# Numbat Systematic Review Manager

## Overview

The source code for Numbat is available on [Github](https://github.com/bgcarlisle/Numbat "Numbat Github").

Numbat is free software first developed by PhD student Benjamin Carlisle in 2014 for use by the STREAM research group[^1] in the Biomedical Ethics Unit at McGill University to facilitate systematic review work for the *Animals, Humans and the Continuity of Evidence* grant as well as the *Signals, Safety and Success* grant. This work was funded by the Canadian Institutes of Health Research (MOP 119574), and it is released as free and open-source under the GNU AGPL v 3.

It is named after the numbat, because numbats feed on termites by extracting them from their hiding places with very long and flexible tongues.

[^1]: <http://www.translationalethics.com/>

## Purpose and limitations

### What Numbat does

Numbat is a piece of software designed for managing the extraction of large volumes of data from primary sources among multiple users, and then reconciling the differences between them. It is designed for use in systematic review projects in an academic context.

The following are the intended uses of Numbat.

* Manage large databases of references
* Different levels of extraction (e.g. title-and-abstract vs full extraction)
* Multiple extraction forms / codebooks
* Multiple users, with an assignment manager
* Can generate reference networks among the publications in the database

### What Numbat doesn't do

* Statistical analysis of results
* Calculating Cohen's/Fleiss' kappa (but I have an *R* script for that, contact me)
* Semantic analysis of papers to extract (you have to read the papers yourself)
* Magic

### Values for the Numbat project

* No user lock-in as a philosophy for data in Numbat—data entered in Numbat should be easily imported and exported, so that users are never trapped
* Open formats
* Usability
* Expandability / modularity of software
* Ownership of one's own data
* Low barriers to entry
* Ease of back-up

### Why not just use a Google Form?

* Google has a bad record for keeping private data private
* No good, built-in way to reconcile multiple extractions
* Built-in blinding from other extractors' work to minimise validity threats to your systematic review work
* Built-in assignment manager
* Google Forms do not accommodate certain data structures, like table data and reference networks

## Installation requirements

You may be able to install Numbat on setups different from what is described below, but the following is what it was designed for.

* Apache
* PHP
* MySQL

Copy the entire file to your web server, and navigate to the Numbat directory with your browser. You will need to know your MySQL server, username and password to complete the installation.

## What's new in 2.12

* Bug fixes
* Button to automate copying of all rows in table to final during reconciliation
* Export now provides informative name for exported file
* Import extractions allows user to set status of imported rows
* Optional regex validation for open text field in main extraction and in sub-extractions

To migrate from Numbat 2.10 or earlier, run `db-migrations.php` while logged in as an administrator.

## How to cite Numbat

Here is a BibTeX entry for Numbat:

```
@Manual{numbat-carlisle,
  Title          = {Numbat {S}ystematic {R}eview {M}anager},
  Author         = {Carlisle, Benjamin Gregory},
  Organization   = {The Grey Literature},
  Address        = {Berlin, Germany},
  url            = {https://numbat.bgcarlisle.com},
  year           = 2014
}
```

You may also cite this resource as: (Numbat, [RRID:SCR_019207](https://scicrunch.org/scicrunch/Resources/record/nlx_144509-1/SCR_019207/resolver "RRID:SCR_019207")).

If you use my software to complete a systematic review and you found it useful, I would take it as a kindness if you cited it. 

Best,

Benjamin Gregory Carlisle PhD
