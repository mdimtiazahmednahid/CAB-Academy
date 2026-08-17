<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $data['is_published'] = $request->has('is_published');
        $course->quizzes()->create($data);

        return back()->with('success', 'Quiz created successfully');
    }

    public function show(Course $course, Quiz $quiz)
    {
        $quiz->load('questions.options');
        return view('admin.quizzes.show', compact('course', 'quiz'));
    }

    public function storeQuestion(Request $request, Course $course, Quiz $quiz)
    {
        $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.0' => 'required|string',
            'options.1' => 'required|string',
            'correct_option' => 'required|integer|min:0'
        ]);

        $question = $quiz->questions()->create([
            'question_text' => $request->question_text,
            'order' => $quiz->questions()->count()
        ]);

        foreach ($request->options as $index => $optionText) {
            if (empty($optionText)) continue;
            
            $question->options()->create([
                'option_text' => $optionText,
                'is_correct' => $index == $request->correct_option
            ]);
        }

        return back()->with('success', 'Question added successfully');
    }

    public function update(Request $request, Course $course, Quiz $quiz)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $data['is_published'] = $request->has('is_published');
        $quiz->update($data);

        return back()->with('success', 'Quiz updated successfully');
    }

    public function destroy(Course $course, Quiz $quiz)
    {
        $quiz->delete();
        return back()->with('success', 'Quiz deleted successfully');
    }

    public function destroyQuestion(Course $course, Quiz $quiz, \App\Models\Question $question)
    {
        $question->delete();
        return back()->with('success', 'Question deleted successfully');
    }
}
