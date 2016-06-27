# MarkupJsonLDSchema Module v0.0.3


## General information
This module is all about arrays, either PHP or JSON+LD. JSON+LD schema arrays give your web pages a vastly improved chance of high visibility in SERPs and, at Google's whim, deliver more engaging results than simply a title, link and brief description.

While the inherent structure of a schema is pre-defined, there are 1000s of combinations.

This module helps you dynamically create schemas from within your templates. Each schema can be configured to meet your requirements. You can even add your own ProcessWire schema classes to the module.

There are plenty of reference sites and guides to learn more about json+ld schemas. Try these for starters:

* <https://developers.google.com/schemas/>
* <http://jsonld.com>
* <https://www.jamesdflynn.com/json-ld-schema-generator/>

There are also a few very informative posts in the ProcessWire forums.

## Installation
* Download the zip file into your site/modules folder then expand the zip file
* Login to ProcessWire > go to Modules > click "Refresh". You should see a note that a new module was found. Install the module. Configuration is not compulsory. It's simply a shortcut to populating an Organization or Business type schema. Worth it though!


## Usage
### Basic usage
In your template:

```
<?php $jsonld = $modules->get('MarkupJsonLDSchema'); ?>
 <script type="application/ld+json">
 	<?php echo $jsonld->render('LocalBusiness'); ?>
 </script>
```

This will output a LocalBusiness schema as per the data saved in the module configuration page.

### Overriding schema elements
A more complex example using the $options array in _footer.php:

```
<?php
    $jsonld = $modules->get('MarkupJsonLDSchema');
    $options = array(
        'logo' => $pages->get(1)->images->first->width(200)
    );
 ?>
<script type="application/ld+json">
<?php 
    switch ($page->template) {
        case 'home':
            echo $jsonld->render('WebSite', $options); 
            echo $jsonld->render('LocalBusiness', $options);
            break;
        case 'blog-post':
            echo $jsonld->render('Article'); 
            echo  $jsonld->render('BreadcrumbList'); 
            break;
        default:                
            echo $jsonld->render('WebPage', $options); 
            echo $jsonld->render('LocalBusiness', $options); 
            echo  $jsonld->render('BreadcrumbList');
            break;
         } 
    ?>
</script>
```
Any value in the default schema may be overwritten in the $options array, eg:

```
<?php 
    $jsonld = $modules->get('MarkupJsonLDSchema'); 
    $options["@type"] = "RealEstateAgent"
?>
<script type="application/ld+json">
    <?php $jsonld->render('LocalBusiness',$options); ?>
</script>
```

Would render the schema "@type" as "RealEstateAgent" instead of "LocalBusiness".

### Custom schema elements
You can add to the pre-defined schema arrays with the use of the 'custom' key in the $options array, eg:

```
<?php
$jsonld = $modules->get("MarkupJsonLDSchema");
$options = array();
$options["custom"] = array (
  "foo" => "bar",
  "foo2 => array (
  		"@type" => "FooBar",
  		"name" => "Mary Contrary",
  )
);
?>
<script type="application/ld+json">
  <?php $jsonld->render('LocalBusiness',$options); ?>
</script>
```
Would render the default LocalBusiness schema plus the items in the $options["custom"] array.

## Schemas included in the module
### Default config fields
```
            "address_locality" => "",  // string - eg city
            "description" => "",       // string - short description, eg meta description
            "address_region" => "",    // string - eg state
            "postcode" => "",          // string - postcode or zip
            "street_address" => "",    // string - self explanatory
            "organization" => "",      // string - name of the business or organization
            "logo" => "",              // string - http url to the logo image
            "telephone" => "",         // string - self explanatory
            "opening_hours" => "",     // string - eg Mo,Tu,We,Th,Fr 09:00-17:00 
            "latitude" => "",          // string - geo latitude
            "longitude" => "",         // string - geo longitude
            "has_map" => "",           // string - google map url
            "same_as" => ""            // array - list of organization's social media page urls
```


###LocalBusiness, Organization
All info taken by default from the module configuration data. Any element may be overridden in the $options array.

Key|Value
---|---
@type|LocalBusiness (or Organization)

### Breadcrumbs
Based on the brilliant ProcessWire forum post by AndZyk at <https://processwire.com/talk/topic/13417-structured-data/>

Kudos also to other the other contributors to this topic, especially Macrura.

### Article, NewsArticle

Again, thanks to both AndZyk and Macrura in the forum post:

Key|Default Value
---|---
@type|Article (or NewsArticle)
mainEntityOfPage[@type]|WebPage
mainEntityOfPage[@id]|$page->httpUrl
headline|$page->get('seo_title\|headline\|title')
url|$page->httpUrl
datePublished|$page->created
dateModifield|$page->modified
author[@type]|Person
author[name]|wire('users')->get($page->created\_user\_id)->title
publisher[@type]|Organization
publisher[name]|Module config Organization name
image[@type]|ImageObject
image[url]|your image httpUrl in $options["image"], eg $page->images->first
image[height]|image height based on $options["image"]
image[width]|image width based on $options["image"]
description|$page->get('seo_description\|summary\|title')
articleBody|$page->get('body\|blog-body')

### Event

Key|Default Value
---|---
@type|Event
name| $page->get('seo_title\|title\|headline')
url|$page->httpUrl
description|$page->get('seo_description\|description\|summary\|headline')
startDate|$page->start_date (custom field in your template)
endDate|end_date (custom field in your template)
location[@type]|Place
location[name]|null - name of venue
location[sameAs]|null - url of the venue
location[streetAddress]|null - venue street address
location[addressRegion|null -  venue state/province
location[postalCode]|null - venue postcode/zip
location[addressCity]|null - venue city/suburb
location[addressCountry]|null - venue country
offers[@type]|Offer
offers[description]|null
offers[url]|null
offers[price]|null
offers[priceCurrency]|null

### Product

Key|Default Value
---|---
@type|Product
brand|$page->get('brand\|manufacturer\|title')
name|$page->get('seo_title\|title\|headline')
description|$page->get('seo_description\|summary\|blog-summary')
image[@type]|ImageObject
image[url]|your image httpUrl in $options["image"], eg $page->images->first
image[height]|image height based on $options["image"]
image[width]|image width based on $options["image"]
aggregateRating[@type]|aggregateRating
aggregateRating[ratingValue]|null
aggregateRating[reviewCount]|null

### WebPage, Website
Key|Default Value
---|---
@type|WebPage (or WebSite)
url|$page->httpUrl
name|$page->get('seo_title\|title\|headline')
description|$page->get('seo_description\|summary\|blog-summary')
image[@type]|ImageObject
image[url]|your image httpUrl in $options["image"], eg $page->images->first
image[height]|image height based on $options["image"]
image[width]|image width based on $options["image"]
potentialAction[@type]|SearchAction (requires functioning search page on your site)
potentialAction[target]|wire('pages')->get(1000)->httpUrl . "?q={search_term}"
potentialAction[query-input]|"required name=search_term"

### Custom
This is a dummy schema and has no default fields. Build your entire schema in $options["custom"] array.


## Change log
2016-06-20: Minor edits, Fixes to jsonld.Custom.php v0.0.3
2016-06-19: Initial release, v0.0.2

