# CU Boulder Campus Map

All notable changes to this project will be documented in this file.

Repo : [GitHub Repository](https://github.com/CuBoulder/ucb_campus_map)

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- ### Map Updates
  Updated the map so that it can display rave alerts above the map if there is one active.
  
  Added new settings page for configurable page display
  `/admin/config/system/ucb-campus-map`
  
  `/` leaves the map at the homepage
  Adding a different path there allows it to be displayed elsewhere like at `/map`
  
  Update hook should keep the current path at `/` and everything should continue to work when updated.
  
  Resolves #9 
  Resolves #10 
---

- ### Add use statement for TrustedRedirectResponse
  
---

- ### Add a workaround due to domain masking issues
  Add a special case for the map site.
---

- ### Adds missing document structure to campus map template (v1.0.1)
  This update adds missing document structure to the campus map template to provide better context for assistive technologies.
  
  [bug] Resolves CuBoulder/ucb_campus_map#1
---
