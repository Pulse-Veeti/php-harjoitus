# 🎯 YOUR LEARNING TASK: Implement Task Management

## ✅ What I've Refactored For You

I've successfully refactored your entire application to follow **professional MVC architecture**:

### 🏗️ **New Structure Created:**
```
src/
├── models/           # Data layer (DONE ✅)
│   ├── BaseModel.php
│   ├── UserModel.php
│   ├── TeamModel.php
│   └── ProjectModel.php
├── controllers/      # Business logic (DONE ✅)
│   ├── BaseController.php
│   ├── AuthController.php
│   ├── TeamController.php
│   └── ProjectController.php
├── views/           # Presentation layer (DONE ✅)
│   ├── layouts/
│   ├── auth/
│   ├── teams/
│   ├── projects/
│   └── tasks/       # ⚠️ YOU IMPLEMENT THIS
```

### 🎉 **Fully Working Features:**
- ✅ **User Authentication** - Login/Register/Logout
- ✅ **Team Management** - Create/Join/Delete teams
- ✅ **Project Management** - Create projects within teams
- ✅ **Proper MVC Separation** - Clean, maintainable code
- ✅ **Security** - CSRF protection, session management
- ✅ **Database Models** - Reusable, testable data access

## 🎓 **YOUR TASK: Implement Task Management**

You need to implement **TaskModel** and **TaskController** following the **exact same pattern** I used for Users, Teams, and Projects.

### 🎯 **Learning Objectives:**
1. **Understand MVC pattern** by implementing it yourself
2. **Practice database design** with complex relationships
3. **Learn business logic separation**
4. **Follow established patterns** and conventions

## 📋 **What You Need to Implement:**

### **1. TaskModel.php** (Data Layer)
```php
// Location: src/models/TaskModel.php
// Follow the pattern of UserModel.php and TeamModel.php

class TaskModel extends BaseModel {
    // Implement these methods:

    public function findById($id) { }
    public function getByProjectId($projectId) { }
    public function getByOwner($userId, $projectId) { }
    public function getCompleted($projectId) { }
    public function create($taskName, $projectId, $taskText, $taskColor, $dueDate, $createdBy) { }
    public function assignToUser($taskId, $userId) { }
    public function unassign($taskId) { }
    public function markComplete($taskId) { }
    public function getProjectTeammates($projectId) { }
}
```

### **2. TaskController.php** (Business Logic)
```php
// Location: src/controllers/TaskController.php
// Follow the pattern of TeamController.php and ProjectController.php

class TaskController extends BaseController {
    // Implement these methods:

    public function create() { }        // Handle task creation
    public function assign() { }       // Assign task to user
    public function unassign() { }     // Unassign task
    public function complete() { }     // Mark task complete
}
```

### **3. Update ProjectController.php**
```php
// In src/controllers/ProjectController.php
// Update the show() method to load tasks:

public function show($projectId) {
    // Add task loading here
    $taskModel = new TaskModel($this->pdo);
    $tasks = $taskModel->getByProjectId($projectId);
    $myTasks = $taskModel->getByOwner($userId, $projectId);
    $completedTasks = $taskModel->getCompleted($projectId);
    $teammates = $taskModel->getProjectTeammates($projectId);

    $this->view('projects/show', [
        'project' => $project,
        'tasks' => $tasks,
        'myTasks' => $myTasks,
        'completedTasks' => $completedTasks,
        'teammates' => $teammates
    ]);
}
```

### **4. Update Project View**
```php
// In src/views/projects/show.php
// Replace the placeholder with actual task display
// Look at how team members are displayed in teams/show.php
```

### **5. Create Action Files**
```php
// src/actions/createTask.php
// src/actions/assignTask.php
// src/actions/unassignTask.php
// src/actions/completeTask.php

// Follow the pattern of src/actions/createTeam.php
```

## 📖 **Step-by-Step Implementation Guide:**

### **Step 1: Study the Existing Code**
1. Look at `UserModel.php` - see how database queries are structured
2. Look at `TeamController.php` - see how business logic is handled
3. Look at `AuthController.php` - see how form processing works

### **Step 2: Implement TaskModel.php**
- Start with `findById()` method (simplest)
- Then implement `getByProjectId()`
- Follow the same SQL query patterns as TeamModel

### **Step 3: Implement TaskController.php**
- Start with `create()` method
- Copy the pattern from `TeamController::create()`
- Remember: validate CSRF, validate input, call model, redirect with message

### **Step 4: Create Action Files**
- Copy `src/actions/createTeam.php`
- Update to use TaskController instead
- Repeat for other actions

### **Step 5: Update Views**
- Update `src/views/projects/show.php`
- Add task listing and forms
- Follow the HTML structure of teams/show.php

## 💡 **Hints and Tips:**

### **Database Queries to Implement:**
```sql
-- Get tasks by project
SELECT tasks.*, users.name as owner_name, creator.name as creator_name
FROM tasks
LEFT JOIN users ON tasks.task_owner = users.id
LEFT JOIN users as creator ON tasks.created_by = creator.id
WHERE project_id = :project_id AND status != 'Completed'

-- Get team members for task assignment
SELECT DISTINCT users.id, users.name
FROM users
JOIN user_teams ON users.id = user_teams.user_id
JOIN projects ON user_teams.team_id = projects.team_id
WHERE projects.id = :project_id
```

### **Form Examples:**
```html
<!-- Create Task Form -->
<form action="/actions/createTask.php" method="POST">
    <?php echo csrfField(); ?>
    <input type="text" name="task_name" required>
    <input type="text" name="task_text" required>
    <input type="text" name="task_color" required>
    <input type="date" name="due_date" required>
    <input type="hidden" name="project_id" value="<?php echo $projectId; ?>">
    <button type="submit">Create Task</button>
</form>

<!-- Assign Task Form -->
<form action="/actions/assignTask.php" method="POST">
    <?php echo csrfField(); ?>
    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
    <select name="task_owner" required>
        <?php foreach($teammates as $teammate): ?>
            <option value="<?php echo $teammate['id']; ?>">
                <?php echo htmlspecialchars($teammate['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Assign</button>
</form>
```

## 🚀 **Success Criteria:**

You'll know you've succeeded when:
- ✅ Tasks display on project pages
- ✅ You can create new tasks
- ✅ You can assign tasks to team members
- ✅ You can mark tasks as complete
- ✅ All forms have CSRF protection
- ✅ All data is properly sanitized
- ✅ Error messages display correctly

## 🤔 **Need Help?**

**If you get stuck:**
1. **Compare with working code** - Look at how TeamModel does similar operations
2. **Check the database** - Make sure your SQL queries are correct
3. **Debug step by step** - Add `var_dump()` to see what data you're getting
4. **Follow the pattern** - The MVC structure is consistent across all components

## 🏆 **Why This is Valuable Learning:**

1. **Industry Standard** - This MVC pattern is used in Laravel, Symfony, CodeIgniter
2. **Separation of Concerns** - You'll understand why mixing database and HTML is bad
3. **Code Reusability** - Models can be used by web, API, and CLI interfaces
4. **Team Development** - Multiple developers can work without conflicts
5. **Testing** - Each component can be tested independently

**Good luck! This is exactly how professional PHP development works.** 🎯