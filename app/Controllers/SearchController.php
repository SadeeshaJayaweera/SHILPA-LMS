<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\SearchService;

class SearchController extends Controller {
    /**
     * Full Search Results View
     */
    public function index(Request $request): Response {
        $query = (string)($request->query('q') ?? '');
        $type = $request->query('type');
        if ($type !== null) {
            $type = (string)$type;
        }

        $userRole = (string)session()->get('user_role', 'student');
        $results = SearchService::search($query, $type, 20, $userRole);

        $data = [
            'query' => $query,
            'type' => $type,
            'results' => $results,
            'title' => ($query !== '' ? "Search: {$query}" : 'Global Search') . ' — SHILPA LMS',
        ];

        return $this->render('search/index', array_merge($data, ['data' => $data]));
    }

    /**
     * Autocomplete Quick Search JSON API for Cmd+K palette
     */
    public function apiSearch(Request $request): Response {
        $query = (string)($request->query('q') ?? '');
        $limit = max(1, min(20, (int)($request->query('limit') ?? 8)));
        $userRole = (string)session()->get('user_role', 'student');

        $quickResults = SearchService::quickSearch($query, $limit, $userRole);

        return $this->json([
            'status' => 'success',
            'query' => $query,
            'count' => count($quickResults),
            'results' => $quickResults,
        ]);
    }

    /**
     * Platform Commands API Endpoint
     */
    public function commands(Request $request): Response {
        $userRole = (string)session()->get('user_role', 'student');
        $commands = SearchService::getCommands($userRole);

        return $this->json([
            'status' => 'success',
            'commands' => $commands,
        ]);
    }
}
