<?php
// app/Http/Controllers/TodoController.php

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
            'title' => 'required|string|max:255',
            'pomodoroValue' => 'required|integer|min:1|max:10',
            'timeCreate' => 'required|date',
            'isComplete' => 'boolean',
            'priority' => 'required|in:No priority,Low priority,Medium priority,High priority'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Преобразуем дату из формата "15, 1, 2024" в timestamp
        $timeCreate = $this->parseDateString($request->timeCreate);

        $todo = Todo::create([
            'id_todo' => $request->idTodo ?? Str::uuid(),
            'user_id' => auth()->id(),
            'title' => $request->title,
            'pomodoro_value' => $request->pomodoroValue,
            'time_create' => $timeCreate,
            'is_complete' => $request->isComplete ?? false,
            'priority' => $request->priority,
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
            'title' => 'sometimes|string|max:255',
            'pomodoroValue' => 'sometimes|integer|min:1|max:10',
            'timeCreate' => 'sometimes|date',
            'isComplete' => 'sometimes|boolean',
            'priority' => 'sometimes|in:No priority,Low priority,Medium priority,High priority'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [];
        
        if ($request->has('title')) {
            $updateData['title'] = $request->title;
        }
        
        if ($request->has('pomodoroValue')) {
            $updateData['pomodoro_value'] = $request->pomodoroValue;
        }
        
        if ($request->has('timeCreate')) {
            $updateData['time_create'] = $this->parseDateString($request->timeCreate);
        }
        
        if ($request->has('isComplete')) {
            $updateData['is_complete'] = $request->isComplete;
        }
        
        if ($request->has('priority')) {
            $updateData['priority'] = $request->priority;
        }

        $todo->update($updateData);

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

    public function stats(): JsonResponse
    {
        $user = auth()->user();
        $todos = Todo::where('user_id', $user->id)->get();

        $totalPomodoro = $todos->sum('pomodoro_value');
        $completedPomodoro = $todos->where('is_complete', true)->sum('pomodoro_value');
        
        $stats = [
            'dayTodo' => 'Сегодня',
            'estimatedTime' => (string)$totalPomodoro,
            'timeSpent' => (string)$completedPomodoro,
        ];

        return response()->json($stats);
    }

    private function parseDateString(string $dateString): string
    {
        // Обрабатываем разные форматы дат
        if (str_contains($dateString, ',')) {
            // Формат "15, 1, 2024"
            $parts = array_map('trim', explode(',', $dateString));
            if (count($parts) === 3) {
                return sprintf('%04d-%02d-%02d 00:00:00', $parts[2], $parts[1], $parts[0]);
            }
        }
        
        // Пытаемся парсить как ISO строку или другой формат
        try {
            return date('Y-m-d H:i:s', strtotime($dateString));
        } catch (\Exception $e) {
            return now()->format('Y-m-d H:i:s');
        }
    }
}