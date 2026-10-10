<?php

namespace App\Http\Controllers;

use App\Models\CourseSession;
use App\Models\CourseSessionTopic;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class CourseSessionTopicController extends Controller
{
    /**
     * List topics for a course session.
     */
    public function index(CourseSession $session)
    {
        return response()->json([
            'data' => $session->topics,
        ]);
    }

    /**
     * Create a topic.
     */
    public function store(Request $request, CourseSession $session)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $data['course_session_id'] = $session->id;

        $topic = CourseSessionTopic::create($data);

        app(AuditLogger::class)->record(
            'course_session_topic.created',
            "Topic created: {$topic->title}",
            $topic,
            [
                'topic_id' => $topic->id,
                'course_session_id' => $session->id,
                'session_title' => $session->title,
                'title' => $topic->title,
                'sort_order' => $topic->sort_order,
            ],
            $request
        );

        return response()->json([
            'data' => $topic,
        ], 201);
    }

    /**
     * Update a topic.
     */
    public function update(Request $request, CourseSessionTopic $topic)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $before = $topic->only([
            'title',
            'description',
            'sort_order',
        ]);

        $topic->update($data);
        $topic->refresh();

        $after = $topic->only([
            'title',
            'description',
            'sort_order',
        ]);

        $changes = [];

        foreach ($after as $field => $value) {
            if (($before[$field] ?? null) != $value) {
                $changes[$field] = [
                    'old' => $before[$field] ?? null,
                    'new' => $value,
                ];
            }
        }

        if (!empty($changes)) {
            app(AuditLogger::class)->record(
                'course_session_topic.updated',
                "Topic updated: {$topic->title}",
                $topic,
                [
                    'topic_id' => $topic->id,
                    'course_session_id' => $topic->course_session_id,
                    'changes' => $changes,
                ],
                $request
            );
        }

        return response()->json([
            'data' => $topic,
        ]);
    }

    /**
     * Delete a topic.
     */
    public function destroy(Request $request, CourseSessionTopic $topic)
    {
        $details = [
            'topic_id' => $topic->id,
            'course_session_id' => $topic->course_session_id,
            'title' => $topic->title,
            'sort_order' => $topic->sort_order,
        ];

        app(AuditLogger::class)->record(
            'course_session_topic.deleted',
            "Topic deleted: {$topic->title}",
            $topic,
            $details,
            $request
        );

        $topic->delete();

        return response()->json([
            'message' => 'Topic deleted',
        ]);
    }
}