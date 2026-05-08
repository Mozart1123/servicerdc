<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\News;
use App\Models\NewsletterCampaign;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class ContentController extends Controller
{
    /**
     * News Section
     */
    public function news()
    {
        $articles = News::orderBy('published_at', 'desc')->get();
        return view('admin.content.news', compact('articles'));
    }

    public function newsStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string',
        ]);

        News::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'] ?? 'GÉNÉRAL',
            'published_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Actualité publiée !']);
    }

    public function newsDelete(News $article)
    {
        $article->delete();
        return response()->json(['success' => true, 'message' => 'Article supprimé.']);
    }

    /**
     * Newsletter Section
     */
    public function newsletter()
    {
        $campaigns = NewsletterCampaign::orderBy('created_at', 'desc')->get();
        return view('admin.content.newsletter', compact('campaigns'));
    }

    public function newsletterStore(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'audience' => 'required|string',
        ]);

        $campaign = NewsletterCampaign::create([
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'audience' => $validated['audience'],
            'status' => 'sent', // For now we mark as sent immediately in this flow
            'sent_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Campagne envoyée !']);
    }

    public function newsletterDuplicate(NewsletterCampaign $campaign)
    {
        $newCampaign = $campaign->replicate();
        $newCampaign->status = 'draft';
        $newCampaign->sent_at = null;
        $newCampaign->subject .= ' (Copie)';
        $newCampaign->save();

        return response()->json(['success' => true, 'message' => 'Campagne dupliquée.']);
    }

    public function newsletterDelete(NewsletterCampaign $campaign)
    {
        $campaign->delete();
        return response()->json(['success' => true, 'message' => 'Campagne supprimée.']);
    }

    /**
     * Push Notifications
     */
    public function push()
    {
        $history = Notification::where('type', 'push')->orderBy('created_at', 'desc')->get();
        return view('admin.content.push', compact('history'));
    }

    public function pushBroadcast(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target' => 'required|string',
        ]);

        // Mocking the broadcast to users
        // In reality, we would queue jobs to send via Firebase/OneSignal
        
        Notification::create([
            'type' => 'push',
            'title' => $validated['title'],
            'message' => $validated['message'],
            'data' => ['target' => $validated['target']],
        ]);

        return response()->json(['success' => true, 'message' => 'Notification diffusée aux terminaux.']);
    }
}
