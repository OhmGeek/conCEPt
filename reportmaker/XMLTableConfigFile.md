# Structure for the config file of the table

## Document Scope
In order to ensure that everything is within the document, we can use the following tags:


\<report>
	THE REST OF THE DOCUMENT GOES HERE
\</report>



Everything that we want to read should be within these two tags.

### Title Scope
The intro tags shall be used to define the section that contains the titles, and the key inputs.


\<intro>
INTRODUCTION TO THE DOCUMENT GOES HERE
\</intro>


#### Assessed Document name

To mark a particular document, we can use the assessed tags, which mark what document we want to mark (i.e. 'Design Report'). These tags contain the main title

\<assess>
Design Report
\</assess>


#### Report title
This is what the table itself will be about. We can put this inside '\<docname>' and '\</docname>' tags.


\<docname>
Individual Examiners Report
\</docname>

#### Introduction Text
This introduces the user to what the document contains. We can mark this by using '\<intro>' and '\</intro>'


\<introtext>
This is a design report
\</introtext>


### Specific Scope
This defines the specific specification sections, including what is being marked specifically (including bullet points, sections, and percentage weightings).

This is defined using the following tags:

\<specifictbl>
	ROWS AND TABLE INFO
\</specifictbl>

#### Row Scope
This defines the information for the row. We mark a row by using the rowdetails tag.

\<rowdetails>
The details about the row as tags (no plain text, as this will be ignored).
\</rowdetails>

##### Section Name
The section name is the heading for the section part of the table. e.g. there might be a particular area being assessed.

\<section>
Structured Abstract
\</section>

##### Percentage Weight
This is the weight as a percentage. Here, ignore the percentage sign as we will assume it's out of 100. For strings, the percentage sign shall be automatically printed out. If we don't require the percentage sign (such as in calculations), we don't have to remove it.

\<perweight>

\</perweight>


##### Bullet Points
We can define the specification in terms of bullet points. These items need to be in the spec so we can use the following notation:


\<specitem>
The system has to work
\</specitem>


