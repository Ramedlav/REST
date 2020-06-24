<?php

namespace App\Http\Controllers;
use App\Book;
use App\Author;
use Illuminate\Http\Request;

class BookController extends Controller
{

    public function GetAllBooks()
    {
        $books = Book::all();
        return response()->json($books);
    }

    public function GetUsersBooks()
    {
        $books = Book::where('user_id',auth()->user()->id)->get();
        return response()->json($books);
    }

    public function CreateBook(Request $request)
    {
        $this->validate($request,[
            'name'=>'required|max:150',
            'id_author'=>'required_without_all:author|numeric',
            'author'=>'required_without_all:author_id||max:150',
            'qty_pages'=>'required|numeric',
            'annotation'=>'required|max:500',
            'img'=>'required',
        ]);
        $data = $request->all();
        if(!isset($request->author_id)){
            $author = Author::first()->where('name','LIKE','%'.$request->author.'%')->get();
            if(!$author){return response()->json('author was not be found');}
            foreach ($author as $aut){
                $author_id = $aut['id'];
            }//добавлять создание автора в случае его отсутствия не буду
            //воизбежании не нужных ошибок
            $data['author_id'] = $author_id;
        }
        unset($data['author']);//just in case ))
        $data['user_id'] = auth()->user()->id;
        $Book = new Book;
        $Book->fill($data);
        $Book->save();
        return response()->json('book has been added');

    }

    public function up($name,$data,$book)
    {
        foreach($data as $key=>$value)//check data
        {
            if(!(in_array($key, $name))){
                unset($data[$key]);// and delete excess
            }
        }

        foreach($name as $key)
        {
            if(!isset($data[$key])){//if data not exist
                $data[$key] = $book->$key;//old data will be added in $data
            }
        }

        return $data;
    }

    public function UpdateBook(Request $request)
    {
        $this->validate($request,[
            'id'=>'required|exists:books,id',
            'name'=>'max:150',
            'id_author'=>'required_without_all:author|numeric',
            'author'=>'required_without_all:author_id||max:150',
            'qty_pages'=>'numeric',
            'annotation'=>'max:500'
        ]);

        $data = $request->all();// fill the data
        $book = Book::find($request->id);// find updating book

        if(!$book){return response()->json('this book was not found in the library');}//check book in DB
        if ($book->user_id !== auth()->user()->id){// check roots for the book
            return response()->json('is not you\'r book! You can\'t to change it');
        }

        $names = ['name','author_id','qty_pages','annotation', 'user_id','img'];//the list of names table value
        $data = $this->up($names, $data ,$book);//check data and fill old data if new data not isset
        $data['user_id'] = auth()->user()->id;//add user id to the data
        //if in request author_id not exist
        if(!isset($request->author_id)){
            //find author and add author id to the data
            $author = Author::where('name','LIKE','%'.$request->author.'%')->first();
            if(!empty($author)){//simple checks...
                $data['author_id'] = $author->id;
            }else{
                return response()->json('author are not exist');
            }
        }
        if (Book::where('id',$request->id)->update($data))// update model
        {
            return response()->json('book is updated');
        }else{
            return response()->json('book was not be updated');
        }
    }

    public function DeleteBook($id)
    {
        $this->validate($id,['id'=>'require|exists:books,id']);
        $book = Book::find($id);// find updating book
        if(!$book){return response()->json('this book was not found in the library');}//check book in DB
        if ($book->user_id !== auth()->user()->id){// check roots for the book
            return response()->json('is not you\'r book! You can\'t to delete it');
        }
        if($book->delete()){
            return response()->json('book is deleted');
        }else{
            return response()->json('book was not to be deleted');
        }
    }
}
