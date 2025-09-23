## About Project

This is the simple REST API project using Laravel Framework which allow user to create new user and get the list of users through API. The authentication using Laravel sanctum

## Main Features

### Create User
- Insert new record
- send emails:
   - to the new user confirming their account creation
   - to the system administrator notifying them of the new user
   - return a response of newly created user (excludin password)

### Get Users
- Retrieve a paginated list of active users (Exclude the password field in the response)
- Filter results using the search parameter (matches name or email)
- Sort results based on sortBy. Default sorting: created_at.
- show the total number of orders for each user using orders_count attribute
- show to authorization of currently logged-in user on edit the user using can_edit attribute

## Additional Features

### User Login
- authenticate user using Login API
- provide token for authenticated user to access the API

### User Logout
- delete user token to make user become not authenticated
- invalidate tokens to prevents unauthorized user to use the token

### Update User
- implement validation to check if the auth user has privilege to edit the user or not
- this API to show how the privilege of role works

## Roles

### Administrator
- privilege: can edit any user
### Manager
- privilege: can only edit users with the role user
### User:
- privilege: can only edit themselves

## Setup Instructions

Follow these steps to run the project locally:

1. Clone the repository
```
git clone https://github.com/projectwonki/checkproof-restapi
cd checkproof-restapi
```

2. Download Dependencies
```
composer install
```
3. Configure the environment

Create a .env file based on the example:
```
cp .env.example .env
```

Update your .env file with your database and mail settings:

```python
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

4. Generate application key
```
php artisan key:generate
```
5. Run migrations
```
php artisan migrate
php artisan db:seed
```

6. Serve the application
```
php artisan serve
```
The application should now be available at http://localhost:8000.

## API List
1. Login
2. Get Users
3. Create User
4. Update User
5. Logout

## Credentials of Initial User for Testing

1. Administrator
```
email: admin@test.com
password: root123
```
2. Manager
```
email: manager@test.com
password: root123
```
3. User
```
email: user1@test.com
password: root123
```

## Postman Collection
I already included postman collection for testing purpose