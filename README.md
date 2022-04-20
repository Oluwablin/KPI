
## KIP

An application that allows employees to submit feedback toward each other’s performance review.

## Project Description

This aplication is about RESTful APIs to be consumed by an application that allows employees to submit feedback toward each other’s performance review.

This includes the following:
### Admin View
• Registration and Login
• Add/Remove/Update/View Employees
• Add /Update/View Performance Reviews
• Assign employees to participate in another employee’s performance review.
### Employee View
• List of performance reviews requiring feedback
• Submit feedback

## Project Setup

### Cloning the GitHub Repository

Clone the repository to your local machine by running the terminal command below.

```bash
git clone https://github.com/Oluwablin/KPI
```

### Setup Database

Create a MySQL database and note down the required connection parameters. (DB Host, Username, Password, Name)

### Install Composer Dependencies

Navigate to the project root directory via terminal and run the following command.

```bash
composer install
```

### Create a copy of your .env file

Run the following command

```bash
cp .env.example .env
```

This should create an exact copy of the .env.example file. Name the newly created file .env and update it with your local environment variables (database connection info and others).

### Generate an app encryption key

```bash
php artisan key:generate
```

### Generate a jwt encryption secret key

```bash
php artisan jwt:secret
```

### Migrate the database

```bash
php artisan migrate
```

### Run the database seeds

```bash
php artisan db:seed
```

## Postman Collection

https://documenter.getpostman.com/view/9649360/Uyr8kxhW