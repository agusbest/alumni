<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostReaction;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::with(['user', 'comments.user', 'reactions'])
            ->orderByDesc('pinned')->latest()->paginate(10);

        return view('mading.index', compact('posts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'body' => ['required', 'string', 'max:600'],
        ]);
        Post::create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('mading.index')->with('ok', 'Ditempel di mading!');
    }

    public function comment(Request $request, Post $post): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:200']]);
        PostComment::create(['post_id' => $post->id, 'user_id' => $request->user()->id, 'body' => $data['body']]);

        return back();
    }

    public function react(Request $request, Post $post): RedirectResponse
    {
        $data = $request->validate(['emoji' => ['required', 'in:'.implode(',', Post::EMOJIS)]]);
        $row = PostReaction::where(['post_id' => $post->id, 'user_id' => $request->user()->id, 'emoji' => $data['emoji']]);

        if ($row->exists()) {
            $row->delete();
        } else {
            PostReaction::create(['post_id' => $post->id, 'user_id' => $request->user()->id, 'emoji' => $data['emoji']]);
        }

        return back();
    }

    public function pin(Post $post): RedirectResponse
    {
        $post->update(['pinned' => ! $post->pinned]);

        return back();
    }

    public function destroy(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
        $post->delete();

        return back()->with('ok', 'Postingan dihapus.');
    }

    public function destroyComment(Request $request, PostComment $comment): RedirectResponse
    {
        abort_unless($comment->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
        $comment->delete();

        return back();
    }

    public function report(Request $request, Post $post): RedirectResponse
    {
        Report::create([
            'reporter_id' => $request->user()->id,
            'type' => 'post',
            'target_id' => $post->id,
            'label' => 'Mading: '.$post->title,
            'reason' => 'Dilaporkan dari mading',
        ]);

        return back()->with('ok', 'Laporan terkirim ke admin.');
    }
}
