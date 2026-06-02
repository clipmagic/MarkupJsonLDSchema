v0.1.7 - 2026-06-02
Street address module config now uses a textarea and LocalBusiness, Organization, and Person preserve multiline street addresses in JSON-LD output.

v0.1.6 - 2026-04-17
LocalBusiness and Organization image data now supports multiple images via arrays or iterable image collections while preserving single image output.
Product schema now supports common Offer data via $data['offers'] or simple price/priceCurrency options.
FAQ schema added

v0.1.5 - 2026-03-25
improved handling of nested arrays in $data['custom']
removed sanitizing $data['custom'] from schemas - all done in the module

v0.1.4 - 2026-03-24
improve schemas

v0.1.3 - 2026-03-09
Added optional $data['custom'] key/value pairs for extending schema output
Custom fields are sanitized as text and appended to schema output
BreadcrumbList now includes the current page in the breadcrumb trail
Standardised schema context to https://schema.org
Minor internal improvements and cleanup

v.0.1.1 - 2025-09-29:
Added 'Proceswire' namespace
PHP8.2 compatibility

v0.0.2- 2016-06-19: 
Initial release
