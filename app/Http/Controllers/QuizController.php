<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function getQuizDetails()
    {
        $googleFormUrl = 'https://docs.google.com/forms/d/15fEu07kDJZCMxkRx041i6HHtisDvVpWa_lTaZQZjScM/viewform';
        $googleFormTitle = 'Community Quiz';

        return response()->json([
            'url' => $googleFormUrl,
            'title' => $googleFormTitle,
        ]);
    }
}
