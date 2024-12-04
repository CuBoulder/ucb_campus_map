# CU Boulder Campus Map

This is a custom Drupal module developed specifically for the
[Campus Map](https://www.colorado.edu/map) site.
**Do not enable on any other sites!** Features for other sites such as the map
[CKEditor plugin](https://github.com/CuBoulder/ucb_ckeditor_plugins) are
located elsewhere.

CU Boulder Campus Map replaces the site's homepage with a campus map in a
fullscreen frame. The page intentionally omits the default headers and footers
coming from the base theme.

CU Boulder Campus Map also handles redirects from outdated links containing
legacy building codes. The configuration for this feature can be found in the
`ucb_campus_map.configuration` config object.

## Major Releases

- Fall 2024: Initial release to migrate from the Drupal 7 Campus Map Bundle.
    - The “Campus Map URL Builder” tool offered previously in the Drupal 7
      bundle was removed, as there are more reliable ways of getting the
      current URL for a location on the map.
    - An undocumented `/location-lookup` API and `/location` redirects, both
      using the legacy building codes, were also removed.
