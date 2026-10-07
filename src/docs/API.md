# TaskFlow API Documentation

## Base URL

```text
http://localhost:8000/api/v1
```

## Authentication

TaskFlow uses Laravel Sanctum for API authentication.

Authenticated endpoints require a Bearer token:

```text
Authorization: Bearer {token}
```

## Response Format

Successful responses generally return a message and data:

```json
{
    "message": "Success message.",
    "data": {}
}
```

Validation errors:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field": [
            "The field is required."
        ]
    }
}
```

## HTTP Status Codes

| Status Code | Meaning |
|---|---|
| 200 | Request successful |
| 201 | Resource created successfully |
| 204 | Request successful with no response body |
| 401 | Unauthenticated |
| 403 | Forbidden |
| 404 | Resource not found |
| 409 | Conflict |
| 422 | Validation error |
| 429 | Too many requests |
| 500 | Server error |

---

# Authentication

## Register

**POST** `/register`

Creates a new TaskFlow user account and sends an email verification notification.

### Authentication

Not required.

### Request Body

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### Rate Limit

5 requests per minute per IP address.

---

## Login

**POST** `/login`

Authenticates a user and returns a Sanctum API token.

### Authentication

Not required.

### Request Body

```json
{
    "email": "john@example.com",
    "password": "password"
}
```

### Rate Limit

5 requests per minute per IP address.

---

## Logout

**POST** `/logout`

Revokes the authenticated user's current API token.

### Authentication

Required.

---

## Get Current User

**GET** `/me`

Returns the currently authenticated user.

### Authentication

Required.

---

## Change Password

**POST** `/change-password`

Changes the authenticated user's password.

### Authentication

Required.

### Request Body

```json
{
    "current_password": "old-password",
    "password": "new-password",
    "password_confirmation": "new-password"
}
```

---

## Forgot Password

**POST** `/forgot-password`

Requests a password reset link.

### Authentication

Not required.

### Request Body

```json
{
    "email": "john@example.com"
}
```

### Rate Limit

3 requests per minute per IP address.

---

## Reset Password

**POST** `/reset-password`

Resets a user's password using the password reset token.

### Authentication

Not required.

### Request Body

```json
{
    "token": "reset-token",
    "email": "john@example.com",
    "password": "new-password",
    "password_confirmation": "new-password"
}
```

### Rate Limit

3 requests per minute per IP address.

---

## Reset Password Token

**GET** `/reset-password/{token}`

Returns the password reset token.

### Authentication

Not required.

### URL Parameters

| Parameter | Description |
|---|---|
| `token` | Password reset token |

---

## Email Verification

**GET** `/email/verify/{id}/{hash}`

Verifies the user's email address using a signed verification URL.

### Authentication

Not required.

### URL Parameters

| Parameter | Description |
|---|---|
| `id` | User ID |
| `hash` | Signed email hash |

The URL must contain a valid Laravel signature.

---

## Resend Verification Email

**POST** `/email/resend`

Resends the email verification notification.

### Authentication

Not required.

### Rate Limit

3 requests per minute per IP address.

---

## Google Login

**GET** `/auth/google`

Redirects the user to Google for authentication.

### Authentication

Not required.

---

## Google Callback

**GET** `/auth/google/callback`

Handles the callback from Google after authentication.

### Authentication

Not required.

---

# Projects

## Create Project

**POST** `/projects`

Creates a new project.

### Authentication

Required.

### Request Body

```json
{
    "name": "TaskFlow Development",
    "description": "Development project for TaskFlow.",
    "status": "active",
    "start_date": "2026-10-01",
    "due_date": "2026-12-31"
}
```

### Allowed Statuses

```text
active
completed
archived
```

### Validation

| Field | Rules |
|---|---|
| `name` | Required, string, maximum 255 characters |
| `description` | Optional, string |
| `status` | Required: active, completed, archived |
| `start_date` | Optional, YYYY-MM-DD |
| `due_date` | Optional, YYYY-MM-DD, must be on or after start date |

---

## List Projects

**GET** `/projects`

Returns projects created by the authenticated user.

### Authentication

Required.

### Pagination

Projects are paginated with 10 records per page.

Example:

```text
GET /projects?page=2
```

---

## Get Project

**GET** `/projects/{project}`

Returns a specific project.

### Authentication

Required.

### Authorization

The authenticated user must be the project creator or a project member.

---

## Update Project

**PUT** `/projects/{project}`

Updates an existing project.

### Authentication

Required.

### Authorization

Only the project creator can update the project.

### Request Body

```json
{
    "name": "Updated Project Name",
    "description": "Updated description.",
    "status": "active",
    "start_date": "2026-10-01",
    "due_date": "2026-12-31"
}
```

---

## Delete Project

**DELETE** `/projects/{project}`

Deletes a project.

### Authentication

Required.

### Authorization

Only the project creator can delete the project.

---

# Project Members

## Add Member

**POST** `/projects/{project}/members`

Adds an existing user to a project.

### Authentication

Required.

### Authorization

The project creator or authorized project manager can add members.

### Request Body

```json
{
    "user_id": 15,
    "role": "member"
}
```

### Allowed Roles

```text
manager
member
```

Duplicate memberships are not allowed.

---

## List Members

**GET** `/projects/{project}/members`

Returns project members.

### Authentication

Required.

---

## Remove Member

**DELETE** `/projects/{project}/members/{user}`

Removes a user from a project.

### Authentication

Required.

### Authorization

The project creator or authorized project manager can remove members.

---

# Project Invitations

## Send Invitation

**POST** `/projects/{project}/invitations`

Sends a project invitation.

### Authentication

Required.

### Request Body

```json
{
    "email": "user@example.com"
}
```

New invitations have a `pending` status and expire after 3 days.

The invitation email is queued for delivery.

---

## View Invitation

**GET** `/projects/invitations/{token}`

Returns project invitation information.

### Authentication

Not required.

### URL Parameters

| Parameter | Description |
|---|---|
| `token` | Invitation token |

---

## Accept Invitation

**POST** `/projects/invitations/{token}/accept`

Accepts a project invitation.

### Authentication

Required.

The authenticated user's email must match the invitation email.

The user is added to the project with the `member` role.

---

# Tasks

## Create Task

**POST** `/projects/{project}/tasks`

Creates a task inside a project.

### Authentication

Required.

### Request Body

```json
{
    "title": "Implement authentication",
    "description": "Implement Sanctum authentication.",
    "status": "todo",
    "priority": "high",
    "assigned_to": 15,
    "due_date": "2026-10-15"
}
```

### Allowed Statuses

```text
todo
in_progress
stuck
review
completed
cancelled
```

### Allowed Priorities

```text
low
medium
high
urgent
```

### Validation

| Field | Rules |
|---|---|
| `title` | Required, string, maximum 255 characters |
| `description` | Optional, string |
| `status` | Required |
| `priority` | Required |
| `assigned_to` | Optional, existing user ID |
| `due_date` | Optional, YYYY-MM-DD |

If a task is assigned, the assigned user must be a member of the project.

---

## List Tasks

**GET** `/projects/{project}/tasks`

Returns tasks belonging to the project.

### Authentication

Required.

### Query Parameters

| Parameter | Description |
|---|---|
| `search` | Searches task title and description |
| `status` | Filter by status |
| `priority` | Filter by priority |
| `assigned_to` | Filter by assigned user |
| `due_date` | Filter by due date |
| `sort_by` | Field used for sorting |
| `sort_direction` | `asc` or `desc` |
| `per_page` | Number of results per page |

Maximum page size is 100.

---

## Get Task

**GET** `/projects/{project}/tasks/{task}`

Returns a specific task.

### Authentication

Required.

The task must belong to the specified project.

---

## Update Task

**PUT** `/projects/{project}/tasks/{task}`

Updates an existing task.

### Authentication

Required.

### Request Body

```json
{
    "title": "Updated task",
    "description": "Updated description.",
    "status": "in_progress",
    "priority": "high",
    "assigned_to": 15,
    "due_date": "2026-10-20"
}
```

Changing the assignee additionally requires the `assign_task` permission.

The new assignee must be a member of the project.

Task status changes are recorded in the activity log.

---

## Delete Task

**DELETE** `/projects/{project}/tasks/{task}`

Deletes a task.

### Authentication

Required.

---

# Comments

## Add Comment

**POST** `/projects/{project}/tasks/{task}/comments`

Adds a comment to a task.

### Authentication

Required.

### Request Body

```json
{
    "comment": "This task is ready for review."
}
```

---

## List Comments

**GET** `/projects/{project}/tasks/{task}/comments`

Returns comments belonging to the task.

### Authentication

Required.

---

## Update Comment

**PUT** `/projects/{project}/tasks/{task}/comments/{comment}`

Updates a comment.

### Authentication

Required.

Only the comment author can update it.

### Request Body

```json
{
    "comment": "Updated comment."
}
```

---

## Delete Comment

**DELETE** `/projects/{project}/tasks/{task}/comments/{comment}`

Deletes a comment.

### Authentication

Required.

Only the comment author can delete it.

---

# Attachments

## Upload Attachment

**POST** `/projects/{project}/tasks/{task}/attachments`

Uploads an attachment to a task.

### Authentication

Required.

### Request

Multipart form-data.

| Field | Type | Description |
|---|---|---|
| `file` | File | Attachment to upload |

### Maximum File Size

10 MB.

### Allowed File Types

```text
jpg
jpeg
png
gif
pdf
doc
docx
xls
xlsx
txt
zip
```

---

## List Attachments

**GET** `/projects/{project}/tasks/{task}/attachments`

Returns task attachments.

### Authentication

Required.

---

## Get Attachment

**GET** `/projects/{project}/tasks/{task}/attachments/{attachment}`

Returns information about an attachment.

### Authentication

Required.

---

## Delete Attachment

**DELETE** `/projects/{project}/tasks/{task}/attachments/{attachment}`

Deletes an attachment.

### Authentication

Required.

Only the user who uploaded the attachment can delete it.

---

# Notifications

## List Notifications

**GET** `/notifications`

Returns notifications belonging to the authenticated user.

### Authentication

Required.

### Pagination

10 notifications per page.

---

## Get Unread Count

**GET** `/notifications/unread-count`

Returns the number of unread notifications.

### Authentication

Required.

---

## Mark Notification as Read

**PATCH** `/notifications/{notification}/read`

Marks a notification as read.

### Authentication

Required.

---

## Mark All Notifications as Read

**PATCH** `/notifications/read-all`

Marks all unread notifications as read.

### Authentication

Required.

---

# Activity Logs

## List Project Activity

**GET** `/projects/{project}/activity`

Returns activity logs for a project.

### Authentication

Required.

### Pagination

20 activity records per page.

---

# Project Statistics

## Get Project Statistics

**GET** `/projects/{project}/stats`

Returns calculated project statistics.

### Authentication

Required.

Statistics include task counts by status and overdue task information.

Statistics are cached using Redis and invalidated when relevant task data changes.

---

# Rate Limiting

TaskFlow applies rate limits to protect authentication and API endpoints.

| Endpoint Group | Limit |
|---|---|
| Register / Login | 5 requests per minute per IP |
| Password Reset | 3 requests per minute per IP |
| Email Verification Resend | 3 requests per minute per IP |
| Authenticated API | 60 requests per minute per user |

A rate-limited request returns:

```text
429 Too Many Requests
```

---

# Authorization

TaskFlow uses:

1. Laravel Sanctum for authentication.
2. Spatie Laravel Permission for roles and permissions.
3. Laravel Policies for resource-level authorization.

Application roles:

```text
admin
manager
member
```

Project membership roles:

```text
manager
member
```

---

# Background Processing

TaskFlow uses Redis queues and Laravel Horizon for background jobs.

Examples include:

- Email verification
- Project invitation emails
- Task assignment notifications
- Task overdue notifications

---

# Scheduled Tasks

TaskFlow includes a scheduled task processor for overdue tasks.

Command:

```text
php artisan tasks:process-overdue
```

The command checks overdue tasks and sends notifications to assigned users.

The command runs daily through Laravel's scheduler.

---

# File Storage

Task attachments are stored using Laravel's filesystem.

Attachment metadata is stored in the database, including:

- Original file name
- Storage path
- File type
- File size
- Uploader
- Task

Create the public storage link with:

```text
php artisan storage:link
```
