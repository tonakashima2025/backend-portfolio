<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    public function index() {

        return Task::all();
    
    }

    public function store(Request $request){

        return Task::create($request->all());
    
    }

    public function update(Request $request, $update_task) {

        $task = Task::find($update_task);
        $task->name = $request->name;
        $task->start_date = $request->start_date;
        $task->end_date = $request->end_date;
        $task->incharge_user = $request->incharge_user;
        $task->percentage = $request->percentage;
        $task->save();

        return $task;
    }

}
