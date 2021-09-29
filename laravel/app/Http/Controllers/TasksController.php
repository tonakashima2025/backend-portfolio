<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function index() {

        // return Task::all();
        return Task::orderBy("sort", "asc")->get();
    
    }

    public function store(Request $request){

        return Task::create($request->all());
    
    }

    public function update(Request $request, $update_task) {

        $task = Task::find($update_task);
        $task->category_id = $request->category_id;
        $task->name = $request->name;
        $task->start_date = $request->start_date;
        $task->end_date = $request->end_date;
        $task->incharge_user = $request->incharge_user;
        $task->percentage = $request->percentage;
        $task->sort = $request->sort;
        $task->save();
        
        return $task;
    }
    
    public function delete($task_id) {
        
        $task = Task::find($task_id);        
        $task->delete();
    
        return $task;
    }

}
