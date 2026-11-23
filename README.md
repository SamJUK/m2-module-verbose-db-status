# SamJUK_VerboseDBStatus

[![Supported Magento Versions](https://img.shields.io/badge/magento-2.4.6%E2%80%932.4.9-orange.svg?logo=magento)](https://github.com/SamJUK/m2-module-verbose-db-status/actions/workflows/ci.yml) [![CI Workflow Status](https://github.com/samjuk/m2-module-verbose-db-status/actions/workflows/ci.yml/badge.svg)](https://github.com/SamJUK/m2-module-verbose-db-status/actions/workflows/ci.yml) [![GitHub Release](https://img.shields.io/github/v/release/SamJUK/m2-module-verbose-db-status?label=Latest%20Release&logo=github)](https://github.com/SamJUK/m2-module-verbose-db-status/releases)

This is a simple module that adds a verbose and extra verbose mode to the `setup:db:status` command. 

Especially useful within a CI environment for identifying what schema changes are causing a database migration, which in turn prevents most typical zero downtime deployment strategies.

Example with the verbose flag set | Example with the extra verbose flag set
--- | ---
![Example of the DB Status command with the verbose flag set](./.github/db-status-verbose.png) | ![Example of the DB Status command with the extra verbose flag set](./.github/db-status-extra-verbose.png)

---

## Installation

```bash
composer require samjuk/m2-module-verbose-db-status
php bin/magento module:enable SamJUK_VerboseDBStatus && php bin/magento cache:flush
```

---

## Usage

Run the `setup:db:status` command, specifying the appropriate verbosity level
```bash
php bin/magento setup:db:status # Standard Magento Output
php bin/magento setup:db:status -v # Display before and after for modified values
php bin/magento setup:db:status -vv # Display before and after for modified values, and raw data diff
```
