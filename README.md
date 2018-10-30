Extractor
=========

**Extractor** is a library that allows you to extract data from *connections* (databases, files, API's, etc) into a simple JSON array for further processing.

No programming is required, all you need to do is write extractor definitions.

## Use-cases

* Extract data from applications you can't modify
* Reporting
* Collecting Context data (all records related to entity "X").

## Extractor + Context = <3

Extractor works particularly well in tandem with github.com/linkorb/context

You can use the output of Extractor to load the data into a Context for further processing.
This gives you the ability to specify advanced multi-directional relationships for flexible in-memory navigation.

## Example: wordpress-post-extractor.yaml

Check the [examples/wordpress-post-extractor.yaml](examples/wordpress-post-extractor.yaml) definition file for a simple example.

This example requires one **Input** called `postName`.

Based on the input, it runs a set of commands.

Each command has a `method`. Different connections support different methods. The `PdoConnection` supports the `query` method, while an `XyzApiConnection` would support methods like `request`.

You can specify an SQL query that contains a set of placeholders.

The placeholders are passed as arguments into the command before it's executed.
This allows you to pass arguments from the "inputs" table, or from a table that has been queried before.

In the wordpress example you'll see that the first command passes the `postName` from the initial inputs.

The second command passes in the `postId` that was retrieved during the first command.

This allows you to stack queries on top of each-other.

## Output format

Extractor collects a set of tables, with rows per table. You can think of it as snapshot of a database.

The output is a simple array (which you can json_encode) that looks like this:

```
{
  "inputs": [
    {
      "postName": "hello-world"
    }
  ],
  "posts": [
    {
      "ID": "3021",
      "post_title": "Hello world",
      "post_name": "hello-world",
      "post_date": "2018-06-17 09:00:00",
      "post_status": "publish"
    }
  ],
  "comments": [
    {
      "comment_ID": "431",
      "comment_author": "Joe Johnson",
      "comment_author_email": "joe@example.com",
      "comment_date": "2018-06-17 11:06:00",
      "comment_content": "Hello to you too!"
    },
    {
      "comment_ID": "452",
      "comment_author": "Alice Alisson",
      "comment_author_email": "alice@example.com",
      "comment_date": "2018-06-17 13:21:00",
      "comment_content": "Nice post! and a 'Hello' back :-)"
    }
  }
}
```

You'll see the "inputs" have is provided as a table containing a single row.
The "posts" table contains one row, the one selected based on the input argument.
And the comments table contains all rows matched by the postId.

Note that rows don't have a key, it's up to the code that uses these extracts to
assign row ids or build indexes if needed.

A simple way would add useful IDs to the comment table based on the `comment_ID` of each row would be:

```php
$comments = [];
foreach ($data['comments'] as $i=>$row) {
  $comments[$row['comment_ID']] = $row;
}
$data['comments'] = $comments;
```

## License

MIT (see [LICENSE.md](LICENSE.md))

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!



