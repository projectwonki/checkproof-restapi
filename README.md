## About Project

This is the simple REST API project using Laravel Framework which allow user to create new user and get the list of users through API

## Features

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

## Roles

### Administrator
- privilege: can edit any user
### Manager
- privilege: can only edit users with the role user
### User:
- privilege: can only edit themselves