# Proyecto Backend de Enfermeros – Symfony API

## Project Description
This project consists of developing a Symfony backend that exposes an API for nurse management.  
It includes CRUD operations, database connection through Doctrine, unit testing, continuous integration with GitHub Actions, and technical documentation.  
The goal is to understand how a professional backend works using PHP and Symfony.

---

## Technologies Used
- Symfony
- PHP
- Composer
- MySQL / MariaDB
- Doctrine ORM
- PHPUnit
- Git and GitHub Actions

---

## Installation and Configuration
The project requires cloning the repository, installing all necessary dependencies, and configuring the environment file to establish the database connection.  
After that, the database must be created and Doctrine migrations applied to prepare the schema.

---

## Project Usage
Symfony allows running a development server to access the API locally.  
Once running, the API provides all required functionalities to manage nurses, perform validations, and retrieve database information.

---

## Unit Testing
The project includes unit tests using PHPUnit, focused on the main controller functions and business logic.  
These tests verify both correct results and expected errors, ensuring stable and predictable behavior.

---

## Continuous Integration (CI)
A GitHub Actions workflow has been configured to automatically execute unit tests whenever changes are pushed to the repository.  
This ensures code quality and allows early detection of issues during development.

---

## Database

### Design
The logical structure of the database was designed using MySQL Workbench, defining the schema required to store and manage nurses.

### Implementation with Doctrine
After configuring Doctrine, the entities and migrations were generated to synchronize the database model with Symfony.  
The result was compared to the initial design to ensure consistency.

### Local and Remote Testing
The project was tested using both a local database and an external centralized database.  
This validated correct behavior in different environments.

---

## Technical Documentation
Comprehensive documentation has been created, including:

- Final database model  
- Full explanation of the implemented CRUD  
- Postman validation tests  
- Evidence of the CI pipeline  
- Comparison between theoretical model and Doctrine-generated model  
- Repository usage and issue tracking  

---

## Repository Link
[https://github.com/AdrianPalmadev/Proyecto-DAW2](https://github.com/AdrianPalmadev/Proyecto-DAW2)

---

## Author
**Adrián Palma**

Individually developed project.
