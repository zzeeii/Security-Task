# Task Management API

## Overview

This project is a **Task Management System** built with Laravel. It allows users to create, update, and manage tasks with advanced features like comments, attachments, task dependencies, and user assignments.

## Features

- Task creation with title, description, type, status, priority, due date, and assignment.
- Updating task status with audit logs for status changes.
- Task reassignment to different users.
- Commenting and adding attachments to tasks.
- Automatic unblocking of dependent tasks when their parent task is completed.
- Advanced filtering of tasks based on type, status, priority, and more.

## Setup Instructions

### Prerequisites

- PHP >= 8.0
- Composer
- Laravel
- MySQL 

### Installation Steps

1. Clone the repository:
    ```bash
    git clone https://github.com/zeeiin/task7.git
    cd task7
    ```

2. Install the dependencies:
    ```bash
    composer install
    ```

3. Set up the environment:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. Update the `.env` file with your database credentials.

5. Run the migrations:
    ```bash
    php artisan migrate
    ```


6. Run the development server:
    ```bash
    php artisan serve
    ```

## API Endpoints

- **POST** `/api/tasks`: Create a new task.
- **PUT** `/api/tasks/{id}/status`: Update task status.
- **PUT** `/api/tasks/{id}/reassign`: Reassign a task.
- **GET** `/api/tasks`: Get a list of tasks with advanced filtering.
- **POST** `/api/tasks/{id}/comments`: Add a comment to a task.
- **POST** `/api/tasks/{id}/attachments`: Add an attachment to a task.

## Authentication

The API uses JWT for authentication. Make sure to include a valid JWT token in the `Authorization` header for each request:

