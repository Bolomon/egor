<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use Inertia\Inertia;

class QuestViewController extends Controller
{
    public function index(string $name)
    {
        Quest::query()
            ->where('name', $name)
            ->firstOrFail();
        return Inertia::render('Quests/'.ucfirst($name), []);
    }
}
