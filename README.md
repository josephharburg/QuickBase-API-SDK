The QuickBase REST API Starter Development Kit for PHP
-------------------------------
Description
-------------------------------
This is a simple class to be used to access the REST API that QuickBase offers for PHP.
This starter development kit does not do all the work for you, however, it does give you a good leg up when trying to access the QuickBase REST API in PHP.

Known Limitations
-------------------------------
I have not included every single method the QuickBase REST API offers, just the basics. If you would like to have a method added please feel free to submit an issue with your request and I will get to it when I can.

Using the Class Basics
-------------------------------
Simply just include or require this file into your project and instantiate a new class replacing the brackets with your own credentials: 

```PHP 
$QuickBaseAPI = new QuickBaseRestAPI({QUICKBASE_USER_TOKEN},{QUICKBASE_APP_TOKEN}, {QUICKBASE_REALM}, {USER_AGENT});
```

Here is an example of making a simple query (replace X with field ids):

```PHP 
$request = $QuickBaseAPI->query_for_data(TABLE_NAME, array(X,XX) ,"{XX.AF.'08-06-2021'}AND{XX.XEX.'NULL'}");
echo $request; 
```

Here is an example of updating or creating a record (replace X with field ids and PRIMARY KEY with primary key field id): 

```PHP 
  $records_to_update = array(
    array(
      PRIMARY KEY  => array("value" => RECORD ID),
      "XX" => array( "value" => "Value to update"),
    ),
    array(
      PRIMARY KEY  => array("value" => RECORD ID),
      "XX" => array("value" => "Some other value to update"),
    ),
);

          
$updateRequest = $QuickBaseAPI->update_or_create_records(TABLE_NAME, $records_to_update, array(RECORD ID,XX,XX,XX));
echo $updateRequest;
```

Bugs and Issues
-------------------------------
If you would like a feature added or have noticed a bug, please let me know in detail by submitting an issue.

For bugs please let me know the following:  
  Method Name: What method is not working.
  
  Error description: What is the unexpected behavior you see when you use the method.
  
  Error Log Info: Any error log information you have (if any)

I unfortunately will not be able to give end user support (how to implement in your own project) or teach you how to code. That being said if you need help just message me. :)

Who is this for?
-------------------------------
Anyone who wants to use it!

Can I contribute?
-------------------------------
Absolutely!
Please feel free to submit a pull request and I will add you to the list of contributors if your code works and I implement it. I would request that you test your submission before making a pull request.
