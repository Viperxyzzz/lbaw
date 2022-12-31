<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Question;
use App\Models\QuestionTag;
use App\Models\Tag;

class SearchController extends Controller
{
    /**
     * Get a set of filtered and ordered questions as requested.
     * 
     * @param Request HTTP request with optional query params 'order' and 'search'.
     * @return Array Array of questions.
     */
    public function get_questions(Request $request) {
      $direction =  $request->input('direction') ?? 'desc';
      $order = $request->input('order') ?? 'date';
      if($request->has('searchText')){
        
        $searchText = $request->input('searchText');
        $stripedText = preg_replace('/[^0-9a-zA-ZÀ-ú\s]/', '', $searchText);
  
        $search = str_replace(' ',' | ', $stripedText);
        $questions = Question::whereRaw("tsvectors @@ to_tsquery('english', ?)", [$search])
        ->orWhereRaw("full_text @@ to_tsquery('english', ?)", [$search])
        ->orWhereRaw("title @@ to_tsquery('english', ?)", [$search])
        ->orWhereRaw("author_id IN (SELECT user_id FROM users WHERE name @@ to_tsquery('english', ?))", [$search])
        ->get();
      $tags = $request->input('tags') ?? [];

      if($request->input('searchText') != null){
        $questions = Question::where('title', 'like', '%' . $request->input('searchText') . '%')
          ->orWhere('full_text', 'like', '%' . $request->input('searchText') . '%')
          ->orWhere('tsvectors', 'like', '%' . $request->input('searchText') . '%')
          ->orderBy($order, $direction)->get();
      }
      else{
        $questions = Question::orderBy($order, $direction)->get();
      }

      if ($tags != []) {
        $questions = $questions->filter(function ($question) use($tags) {
          return QuestionTag::where('question_id', '=', $question->question_id)
            ->whereIn('tag_id', $tags)->exists();
        });
      }

      foreach($questions as $question) {
        $question['author_name'] = $question->author->name;
        $question['date_distance'] = $question->date_distance();
        $question['tags'] = $question->tags()->orderBy('tag_id')->get();
      }
      return $questions;
    }

    /**
     * Shows all questions.
     *
     * @return Response
     */
    public function home(Request $request)
    {
      $questions = $this->get_questions($request);
      $tags = Tag::all();
      return view('pages.browse', ['questions' => $questions, 'tags' => $tags ]);
    }

    /**
     * Shows all questions.
     *
     * @return Response
     */
    public function browse(Request $request)
    {
      $questions = $this->get_questions($request);
      return json_encode($questions);
    }
}
