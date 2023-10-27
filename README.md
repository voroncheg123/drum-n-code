Test Task - Task Management API
This is an API that provides functionality for task management. Users can create, retrieve, update, delete, and filter tasks based on specific criteria.

API Endpoints:
1. Retrieve Tasks
   Endpoint: /api/tasks
   Method: GET
   Description: Retrieves a list of tasks. Supports filtering by status, priority, and a full-text search on title and description. Also supports sorting by createdAt, completedAt, and priority.
   Sample Filters: /api/tasks?status=completed&priority=high&search=project&sort=-priority,createdAt
2. Create Task
   Endpoint: /api/tasks
   Method: POST
   Description: Creates a new task. Requires a title.
   Payload: { "title": "New Task", "description": "This is a new task." }
3. Update Task
   Endpoint: /api/tasks/{taskId}
   Method: PUT
   Description: Updates an existing task.
   Payload: { "title": "Updated Task" }
4. Mark Task as Done
   Endpoint: /api/tasks/{taskId}/done
   Method: PUT
   Description: Marks a specific task as done.
5. Delete Task
   Endpoint: /api/tasks/{taskId}
   Method: DELETE
   Description: Deletes a specific task.