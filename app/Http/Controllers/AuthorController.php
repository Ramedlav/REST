<?php

namespace App\Http\Controllers;
use App\Book;
use App\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function GetAllAuthors()
    {
        $authors = Author::all();
        return response()->json($authors);
    }

    public function GetAuthorsBooks(Request $request)
    {
        $this->validate($request,[
            'id_author'=>'required_without_all:author|numeric',
            'author'=>'required_without_all:author_id||max:150',
        ]);

        if(!isset($request->author_id)){
            $author = $request->author;
            $books = Book::whereHas('author',function ($query) use($author){
                $query->where('name','LIKE','%'.$author.'%');
            })->get();
            return response()->json($books);
        }else{
            if($books = Book::where('author_id',$request->author_id)->get()){
                return response()->json($books);
            }else{return response()->json('Author with this ID are not exist');}
        }
    }
}
