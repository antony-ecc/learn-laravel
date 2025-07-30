<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $title = $request->title;

        //Query Builder
        // $blogs = DB::table('blogs')->where('title', 'LIKE', '%'. $title . '%')->orderBy('created_at', 'desc')->paginate(10);

        //Eloquent ORM
        $blogs = Blog::where('title', 'LIKE', '%' .$title. '%')->orderBy('created_at', 'desc')->paginate(10);
        return view('blog', ['blogs' => $blogs, 'title' => $title]);
    }

    public function create()
    {
        return view('/blogs/create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:blogs|max:20',
            'description' => 'required',
            'status' => 'required',
        ]);

        //Query Builder
        // $data = DB::table('blogs')->insert([
        //     'title' => $request->title,
        //     'deskripsi' => $request->description,
        //     'status' => $request->status,
        //     'user_id' => fake()->numberBetween(205, 304),
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        //Eloquent ORM
        $id_min = User::pluck('id')->min();
        $id_max = User::pluck('id')->max();
        $data = Blog::create([
            'title' => $request->title,
            'deskripsi' => $request->description,
            'status' => $request->status,
            'user_id' => fake()->numberBetween($id_min, $id_max),
        ]);

        if (!$data) {
            return redirect()->route('blog.index')->with('error', 'Blog Failed tobe Created');
        }
        return redirect()->route('blogs.index')->with('success', 'New Blog Added Successfully!');
    }

    public function show($id)
    {
        //Query Builder
        // $blog = DB::table('blogs')->where('id', $id)->first();

        //Eloquent ORM
        $blog = Blog::findOrFail($id);
        // if (!$blog){
        //     abort(404);
        // }
        return view('blogs/detail', ['blog' => $blog]);
    }

    public function edit($id)
    {
        //Query Builder 
        // $blog = DB::table('blogs')->where('id', $id)->first();

        //Eloquent ORM
        $blog = Blog::findOrFail($id);
        return view('blogs/edit', ['blog' => $blog]);
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'title' => 'required|unique:blogs,title,'.$id.'|max:255',
            'description' => 'required',
            'status' => 'required'
        ]);

        //Query Builder
        // DB::table('blogs')->where('id', $id)->update([
        //     'title' => $request->title,
        //     'deskripsi' => $request->description,
        //     'status' => $request->status,
        //     'user_id' => fake()->numberBetween(204, 304),
        //     'updated_at' => now()
        // ]);

        //Eloquent ORM
        $id_min = User::pluck('id')->min();
        $id_max = User::pluck('id')->max();
        $blog = Blog::findOrFail($id);
        $blog->update([
            'title' => $request->title,
            'deskripsi' => $request->description,
            'status' => $request->status,
            'user_id' => fake()->numberBetween($id_min, $id_max),
        ]);

        return redirect()->route('blogs.index')->with('success', 'Blog Edited Successfully!');
    }

    public function delete($id)
    {
        // $blog = DB::table('blogs')->where('id', $id)->delete();
        $blog = Blog::destroy($id);

        if(!$blog) {
            return redirect()->route('blog.index')->with('failed', 'Blog Failed to Delete!');
        }
            return redirect()->route('blogs.index')->with('success', 'Blog Deleted Successfully!');
    }

    public function trash()
    {
        $blogs = Blog::onlyTrashed()->get();
        return view('blogs.restore', ['blogs' => $blogs]);
    }

    public function restore($id)
    {
        $blogs = Blog::onlyTrashed()->findOrFail($id)->restore();

        if (!$blogs) {
            return redirect()->route('blog.index')->with('failed', 'Data Blog Failed to Restore!');
        }
        return redirect()->route('blogs.index')->with('success', 'Data Blog Restored Successfully!');
    }

    public function homepage()
    {
        $blogs = Blog::with('user')->where('status', 'Active')->orderBy('created_at', 'desc')->get();
        return view('blogs.index', compact('blogs'));
    }

    public function detail($id)
    {
        $blog = Blog::with('user')->findOrFail($id);
        return view('blogs.show', compact('blog'));
    }
}
