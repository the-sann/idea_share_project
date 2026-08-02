<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneratePromptRequest;
use App\Http\Resources\ImageGenerationResource;
use App\Services\OpenAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromptGenerationController extends Controller
{

    public function __construct(private OpenAiService $openAIService) {}

    /**
     * Summary of index
     * 
     * List Image Generations
     * 
     * Retrieve a paginated list of all image generations created by the authenticated user
     * 
     * Supports filtering by generated prompt and sorting by various fields.
     * 
     * Query Parameters:
     *  - search : Search term to filter by generated_prompt field
     *  - sort : Field name with optional. '_' prefix for descending order
     *  
     *  Example: 'created_at','-created_at','generated_prompt','-file_size'
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = request()->user();
        $query = $user->imageGenerations();

        // apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where('generated_prompt', 'LIKE', '%' . $request->search . '%');
        }

        // apply sorting
        $allowedSortField = ['created_at', 'generated_prompt', 'original_filename', 'file_size'];
        $sortField = 'created_at';
        $sortDirection = 'desc';
        if ($request->has('sort') && !empty($request->sort)) {
            $sort = $request->sort;
            if (str_starts_with($sort, '-')) {
                $sortField = substr($sort, 1);
                $sortDirection = 'desc';
            } else {
                $sortField = $sort;
                $sortDirection = 'asc';
            }
        }
        // validate sort field
        if (!in_array($sortField, $allowedSortField)) {
            $sortField = 'created_at';
            $sortDirection = 'desc';
        }
        $query->orderBy($sortField, $sortDirection);

        $imageGenerations = $user->imageGenerations()
            ->latest()
            ->paginate($request->input('per_page', 10));
        return ImageGenerationResource::collection($imageGenerations);
    }
    /**
     * Generate Prompt
     * 
     * Generate descriptive prompt from image
     * @param GeneratePromptRequest $request
     * @return ImageGenerationResource
     */
    public function store(GeneratePromptRequest $request)
    {
        $user = $request->user();
        $image = $request->file('image');
        $originalFileName = $image->getClientOriginalName();
        $sanizedFileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($originalFileName, PATHINFO_FILENAME));
        $extension = $image->getClientOriginalExtension();
        $safeFileName = $sanizedFileName . '_' . Str::random(32) . ('.') . $extension;
        $imagePath = $image->storeAs('uploads/images', $safeFileName, 'public');
        $generatedPrompt = $this->openAIService->generatePromptFromImage($image);
        $imageGeneration = $user->imageGenerations()->create([
            'image_path' => $imagePath,
            'generated_prompt' => $generatedPrompt,
            'orignal_filename' => $originalFileName,
            'file_size' => $image->getSize(),
            'mime_type' => $image->getMimeType(),
        ]);
        return new ImageGenerationResource($imageGeneration);
    }
}
