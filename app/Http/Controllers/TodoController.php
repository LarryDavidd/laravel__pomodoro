<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TodoController extends Controller
{
    public function index(): JsonResponse
    {
        $todos = Todo::where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($todos);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'idTodo' => 'sometimes|string',
            'pomodoroValue' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'timeCreate' => 'required|date',
            'isComplete' => 'boolean',
            'priority' => 'required|in:low,medium,high'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $todo = Todo::create([
            'id_todo' => $request->idTodo ?? Str::uuid(),
            'pomodoro_value' => $request->pomodoroValue,
            'title' => $request->title,
            'time_create' => $request->timeCreate,
            'is_complete' => $request->isComplete ?? false,
            'priority' => $request->priority,
            'user_id' => auth()->id()
        ]);

        return response()->json($todo, 201);
    }

    public function show(Todo $todo): JsonResponse
    {
        if ($todo->user_id !== auth()->id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json($todo);
    }

    public function update(Request $request, Todo $todo): JsonResponse
    {
        if ($todo->user_id !== auth()->id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'pomodoroValue' => 'integer|min:1',
            'title' => 'string|max:255',
            'isComplete' => 'boolean',
            'priority' => 'in:low,medium,high'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $todo->update([
            'pomodoro_value' => $request->pomodoroValue ?? $todo->pomodoro_value,
            'title' => $request->title ?? $todo->title,
            'is_complete' => $request->isComplete ?? $todo->is_complete,
            'priority' => $request->priority ?? $todo->priority
        ]);

        return response()->json($todo);
    }

    public function destroy(Todo $todo): JsonResponse
    {
        if ($todo->user_id !== auth()->id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $todo->delete();

        return response()->json(['message' => 'Todo deleted successfully']);
    }
}