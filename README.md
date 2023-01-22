## The Story

Assume you have a huge file to import into a database, like a text log file with more than 10 million lines. I have experimented with many ways to handle this issue during the creation of this app.
I had a simple text file with its specific structure to import. So, I needed to parse this file line by line. I created a command to get and parse the file:   
```
php artisan import:from-text "filename.txt"
```   
By running this command, our story gets to start. Just make sure that the file is in this directory:    
````
"storage/app/logFiles"
````   
This command will fetch the full path of the file and will pass it to a job. In the earlier version of the app, I did the entire implementation inside this job, but later I made separate classes to decouple the implementations and logic. I tried to utilize single responsibility.  
The queue worker should be run before or after previous command:   
````
php artisan queue:listen --queue="import-text-log-file"
````
You may complain that the better way to run queue workers is "queue:work", but trust me, I test it many times with spending a lot of time. If you run the queue workers with this command, you will encounter the memory limitation error.  
As **Mohamed Said**  mentioned in this [link ](https://divinglaravel.com/avoiding-memory-leaks-when-running-laravel-queue-workers), if you want to prevent memory leaking while running the queue workers you should restart them before that happen. He suggest some ways for do that.   
But in our case the better way is using **queue:listen** instead and you will never face any issue. This command just run the jobs a little bit slower.   
The job is responsible for importing lines to database, each time import ten lines of the file. Why 10 lines? because I tested this also and if you set the lines in bigger number, like to 20 lines, the time of inserting the lines into database is steadily increased.
After that the call itself to read and parse next ten lines until the last line of file.   
We also have a endpoint to filter and count the imported data:     
````
localhost:8000/api/logs/count
````
The example of usage:   
- Filter by service name,
````
localhost:8000/api/logs/count?serviceName=invoice-service
````

- Filter by status code:   
````
localhost:8000/api/logs/count?statusCode=201
````

- Filter by start date and end date:   
````
localhost:8000/api/logs/count?startDate=2022-03-17&endDate=2022-03-20
````

- You can combine filter together:   

````
localhost:8000/api/logs/count?serviceName=invoice-service&startDate=2022-03-17&endDate=2022-03-20
````

You may want to make your own file with specific number of lines to test:     
```
php artisan create:log-file <filename> <numberOfLines>

php artisan create:log-file "testFile" 100000
```
## Installation
I used php version 8.2 and mysql version 8.
Just create your own database, clone the repository, open the project directory make your own **.env** file and set the keys inside it.   
Then, run the following commands:
```
- composer install
- php artisan key:generate
- php artisan migrate
- php artisan serve
```
That's it!

Notice: normally the port for using the app is 8000, it will be shown after running last command. 
