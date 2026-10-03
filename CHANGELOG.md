# Changelog

All notable changes to this extension are documented here. The format
is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.1.3] - 2026-10-03

### Fixed
- The range JSON import no longer stops with a TypeError when the file is a JSON object with named keys and one of its rows is invalid. Rows are numbered by position, invalid rows are reported and skipped, and the remaining valid rows are still imported and counted in the result message.
