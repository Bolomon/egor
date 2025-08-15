<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use Illuminate\Http\Request;

class QuestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return Quest::all()->map(function ($quest) use ($user) {
            $quest->completed = $user
                ? $user->quests()->where('quest_id', $quest->id)->wherePivot('completed', true)->exists()
                : false;
            return $quest;
        });
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $quest = Quest::findOrFail($id);

        $quest->completed = $user
            ? $user->quests()->where('quest_id', $quest->id)->wherePivot('completed', true)->exists()
            : false;

        return $quest;
    }

    public function showByName(Request $request, $name)
    {
        $user = $request->user();
        $quest = Quest::where('name', $name)->firstOrFail();

        $quest->completed = $user
            ? $user->quests()->where('quest_id', $quest->id)->wherePivot('completed', true)->exists()
            : false;

        return $quest;
    }

    public function complete(Request $request, $name)
    {
        $user = $request->user();

        $quest = Quest::where('name', $name)->firstOrFail();

        $user->quests()->syncWithoutDetaching([
            $quest->id => ['completed' => true]
        ]);

        return response()->json([
            'message' => 'Квест отмечен как выполненный',
            'quest' => $quest,
            'completed' => true
        ]);
    }

    public function completedCount(Request $request)
    {
        $user = $request->user();

        $count = $user->quests()->where('completed', true)->count();

        return response()->json([
            'completed_quests_count' => $count
        ]);
    }
}
