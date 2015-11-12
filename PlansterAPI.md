# Introduction #

This is how we communicate with planster's server side.

# Details #

|URL|Method|Does|Arguments|Code|Means|Return value|
|:--|:-----|:---|:--------|:---|:----|:-----------|
|/  |PUT   |Create plan|title=? (JSON)|201 |Created|URL for new plan resource (in HTTP header)|
|   |      |    |         |403 |Wrong captcha value|-           |
|`<`plan`>`/title|GET   |Returns title|         |200 |OK   |The title   |
|   |      |    |         |404 |No such plan|-           |
|   |POST  |Set title|title=?  |200 |OK   |The newly set title|
|   |      |    |         |403 |Permission denied|-           |
|   |      |    |         |404 |No such plan|-           |
|`<`plan`>`/owner|PUT   |Set the owner to the current session user|key=?    |200 |OK   |New owner   |
|   |      |    |         |401 |No session user|-           |
|   |      |    |         |403 |Wrong key|-           |
|   |      |    |         |409 |Plan has an owner|-           |
|   |GET   |Returns owner|         |200 |OK   |Current owner|
|   |      |    |         |401 |No session user|-           |
|   |      |    |         |404 |No such plan|-           |
|`<`plan`>`/instructions|GET   |Returns instructions|         |200 |OK   |Current instructions|
|   |      |    |         |404 |No such plan|-           |
|   |POST  |Set instructions|...      |200 |OK   |New instructions|
|   |      |    |         |403 |Permission denied|-           |
|   |      |    |         |404 |No such plan|-           |
|`<`plan`>`/permissions|POST  |Set permissions|lock\_settings,lock\_participants|200 |OK   |New permissions|
|   |      |    |         |401 |No session user|-           |
|   |      |    |         |403 |Wrong session user|-           |
|`<`plan`>`/items|GET   |Get items|-        |200 |OK   |Items (title/id)|
|   |      |    |         |404 |No such plan|-           |
|   |PUT   |Add item|title=?  |201 |OK   |URL for new item's resource in HTTP header, new item's title and id (JSON)|
|   |      |    |         |403 |Permission denied|-           |
|   |      |    |         |404 |No such plan|-           |
|`<`plan`>`/items/`<`item`>`|GET   |Get item|         |200 |OK   |title=, id= |
|   |      |    |         |404 |No such plan/item|-           |
|   |DELETE|Delete item|         |204 |OK   |-           |
|   |      |    |         |403 |Permission denied|-           |
|   |      |    |         |404 |No such plan/item|-           |
|   |POST  |Modify item|title=?  |200 |OK   |New title   |
|   |      |    |         |403 |Permission denied|-           |
|   |      |    |         |404 |No such plan/item|-           |