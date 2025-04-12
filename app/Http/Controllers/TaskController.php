<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Task;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    use AuthorizesRequests;

    protected $taskService;

    /**
     * The constructor function initializes a TaskService object within the class.
     * 
     * @param TaskService taskService The `TaskService` parameter in the constructor is a dependency
     * injection of the `TaskService` class. This means that an instance of `TaskService` needs to be
     * provided when creating an object of the class that contains this constructor. This allows the
     * class to utilize the functionality provided by the `Task
     */
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    
    /**
     * The function retrieves tasks based on user ID, status, search query, and sorting criteria,
     * paginates the results, and returns JSON response for AJAX requests or renders a view for regular
     * requests.
     * 
     * @param Request request The `index` function you provided is a controller method that handles the
     * logic for displaying a list of tasks based on certain criteria. Let's break down the parameters
     * used in this function:
     * 
     * @return If the request is an AJAX request, the function will return a JSON response containing
     * the formatted tasks and pagination links. If the request is not an AJAX request, the function
     * will return a view called 'tasks.index' with the tasks passed as a compact variable.
     */
    public function index(Request $request)
    {
        $query = Task::where('user_id', Auth::id());
        
        if ($request->status !== null) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('order_by')) {
            $query->orderBy($request->order_by, $request->get('direction', 'asc'));
        }

        $limit = $request->get('limit', 10);
        $tasks = $query->paginate($limit);
        
        if ($request->ajax()) {
            $formattedTasks = array_map(function ($task) {
                $task['created_at'] = \Carbon\Carbon::parse($task['created_at'])->format('M-d-Y');
                return $task;
            }, $tasks->items());
        
            return response()->json([
                'tasks' => $formattedTasks,
                'pagination' => (string) $tasks->links(),
            ]);
        }

        return view('tasks.index', compact('tasks'));
    }

    /**
     * The `store` function in PHP processes a request to create a task, filters out empty subtasks,
     * assigns the user ID, and returns a JSON response with the created task or validation errors.
     * 
     * @param StoreTaskRequest request The `store` function in the code snippet is responsible for
     * storing a new task in the system. Let me explain the parameters used in this function:
     * 
     * @return The `store` function is returning a JSON response. If the task creation is successful,
     * it returns a JSON response with a message "Task created" and the created task data. If there is
     * a validation exception (\Illuminate\Validation\ValidationException), it returns a JSON response
     * with the validation errors and a status code of 422.
     */
    public function store(StoreTaskRequest $request)
    {
        try {
            $data = $request->all();
            $data['subtasks'] = array_filter($request->input('subtasks', [])); // only keep non-empty
            $data['user_id'] = Auth::id();
            $task = $this->taskService->store($data);

            return response()->json(['message' => 'Task created', 'task' => $task]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * The function `update` in PHP updates a task and its subtasks based on the provided request
     * data.
     * 
     * @param UpdateTaskRequest request The `update` function you provided seems to handle updating
     * a task with its subtasks. The function takes two parameters: `` of type
     * `UpdateTaskRequest` and `` of type `Task`.
     * @param Task task The `update` function you provided seems to be handling the update of a task
     * and its subtasks based on the data received in the `UpdateTaskRequest` and the `Task` model.
     * 
     * @return The `update` function returns a redirect response to the 'tasks.index' route with a
     * success message 'Task updated!' if the task update is successful. If a validation exception
     * occurs, it returns a redirect back with validation errors and input data. If any other
     * exception occurs, it returns a redirect back with a general error message.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        try {
            $subtasks = $request->input('subtasks', []);
            $subtaskStatuses = $request->input('subtask_status', []);

            if (count($subtasks) !== count($subtaskStatuses)) {
                throw new \Exception('Subtasks and subtask statuses do not match.');
            }

            $subtasksData = [];
            foreach ($subtasks as $index => $subtask) {
                if (!empty($subtask)) {
                    $subtasksData[] = [
                        'description' => $subtask,
                        'status' => $subtaskStatuses[$index]
                    ];
                }
            }

            $allSubtasksDone = $this->taskService->checkIfAllSubtasksDone($subtasksData);

            if ($allSubtasksDone) {
                $request['status'] = 'done';
            }

            $this->taskService->updateSubtasksStatus($task, $subtasksData);
            $this->taskService->update($task, $request->validated());

            return redirect()->route('tasks.index')->with('success', 'Task updated!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['general' => $e->getMessage()])->withInput();
        }
    } 

    /**
     * The function "destroy" soft deletes a task by its ID and returns a JSON response indicating
     * success or failure.
     * 
     * @param id The `destroy` function you provided seems to be a part of a Laravel controller that
     * soft deletes a task based on the given ``. The `` parameter represents the unique
     * identifier of the task that needs to be soft deleted.
     * 
     * @return If the task with the given ID is successfully soft deleted, a JSON response with the
     * message 'Task soft deleted successfully.' is returned. If the task is not found (i.e., not
     * deleted), a JSON response with the message 'Task not found.' and a status code of 404 is
     * returned.
     */
    public function destroy($id)
    {
        $deleted = $this->taskService->softDelete($id);
    
        if ($deleted) {
            return response()->json(['message' => 'Task soft deleted successfully.']);
        }
    
        return response()->json(['message' => 'Task not found.'], 404);
    }
    
   /**
    * The function `getDeletedTasks` retrieves deleted tasks, calculates the remaining days until
    * permanent deletion, and returns the results in JSON format.
    * 
    * @return The `getDeletedTasks` function returns a JSON response containing information about
    * deleted tasks. The response includes details about each deleted task, such as the number of days
    * remaining until permanent deletion or if the task has already been permanently deleted.
    */
    public function getDeletedTasks()
    {
        $deletedTasks = Task::onlyTrashed()->get()->map(function ($task) {
            $deletedAt = Carbon::parse($task->deleted_at);
            $daysSinceDeletion = $deletedAt->diffInDays(Carbon::now());
            $remainingDays = 30 - $daysSinceDeletion;
            $remainingDays = (int) $remainingDays;

            if ($remainingDays <= 0) {
                $task->deletion_status = 'Permanently deleted';
            } else {
                $task->deletion_status = "{$remainingDays} day(s) remaining until permanent deletion";
            }

            $task->remaining_days_to_delete = $remainingDays;

            return $task;
        });

        return response()->json($deletedTasks);
    }

    /**
     * The function `recoverTask` retrieves a deleted task by its ID, restores it if it is deleted,
     * updates its status to 'to-do', and returns a JSON response indicating the success or failure of
     * the recovery process.
     * 
     * @param taskId The `recoverTask` function is used to recover a soft-deleted task by its ID. The
     * function first tries to find the task with the specified ID, including soft-deleted tasks. If
     * the task is found and it is soft-deleted, it restores the task, updates its status to '
     * 
     * @return The `recoverTask` function returns a JSON response with a message indicating the outcome
     * of the task recovery process.
     */
    public function recoverTask($taskId)
    {
        try {
            $task = Task::withTrashed()->findOrFail($taskId); 
    
            if ($task->trashed()) {
                $task->restore(); 
                $task->status = 'to-do';
                $task->save();
                return response()->json(['message' => 'Task successfully recovered']);
            }
    
            return response()->json(['message' => 'Task was not deleted'], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found'], 404);
        }
    }
}
