<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentTemplateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DocumentTemplate::active()->ordered();

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->boolean('featured')) {
            $query->featured();
        }
        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(fn ($q) => $q->where('title', 'like', $search)->orWhere('description', 'like', $search));
        }

        $perPage = min((int) $request->input('per_page', 50), 100);
        $paginator = $query->paginate($perPage);

        $items = $paginator->getCollection()->map(fn ($t) => $this->serialize($t, false));

        return response()->json([
            'success' => true,
            'data'    => [
                'templates'   => $items,
                'featured'    => DocumentTemplate::active()->featured()->ordered()
                    ->limit(8)->get()->map(fn ($t) => $this->serialize($t, false)),
                'categories'  => $this->groupedCategoryCounts(),
                'pagination'  => [
                    'total'        => $paginator->total(),
                    'per_page'     => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                ],
            ],
        ]);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $template = DocumentTemplate::where('slug', $slug)
            ->when(!$request->user()?->is_admin, fn ($q) => $q->active())
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => ['template' => $this->serialize($template, true)],
        ]);
    }

    public function download(Request $request, string $slug): StreamedResponse|JsonResponse
    {
        $template = DocumentTemplate::where('slug', $slug)
            ->when(!$request->user()?->is_admin, fn ($q) => $q->active())
            ->firstOrFail();

        if (!$template->template_file_path || !Storage::disk('public')->exists($template->template_file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'Template file not available for download yet.',
            ], 404);
        }

        $template->incrementDownload();

        return Storage::disk('public')->download(
            $template->template_file_path,
            $template->template_file_name ?? $template->slug . '.' . pathinfo($template->template_file_path, PATHINFO_EXTENSION)
        );
    }

    private function serialize(DocumentTemplate $t, bool $full): array
    {
        $base = [
            'id'              => $t->id,
            'slug'            => $t->slug,
            'title'           => $t->title,
            'category'        => $t->category,
            'category_label'  => DocumentTemplate::CATEGORIES[$t->category] ?? ucfirst((string) $t->category),
            'description'     => $t->description,
            'preview_image'   => $t->preview_image ? asset($t->preview_image) : null,
            'has_file'        => (bool) $t->template_file_path,
            'file_name'       => $t->template_file_name,
            'file_size_kb'    => $t->template_file_size ? round($t->template_file_size / 1024, 1) : null,
            'field_count'     => is_array($t->placeholder_fields) ? count($t->placeholder_fields) : 0,
            'is_featured'     => (bool) $t->is_featured,
            'sort_order'      => $t->sort_order,
            'download_count'  => (int) $t->download_count,
        ];

        if ($full) {
            $base['placeholder_fields'] = $t->placeholder_fields ?? [];
            $base['guidelines']         = $t->guidelines;
            $base['download_url']       = $t->template_file_path
                ? url('/api/v1/document-templates/' . $t->slug . '/download')
                : null;
            $base['created_at']         = $t->created_at->toIso8601String();
            $base['updated_at']         = $t->updated_at->toIso8601String();
        }

        return $base;
    }

    private function groupedCategoryCounts(): array
    {
        $rows = DocumentTemplate::active()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $result = [];
        foreach (DocumentTemplate::CATEGORIES as $key => $label) {
            $count = (int) ($rows[$key] ?? 0);
            if ($count > 0) {
                $result[] = [
                    'key'       => $key,
                    'label'     => $label,
                    'templates' => $count,
                ];
            }
        }
        return $result;
    }
}
