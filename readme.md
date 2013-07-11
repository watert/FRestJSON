# FRestJSON: 

File Based Restful JSON Service ( in one file PHP )

Browser end can just use `file_rest.php?f=filename.json` way to control the content.

Feel free for prototyping & intranet developing. :)

### Usage:

Put file_rest.php any where, json files will store at the same directory.

Examples :

```javascript
var url = "data/file/test.json";
var data = {case:"jQuery directly"};
$.post(url,data,function(data){ // Saving
	/*TODOs*/ });
$.get(url,function(data){ // Fetching
	/*TODOs*/ });
$.ajax({url:url, // Deleting
	type:"DELETE",
	success:function(){ /*TODOs*/ 
}});
```

Or a Backbonejs way:

```javascript
var url = "data/file/test2.json";
var data = {
	id:url, // NOTICE: backbone way always needs an ID
	case:"Backbone model accessing"
};
var model = new Backbone.Model(data);
model.url = url;
model.save(); // Saving
model.fetch(); // Fetching
model.destroy(); // Deleting
```
If file not exists, will return an 400 HTTP Error.(File style only)

Collection Style: 

*ATTENSION:* url ends with a slash.

```javascript
var collection = new Backbone.Collection();
collection.url = "data/list/testlist.json/"; 
collection.add({data:"testing"});
collection.at(0).save({},{success:function(){
	collection.at(0).destroy();
}});
```


### TODOs

* More error infos like file editing permissions detect.
* *DONE* support a list style Restful CRUD with a ID attribute
	* list may support simple filtering & ordering
