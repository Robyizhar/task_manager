<?php
    include 'config.php';

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error)
        die("Connection failed: " . $conn->connect_error);

    function getTasks($conn, $searchTitle = '', $searchStatus = '') {
        $query = "SELECT * FROM tasks WHERE 1";

        if ($searchTitle)
            $query .= " AND title LIKE '%$searchTitle%'";

        if ($searchStatus)
            $query .= " AND status = '$searchStatus'";

        $result = $conn->query($query);
        return $result;
    }

    /* Function to sanitize user input */
    function sanitizeInput($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        /* Check if the form to add a task has been submitted */
        if (isset($_POST['add_task'])) {
            /* Sanitize the user input */
            $title = sanitizeInput($_POST['title']);
            $description = sanitizeInput($_POST['description']);

            /* Use prepared statements to insert the task into the database */
            $stmt = $conn->prepare("INSERT INTO tasks (title, description) VALUES (?, ?)");
            if ($stmt) {
                $stmt->bind_param('ss', $title, $description);
                $stmt->execute();
                $stmt->close();
            } else {
                /* Handle the error */
                echo "Error preparing statement: " . $conn->error;
            }
        }

        /* Check if the form to update the task status has been submitted */
        if (isset($_POST['update_status'])) {
            /* Sanitize the user input */
            $taskId = sanitizeInput($_POST['task_id']);
            $newStatus = sanitizeInput($_POST['status']);

            /* Use prepared statements to update the task status */
            $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('si', $newStatus, $taskId);
                $stmt->execute();
                $stmt->close();
            } else {
                /* Handle the error */
                echo "Error preparing statement: " . $conn->error;
            }
        }
    }

    $tasks = getTasks($conn, $_GET['search_title'] ?? '', $_GET['search_status'] ?? '');
    $index = 1;
    // var_dump($tasks->fetch_assoc());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Task Manager</h1>        
        <div class="bg-white p-4 rounded shadow mb-4">
            <h2 class="text-xl font-semibold mb-2">Add New Task</h2>
            <form method="post" class="grid grid-cols-3 gap-2">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title:</label>
                    <input type="text" id="title" name="title" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                    <textarea id="description" name="description" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>
                <div class="flex items-center">
                    <button type="submit" name="add_task" class="bg-blue-500 text-white px-4 py-2 rounded">Add Task</button>
                </div>
            </form>
        </div>
        <div class="bg-white p-4 rounded shadow mb-4">
            <h2 class="text-xl font-semibold mb-2">Search Tasks</h2>
            <form method="get" class="grid grid-cols-3 gap-2">
                <div>
                    <label for="search_title" class="block text-sm font-medium text-gray-700">Title:</label>
                    <input type="text" id="search_title" name="search_title" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="search_status" class="block text-sm font-medium text-gray-700">Status:</label>
                    <select id="search_status" name="search_status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">All</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded my-5">Search</button>
                </div>
            </form>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold mb-2">Task List</h2>
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="border-double border-4 border-sky-500 py-2">No</th>
                        <th class="border-double border-4 border-sky-500 py-2">Title</th>
                        <th class="border-double border-4 border-sky-500 py-2">Status</th>
                        <th class="border-double border-4 border-sky-500 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($task = $tasks->fetch_assoc()): ?>
                        <tr class="bg-gray-100">
                            <td class="border-double border-4 border-sky-500 py-2 px-4"><?php echo $index; ?></td>
                            <td class="border-double border-4 border-sky-500 py-2 px-4"><?php echo htmlspecialchars($task['title']); ?></td>
                            <td class="border-double border-4 border-sky-500 py-2 px-4"><?php echo htmlspecialchars($task['status']); ?></td>
                            <td class="border-double border-4 border-sky-500 py-2 px-4">
                                <form method="post" class="inline-block">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <select name="status" class="px-2 py-1 border border-gray-300 rounded-md">
                                        <option value="Pending" <?php echo $task['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="In Progress" <?php echo $task['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="Completed" <?php echo $task['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                    <button type="submit" name="update_status" class="bg-green-500 text-white px-2 py-1 rounded">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php $index++; endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
