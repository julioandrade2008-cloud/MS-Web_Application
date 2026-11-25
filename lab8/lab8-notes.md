# Lab 8: Laravel MVC Demo

## Exploring MVC

### 1. Business Purpose

The purpose of the Peer Tutoring Hub application is to provide students with a way to request tutoring for a course they may be struggling with. The application allows students to select a course, identify topics they need help with, and provide their preferred date and time. It also allows students to go back and view the requests that have been submitted. 

### 2. Route

One of the routes I identified was `POST /requests`, which is located in `routes/web.php` file. This route is used when a student fills out the tutoring request form and clicks the Request session button. The route directs the submitted information to the `store()` function within the controller.

### 3. Controller

The controller I identified was `TutoringRequestController.php`. One of its responsibilities is to validate the information that the student submits. For example, it checks that the student has selected a course, entered a valid date, and selected at least on topic. Once the information has met the validation requirements, the conrtroller uses the model to save the request and redirects the student to a page displaying the submitted information.

### 4. Model

The model I identified was `TutoringRequest.php`. This model represents the information associated with a tutoring request, such as the course, reason for the request, topics, preferred date, andpreferred time.

### 5. View

One of the views I identified was  `create.blade.php`. This is the page where students enters the information needed to submit a request. This view is also responsible for displaying error messages if the information entered by the student does not meet the validation requirements.

### 6. MVC Workflow

When a student submits a tutoring request, the information entered in the form is sent through the `Post /requests` route. This route directs the information to the `store()` function in the `TutoringRequestConroller. php` file. 

The controller then validates the information to ensure that it meets the requirements before it is stored in the databse. If the information is valid, the controller uses the `TuoringRequest` model to create a new record in the SQLLite database. 

Once the information has been stored, the controller redirects the student to the URL for that specific request. Laravel retrieves the saved record, and the controller's `show()` function passes the information to the `show.blade.php` view. This is where the student is able to see the details of the requests that was submitted. 

### 7. Comparison with Lab 7

Some similarities that I noticed from Laravel and the architecture we have been using throughtout our previous labs, is that both approaches separate responsibilities within the application. For example, in our labs we use PHP pages, services, and repositories to handle different responsibilities, and we use PDO to communicate with the SQLite database.

What I see Laravel offering is that it provides a more standardized way of organizing these files. This could be helpful when working on larger applications because it would give you a better understanding of where to look for specific functionality. 

I think one of the reasons we are not converting our current project to Laravel is because we are still learning how the different components of a web application communicate with each other. If we were to move to Laravel, some of these responsbilities would be handled by the framework. 

## Part 3: Evidence of Completion

### Screenshot 1: Running Application

![Running Laravel application](images/working-application.png)

### MVC Files Examined

- **Route:** `app/routes/web.php` — Defines the URLs and controller actions.
- **Controller:** `app/app/Http/Controllers/TutoringRequestController.php` — Validates and coordinates requests.
- **Model:** `app/app/Models/TutoringRequest.php` — Represents the tutoring request data.
- **View:** `app/resources/views/requests/create.blade.php` — Displays the request form.

### Reflection

Before this lab, I was not familiar with Laravel or how the MVC pattern worked. I am still trying to understand how all the different files in a web application communicate with each other. Going through the process of submitting a tutoring request and then looking throught the code helpe me make some of those connections.I did find it a little overwhelming looking through all the folders and files that Laravel has compared to what we have been working with in our previous labs. 