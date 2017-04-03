# pressbooks-metadata
Educational Metadata for Pressbooks

## About
Pressbooks gives you the ability to add metadata to your books thus helping Google and other search engines to recognize it. 
The problem comes if your book is for educational purposes.
Pressbooks-metadata, extends the functionality of Pressbooks and gives you the flexibility to add more metadata in your books, 
taking advantage of the LRMI schema markup.

## Requirements
This is a plugin for Wordpress (tested on 4.3)

This plugin uses some styles from PressBooks, thus you should have installed and
activated this plugin (tested on 2.8).

This plugin requires:
* Wordpress Multisite
* Pressbooks Plugin

## Installation

1. Clone (or copy) this repository to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

## TODO

* Make some fields required
* Add more fields
* Extend the plugin to add metadata to the root, so that they will be used in every book
* Add metadata to the page-level so that every page will be indipendent

### Version A
* Initial version.
  
* **Custom Chapter Metadata:** new custom metaboxes for the custom page chapter
  * _Questions And Answers:_ this field allows teachers to insert a custom link. 
  * _Class Learning Time (minutes):_ how long the students will need for the topic.
  
* **Educational Information:**
  * _Subject:_ Subject name
    *_Educational Level:_ Level of the course
    *_Educational Framework:_ Framework the Educational Level belongs to
  * _Small Description:_ Small Description of the Subject
  * _Provider:_ Provider of the Subject
  * _Learning Reasource Type:_ Course, Examination, Exercise, Descriptor
  * _Interactivity Type:_ Active, Expositive, Mixed
  * _Age range:_ 3-5, 6-7, 7-8, 8-9, 9-10, 10-11, 11-12, 12-13, 13-14, 14-15, 15-16, 16-17, 17-18 years, Adults
  * _Class Learning Time:_ how long the students will need for the book
  * _License URL:_ custom link to a licence
  * _Bibliography URL:_ custom link to a bibliography
