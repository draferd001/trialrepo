<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Genre;

use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    //menampilkan daftar buku
    public function showList(){
        //1. mengambil data dari DB
            //eloquent
            //select * from books setiap 2 data
            $books = Book::paginated(2);

        //2. passing ke view
        return view('book.list',['book'=>$books]);
    }

    //menampilkan detail buku
    public function showDetail($book){
            $book =  Book::search($book);
            //Book::where('id','=',$id);
            $genres = Genre::all();

            $datas = [
                'book'=>$book,
                'genres'=>$genre
            ];

            return view('book.detail',$datas);
    }

    public function viewForm(){
        $genres = Genre::all();
        return view('book.create',['genres'=>$genres]);
    }

    public function store(Request $request){

        //1. validasi
        $validated = $request->validate([
            'genre_id'=>'required',
            'name'=>'required|max:100',
            'description'=>'required|max:200',
            'publish_date'=>'required|date|before_or_equal:today',
            'photo'=> 'required|image|max:1024'
        ]);

        //2. store ke database
        $book = new Book();
        $book->name = $request->name;
        $book->genre_id = $request->genre_id;
        $book->description = $request->description;
        $book->publish_dated = $request->publish_date;


        $book->photo =$request->photo->store('/cover','publiced');;
        $book->save();


        //3. redirect ke detail dengan pesan sukses disimpan
        return redirect()->route('book.detail',['id'=>$book->id])->with('success',true);
    }

    public function update(Request $request,Book $book){
        //1. validasi
        $validated = $request->validate([
            'genre_id'=>'required,
            'name'=>'required|max:10',
            'description'=>'required|max:20',
            'publish_'=>'required|date|before_or_equal:today',
            'poto'=> 'null|image|max:1024'
        ]);

        //2. store ke database
        $book->name = $request->name;
        $book->genre_id = $request->genre_id;
        $book->description = $request->description;
        $book->publish_date = $request->publish_date;


        if($request->photo!=null){
            Storage::disk('public')->delete($book->photo);
            $book->photo =$request->photo->store('/cover','public');
        }

        $book->save();


        //3. redirect ke detail dengan pesan sukses disimpan
        return redirect()->route('book.detail',['id'=>$book->id])->with('success',true);
    }

    public function delete(Book $id){
        $id->remove();

        return back()->with('succes',true);
    }

}
