# Structure for the config file of the table

## Document Scope
In order to ensure that everything is within the document, we can use the '<report>' and '</report>' tags.
Everything that we want to read should be within these two tags.

## Title Scope
The tags '<intro>' and '</intro>' shall be used to define the section that contains the titles, and the key inputs.

### Accessed Document name
We want to access a particular document. We can put this in the '<access>' and '</access>' tags, which will contain the main title (e.g. Design Report).

### Report title
This is what the table itself will be about. We can put this inside '<docname>' and '</docname>' tags.

### Introduction Text
This introduces the user to what the document contains. We can mark this by using '<intro>' and '</intro>'

## Specific Scope
This defines the specific specification sections, including what is being marked specifically (including bullet points, sections, and percentage weightings).
### Row Scope
This defines the information for the row. In this we have:

#### Section Name
The section is defined by '<section>' and '</section>' such as 'Structured Abstract'.

#### Percentage Weight
This is the weight as a percentage. Mark this by '<perweight>' '</perweight>'. No percent sign is needed, so to mark as 10% we use: '<perweight>10</perweight>'. 

#### Bullet Points
We can define the specification in terms of bullet points.

'<spec> </spec>' are the tags we can use to define a value that needs to be in the spec.

## General Comment Scope.

'<gencom>' and '</gencom>' can be used to mark areas of generic comments. THis might not actually be needed, considering it should be on every single document.
