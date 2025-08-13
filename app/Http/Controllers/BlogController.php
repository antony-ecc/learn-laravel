<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $title = $request->title;

        //Query Builder
        // $blogs = DB::table('blogs')->where('title', 'LIKE', '%'. $title . '%')->orderBy('created_at', 'desc')->paginate(10);

        //Eloquent ORM
        $user = Auth::user();
        $blogs = Blog::with(['tags', 'comments'])->when($user->role !== 'admin', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('title', 'LIKE', '%' .$title. '%')->orderBy('created_at', 'desc')->paginate(10);
        return view('blog', ['blogs' => $blogs, 'title' => $title]);
    }

    public function create()
    {
        $tags = Tag::all();
        return view('/blogs/create', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:blogs|max:20',
            'description' => 'required',
            'status' => 'required',
            'image' => 'image|mimes:png,jpg|max:2048',
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
        // $id_min = User::pluck('id')->min();
        // $id_max = User::pluck('id')->max();
        $user = Auth::user();

        if($request->file('image')) {
            $image = Storage::disk('public')->putFile('images', $request->file('image'));
        }

        $data = Blog::create([
            'title' => $request->title,
            'deskripsi' => $request->description,
            'status' => $request->status,
            'user_id' => $user->id,
            'image' => $image,
        ]);

        $data->tags()->attach($request->tags);

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
        $tags = Tag::all();
        $blog = Blog::with('tags')->findOrFail($id);

        if (Gate::denies('update-post', $blog)) {
            // abort(403);
            return redirect()->route('blogs.index')->with('error', 'Tidak bisa edit blog punya orang lain');
        }
        
        return view('blogs/edit', ['blog' => $blog, 'tags' => $tags]);
    }

    public function update($id, Request $request)
    {
        // return $request->all();
        $request->validate([
            'title' => 'required|unique:blogs,title,'.$id.'|max:255',
            'description' => 'required',
            'status' => 'required',
            'image' => 'image|mimes:png,jpg|max:2048',
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
        // $id_min = User::pluck('id')->min();
        // $id_max = User::pluck('id')->max();
        $user = Auth::user();
        $blog = Blog::findOrFail($id);

        // if($request->user()->cannot('update', $blog)) {
        //     abort(403);
        // }
        Gate::authorize('update', $blog);

        if($request->hasFile('image')) {
            if($blog->image && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }

            $image = Storage::disk('public')->putFile('images', $request->file('image'));
        }

        $blog->update([
            'title' => $request->title,
            'deskripsi' => $request->description,
            'status' => $request->status,
            'user_id' => $user->id,
            'image' => $image,
        ]);

        $blog->tags()->sync($request->tags);
        
        return redirect()->route('blogs.index')->with('success', 'Blog Edited Successfully!');
    }

    public function delete(Request $request, $id)
    {
        // $blog = DB::table('blogs')->where('id', $id)->delete();
        $blog = Blog::findOrFail($id);

        // if($request->user()->cannot('delete', $blog)) {
        //     abort(403);
        // }
        $response = Gate::inspect('delete', $blog);
        if ($response->allowed()) {
            $blog->delete();
            
            return redirect()->route('blogs.index')->with('success', 'Blog Deleted Successfully!');
        } else {
            abort(403, $response->message());
        }

        if(!$blog) {
            return redirect()->route('blog.index')->with('failed', 'Blog Failed to Delete!');
        }
            
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
        $blog = Blog::with(['user', 'comments', 'tags'])->findOrFail($id);
        return view('blogs.show', compact('blog'));
    }
}
